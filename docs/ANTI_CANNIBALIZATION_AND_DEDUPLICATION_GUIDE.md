# 🛡️ Guía de Blindaje Anti-Canibalización, Deduplicación y Consolidación de Noticias (Arquitectura Grado Industrial)

Este documento describe la arquitectura, fórmulas matemáticas, lógica de partición y directrices de ingeniería implementadas en **Glodaxia** para evitar la canibalización de palabras clave en Google y la duplicación de artículos cuando múltiples fuentes cubren la misma noticia.

---

## 🎯 El Desafío: Cobertura Concurrente en 62 Fuentes RSS

Al monitorear **62 fuentes RSS simultáneas** (*The Verge*, *Ars Technica*, *TechCrunch*, *Wired*, *The Register*, etc.), cuando ocurre un evento importante de la industria tecnológica, entre 5 y 10 medios publican sobre el mismo hecho con titulares diferentes.

### ⚠️ Consecuencias de No Filtrar Quirúrgicamente:
1. **Canibalización de Keywords**: Múltiples artículos compitiendo entre sí en Google Search, dividiendo el tráfico y la autoridad.
2. **Crawl Budget Wastage**: Googlebot agota su cuota de rastreo en URLs redundantes y deja sin indexar contenido nuevo.
3. **Pérdida de Calidad E-E-A-T**: Señales de automatización masiva sin curaduría (*Scaled Content Abuse*).

---

## 🏗️ La Solución: Pipeline Multi-Nivel de 5 Fases

```
                         ┌──────────────────────────────────────┐
                         │     Nueva Noticia Cruda Ingerida     │
                         └──────────────────┬───────────────────┘
                                            │
                                            ▼
              ┌────────────────────────────────────────────────────────────┐
              │ FASE 1: Coincidencia Estricta de URL Canónica              │
              └─────────────────────────────┬──────────────────────────────┘
                                            │ (¿Ya existe?)
                                     No ────┴──── Sí ──► [Ignorado / Actualizado]
                                            │
                                            ▼
              ┌────────────────────────────────────────────────────────────┐
              │ FASE 2: Extracción IA de Slug Canónico (`event_slug`)      │
              │  • Ejemplo: 'apple-m5-ultra-mac-studio-launch-2026'        │
              │  • Búsqueda en ventana de 36 horas en base de datos        │
              └─────────────────────────────┬──────────────────────────────┘
                                            │ (¿Slug idéntico?)
                                     No ────┴──── Sí ──► [Consolidar como Update]
                                            │
                                            ▼
              ┌────────────────────────────────────────────────────────────┐
              │ FASE 2.5: Coincidencia Difusa de Titular (Jaccard + Fuzzy) │
              │  • Jaccard Token Overlap >= 70% | Similar Text >= 78%      │
              │  • In-Queue Deduplication (evita colisiones en mismo batch)│
              └─────────────────────────────┬──────────────────────────────┘
                                            │ (¿Duplicado?)
                                     No ────┴──── Sí ──► [Consolidar / Ignorado]
                                            │
                                            ▼
              ┌────────────────────────────────────────────────────────────┐
              │ FASE 3: Partición Vectorial por Categoría (`pgvector`)     │
              │  • Consulta: WHERE category_id = X AND created_at >= -48h  │
              │  • Fórmula: Cosine Distance (<=>)                          │
              └─────────────────────────────┬──────────────────────────────┘
                                            │
               ┌────────────────────────────┼────────────────────────────┐
               │                            │                            │
               ▼ (< 0.18)                   ▼ (0.18 - 0.35)              ▼ (> 0.35)
      [Duplicado Exacto]             [ZONA GRIS / AMBIGUA]         [Noticia Única]
               │                            │                            │
               ▼                            ▼                            │
      [Consolidar Update]         ┌───────────────────┐                  │
                                  │   LLM-as-a-Judge  │                  │
                                  │ (Evaluación Flash)│                  │
                                  └─────────┬─────────┘                  │
                                            │                            │
                            ┌───────────────┼───────────────┐            │
                            ▼               ▼               ▼            │
                       [FUSIONAR]      [DESCARTAR]     [PUBLICAR]        │
                            │               │               │            │
                            ▼               ▼               └────────────┼──┐
                   [Consolidar Update]  [Ignorado]                       │  │
                                                                         │  │
                                            ┌────────────────────────────┘  │
                                            │ ◄─────────────────────────────┘
                                            ▼
              ┌────────────────────────────────────────────────────────────┐
              │ FASE 4: Guardián Editorial de Importancia (Score >= 7)     │
              │  • Requiere Importancia >= 7 / 10 (o Fuente Trusted)       │
              │  • 🚀 BYPASS BREAKING NEWS: Si Score >= 9, publicación     │
              │    inmediata garantizada                                   │
              └─────────────────────────────┬──────────────────────────────┘
                                            │ (¿Pasa el corte?)
                                    Sí ─────┴───── No ──► [Ignorado]
                                            │
                                            ▼
              ┌────────────────────────────────────────────────────────────┐
              │ FASE 5: Redacción Bilingüe del Artículo Pilar (700-1300 p) │
              └────────────────────────────────────────────────────────────┘
```

