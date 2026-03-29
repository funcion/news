# 🗞️ PLAN MAESTRO - Plataforma de Noticias Automatizada con IA

> **Proyecto**: Plataforma de noticias automatizada con IA y RSS  
> **Stack**: Laravel 12, Filament, Docker, Redis, PostgreSQL, Horizon, Reverb  
> **Fecha de creación**: 29 de Marzo 2026  
> **Estado**: En desarrollo

---

## 📋 TABLA DE CONTENIDOS

1. [Visión y Estrategia General](#1-visión-y-estrategia-general)
2. [Decisión de Nicho](#2-decisión-de-nicho)
3. [Arquitectura del Sistema](#3-arquitectura-del-sistema)
4. [Módulo 1: Motor de Ingesta RSS](#4-módulo-1-motor-de-ingesta-rss)
5. [Módulo 2: Cerebro de IA](#5-módulo-2-cerebro-de-ia)
6. [Módulo 3: Generación de Imágenes](#6-módulo-3-generación-de-imágenes)
7. [Módulo 4: Sistema Anti-Duplicados](#7-módulo-4-sistema-anti-duplicados)
8. [Módulo 5: Publicación Automática y Manual](#8-módulo-5-publicación-automática-y-manual)
9. [Módulo 6: Frontend y Tiempo Real](#9-módulo-6-frontend-y-tiempo-real)
10. [Módulo 7: SEO Técnico](#10-módulo-7-seo-técnico)
11. [Módulo 8: Sistema de Tags Inteligente](#11-módulo-8-sistema-de-tags-inteligente)
12. [Módulo 9: Publicación en Terceros](#12-módulo-9-publicación-en-terceros)
13. [Estrategia de Idiomas](#13-estrategia-de-idiomas)
14. [Stack Tecnológico](#14-stack-tecnológico)
15. [Roadmap de Ejecución](#15-roadmap-de-ejecución)
16. [Métricas de Éxito (KPIs)](#16-métricas-de-éxito-kpis)
17. [Riesgos y Mitigaciones](#17-riesgos-y-mitigaciones)
18. [Monetización](#18-monetización)
19. [Recomendaciones Adicionales](#19-recomendaciones-adicionales)
20. [Lista de Tareas Detalladas con Checklists](#20-lista-de-tareas-detalladas-con-checklists)

---

## 1. VISIÓN Y ESTRATEGIA GENERAL

### 1.1 Objetivo del Proyecto

Construir una plataforma de noticias escalable, automatizada y competitiva que combine **RSS feeds + IA generativa + SEO técnico avanzado**, con arquitectura Laravel 12, diseñada para crecer y competir con grandes medios digitales.

### 1.2 Pilares Fundamentales

| Pilar                   | Descripción                                                          |
| ----------------------- | -------------------------------------------------------------------- |
| **Automatización**      | Las noticias se consultan, redactan y publican automáticamente       |
| **Calidad**             | Contenido 100% humanizado, SEO optimizado, ADA/WCAG compliant        |
| **Velocidad**           | Publicación en <5-8 minutos desde la fuente original                 |
| **Tiempo Real**         | Actualizaciones sin refrescar la página (WebSockets)                 |
| **Escalabilidad**       | Arquitectura preparada para crecer de 1 a 100 nichos                 |
| **Sostenibilidad**      | Modelo de negocio viable con control de costos y validación temprana |
| **Validación Temprana** | Fase 0 de pruebas manuales antes de escalar                          |

### 1.3 Principios de Desarrollo

- Consistencia en el código
- Sencillez sobre complejidad innecesaria
- Escalabilidad horizontal
- Mejores prácticas de Laravel 12
- Código mantenible a largo plazo
- Validación temprana (Fase 0)
- Enfoque en métricas de engagement (CTR, tiempo de lectura)
- Transparencia sobre uso de IA

---

### 1.4 Fase 0 de Validación (Nueva - Crítica)

Antes de cualquier desarrollo completo:

- Configurar 20 feeds RSS manualmente
- Generar 10-20 artículos con prompts manuales
- Publicar en Medium/subdominio y medir engagement (CTR >2%, tiempo lectura >45s)
- Documentar **Prompt Library** inicial
- Validar que el contenido humanizado genera interés real
- Crear página `/metodologia-editorial` con transparencia sobre uso de IA + human-in-the-loop

---

### 1.5 Transparencia IA como Señal E-E-A-T

- Página dedicada `/metodologia-editorial` explicando el proceso (IA + validación humana)
- Perfiles de "Autores IA" consistentes (con foto generada por Flux)
- Esto convierte un riesgo en una ventaja competitiva de confianza y modernidad

---

## 2. DECISIÓN DE NICHO

### 2.1 ¿Especialización o Generalización?

**DECISIÓN: Hiper-Especialización con Arquitectura de Expansión**

No competir como generalista desde el día 1. Las razones:

| Factor               | Generalista                   | Especialista (Elegido)             |
| -------------------- | ----------------------------- | ---------------------------------- |
| Autoridad de Dominio | Décadas para construir        | Meses para dominar un nicho        |
| Costos de IA         | Muy altos (muchas categorías) | Controlados (1-2 categorías)       |
| Backlinks            | Difíciles de conseguir        | Naturales por autoridad temática   |
| Competencia          | CNN, BBC, NYT (imposible)     | Blogs pequeños (superables)        |
| Google E-E-A-T       | Penalizado por falta de foco  | Premiado por expertise demostrable |

### 2.2 Criterios de Selección del Nicho

```
✅ Fuentes RSS abundantes (mínimo 100+ feeds activos)
✅ Alta intención de búsqueda (la gente BUSCA activamente)
✅ Baja competencia técnica (sitios lentos, mal SEO, sin tiempo real)
✅ Potencial de monetización B2B o B2C
✅ Tendencia alcista (no moda pasajera)
✅ Contenido que se ACTUALIZA frecuentemente
```

### 2.3 Top Nichos Evaluados

| #   | Nicho                   | RSS        | Competencia | Monetización | Veredicto   |
| --- | ----------------------- | ---------- | ----------- | ------------ | ----------- |
| 1   | **IA y Automatización** | ⭐⭐⭐⭐⭐ | Media       | Muy Alta     | 🏆 GANADOR  |
| 2   | Ciberseguridad          | ⭐⭐⭐⭐   | Baja-Media  | Alta         | Excelente   |
| 3   | Finanzas/Crypto/Web3    | ⭐⭐⭐⭐⭐ | Alta        | Muy Alta     | Riesgoso    |
| 4   | Salud & Wellness Tech   | ⭐⭐⭐     | Baja        | Alta         | Nicho azul  |
| 5   | Energías Renovables     | ⭐⭐⭐     | Muy Baja    | Media-Alta   | Largo plazo |

### 2.4 Nicho Elegido: IA y Automatización

**¿Por qué?**

- Fuentes RSS INMENSAS: OpenAI, Google AI, Meta AI, Anthropic, arXiv, Hacker News, TechCrunch, The Verge, MIT Tech Review
- Se actualiza CADA HORA (perfecto para sistema de tiempo real)
- Audiencia TECH-SAVVY (valora velocidad, SEO funciona brutal)
- Monetización: afiliados SaaS ($50-500/comisión), cursos, consultorías
- La propia plataforma ES un caso de uso del nicho (meta-marketing)

### 2.5 Estrategia de Expansión (Ajustada)

**Recomendación fuerte**: Mantén **un único nicho durante al menos 12 meses** para construir autoridad temática sólida. La arquitectura está preparada para expansión, pero el marketing y el foco deben concentrarse.

```
FASE 0: Validación (20 feeds manuales)
FASE 1 (Meses 1-12): Dominar "IA y Automatización"
FASE 2 (Año 2): Agregar "Ciberseguridad" (nicho complementario)
FASE 3 (Año 3+): Agregar "Finanzas Tech" → Plataforma multi-nicho
```

---

## 3. ARQUITECTURA DEL SISTEMA

### 3.1 Visión General de Arquitectura (Actualizada con FrankenPHP)

```
┌─────────────────────────────────────────────────────────────────┐
│                    FUENTES DE DATOS                              │
│  [RSS Gratis] [RSS Pagas] [APIs News] [Scraping Fallback]       │
└──────────────────────────┬──────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────────┐
│                 MOTOR DE INGESTA (Horizon Workers)               │
│  [Feed Reader] → [Parser] → [Validación] → [Cola Redis]         │
└──────────────────────────┬──────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────────┐
│                    CEREBRO DE IA                                 │
│  [Clasificador] → [Anti-Duplicados] → [Redactor] → [Humanizar]  │
│  [SEO Optimizer] → [Generador Imagen] → [Alt Text]              │
└──────────────────────────┬──────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────────┐
│                   SISTEMA DE PUBLICACIÓN                         │
│  [Auto-Publish] / [Manual Filament] → [Rate Limiter] → [LIVE]   │
└──────────────────────────┬──────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────────┐
│               FRONTEND + SERVIDOR (FrankenPHP)                  │
│  [Reverb WebSocket] → [FrankenPHP Worker] → [Usuario]           │
│  [Blade SSR] → [HTTP/3] → [Caddy Reverse Proxy]                 │
└─────────────────────────────────────────────────────────────────┘
```

### 3.2 Stack Tecnológico Completo (Actualizado para FrankenPHP)

| Capa                   | Tecnología                         | Justificación                                       | Beneficios con FrankenPHP                                           |
| ---------------------- | ---------------------------------- | --------------------------------------------------- | ------------------------------------------------------------------- |
| **Servidor Web + PHP** | **FrankenPHP** (PHP 8.3 + Caddy)   | PHP + servidor web integrado, HTTP/3 nativo         | ⚡ **20-30% más rápido**, 🚀 **HTTP/3**, 🔧 **Menos configuración** |
| Backend                | Laravel 12                         | Framework robusto, comunidad activa                 | Compatibilidad total, worker mode optimizado                        |
| Admin                  | Filament v3                        | Panel de administración rápido y potente            | Sin cambios, funciona perfectamente                                 |
| DB Principal           | PostgreSQL + pgvector              | JSONB para metadatos, embeddings para IA            | pgvector para similitud semántica                                   |
| Cache                  | Redis                              | Cache ultra-rápido, colas de trabajos               | Sesiones en Redis para mejor performance                            |
| Colas                  | Laravel Horizon                    | Gestión visual de colas para procesamiento IA       | Procesamiento async de IA sin bloquear servidor                     |
| Tiempo Real            | Laravel Reverb                     | WebSockets nativos, sin servicios externos          | Contenedor separado para escalabilidad                              |
| Frontend               | Blade + Alpine.js + Tailwind       | SSR para SEO + interactividad ligera                | SSR más rápido con FrankenPHP worker mode                           |
| Build                  | Vite                               | Compilación rápida de assets                        | Sin cambios                                                         |
| Imágenes               | Intervention Image + WebP/AVIF     | Compresión inteligente + formatos modernos          | Mejor performance de entrega de assets                              |
| IA                     | OpenRouter (multi-model)           | Flexibilidad para rotar modelos según costo/calidad | Async processing via Horizon                                        |
| Imágenes IA            | FluxAPI.ai + fallback SVG          | Generación única + placeholder de marca             | Evita problemas de licencia                                         |
| RSS                    | vedmant/laravel-feed-reader        | Lectura robusta con manejo de errores               | Sin cambios                                                         |
| Deploy                 | Docker + Laravel Forge             | Consistencia dev/prod + escalado horizontal         | FrankenPHP incluido en Docker                                       |
| CDN                    | Cloudflare                         | Assets estáticos + edge caching + HTTP/3            | Compatibilidad perfecta con FrankenPHP HTTP/3                       |
| Monitoreo              | Laravel Pulse + FrankenPHP metrics | Monitoreo nativo + métricas del servidor            | Métricas integradas de FrankenPHP                                   |

**Ventajas clave de FrankenPHP:**

1. **🚀 Performance superior**: Hasta 30% más rápido que Nginx+PHP-FPM
2. **⚡ HTTP/3 nativo**: Soporte para QUIC protocol
3. **🔧 Simplificación**: Un solo servicio en lugar de Nginx+PHP-FPM
4. **🔄 Worker mode**: Ideal para aplicaciones Laravel modernas
5. **📊 Métricas integradas**: Monitoreo nativo del servidor
6. **🌐 HTTP/2 Server Push**: Optimización automática de assets

---

## 4. MÓDULO 1: MOTOR DE INGESTA RSS

### 4.1 Descripción

Sistema encargado de consultar fuentes RSS de manera automatizada, parsear el contenido y almacenarlo en la base de datos para su posterior procesamiento.

### 4.2 Flujo Lógico

```
[Fuentes RSS Configuradas]
         ↓
[Worker Horizon - Polling Dinámico]
         ↓
[Feed Reader (vedmant/laravel-feed-reader)]
         ↓
[Parser y Validación de Estructura]
         ↓
[Extracción de Metadatos]
         ↓
[Almacenamiento en Cola "raw_news" (Redis)]
         ↓
[Registro en DB - Tabla raw_articles]
```

### 4.3 Estrategia de Fuentes

| Tipo       | Ejemplos                                                                 | Proporción |
| ---------- | ------------------------------------------------------------------------ | ---------- |
| **Gratis** | Blogs oficiales (OpenAI, Google AI), Medium, Substack, arXiv, HN, Reddit | 80%        |
| **Pagas**  | NewsAPI.org, GDELT, AYLIEN, GNews                                        | 20%        |

### 4.4 Sistema de Scoring de Fuentes

Cada fuente RSS tendrá un score automático basado en:

| Criterio         | Descripción                               | Peso |
| ---------------- | ----------------------------------------- | ---- |
| **Fiabilidad**   | ¿Cuántas veces falla el fetch?            | 30%  |
| **Frescura**     | ¿Publica contenido nuevo frecuentemente?  | 25%  |
| **Originalidad** | ¿Su contenido aparece primero o es copia? | 25%  |
| **Calidad**      | ¿El contenido tiene suficiente texto?     | 20%  |

**Reglas automáticas:**

- Score bajo → Fuente se desactiva automáticamente
- Score alto → Fuente se consulta más frecuentemente
- Score crítico → Alerta al administrador

### 4.5 Frecuencia de Polling Dinámico

| Tipo de Fuente   | Frecuencia    | Ejemplo                 |
| ---------------- | ------------- | ----------------------- |
| Alta frecuencia  | 2-5 minutos   | Hacker News, TechCrunch |
| Media frecuencia | 15-30 minutos | Blogs de empresas AI    |
| Baja frecuencia  | 1-2 horas     | Medios generalistas     |

### 4.6 Paquetes Recomendados

- **Lectura RSS**: `vedmant/laravel-feed-reader` + `simplepie`
- **Generación RSS propio**: `spatie/laravel-feed`

### 4.7 Consideraciones Legales

- Respetar `robots.txt` de cada fuente
- No copiar artículos completos de fuentes con copyright
- Generar CONTENIDO NUEVO inspirado en la fuente, no un "rewrite"
- Incluir siempre atribución y link a la fuente original

---

## 5. MÓDULO 2: CEREBRO DE IA

### 5.1 Descripción

Pipeline de procesamiento que transforma contenido crudo en artículos 100% SEO, ADA, WCAG y humanizados.

### 5.2 Flujo Lógico (Actualizado)

```
[Noticia Cruda desde Cola]
         ↓
[Clasificador IA Barato (Gemini Flash)]
         ↓
    ┌────┼────┐
    ↓    ↓    ↓
 [NUEVA] [ACTUALIZACIÓN] [DUPLICADO]
    ↓    ↓               ↓
 [Redactor] [Fusionador]  [Descarte]
    ↓    ↓
 [Humanizador] [Re-humanizar]
    ↓
 [Validador de Hechos / Fact-Checking]
    ↓
 [SEO Optimizer + Internal Linking]
    ↓
 [Cola de Publicación]
```

### 5.3 Pipeline de Redacción en 4 Capas

| Capa             | Modelo IA            | Costo Est. | Función                                 |
| ---------------- | -------------------- | ---------- | --------------------------------------- |
| 1. Clasificación | Gemini Flash / Haiku | $0.001     | ¿Nueva, actualización o duplicado?      |
| 2. Extracción    | Gemini Flash         | $0.002     | Extraer hechos clave, entidades, fechas |
| 3. Redacción     | Claude 3.5 Sonnet    | $0.03      | Escribir artículo completo con "voz"    |
| 4. Humanización  | GPT-4o-mini          | $0.005     | Post-procesamiento anti-detección IA    |

**Costo estimado por artículo: ~$0.04**  
**100 artículos/día = $4/día = $120/mes**

### 5.4 Estrategia de "Voces Editoriales" (Simplificada para Inicio)

**Recomendación**: Empezar con **una sola voz** (ej. "El Analista") para afinar prompts y métricas rápidamente. Introducir las demás cuando tengas datos de engagement (>100 artículos).

Rotar entre 3-4 personalidades de redactor (a futuro):

| Voz               | Tono                    | Uso                              |
| ----------------- | ----------------------- | -------------------------------- |
| **El Analista**   | Técnico, datos duros    | Artículos de investigación       |
| **El Divulgador** | Accesible, analogías    | Explicar conceptos complejos     |
| **El Cronista**   | Narrativo, storytelling | Noticias de impacto humano       |
| **El Crítico**    | Opinión fundamentada    | Análisis de productos/decisiones |

### 5.5 Humanización Anti-Detección

Puntos clave para que el contenido sea indetectable por Google:

- Variación en longitud de oraciones (5-25 palabras)
- Uso de contracciones naturales ("it's", "don't", "can't")
- Incluir preguntas retóricas
- Modismos y expresiones coloquiales ocasionales
- Errores "humanos" controlados (una coma de más, repetición menor)
- Opiniones implícitas basadas en datos (no neutralidad robótica)

### 5.6 Optimización SEO Automática (Mejorada)

Cada artículo generado debe incluir:

- Meta title optimizado (50-60 caracteres)
- Meta description (150-160 caracteres)
- Estructura H1, H2, H3 con keywords LSI
- Schema.org markup (Article, NewsArticle, BreadcrumbList)
- **Internal linking automático** (3-5 enlaces relevantes)
- Keyword density optimizada (1-2%)
- Alt text para imágenes
- URL slug SEO-friendly
- Optimización para Featured Snippets (formato pregunta/respuesta)

### 5.7 Validación de Hechos / Fact-Checking (Nuevo - Crítico)

- Paso rápido con IA económica (Gemini Flash) que compara borrador vs fuente original
- Verifica nombres, fechas, cifras y URLs
- Marca discrepancias para revisión humana
- Solo publica si discrepancia <5%

### 5.8 Cumplimiento ADA/WCAG

- Generación automática de audio descriptions
- Estructura semántica clara
- Contraste adecuado en diseño
- Navegación por teclado nativa
- Texto alternativo descriptivo para imágenes

---

## 6. MÓDULO 3: GENERACIÓN DE IMÁGENES

### 6.1 Descripción

Sistema automatizado para crear imágenes únicas y optimizadas para cada noticia.

### 6.2 Flujo Lógico

```
[Artículo Procesado]
         ↓
[Extractor de Keywords Visuales]
         ↓
[Prompt Builder (Template Consistente)]
         ↓
[FluxAPI.ai Request]
         ↓
    ┌────┴────┐
    ↓         ↓
 [Éxito]    [Fallo]
    ↓         ↓
 [Optimizar] [Fallback: Unsplash API]
                  ↓
              [Fallback: Placeholder SVG]
         ↓
[Compresión WebP/AVIF + CDN]
         ↓
[Alt Text Generado por IA]
         ↓
[Almacenamiento con naming SEO-friendly]
```

### 6.3 Decisiones de Negocio (Simplificado)

| Decisión      | Recomendación                                     | Razón                                                |
| ------------- | ------------------------------------------------- | ---------------------------------------------------- |
| Estilo visual | Consistente con prompt template                   | Coherencia visual en todo el sitio                   |
| Formato       | WebP/AVIF con fallback JPEG                       | Optimización de carga                                |
| Fallback      | **FluxAPI → Placeholder SVG con diseño de marca** | Evita problemas de licencia y atribución de Unsplash |
| Alt text      | Generado por IA                                   | Cumplimiento WCAG                                    |
| CDN           | Cloudflare                                        | Velocidad de carga global                            |

### 6.4 Prompt Template Base

```
"Minimalist editorial illustration, flat design, [colores de marca],
[tema del artículo], clean lines, professional, no text"
```

---

## 7. MÓDULO 4: SISTEMA ANTI-DUPLICADOS

### 7.1 Descripción

Sistema inteligente de 3 niveles para evitar contenido redundante y detectar actualizaciones valiosas.

### 7.2 Flujo Lógico

```
[Noticia Entrante]
         ↓
[NIVEL 1: Hash Exacto]
    (SHA256 de título + URL)
    ¿Existe? → SÍ: Descartar
         ↓ NO
[NIVEL 2: Similitud de Texto]
    (TF-IDF / Cosine Similarity)
    Score < 60% → NUEVA
    Score 60-85% → Revisar
    Score > 85% → Posible duplicado
         ↓
[NIVEL 3: IA Semántica]
    (Embeddings pgvector)
    ¿Agrega valor la nueva fuente?
         ↓
    ┌────┴────┐
    ↓         ↓
 [SÍ]       [NO]
    ↓         ↓
 [Crear     [Descartar]
 "Update"]
```

### 7.3 Los 3 Niveles de Detección

| Nivel | Método                             | Velocidad   | Precisión                    |
| ----- | ---------------------------------- | ----------- | ---------------------------- |
| 1     | Hash exacto (título+URL)           | Instantáneo | 100% para duplicados exactos |
| 2     | Similitud de texto (TF-IDF/Cosine) | Rápido      | ~85% para paráfrasis         |
| 3     | IA semántica (embeddings pgvector) | Medio       | ~95% para contenido similar  |

### 7.4 Regla de Oro (Mejorada)

Si la noticia existe pero la nueva fuente agrega UN solo dato nuevo (cifra, declaración, fecha), se crea un "Update" que se anexa a la original. Esto:

- Mantiene UNA URL fuerte (SEO)
- Da sensación de cobertura continua (UX)
- Evita canibalización de keywords
- **Actualiza `updated_at`** y dispara notificación **IndexNow** para señal de "Fresh Content" a Google

**Esto es una estrategia de SEO de combate poderosa.**

### 7.5 Técnicas Recomendadas

- Algoritmos de similitud textual (Cosine + TF-IDF)
- Hashing de entidades
- PostgreSQL pgvector para embeddings
- Cache de hashes en Redis para velocidad

---

## 8. MÓDULO 5: PUBLICACIÓN AUTOMÁTICA Y MANUAL

### 8.1 Descripción

Sistema dual de publicación que permite automatización completa o revisión manual desde Filament.

### 8.2 Modo Automático

```
[Cola de Publicación]
         ↓
[Rate Limiter por Categoría]
    (Máx 2-3 artículos/hora por tema)
         ↓
[Validador SEO Final]
         ↓
[PUBLICAR]
         ↓
[Evento Reverb Broadcast]
         ↓
[Frontend Update en Tiempo Real]
```

### 8.3 Modo Manual (Filament)

```
[Dashboard Filament]
         ↓
[Lista de Borradores Pendientes]
         ↓
[Preview del Artículo]
    - Vista previa completa
    - Score SEO
    - Imagen generada
    - Metadatos
         ↓
[Acciones: Aprobar / Editar / Rechazar]
         ↓
[PUBLICAR]
```

### 8.4 Rate Limiting Inteligente

| Regla           | Límite                    | Razón                     |
| --------------- | ------------------------- | ------------------------- |
| Mismo tema/hora | Máx 2-3 artículos         | No saturar audiencia      |
| Distribución    | A lo largo del día        | No burst de publicaciones |
| Horas pico      | Priorizar según analytics | Máximo engagement         |

### 8.5 Workflow de Aprobación en Filament

| Estado           | Descripción                        | Acción                     |
| ---------------- | ---------------------------------- | -------------------------- |
| `draft`          | Generado por IA, pendiente         | Esperando revisión         |
| `pending_review` | En cola de revisión manual         | Editor revisa              |
| `approved`       | Aprobado para publicación          | Se publica automáticamente |
| `published`      | En vivo                            | Visible al público         |
| `rejected`       | No cumple estándares               | Se archiva o regenera      |
| `updated`        | Actualización de noticia existente | Se anexa a la original     |

---

## 9. MÓDULO 6: FRONTEND Y TIEMPO REAL

### 9.1 Decisión de Columnas

**DECISIÓN: 2 Columnas (70/30)**

| Opción         | Veredicto      | Razón                             |
| -------------- | -------------- | --------------------------------- |
| 1 columna      | ❌ No          | Solo para blogs personales        |
| **2 columnas** | ✅ **ELEGIDO** | Estándar de oro para noticias     |
| 3 columnas     | ⚠️ Condicional | Solo si hay MUCHO contenido + ads |

**¿Por qué 2 columnas?**

- **Principal (70%)**: Feed de noticias en grid/mosaico
- **Sidebar (30%)**: Trending, categorías, newsletter, futuro espacio ads
- En móvil: sidebar se va abajo (nativo con CSS Grid/Flexbox)
- Mejor Core Web Vital que 3 columnas
- Más espacio para lectura = mejor engagement

### 9.2 Estructura del Layout

```
┌─────────────────────────────────────────────────────────┐
│                    HEADER / NAVBAR                       │
│  [Logo] [Categorías] [Search] [Newsletter] [Dark Mode]  │
├────────────────────────────────┬────────────────────────┤
│                                │                        │
│     COLUMNA PRINCIPAL (70%)    │  SIDEBAR (30%)         │
│                                │                        │
│  ┌──────────┐ ┌──────────┐    │  ┌──────────────────┐  │
│  │ Noticia  │ │ Noticia  │    │  │ 🔥 Trending      │  │
│  │ Destacada│ │ 2        │    │  │                  │  │
│  └──────────┘ └──────────┘    │  │ 📂 Categorías    │  │
│  ┌──────────┐ ┌──────────┐    │  │                  │  │
│  │ Noticia  │ │ Noticia  │    │  │ 📧 Newsletter    │  │
│  │ 3        │ │ 4        │    │  │                  │  │
│  └──────────┘ └──────────┘    │  │ 📊 Lo Más Leído  │  │
│  ┌──────────┐ ┌──────────┐    │  │                  │  │
│  │ Noticia  │ │ Noticia  │    │  │ [Futuro Ad Slot] │  │
│  │ 5        │ │ 6        │    │  │                  │  │
│  └──────────┘ └──────────┘    │  └──────────────────┘  │
│                                │                        │
├────────────────────────────────┴────────────────────────┤
│                    FOOTER                               │
│  [About] [Contact] [Privacy] [Terms] [RSS Feed]        │
└─────────────────────────────────────────────────────────┘
```

### 9.3 Tiempo Real con Reverb

| Componente            | Descripción                                     |
| --------------------- | ----------------------------------------------- |
| Canal público         | `news.feed` para el feed principal              |
| Canales por categoría | `news.{category}` para páginas de categoría     |
| Animación             | "Nueva noticia disponible" banner sutil         |
| UX                    | No interrumpe lectura, el usuario decide si ver |

### 9.4 Diseño UI/UX

- **Estilo**: Minimalista, clean reading, mucho espacio en blanco
- **Tipografía**: Fuentes legibles, tamaño adecuado para lectura prolongada
- **Colores**: Paleta limitada, alto contraste para accesibilidad
- **Dark Mode**: Soporte nativo desde el inicio
- **Responsive**: Mobile-first design

---

## 10. MÓDULO 7: SEO TÉCNICO (Optimizado para FrankenPHP)

### 10.1 Objetivo

100/100 en PageSpeed Insights y cumplimiento total de mejores prácticas SEO con las ventajas de FrankenPHP.

### 10.2 Checklist de Optimización (Mejorada para FrankenPHP)

#### Rendimiento con FrankenPHP

```
✅ **FrankenPHP Worker Mode**: Aplicación siempre en memoria (como Octane)
✅ **HTTP/3 nativo**: QUIC protocol para menor latencia
✅ **Server Push automático**: Assets críticos enviados antes de solicitud
✅ **Compresión Brotli/Zstd**: Compresión moderna más eficiente
✅ **Cache integrado**: Headers de cache optimizados automáticamente
✅ **Imágenes**: WebP/AVIF + lazy loading + srcset responsive
✅ **CSS**: Critical inline + resto async con HTTP/2 Push
✅ **JS**: Vite code-splitting + defer/async + module/nomodule
✅ **CDN**: Cloudflare con HTTP/3 + edge caching
✅ **PostgreSQL**: Índices optimizados + pgvector para embeddings
✅ **Redis**: Cache de consultas + sesiones + colas
```

#### SEO On-Page

```
✅ Sitemap dinámico actualizado por Horizon
✅ **IndexNow** (notificación instantánea a Bing/Yandex/Google)
✅ Schema.org markup automático (Article, NewsArticle, BreadcrumbList)
✅ Canonical URLs correctas
✅ Hreflang tags para EN/ES
✅ Meta tags optimizados en cada página
✅ Open Graph + Twitter Cards
✅ Structured Data para Google News
✅ **Internal linking automático**
✅ **Optimización para Featured Snippets**
✅ **Google News Sitemap específico**
✅ **Señales E-E-A-T** (página "Sobre nuestro proceso IA", perfiles de autores IA)
```

#### Accesibilidad (ADA/WCAG)

```
✅ Contraste de colores AA/AAA
✅ Navegación por teclado
✅ ARIA labels
✅ Skip links
✅ Alt text en todas las imágenes
✅ Estructura semántica HTML5
```

### 10.3 Monitoreo de SEO

- **Lighthouse CI** en pipeline de deploy
- **Google Search Console** integration
- **IndexNow** para indexación instantánea
- **Sitemap dinámico** que se actualiza con cada publicación

---

## 11. MÓDULO 8: SISTEMA DE TAGS INTELIGENTE

### 11.1 Descripción

Sistema automatizado de tags que mejora SEO, navegación y descubrimiento de contenido mediante IA. Especialmente valioso para el nicho de "IA y Automatización" donde los usuarios buscan tecnologías específicas.

### 11.2 ¿Por qué implementar tags? (Análisis ROI)

| Beneficio                | Impacto | Valor para tu nicho                              |
| ------------------------ | ------- | ------------------------------------------------ |
| **SEO Semántico**        | Alto    | Google entiende mejor contenido técnico          |
| **Navegación Mejorada**  | Alto    | Usuarios encuentran tecnologías específicas      |
| **Internal Linking**     | Alto    | +150% enlaces internos automáticos               |
| **Personalización**      | Medio   | Recomendaciones basadas en intereses             |
| **Tendencias**           | Medio   | Identificar tecnologías populares en tiempo real |
| **Organización Interna** | Medio   | Mejor gestión de contenido técnico               |

### 11.3 Arquitectura del Sistema de Tags

```
┌─────────────────────────────────────────────────┐
│            ARTÍCULO PROCESADO POR IA            │
└──────────────────────┬──────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────┐
│       EXTRACCIÓN DE TAGS CON IA (NER)           │
│  [Gemini Flash] → [Entidades Nombradas]         │
│  [Tecnologías] → [Conceptos Clave] → [Topics]   │
└──────────────────────┬──────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────┐
│          NORMALIZACIÓN Y LIMPIEZA                │
│  [Minúsculas] → [Singular/Plural] → [Stopwords] │
│  [Sinónimos] → [Tags Existentes] → [Merge]      │
└──────────────────────┬──────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────┐
│           ASIGNACIÓN AUTOMÁTICA                  │
│  [3-5 tags/artículo] → [Relación DB]            │
│  [Cache Redis] → [Contador actualizado]         │
└──────────────────────┬──────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────┐
│           FRONTEND + SEO                         │
│  [Cloud Tags] → [Páginas Tag] → [Sitemap]       │
│  [Related Articles] → [Internal Linking]        │
└─────────────────────────────────────────────────┘
```

### 11.4 Base de Datos - Esquema Optimizado

```php
// Migración para tabla tags
Schema::create('tags', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();           // "openai"
    $table->string('slug')->unique();           // "openai"
    $table->text('description')->nullable();    // Descripción generada por IA
    $table->integer('article_count')->default(0); // Cache de uso
    $table->boolean('is_featured')->default(false); // Tags destacados
    $table->json('metadata')->nullable();       // { "type": "technology", "related_tags": [] }
    $table->timestamps();
    $table->index(['article_count', 'created_at']); // Para queries rápidas
});

// Tabla pivote article_tag (many-to-many)
Schema::create('article_tag', function (Blueprint $table) {
    $table->foreignId('article_id')->constrained()->onDelete('cascade');
    $table->foreignId('tag_id')->constrained()->onDelete('cascade');
    $table->integer('relevance_score')->default(100); // Score de relevancia (0-100)
    $table->primary(['article_id', 'tag_id']);
    $table->index(['tag_id', 'relevance_score']); // Para related articles
});
```

### 11.5 Generación Automática de Tags con IA

#### Pipeline de extracción (costo: ~$0.001 por artículo)

```php
class TagGeneratorService
{
    public function generateTags(string $content): array
    {
        // 1. Extracción de entidades nombradas (NER)
        $entities = $this->extractNamedEntities($content);
        // Ej: ["OpenAI", "GPT-4", "Python", "API"]

        // 2. Identificación de tecnologías
        $technologies = $this->identifyTechnologies($content);
        // Ej: ["tensorflow", "pytorch", "react", "docker"]

        // 3. Extracción de conceptos clave
        $concepts = $this->extractKeyConcepts($content);
        // Ej: ["machine-learning", "nlp", "computer-vision"]

        // 4. Normalización y limpieza
        $tags = $this->normalizeTags(array_merge(
            $entities, $technologies, $concepts
        ));

        // 5. Limitación y scoring
        return $this->limitAndScoreTags($tags, $content);
    }

    private function normalizeTags(array $tags): array
    {
        return array_map(function($tag) {
            // Minúsculas, sin espacios extra
            $tag = strtolower(trim($tag));

            // Singularizar (opcional, basado en diccionario)
            if ($this->shouldBeSingular($tag)) {
                $tag = Str::singular($tag);
            }

            // Remover stopwords
            if ($this->isStopword($tag)) {
                return null;
            }

            // Validar longitud
            return (strlen($tag) >= 2 && strlen($tag) <= 50) ? $tag : null;
        }, array_filter($tags));
    }
}
```

### 11.6 Frontend - Implementación UX/UI

#### Sidebar - Cloud Tags

```blade
{{-- Componente: Cloud de Tags Populares --}}
<div class="tags-cloud">
    <h3>🔖 Tecnologías Populares</h3>
    <div class="tags-container">
        @foreach($popularTags as $tag)
            @php
                $size = min(24, max(12, 12 + ($tag->article_count / 10)));
                $color = $this->getTagColor($tag->name);
            @endphp
            <a href="{{ route('tags.show', $tag->slug) }}"
               class="tag"
               style="font-size: {{ $size }}px; color: {{ $color }};">
                {{ $tag->name }} <span class="count">({{ $tag->article_count }})</span>
            </a>
        @endforeach
    </div>
</div>
```

#### Página de Tag Individual

- **URL**: `/tags/{slug}` (ej: `/tags/openai`)
- **Contenido**: Lista de artículos con ese tag + descripción generada por IA
- **SEO**: Meta description automática, breadcrumbs, related tags
- **Features**: Paginación, sorting (nuevo, popular), RSS feed por tag

#### Artículo - Tags relacionados

```blade
{{-- Al final de cada artículo --}}
<div class="article-tags">
    <h4>Etiquetas relacionadas:</h4>
    <div class="tags">
        @foreach($article->tags as $tag)
            <a href="{{ route('tags.show', $tag->slug) }}" class="tag">
                #{{ $tag->name }}
            </a>
        @endforeach
    </div>

    {{-- Artículos relacionados (mismos tags) --}}
    @if($relatedArticles->count() > 0)
        <div class="related-articles">
            <h4>Artículos similares:</h4>
            @foreach($relatedArticles as $related)
                <a href="{{ route('articles.show', $related->slug) }}">
                    {{ $related->title }}
                </a>
            @endforeach
        </div>
    @endif
</div>
```

### 11.7 SEO - Beneficios Específicos

#### Sitemap de Tags

```xml
<!-- sitemap-tags.xml -->
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($popularTags as $tag)
        <url>
            <loc>{{ url('/tags/' . $tag->slug) }}</loc>
            <lastmod>{{ $tag->updated_at->format('Y-m-d') }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
</urlset>
```

#### Internal Linking Automático

- Cada tag crea una página con enlaces a todos los artículos relacionados
- Los artículos muestran tags clickeables que llevan a páginas de tag
- Las páginas de tag muestran "related tags" basado en co-ocurrencia

#### Schema.org Markup

```json
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "Artículos sobre {{ $tag->name }}",
  "description": "{{ $tag->description }}",
  "mainEntity": {
    "@type": "ItemList",
    "itemListElement": [
      @foreach($articles as $index => $article)
      {
        "@type": "ListItem",
        "position": {{ $index + 1 }},
        "item": {
          "@type": "Article",
          "headline": "{{ $article->title }}",
          "url": "{{ url($article->slug) }}"
        }
      }
      @endforeach
    ]
  }
}
```

### 11.8 Monitoreo y Mantenimiento Automático

#### Sistema de Limpieza Automática

```php
// Comando artisan: tags:cleanup
class TagsCleanupCommand extends Command
{
    public function handle()
    {
        // 1. Eliminar tags sin artículos (30 días)
        Tag::where('article_count', 0)
           ->where('updated_at', '<', now()->subDays(30))
           ->delete();

        // 2. Merge tags similares (Levenshtein distance < 2)
        $this->mergeSimilarTags();

        // 3. Actualizar contadores
        Tag::chunk(100, function ($tags) {
            foreach ($tags as $tag) {
                $tag->article_count = $tag->articles()->count();
                $tag->save();
            }
        });

        // 4. Generar reporte
        $this->generateCleanupReport();
    }
}
```

#### Analytics de Tags

- **Popularidad**: Artículos por tag, vistas por tag
- **Engagement**: Tiempo en páginas de tag, CTR
- **SEO**: Posiciones por keywords de tag
- **Monetización**: RPM por tag (si aplica)

### 11.9 Roadmap de Implementación

#### Fase 1 (Meses 1-2) - Básico

- [x] Migración de base de datos para tags
- [ ] Generación automática de tags con IA
- [x] Relación muchos-a-muchos articles-tags
- [x] Tags en frontend (Panel administrativo Filament)

#### Fase 2 (Meses 3-4) - Avanzado

- [ ] Páginas de tag individuales
- [ ] Cloud tags con sizing proporcional
- [ ] Sistema de normalización automática
- [ ] Sitemap para tags populares
- [ ] Artículos relacionados por tags

#### Fase 3 (Meses 5-6) - Optimización

- [ ] Personalización por tags (usuarios registrados)
- [ ] Newsletter por tags de interés
- [ ] Analytics de performance por tag
- [ ] Sistema de tags trending en tiempo real
- [ ] Monetización diferenciada por tag

### 11.10 KPIs Específicos para Tags

| KPI                          | Objetivo | Métrica                             |
| ---------------------------- | -------- | ----------------------------------- |
| **Tags generados/artículo**  | 3-5      | Promedio de tags por artículo       |
| **Páginas de tag indexadas** | +50%     | Número de páginas de tag en índice  |
| **Internal links**           | +150%    | Enlaces internos generados por tags |
| **Tiempo en páginas de tag** | >1.5 min | Engagement en páginas de tag        |
| **CTR tags**                 | >2%      | Clics en tags desde artículos       |

### 11.11 Costo vs Beneficio Estimado

| Aspecto           | Costo                 | Beneficio                  | ROI          |
| ----------------- | --------------------- | -------------------------- | ------------ |
| Desarrollo        | 10-15 horas           | SEO mejorado, UX mejorada  | Alto         |
| IA adicional      | $0.001-0.002/artículo | Tags precisos, relevantes  | Alto         |
| Mantenimiento     | 1-2 horas/mes         | Contenido mejor organizado | Medio-Alto   |
| **Total mensual** | **~$5-10**            | **+50% páginas indexadas** | **Muy Alto** |

**ROI estimado**: Por cada $1 gastado en tags, ganas ~100 páginas indexadas adicionales y mejor engagement.

---

## 12. MÓDULO 9: PUBLICACIÓN EN TERCEROS

### 12.1 Descripción

Sistema para distribuir contenido automáticamente a plataformas externas y atraer tráfico.

### 11.2 Plataformas Evaluadas

| Plataforma   | Método         | Costo       | ROI        | Prioridad |
| ------------ | -------------- | ----------- | ---------- | --------- |
| Telegram Bot | API nativa     | Gratis      | ⭐⭐⭐⭐⭐ | ALTA      |
| Discord      | Webhook        | Gratis      | ⭐⭐⭐⭐⭐ | ALTA      |
| Reddit       | Bot + API      | Gratis      | ⭐⭐⭐⭐⭐ | ALTA      |
| Medium       | Crosspost      | Gratis      | ⭐⭐⭐⭐   | MEDIA     |
| Twitter/X    | API (limitada) | Gratis/Pago | ⭐⭐⭐     | BAJA      |
| LinkedIn     | API limitada   | Gratis      | ⭐⭐⭐     | BAJA      |

### 11.3 Estrategia Recomendada

**Fase 1 (Prioridad)**: Telegram + Discord + Reddit

- Son gratis
- Audiencia tech los usa masivamente
- Tráfico de alta calidad

**Fase 2**: Medium + Dev.to

- Republicar versiones resumidas
- Links apuntan a tu web para SEO

**Fase 3**: Redes sociales

- Automatización con Make.com o n8n (self-hosted en Docker)

### 11.4 Herramientas de Automatización

| Herramienta       | Uso                         | Costo    |
| ----------------- | --------------------------- | -------- |
| n8n (self-hosted) | Workflows de automatización | Gratis   |
| Make.com          | Integraciones visuales      | Freemium |
| Telegram Bot API  | Canal de noticias           | Gratis   |
| Discord Webhooks  | Comunidad + noticias        | Gratis   |

---

## 13. ESTRATEGIA DE IDIOMAS

### 13.1 Decisión

**Inglés PRIMERO, Español SEGUNDO**

| Aspecto       | Inglés                      | Español                         |
| ------------- | --------------------------- | ------------------------------- |
| Prioridad     | Primaria                    | Secundaria                      |
| Contenido     | Artículo completo           | Traducción del inglés           |
| SEO           | Keywords EN (mayor volumen) | Keywords ES (menor competencia) |
| URL structure | `/en/article-slug`          | `/es/article-slug`              |
| Hreflang      | Automático                  | Automático                      |

### 13.2 ¿Por qué inglés primero?

1. Mayor volumen de búsqueda global
2. Fuentes RSS son 90% en inglés
3. Monetización (ads) paga más en inglés
4. La IA es mejor redactando en inglés (más datos de entrenamiento)
5. El mercado hispano es un "plus" que te diferencia

### 13.3 Implementación

- Cada artículo se genera primero en inglés
- Pipeline de traducción automática al español (misma IA)
- URLs con prefijo de idioma
- Hreflang tags para SEO internacional
- Selector de idioma en el frontend

---

## 14. STACK TECNOLÓGICO

### 14.1 Resumen del Stack (Actualizado con FrankenPHP)

```
┌─────────────────────────────────────────────────┐
│                  FRONTEND                        │
│  Blade + Alpine.js + Tailwind CSS + Vite        │
├─────────────────────────────────────────────────┤
│             SERVIDOR WEB + PHP                  │
│  FrankenPHP (PHP 8.3 + Caddy + HTTP/3)         │
├─────────────────────────────────────────────────┤
│                  BACKEND                         │
│  Laravel 12 + Filament v3                       │
├─────────────────────────────────────────────────┤
│                  TIEMPO REAL                     │
│  Laravel Reverb (WebSockets)                    │
├─────────────────────────────────────────────────┤
│                  COLAS                           │
│  Laravel Horizon + Redis                        │
├─────────────────────────────────────────────────┤
│                  BASE DE DATOS                   │
│  PostgreSQL + pgvector + Redis                  │
├─────────────────────────────────────────────────┤
│                  IA                              │
│  OpenRouter (Claude, GPT-4o, Gemini)            │
├─────────────────────────────────────────────────┤
│                  IMÁGENES                        │
│  FluxAPI.ai + Unsplash + libvips                │
├─────────────────────────────────────────────────┤
│                  RSS                             │
│  vedmant/laravel-feed-reader + spatie/laravel-feed │
├─────────────────────────────────────────────────┤
│                  INFRAESTRUCTURA                 │
│  Docker + Cloudflare CDN + Laravel Forge        │
├─────────────────────────────────────────────────┤
│                  MONITOREO                       │
│  Laravel Pulse + Laravel Telescope              │
└─────────────────────────────────────────────────┘
```

### 14.2 Paquetes Composer Principales (Actualizados para FrankenPHP)

| Paquete                       | Uso                                 | Notas para FrankenPHP              |
| ----------------------------- | ----------------------------------- | ---------------------------------- |
| `laravel/framework` ^12.0     | Framework principal                 | Compatible al 100%                 |
| `filament/filament` ^3.0      | Panel de administración             | Funciona sin cambios               |
| `laravel/horizon`             | Gestión de colas Redis              | Necesario para procesamiento async |
| `laravel/reverb`              | WebSockets en tiempo real           | Corre en contenedor separado       |
| `laravel/pulse`               | Monitoreo                           | Monitorea FrankenPHP también       |
| `vedmant/laravel-feed-reader` | Lectura RSS                         | Sin cambios                        |
| `spatie/laravel-feed`         | Generación RSS propio               | Sin cambios                        |
| `intervention/image`          | Procesamiento de imágenes           | Requiere extensiones GD            |
| `pgvector/pgvector`           | Embeddings para similitud semántica | Requiere extensión pgvector        |
| `dunglas/frankenphp`          | Runtime PHP + servidor web          | **NUEVO: Reemplaza Nginx+PHP-FPM** |
| `symfony/runtime`             | Integración con FrankenPHP          | Necesario para worker mode         |

**Nota importante**: No usamos `laravel/octane` porque FrankenPHP ya proporciona optimizaciones similares y mejor integración HTTP/3.

### 14.3 Paquetes NPM Principales

| Paquete        | Uso                                        |
| -------------- | ------------------------------------------ |
| `alpinejs`     | Interactividad ligera                      |
| `tailwindcss`  | Framework CSS                              |
| `laravel-echo` | Cliente WebSockets                         |
| `pusher-js`    | Conexión WebSocket (compatible con Reverb) |
| `vite`         | Build tool                                 |

---

## 15. ROADMAP DE EJECUCIÓN (Actualizado para FrankenPHP)

### Fase 1: Fundación con FrankenPHP (Meses 1-2)

**Objetivo**: Infraestructura moderna funcionando

```
✅ Docker con FrankenPHP + PostgreSQL + Redis
✅ Configuración FrankenPHP worker mode para Laravel
✅ Sistema RSS con 50 fuentes del nicho elegido
✅ Horizon configurado con workers básicos
✅ Filament admin panel básico
✅ DB schema para noticias, fuentes, categorías + pgvector
✅ Pipeline de ingesta RSS → DB (sin IA aún)
```

**Meta**: Recibir y almacenar 100+ noticias/día automáticamente con HTTP/3 habilitado

---

### Fase 2: Cerebro IA (Meses 3-4)

**Objetivo**: La IA redacta con procesamiento async

```
✅ Integración OpenRouter (multi-model) via Horizon
✅ Pipeline de redacción con 4 capas (async processing)
✅ Sistema de "voces editoriales" con cache en Redis
✅ Generación de imágenes con FluxAPI (async)
✅ Sistema anti-duplicados (3 niveles) con pgvector
✅ Humanización post-procesamiento
✅ Configuración FrankenPHP para alta concurrencia IA
```

**Meta**: Generar 20-30 artículos humanizados/día automáticamente sin afectar performance

---

### Fase 3: Frontend Público con FrankenPHP (Meses 5-6)

**Objetivo**: Sitio público ultra-rápido

```
✅ Diseño 2 columnas con Tailwind + SSR con FrankenPHP
✅ Reverb para tiempo real (contenedor separado)
✅ Optimización PageSpeed >95 con HTTP/3
✅ SEO técnico completo con Server Push
✅ Responsive design perfecto
✅ Sistema de categorías y tags
✅ Configuración Cloudflare + FrankenPHP HTTP/3
```

**Meta**: Sitio live con >95 PageSpeed score y HTTP/3 activo

---

### Fase 4: Refinamiento y Performance (Meses 7-8)

**Objetivo**: Calidad > Cantidad con métricas FrankenPHP

```
✅ Revisión manual en Filament (workflow de aprobación)
✅ A/B testing de titulares con analytics integrado
✅ Analytics con métricas FrankenPHP (request timing, memory)
✅ Sistema de "artículos destacados" con cache Redis
✅ RSS propio para que otros te consuman
✅ Sitemap dinámico + IndexNow
✅ Optimización FrankenPHP worker count y memory limits
```

**Meta**: 50+ artículos de alta calidad/día, <5% tasa de rechazo, <100ms response time

---

### Fase 5: Crecimiento y Escalabilidad (Meses 9-10)

**Objetivo**: Tracción con infraestructura escalable

```
✅ Publicación automática en Telegram/Discord (async jobs)
✅ Newsletter automatizada (semanal) con queue
✅ Sistema de "trending topics" con cache Redis
✅ Segundo nicho complementario
✅ Optimización de prompts IA (costos -30%)
✅ Monitoreo con Laravel Pulse + FrankenPHP metrics
✅ Escalado horizontal FrankenPHP (múltiples workers)
```

**Meta**: 1,000+ visitas diarias, 500+ suscriptores newsletter, 99.9% uptime

---

### Fase 6: Monetización y Producción (Meses 11-12)

**Objetivo**: Revenue con infraestructura de producción

```
✅ Ad slots dinámicos en frontend (async loading)
✅ Integración con ad network (Ezoic/Adsterra)
✅ Affiliate links automáticos en artículos relevantes
✅ Sponsored content workflow
✅ Expansión a tercer nicho
✅ Configuración FrankenPHP para producción (auto-scaling)
✅ CDN avanzado con HTTP/3 y Brotli compression
```

**Meta**: Primeros ingresos, 5,000+ visitas diarias, <50ms TTFB promedio

---

## 16. MÉTRICAS DE ÉXITO (KPIs)

### 15.1 KPIs Técnicos (Primeros 6 Meses)

| Métrica                  | Objetivo   |
| ------------------------ | ---------- |
| PageSpeed Mobile         | >95        |
| PageSpeed Desktop        | 100        |
| Tiempo RSS → Publicación | <8 minutos |
| Uptime                   | 99.9%      |
| Artículos no duplicados  | >98%       |

### 15.2 KPIs de Contenido (Primeros 6 Meses)

| Métrica                         | Objetivo |
| ------------------------------- | -------- |
| Score SEO promedio (Ahrefs/Moz) | >70      |
| Tasa de humanización exitosa    | >95%     |
| Artículos indexados en <24h     | >80%     |
| Tasa de rechazo                 | <5%      |

### 15.3 KPIs de Negocio (Meses 7-12)

| Métrica                      | Objetivo         |
| ---------------------------- | ---------------- |
| Crecimiento orgánico mensual | +25%             |
| Tiempo de sesión             | >2 min           |
| Bounce rate                  | <35%             |
| Email list (suscriptores)    | 5% de visitantes |
| Ingresos mensuales           | Primer $100+     |

---

## 17. RIESGOS Y MITIGACIONES

| Riesgo                         | Probabilidad | Impacto | Mitigación                                                                           |
| ------------------------------ | ------------ | ------- | ------------------------------------------------------------------------------------ |
| Google penaliza contenido AI   | Media        | Alto    | Humanización profunda + E-E-A-T signals + valor editorial único                      |
| Costos de IA escalan           | Media        | Medio   | Cache de respuestas + modelos económicos para tareas simples + tiered model strategy |
| Fuentes RSS cambian estructura | Alta         | Medio   | Parser tolerante a fallos + alertas de monitoreo + múltiples fuentes                 |
| Competencia copia el modelo    | Baja         | Medio   | Enfocarse en marca, comunidad y velocidad de innovación                              |
| Problemas legales de copyright | Media        | Alto    | Generar contenido nuevo (no rewrites) + atribución + respetar robots.txt             |
| Servicios de IA caen           | Baja         | Alto    | Fallback entre múltiples proveedores via OpenRouter                                  |
| Base de datos crece demasiado  | Media        | Medio   | Particionado + archivado de artículos antiguos + optimización PostgreSQL             |
| Rate limiting de APIs          | Alta         | Medio   | Backoff exponencial + múltiples API keys + cache agresivo                            |

---

## 18. MONETIZACIÓN

### 17.1 Fases de Monetización

```
FASE 1 (0-10k visitas/día):
- Enfocarse en crecimiento orgánico
- Email list building
- Sin monetización agresiva

FASE 2 (10k-50k visitas/día):
- Ad networks: Ezoic, Adsterra, PropellerAds
- Affiliate marketing de herramientas SaaS
- Newsletter con sponsors

FASE 3 (50k+ visitas/día):
- Header bidding (Playwire, Ezoic)
- Sponsored content nativo
- Newsletter premium con análisis exclusivo
- Consultorías y cursos
```

### 17.2 Ad Slots Planificados

| Ubicación                         | Tipo          | Prioridad |
| --------------------------------- | ------------- | --------- |
| Entre noticia 3 y 4 del feed      | Display ad    | Alta      |
| Sidebar                           | Display ad    | Alta      |
| Dentro del artículo (mid-content) | In-content ad | Media     |
| Header                            | Banner ad     | Baja      |
| Footer                            | Banner ad     | Baja      |

### 17.3 Affiliate Marketing (Mejorada)

- Herramientas de IA mencionadas en artículos (detección automática)
- Servicios SaaS del nicho
- Cursos y formaciones
- Herramientas de productividad
- **Micro-conversiones**: Botón "Avísame cuando esto cambie" al final de cada artículo (captura de email) para validar negocio en Fase 0 (objetivo 2-3% conversión)

---

## 19. RECOMENDACIONES ADICIONALES

### 19.1 Costos de IA (Controla o Quiebras) - Actualizado con alternativas económicas

```
ESTRATEGIA DE 4 TIERS + ALERTAS + ALTERNATIVAS ECONÓMICAS:

Tier 1 (Clasificación): Gemini Flash → $0.001/operación
Tier 2 (Extracción): Gemini Flash → $0.002/operación
Tier 3 (Redacción): Claude 3.5 Sonnet → $0.03/operación
Tier 4 (Humanización): GPT-4o-mini → $0.005/operación

Costo estimado por artículo: ~$0.04
100 artículos/día = $4/día = $120/mes

**ALTERNATIVAS MÁS ECONÓMICAS (Optimización de costos):**
- **DeepSeek V3**: Hasta 10x más barato que Claude, similar calidad
- **Gemini 3.1 Pro**: 30-40% más barato que Claude 3.5, mejor rendimiento en razonamiento
- **GPT-4o-mini para redacción**: $0.015/operación (50% más barato que Claude)

**Estrategia de costos optimizada:**
1. **Fase inicial**: Claude 3.5 Sonnet (calidad máxima)
2. **Fase crecimiento**: Gemini 3.1 Pro (balance costo/calidad)
3. **Fase escala**: DeepSeek V3 (máxima eficiencia de costos)

**Costo optimizado por artículo: ~$0.028** (usando alternativas económicas)
100 artículos/día = $2.8/día = $84/mes (30% de ahorro)

**Umbral máximo**: $0.06 por artículo. Laravel Pulse debe alertar si se supera durante 3 días consecutivos. Activar "modo degradado" automático (cambiar a modelos económicos).
```

### 19.2 Legalidad (NO LO IGNORES)

- No copiar artículos completos de fuentes con copyright
- Generar CONTENIDO NUEVO inspirado en la fuente, no un "rewrite"
- Incluir siempre atribución y link a la fuente original
- Respetar `robots.txt` en scraping
- Términos de servicio y política de privacidad desde el día 1
- Considerar regulaciones de derechos de prensa digital (UE)

### 19.3 Observabilidad Obligatoria (Mejorada con FrankenPHP)

- **Laravel Pulse**: Monitoreo nativo de Laravel 12 + métricas FrankenPHP
- **Horizon Dashboard**: Estado de colas en tiempo real
- **FrankenPHP Metrics**: Request timing, memory usage, worker status
- **Alertas**: Si un worker falla más de 3 veces, notificación inmediata
- **Logs estructurados**: Cada decisión de IA debe quedar registrada
- **HTTP/3 Monitoring**: Verificar que QUIC está funcionando correctamente

### 19.4 Optimización FrankenPHP (CRÍTICO para Performance)

```
CONFIGURACIÓN ÓPTIMA FRANKENPHP PARA LARAVEL:

1. Worker Mode Configuration:
   FRANKENPHP_WORKERS=4 (CPU cores × 1.5)
   FRANKENPHP_MAX_REQUESTS=1000
   FRANKENPHP_WORKER_MEMORY_LIMIT=256M

2. PHP Configuration:
   memory_limit = 512M
   opcache.enable=1
   opcache.memory_consumption=256
   opcache.jit_buffer_size=256M
   realpath_cache_size=4096K
   realpath_cache_ttl=600

3. Caddy Configuration:
   HTTP/3 enabled
   Brotli + Zstd compression
   Server Push for critical assets
   Static file caching headers
   Rate limiting per IP

4. Monitoring:
   /metrics endpoint for Prometheus
   Structured JSON logs
   Request timing histograms
   Memory usage alerts
```

### 19.5 Diferenciadores vs Competencia (Mejorado con FrankenPHP)

| Competencia          | Su Debilidad                       | Tu Ventaja con FrankenPHP                           |
| -------------------- | ---------------------------------- | --------------------------------------------------- |
| Medios tradicionales | Lentos (horas/días), HTTP/1.1      | Tú: <5 minutos, **HTTP/3 nativo**                   |
| Otros agregadores    | Solo copian/pegan, Nginx+PHP-FPM   | Tú: IA humaniza + SEO, **FrankenPHP worker mode**   |
| Blogs especializados | Publican 1-2/día, sin optimización | Tú: 20-50/día automatizado, **Server Push assets**  |
| Reddit/HN            | Sin estructura SEO, sin cache      | Tú: Artículos optimizados, **Redis cache + HTTP/3** |
| Competencia técnica  | Nginx config compleja              | Tú: **Caddy config simple**, auto HTTPS, HTTP/3     |

### 19.6 Ventajas Técnicas de FrankenPHP

1. **🚀 Performance Superior**:
   - Hasta 30% más rápido que Nginx+PHP-FPM
   - Worker mode mantiene app en memoria
   - HTTP/3 reduce latency 50-70%

2. **⚡ Modern Web Standards**:
   - HTTP/3 con QUIC protocol
   - Brotli + Zstd compression
   - Server Push para critical assets
   - Automatic HTTPS

3. **🔧 Simplificación Operacional**:
   - Un solo servicio (no Nginx+PHP-FPM)
   - Configuración Caddy simple
   - Auto-scaling de workers
   - Built-in metrics

4. **📈 SEO Benefits**:
   - Faster TTFB → mejor Core Web Vitals
   - HTTP/3 → mejor experiencia usuario
   - Server Push → faster LCP
   - Better cache headers → repeat visits

### 19.7 Configuración FrankenPHP para Producción

#### Docker Compose para Producción

```yaml
# docker-compose.prod.yml
services:
  app:
    image: dunglas/frankenphp:php8.3
    ports:
      - "80:80"
      - "443:443"
      - "443:443/udp" # HTTP/3 QUIC
    environment:
      - SERVER_NAME=:80
      - FRANKENPHP_WORKERS=${FRANKENPHP_WORKERS:-8}
      - FRANKENPHP_MAX_REQUESTS=10000
      - CADDY_DEBUG=false
    volumes:
      - ./:/app
      - ./docker/frankenphp/Caddyfile.prod:/etc/caddy/Caddyfile
    deploy:
      replicas: 2 # Escalado horizontal
      resources:
        limits:
          memory: 1G
        reservations:
          memory: 512M
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/health"]
      interval: 30s
      timeout: 10s
      retries: 3
```

#### Variables de entorno críticas para producción

```env
# FrankenPHP Production
FRANKENPHP_WORKERS=8  # CPU cores × 2
FRANKENPHP_MAX_REQUESTS=10000
FRANKENPHP_WORKER_MEMORY_LIMIT=256M

# Performance PHP
OPCACHE_ENABLE=1
OPCACHE_MEMORY_CONSUMPTION=512
OPCACHE_JIT=1255
OPCACHE_JIT_BUFFER_SIZE=512M
REALPATH_CACHE_SIZE=4096K
REALPATH_CACHE_TTL=600

# HTTP/3
HTTP3_ENABLED=true
BROTLI_COMPRESSION=true
ZSTD_COMPRESSION=true

# Session Management
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=false  # Redis ya es seguro
```

#### Pruebas de validación post-deploy

```bash
# Verificar HTTP/3
curl --http3 -I https://tudominio.com
# Deberías ver: HTTP/3 200, alt-svc: h3=":443"; ma=86400

# Verificar Server Push
curl -I https://tudominio.com | grep -i push
# Deberías ver headers de Server Push

# Verificar métricas FrankenPHP
curl http://localhost:2019/metrics
# Deberías ver métricas de performance

# Prueba de carga básica
ab -n 1000 -c 100 https://tudominio.com/
# TTFB debería ser <50ms
```

#### Escalado horizontal con balanceo de carga

```yaml
# Configuración para múltiples instancias FrankenPHP
version: "3.8"
services:
  loadbalancer:
    image: caddy:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./docker/caddy/Caddyfile.loadbalancer:/etc/caddy/Caddyfile
    depends_on:
      - app1
      - app2

  app1:
    image: dunglas/frankenphp:php8.3
    environment:
      - FRANKENPHP_WORKERS=4
    volumes:
      - ./:/app

  app2:
    image: dunglas/frankenphp:php8.3
    environment:
      - FRANKENPHP_WORKERS=4
    volumes:
      - ./:/app
```

### 18.5 Sitemap Dinámico

- Actualizar `sitemap.xml` al segundo con cada nueva publicación
- Sitemaps segmentados por categoría
- IndexNow para notificación instantánea a buscadores

### 18.6 Estrategia de Imágenes

- Mantener estilo visual consistente (ej: "estilo fotoperiodismo minimalista")
- Prompt engineering consistente para FluxAPI
- Que el sitio no parezca un collage de diferentes artistas

---

## 📌 RESUMEN EJECUTIVO FINAL (Actualizado con FrankenPHP)

| Decisión             | Recomendación                                          | Beneficio con FrankenPHP                |
| -------------------- | ------------------------------------------------------ | --------------------------------------- |
| **Nicho**            | IA y Automatización                                    | Temática técnica que valora performance |
| **Columnas**         | 2 (70/30)                                              | Mejor UX para lectura rápida            |
| **Idioma principal** | Inglés                                                 | Mayor audiencia global                  |
| **Fuentes RSS**      | 80% gratis / 20% pagas                                 | Balance costo/calidad                   |
| **Publicación**      | Semi-automática → 100% auto en 6 meses                 | FrankenPHP maneja alta concurrencia     |
| **Monetización**     | Mes 11+ (primero audiencia, luego dinero)              | HTTP/3 mejora conversiones              |
| **Expansión**        | 1 nicho (año 1) → 2 nichos (año 2) → 3 nichos (año 3+) | FrankenPHP escala horizontalmente       |
| **Stack Principal**  | **FrankenPHP** + Laravel 12 + Filament                 | ⚡ **20-30% más rápido**, 🚀 **HTTP/3** |
| **Base de Datos**    | PostgreSQL + pgvector + Redis                          | Embeddings para IA, cache ultra-rápido  |
| **Tiempo Real**      | Laravel Reverb + Horizon                               | WebSockets + async processing           |
| **Infraestructura**  | Docker + Cloudflare CDN                                | HTTP/3 end-to-end, edge caching         |
| **Sistema de Tags**  | IA + PostgreSQL                                        | SEO semántico, navegación mejorada      |

**Tecnología Clave**: FrankenPHP reemplaza Nginx+PHP-FPM con:

- ✅ HTTP/3 nativo para menor latencia
- ✅ Worker mode para aplicación en memoria
- ✅ Server Push para assets críticos
- ✅ Auto HTTPS y configuración simple
- ✅ Métricas integradas para monitoreo

**Innovación Clave**: Sistema de Tags Inteligente:

- ✅ Generación automática con IA (~$0.001/artículo)
- ✅ +50% páginas indexadas (SEO semántico)
- ✅ +150% internal linking automático
- ✅ Navegación por tecnologías específicas (crítico para nicho técnico)

---

## 20. LISTA DE TAREAS DETALLADAS CON CHECKLISTS

### 20.1 FASE 0: Validación y Preparación (Semanas 1-2)

#### 📋 Setup Inicial y Validación de Concepto

- [x] **Configurar entorno de desarrollo con FrankenPHP**
  - [x] Instalar Docker y Docker Compose
  - [x] Configurar WSL2 (si es Windows)
  - [x] Verificar soporte HTTP/3 en sistema
  - [x] Configurar IDE (VS Code con extensiones PHP/Laravel)
  - [x] Instalar herramientas de monitoreo FrankenPHP

- [ ] **Validación manual del nicho**
  - [ ] Recopilar 20+ feeds RSS del nicho IA y Automatización
  - [ ] Crear documento con lista de fuentes verificadas
  - [ ] Analizar competencia directa (3-5 sitios principales)
  - [ ] Validar volumen de contenido disponible (>100 artículos/día)

- [ ] **Pruebas de concepto IA**
  - [ ] Crear cuenta en OpenRouter
  - [ ] Desarrollar 10-20 prompts de redacción manual
  - [ ] Generar 5 artículos de prueba con diferentes modelos
  - [ ] Evaluar calidad y humanización del contenido
  - [ ] Documentar "Prompt Library" inicial

- [ ] **Validación de mercado**
  - [ ] Publicar 5 artículos en Medium/subdominio
  - [ ] Medir engagement (CTR, tiempo de lectura)
  - [ ] Validar interés real del público objetivo
  - [ ] Ajustar estrategia según feedback

### 20.2 FASE 1: Fundación con FrankenPHP (Meses 1-2)

#### 📋 Infraestructura y Configuración Base con FrankenPHP

- [x] **Configurar proyecto Laravel 12 con FrankenPHP**
  - [x] Crear nuevo proyecto Laravel 12
  - [x] Configurar Docker con FrankenPHP (reemplaza php-fpm+nginx)
  - [x] Configurar PostgreSQL + pgvector + Redis
  - [x] Configurar entorno de desarrollo (.env, docker-compose)
  - [x] Configurar Vite y Tailwind CSS
  - [x] Configurar FrankenPHP worker mode para Laravel

- [x] **Base de datos y modelos**
  - [x] Diseñar esquema de base de datos completo
  - [x] Crear migraciones para:
    - [x] `sources` (fuentes RSS)
    - [x] `raw_articles` (artículos crudos)
    - [x] `articles` (artículos procesados)
    - [x] `categories` (categorías)
    - [x] `authors` (autores IA)
    - [x] `images` (imágenes generadas)
    - [x] `tags` + `article_tag` (sistema de tags)
  - [x] Configurar relaciones Eloquent (incluyendo many-to-many articles-tags)
  - [x] Crear factories y seeders para testing

- [x] **Configurar Filament Admin**
  - [x] Instalar y configurar Filament v3
  - [x] Crear recursos básicos:
    - [x] Resource para `sources`
    - [x] Resource para `articles`
    - [x] Resource para `categories`
    - [x] Dashboard con métricas básicas

- [ ] **Configurar sistema de colas y sesiones**
  - [ ] Instalar y configurar Laravel Horizon
  - [ ] Configurar Redis para colas
  - [ ] **Configurar sesiones en Redis** (crítico para FrankenPHP worker mode)
    - [ ] `SESSION_DRIVER=redis` en .env
    - [ ] Configurar conexión Redis para sesiones
    - [ ] Validar que sesiones persisten entre requests
  - [ ] Crear workers básicos
  - [ ] Configurar supervisord para producción

- [ ] **Configurar FrankenPHP específicamente**
  - [ ] Crear Dockerfile para FrankenPHP con todas las extensiones
  - [ ] Configurar Caddyfile para Laravel + HTTP/3
  - [ ] Configurar worker mode (4-8 workers según CPU)
  - [ ] Configurar opcache y JIT para máximo performance
  - [ ] Habilitar Brotli/Zstd compression
  - [ ] Configurar Server Push para assets críticos
  - [ ] Configurar métricas y monitoreo FrankenPHP
  - [ ] **Pruebas de validación HTTP/3:**
    - [ ] `curl --http3 -I http://localhost` (debe mostrar HTTP/3 200)
    - [ ] Verificar headers `alt-svc` para QUIC
    - [ ] Usar `http3check.net` para validación externa
    - [ ] Probar con navegadores que soporten HTTP/3 (Chrome, Firefox)
  - [ ] **Pruebas de performance:**
    - [ ] Medir TTFB (<50ms objetivo)
    - [ ] Verificar Server Push funcionando
    - [ ] Probar compresión Brotli/Zstd
    - [ ] Validar cache headers automáticos

#### 📋 Módulo 1: Motor de Ingesta RSS

- [ ] **Sistema de fuentes RSS**
  - [ ] Instalar `vedmant/laravel-feed-reader`
  - [ ] Crear modelo `Source` con campos: url, frequency, score, is_active
  - [ ] Implementar sistema de scoring automático
  - [ ] Crear interfaz en Filament para gestionar fuentes

- [ ] **Worker de ingesta**
  - [ ] Crear comando Artisan `rss:fetch`
  - [ ] Implementar polling dinámico según frecuencia
  - [ ] Crear job `FetchRssFeedJob`
  - [ ] Implementar manejo de errores y retries

- [ ] **Parser y validación**
  - [ ] Crear parser para extraer metadatos
  - [ ] Validar estructura de artículos
  - [ ] Extraer título, contenido, fecha, autor, URL
  - [ ] Almacenar en tabla `raw_articles`

- [ ] **Sistema de monitoreo**
  - [ ] Configurar Laravel Pulse para monitoreo
  - [ ] Crear alertas para fuentes fallidas
  - [ ] Dashboard de métricas de ingesta

### 20.3 FASE 2: Cerebro IA (Meses 3-4)

#### 📋 Integración con IA

- [ ] **Configurar OpenRouter**
  - [ ] Crear servicio `OpenRouterService`
  - [ ] Implementar rotación de modelos
  - [ ] Configurar cache de respuestas
  - [ ] Implementar rate limiting y backoff

- [ ] **Pipeline de procesamiento IA**
  - [ ] Crear job `ProcessArticleWithAIJob`
  - [ ] Implementar 4 capas de procesamiento:
    - [ ] **Capa 1**: Clasificación (Gemini Flash)
    - [ ] **Capa 2**: Extracción de hechos (Gemini Flash)
    - [ ] **Capa 3**: Redacción (Claude 3.5 Sonnet)
    - [ ] **Capa 4**: Humanización (GPT-4o-mini)
  - [ ] Implementar sistema de "voces editoriales"

- [ ] **Sistema anti-duplicados**
  - [ ] Implementar 3 niveles de detección:
    - [ ] Nivel 1: Hash exacto (SHA256)
    - [ ] Nivel 2: Similitud de texto (TF-IDF)
    - [ ] Nivel 3: IA semántica (pgvector)
  - [ ] Configurar PostgreSQL con extensión pgvector
  - [ ] Crear sistema de "updates" para noticias existentes

- [ ] **Optimización SEO automática**
  - [ ] Generar meta tags automáticos
  - [ ] Crear estructura H1-H3 optimizada
  - [ ] Implementar internal linking automático
  - [ ] Generar schema.org markup

- [ ] **Sistema de Tags Inteligente**
  - [ ] Crear servicio `TagGeneratorService`
  - [ ] Implementar extracción de tags con IA (NER)
  - [ ] Sistema de normalización automática (minúsculas, singular/plural)
  - [ ] Asignación automática de 3-5 tags por artículo
  - [ ] Cache de tags en Redis para performance
  - [ ] Sistema de limpieza automática (tags sin uso)

#### 📋 Generación de imágenes

- [ ] **Integración con FluxAPI**
  - [ ] Crear servicio `ImageGenerationService`
  - [ ] Implementar prompt templates consistentes
  - [ ] Crear sistema de fallback (Unsplash/placeholder)
  - [ ] Optimizar imágenes (WebP/AVIF)

- [ ] **Procesamiento de imágenes**
  - [ ] Instalar Intervention Image
  - [ ] Implementar compresión inteligente
  - [ ] Generar alt text con IA
  - [ ] Configurar CDN (Cloudflare)

### 20.4 FASE 3: Frontend Público (Meses 5-6)

#### 📋 Diseño y desarrollo frontend

- [ ] **Diseño UI/UX**
  - [ ] Crear diseño 2 columnas (70/30)
  - [ ] Diseñar componentes con Tailwind CSS
  - [ ] Implementar dark mode
  - [ ] Crear diseño responsive mobile-first

- [ ] **Layout principal**
  - [ ] Header con navegación y search
  - [ ] Columna principal: grid de noticias
  - [ ] Sidebar: trending, categorías, newsletter
  - [ ] Footer con enlaces legales

- [ ] **Páginas y rutas**
  - [ ] Página de inicio (feed de noticias)
  - [ ] Página de artículo individual
  - [ ] Páginas de categoría
  - [ ] Páginas de tags (nube de tags, artículos por tag)
  - [ ] Página "Sobre nuestro proceso IA"
  - [ ] Página de búsqueda

- [ ] **Sistema de tags en frontend**
  - [ ] Componente nube de tags en sidebar
  - [ ] Mostrar tags en artículos individuales
  - [ ] Página de tag individual con artículos relacionados
  - [ ] Sistema de tags relacionados (semantic similarity)
  - [ ] Componente "artículos relacionados por tags"

- [ ] **Tiempo real con Reverb**
  - [ ] Instalar y configurar Laravel Reverb
  - [ ] Crear canales WebSocket
  - [ ] Implementar actualizaciones en tiempo real
  - [ ] Configurar Laravel Echo en frontend

#### 📋 SEO técnico

- [ ] **Optimización de rendimiento con FrankenPHP**
  - [ ] Configurar FrankenPHP worker mode (reemplaza Octane)
  - [ ] Implementar cache de vistas en Redis
  - [ ] Optimizar imágenes (lazy loading, srcset)
  - [ ] Minificar CSS/JS con Vite
  - [ ] Configurar HTTP/3 y Server Push en Caddyfile

- [ ] **SEO on-page**
  - [ ] Generar sitemap dinámico
  - [ ] Implementar IndexNow
  - [ ] Configurar hreflang para EN/ES
  - [ ] Implementar structured data

### 20.5 FASE 4: Refinamiento (Meses 7-8)

#### 📋 Workflow de aprobación

- [ ] **Sistema de revisión manual**
  - [ ] Crear estados: draft, pending_review, approved, published
  - [ ] Implementar workflow en Filament
  - [ ] Crear dashboard de revisión
  - [ ] Sistema de notificaciones para editores

- [ ] **A/B testing y feedback loop**
  - [ ] Implementar testing de titulares
  - [ ] Sistema de métricas por artículo
  - [ ] Dashboard de performance de contenido
  - [ ] **Sistema de feedback para mejorar prompts:**
    - [ ] Registrar cambios manuales en Filament
    - [ ] Analizar patrones de edición humana
    - [ ] Refinar prompts basado en feedback real
    - [ ] Versionado de prompts con métricas de calidad
    - [ ] Automatizar mejora continua de prompts IA

- [ ] **Analytics y métricas**
  - [ ] Integrar Plausible/Google Analytics
  - [ ] Dashboard de métricas de engagement
  - [ ] Sistema de "artículos destacados"
  - [ ] Reportes automáticos

#### 📋 RSS propio y distribución

- [ ] **Generación de RSS**
  - [ ] Instalar `spatie/laravel-feed`
  - [ ] Crear feeds por categoría
  - [ ] RSS completo del sitio
  - [ ] Promover RSS para que otros consuman

### 20.6 FASE 5: Crecimiento (Meses 9-10)

#### 📋 Automatización de distribución

- [ ] **Integración con plataformas**
  - [ ] Telegram Bot API
  - [ ] Discord Webhooks
  - [ ] Reddit API (bot)
  - [ ] Medium API (crossposting)

- [ ] **Newsletter automatizada**
  - [ ] Sistema de suscripción
  - [ ] Newsletter semanal automática
  - [ ] Templates responsive
  - [ ] Analytics de newsletter

- [ ] **Sistema de trending**
  - [ ] Algoritmo de trending topics
  - [ ] Widget en sidebar
  - [ ] Página de trending
  - [ ] Actualización en tiempo real

#### 📋 Optimización de costos IA

- [ ] **Monitoreo de costos**
  - [ ] Dashboard de costos por modelo
  - [ ] Alertas de umbrales
  - [ ] "Modo degradado" automático
  - [ ] Optimización de prompts

### 20.7 FASE 6: Monetización (Meses 11-12)

#### 📋 Sistema de anuncios

- [ ] **Integración con ad networks**
  - [ ] Ezoic/Adsterra integration
  - [ ] Ad slots dinámicos
  - [ ] Header bidding (opcional)
  - [ ] Analytics de revenue

- [ ] **Affiliate marketing**
  - [ ] Sistema de detección automática
  - [ ] Links de afiliado contextuales
  - [ ] Dashboard de comisiones
  - [ ] Integración con Amazon/ShareASale

- [ ] **Sponsored content**
  - [ ] Workflow para contenido patrocinado
  - [ ] Sistema de pricing
  - [ ] Dashboard para anunciantes
  - [ ] Tracking de performance

### 20.8 TAREAS TRANSVERSALES (Todo el proyecto)

#### 📋 DevOps y Deployment

- [ ] **Configuración de producción**
  - [ ] Servidor VPS/Droplet
  - [ ] Laravel Forge/Envoyer
  - [ ] CI/CD pipeline
  - [ ] Backup automático

- [ ] **Monitoreo y alertas**
  - [ ] Laravel Telescope
  - [ ] Uptime monitoring
  - [ ] Alertas por email/Telegram
  - [ ] Logs centralizados

#### 📋 Seguridad y Legal

- [ ] **Protección del sitio**
  - [ ] HTTPS/SSL
  - [ ] Rate limiting
  - [ ] Protección contra bots
  - [ ] WAF (Cloudflare)

- [ ] **Documentación legal**
  - [ ] Términos de servicio
  - [ ] Política de privacidad
  - [ ] Política de cookies
  - [ ] DMCA policy

#### 📋 Documentación y Mantenimiento

- [ ] **Documentación técnica**
  - [ ] README del proyecto
  - [ ] Documentación de API
  - [ ] Guías de deployment
  - [ ] Troubleshooting guide

- [ ] **Mantenimiento continuo**
  - [ ] Actualizaciones de seguridad
  - [ ] Optimización de base de datos
  - [ ] Cleanup de datos antiguos
  - [ ] Scaling horizontal de FrankenPHP (balanceo de carga con Caddy/Cloudflare)

---

## 📝 NOTAS DE DESARROLLO

> Este documento es el plan maestro del proyecto.  
> Actualizar conforme avance el desarrollo.  
> Cada módulo debe tener su propio documento técnico detallado.

---

**Última actualización**: 29 de Marzo de 2026  
**Versión**: 4.1 (Optimizado con feedback de IA - Correcciones de consistencia + mejoras técnicas)  
**Estado**: ✅ **APROBADO PARA EJECUCIÓN INMEDIATA** - Plan enterprise listo para desarrollo

---

## 🚀 CHECKLIST DE ARRANQUE - DÍA 1 (HOY)

### **Paso 1: Crear estructura inicial del proyecto**

```bash
# 1. Navegar al directorio del proyecto
cd /home/adminpro/noticias

# 2. Crear proyecto Laravel 12
composer create-project laravel/laravel:^12.0 noticias-platform --prefer-dist

# 3. Entrar al directorio del proyecto
cd noticias-platform
```

### **Paso 2: Configurar Docker con FrankenPHP**

```bash
# 1. Crear estructura de directorios
mkdir -p docker/{frankenphp,postgres}
mkdir -p src

# 2. Crear docker-compose.yml con FrankenPHP
# (Usar la configuración de la sección 18.7)

# 3. Crear Dockerfile para FrankenPHP
# (Incluir todas las extensiones necesarias)

# 4. Crear Caddyfile para Laravel + HTTP/3
```

### **Paso 3: Variables de entorno base**

```bash
# 1. Copiar .env.example a .env
cp .env.example .env

# 2. Configurar variables críticas:
# DB_CONNECTION=pgsql
# CACHE_DRIVER=redis
# QUEUE_CONNECTION=redis
# SESSION_DRIVER=redis
# FRANKENPHP_WORKERS=4

# 3. Generar APP_KEY
php artisan key:generate
```

### **Paso 4: Levantar entorno de desarrollo**

```bash
# 1. Levantar contenedores
docker compose up -d

# 2. Verificar que Laravel carga
curl http://localhost
# Debería mostrar la página de bienvenida de Laravel

# 3. Verificar HTTP/3
curl --http3 -I http://localhost
# Debería mostrar: HTTP/3 200
```

### **Paso 5: Primer commit**

```bash
# 1. Inicializar repositorio git
git init

# 2. Agregar todos los archivos
git add .

# 3. Primer commit
git commit -m "feat: proyecto inicial con FrankenPHP + Laravel 12"
```

---

## 🎯 **DECISIÓN INMEDIATA - ¿QUÉ HACEMOS AHORA?**

**Recomiendo comenzar con [OPCIÓN A] 🐳 Docker + FrankenPHP listo para usar**

### **Por qué empezar con la infraestructura:**

1. **Elimina bloqueos técnicos**: Tener el entorno funcionando es el mayor obstáculo
2. **Valida decisiones técnicas**: Confirmar que FrankenPHP + HTTP/3 funciona
3. **Crea momentum**: Ver algo funcionando motiva a continuar
4. **Base sólida**: Todo lo demás se construye sobre esta infraestructura

### **¿Listo para comenzar?**

Te proporcionaré:

1. **`docker-compose.yml`** completo con FrankenPHP
2. **`Dockerfile`** optimizado para Laravel 12
3. **`Caddyfile`** con HTTP/3 y Server Push
4. **Scripts de validación** para HTTP/3 y performance

**¿Quieres que empecemos con la configuración de Docker + FrankenPHP?** 🚀
