# 🛡️ Guía de Blindaje Anti-Canibalización, Deduplicación y Consolidación de Noticias

Este documento describe la arquitectura, fórmulas matemáticas y directrices de ingeniería implementadas en **Glodaxia** para evitar la canibalización de palabras clave en Google y la duplicación de artículos cuando múltiples fuentes cubren la misma noticia.

---

## 🎯 El Problema: Cobertura Concurrente en Medios Tech

Al monitorear **62 fuentes RSS simultáneas** (*The Verge*, *Ars Technica*, *TechCrunch*, *Wired*, *The Register*, etc.), cuando ocurre un evento importante (ej. lanzamiento de hardware, vulnerabilidad zero-day o release de IA), entre 5 y 10 medios publican sobre el mismo hecho con titulares diferentes.

### ⚠️ Consecuencias de No Filtrar:
1. **Canibalización de Keywords**: 5 artículos compitiendo entre sí en Google Search, dividiendo la autoridad del dominio.
2. **Crawl Budget Wastage**: Googlebot gasta su cuota de rastreo en páginas redundantes y deja de indexar contenido nuevo.
3. **Pérdida de Calidad E-E-A-T**: Señales de automatización masiva sin curaduría (*Scaled Content Abuse*).

---

## 🏗️ La Solución: Pipeline Multi-Nivel de Deduplicación y Consolidación

```
                        ┌─────────────────────────────────────┐
                        │     Nueva Noticia Cruda Ingerida    │
                        └──────────────────┬──────────────────┘
                                           │
                                           ▼
             ┌───────────────────────────────────────────────────────────┐
             │ NIVEL 1: Coincidencia Estricta de URL Canónica           │
             └─────────────────────────────┬─────────────────────────────┘
                                           │ (¿Ya existe?)
                                    No ────┴──── Sí ──► [Ignorado / Actualizado]
                                           │
                                           ▼
             ┌───────────────────────────────────────────────────────────┐
             │ NIVEL 2: Coincidencia Difusa de Titular (Jaccard + Fuzzy) │
             │  • Jaccard Token Overlap >= 70%                           │
             │  • Similar Text >= 78%                                    │
             └─────────────────────────────┬─────────────────────────────┘
                                           │ (¿Duplicado?)
                                    No ────┴──── Sí ──► [Consolidar como Update]
                                           │
                                           ▼
             ┌───────────────────────────────────────────────────────────┐
             │ NIVEL 2.5: Deduplicación en Cola Concurrente              │
             │  • Evita procesar dos noticias idénticas en el mismo batch│
             └─────────────────────────────┬─────────────────────────────┘
                                           │ (¿En cola?)
                                    No ────┴──── Sí ──► [Ignorado]
                                           │
                                           ▼
             ┌───────────────────────────────────────────────────────────┐
             │ NIVEL 3: Similitud Semántica pgvector (Coseno Adaptativo) │
             │  • Ventana <= 48h: Distancia de Coseno <= 0.28            │
             │  • Ventana > 48h:  Distancia de Coseno <= 0.20            │
             └─────────────────────────────┬─────────────────────────────┘
                                           │ (¿Misma historia?)
                                    No ────┴──── Sí ──► [Consolidar como Update]
                                           │
                                           ▼
             ┌───────────────────────────────────────────────────────────┐
             │ NIVEL 4: Guardián Editorial de Relevancia (Gatekeeper)    │
             │  • Requiere Importancia >= 7 / 10                         │
             │  • Requiere Fuente Verificada (Trusted) si score < 7      │
             │  • Filtro de contenido sensible o potencialmente falso    │
             └─────────────────────────────┬─────────────────────────────┘
                                           │ (¿Pasa el corte?)
                                   Sí ─────┴───── No ─► [Ignorado]
                                           │
                                           ▼
             ┌───────────────────────────────────────────────────────────┐
             │ 🚀 Redacción Bilingüe del Artículo Pilar (700-1300 pal.)  │
             └───────────────────────────────────────────────────────────┘
```

---

## 🧮 1. Distancia de Cosenos Vectorial en PostgreSQL (`pgvector`)

El sistema convierte el título y resumen de la noticia en un vector denso de **1536 dimensiones** mediante el modelo de embeddings:

$$	ext{Cosine Distance}(u, v) = 1 - rac{u \cdot v}{\|u\|_2 \|v\|_2}$$

* **$	ext{Distancia} = 0.00$**: Textos idénticos.
* **$	ext{Distancia} \le 0.28$ (en las últimas 48 horas)**: **Misma Noticia** cubierta por dos medios distintos con diferente ángulo (ej: *"Apple unveils M5"* vs *"New Mac Studio launched with M5 Ultra"*).
* **Acción**: En lugar de crear un segundo post redundante, el sistema anexa la segunda fuente como un **`ArticleUpdate`** en el artículo original.

---

## 📰 2. Fusión y Enriquecimiento Multi-Fuente (*Story Consolidation*)

Cuando una segunda o tercera fuente cubre una noticia ya existente:
1. El artículo original conserva su URL y posicionamiento SEO.
2. Se actualiza su fecha `updated_at`.
3. Se anexa la fuente a la sección de **Cobertura Multi-Medio / Fuentes Consultadas**.
4. **Beneficio E-E-A-T**: Para Google, un artículo que cita a *TechCrunch*, *The Verge* y *Reuters* en una sola pieza tiene mucha mayor autoridad que 3 artículos separados de 300 palabras.

---

## 🎯 3. Guardián Editorial de Importancia ($	ext{Score} \ge 7$)

Para mantener un ritmo editorial humano y profesional de **15 a 30 artículos de élite por día**:
* La fase de clasificación evalúa la importancia intrínseca de la noticia (1 al 10).
* Solo pasan a redacción las noticias con **$	ext{Importancia} \ge 7$** (noticias mayores, anuncios oficiales, descubrimientos, lanzamientos y análisis de fondo).
* Las notas menores, rumores no contrastados o notas de relleno son marcadas como `ignored`, ahorrando tokens y protegiendo el *Crawl Budget*.

---

## ⚙️ Archivos del Sistema Involucrados:
* [`app/Services/AI/DuplicateCheckerService.php`](../app/Services/AI/DuplicateCheckerService.php): Motor de similitud vectorial y fuzzy.
* [`app/Jobs/ProcessArticleWithAIJob.php`](../app/Jobs/ProcessArticleWithAIJob.php): Guardián editorial y pipeline de redacción.
* [`config/global.php`](../config/global.php): Metas de palabras y reglas editoriales.