---

## 🧮 1. Partición Vectorial por Categoría y Distancia de Cosenos

Para evitar falsos positivos entre industrias distintas (ejemplo: *Nvidia anuncia GPU RTX 5000* vs *AMD anuncia GPU RX 9000*), la búsqueda en `pgvector` está estrictamente **particionada por la categoría del artículo**:

$$	ext{Query: } 	ext{SELECT id FROM articles WHERE category\_id = \$cat AND created\_at} \ge 	ext{now() - 48h ORDER BY embedding } \Leftrightarrow 	ext{ \$vector}$$

---

## ⚖️ 2. El "LLM-as-a-Judge" para la Zona Gris ($0.18 \le 	ext{Distancia} \le 0.35$)

Cuando dos artículos tienen similitud semántica cercana pero no idéntica:
* Un modelo ultra-rápido evalúa los dos resúmenes y emite un veredicto formal:
  - **`FUSIONAR`**: Mismo hecho noticioso de otra fuente. Se anexa como actualización complementaria.
  - **`DESCARTAR`**: Duplicado menor o eco de prensa sin ningún dato nuevo.
  - **`PUBLICAR`**: Trata un hecho distinto o aporta un ángulo técnico/financiero radicalmente nuevo que amerita un artículo independiente.

---

## 🔒 3. Fusión E-E-A-T Inmutable (Núcleo + Apéndice con Ventana de 6 Horas)

* **Inmutabilidad del Núcleo**: El `H1`, el primer párrafo y la `URL` original son **100% inmutables** para preservar las señales de rastreo e indexación que Googlebot ya procesó.
* **Apéndice de Cobertura**: Las fuentes consolidadas se agregan como confirmaciones adicionales en la tabla `article_updates`.
* **Ventana de Frescura**: Solo se actualiza la fecha `updated_at` si el artículo original tiene menos de **6 horas de vida**.

---

## 🚀 4. Bypass de Emergencia para Breaking News ($	ext{Importance} \ge 9$)

Si ocurre un acontecimiento de impacto masivo mundial (ej. falla global de infraestructura, quiebra de una Big Tech o fallo crítico de ciberseguridad nacional):
* Se activa el **Bypass de Alta Prioridad**.
* La noticia se redacta y publica de inmediato sin importar cuotas ni límites diarios.

---

## ⚙️ Archivos del Sistema Involucrados:
* [`app/Services/AI/DuplicateCheckerService.php`](../app/Services/AI/DuplicateCheckerService.php): Motor de partición vectorial, slug canónico y LLM Judge.
* [`app/Jobs/ProcessArticleWithAIJob.php`](../app/Jobs/ProcessArticleWithAIJob.php): Guardián editorial, clasificación enriquecida y bypass de Breaking News.
* [`config/global.php`](../config/global.php): Metas de palabras y reglas editoriales.
