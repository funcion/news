# 🌐 Guía Integral de Servicios Externos, Cloudflare e Integraciones de API

Este documento contiene la referencia técnica completa, variables de entorno, permisos requeridos y buenas prácticas de seguridad para todos los servicios externos y APIs conectadas a **Glodaxia**.

---

## 1. Cloudflare: CDN, Cache Purge y Storage R2

Glodaxia utiliza Cloudflare para aceleración CDN, purga de caché automática y almacenamiento de objetos S3-compatible mediante **Cloudflare R2**.

### A. Token de Purga de Caché (`Zone.Cache Purge`)
Permite a la aplicación invalidar y actualizar la caché de la CDN cuando un artículo es editado o publicado.

* **Variables `.env`**:
  ```env
  CLOUDFLARE_ZONE_ID=a143de8f9ff38c189f40c3456237f771
  CLOUDFLARE_API_TOKEN=cfut_...
  ```
* **Permisos requeridos en Cloudflare Dashboard**:
  * **Tipo de Token**: *User API Token* / *Account API Token*.
  * **Permiso**: `Zone` ➔ `Cache Purge` ➔ `Purge`.
  * **Recurso de Zona**: `All Zones` o `glodaxia.com`.
* **Procedimiento de Rotación Segura (*Rolling Token*)**:
  1. Si un token se revoca o se detecta expuesto, ve a [Cloudflare API Tokens](https://dash.cloudflare.com/profile/api-tokens).
  2. En el menú `...` del token, selecciona **Roll** (generará una nueva clave manteniendo los mismos permisos).
  3. Actualiza el valor en tu archivo `.env` en el servidor Coolify.

---

### B. Cloudflare R2: Almacenamiento de Medios e Imágenes
Todas las imágenes generadas por IA (portadas e imágenes interiores) se alojan en el bucket R2 y se sirven a través del subdominio de medios.

* **Variables `.env`**:
  ```env
  FILESYSTEM_DISK=local
  MEDIA_DISK=r2
  R2_ACCESS_KEY_ID=e01f7d19440820846d99dd24cf00c690
  R2_SECRET_ACCESS_KEY=...
  R2_BUCKET=glodaxia-media
  R2_ENDPOINT=https://2f804b6957d992282275865a8626b949.r2.cloudflarestorage.com
  R2_PUBLIC_URL=https://media.glodaxia.com
  ```
* **Características**:
  * Compatible con AWS S3 SDK.
  * Cero costos de transferencia de salida (*zero egress fees*).
  * Servido a través del dominio personalizado con proxy y caché CDN: `https://media.glodaxia.com`.

---

## 2. OpenRouter: Motor de Redacción e Inteligencia Artificial

OpenRouter orquesta el pool de modelos de lenguaje (LLMs) para la clasificación de noticias, extracción de hechos y redacción bilingüe independiente.

* **Variables `.env`**:
  ```env
  OPENROUTER_API_KEY=sk-or-v1-...
  OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
  ```
* **Configuración del Pool (`config/ai_models.php`)**:
  * **Modelo Principal**: `deepseek/deepseek-v4-flash`
  * **Failover Chain**: `qwen/qwen3.7-flash`, `deepseek/deepseek-chat`
* **Límites**:
  * `max_tokens`: 10,000 por llamada.
  * Circuit Breaker automático en fallos 401/429.

---

## 3. SiliconFlow: Motor de Imágenes Fotorrealistas (FLUX.1)

Genera imágenes fotorrealistas con formato de fotografía periodística de 35mm DSLR para portadas y secciones interiores.

* **Variables `.env`**:
  ```env
  SILICONFLOW_API_KEY=sk-rjxt...
  SILICONFLOW_IMAGE_MODEL=black-forest-labs/FLUX.1-schnell
  ```
* **Pipeline de Medios**:
  * Genera formato WebP optimizado en 3 resoluciones (`thumb: 480w`, `medium: 800w`, `large: 1200w`).
  * Sube automáticamente los archivos procesados al bucket Cloudflare R2.

---

## 4. Jina Reader: Extracción Limpia de Contenido Web

Extrae el texto plano, titular y metadatos de las URLs fuente en formato Markdown limpio, omitiendo anuncios y scripts.

* **Variables `.env`**:
  ```env
  JINA_API_KEY=jina_...
  ```
* **Endpoint**: `https://r.jina.ai/{url}`

---

## 5. IndexNow: Protocolo de Indexación Instantánea

Notifica en tiempo real a los motores de búsqueda (Bing, Yandex, Microsoft Copilot, ChatGPT Search) cada vez que una noticia es publicada o reprocesada.

* **Variables `.env`**:
  ```env
  INDEXNOW_KEY=e9a4f781c03d42bfa168925bc01e4a77
  ```
* **Ruta de Verificación**: `https://glodaxia.com/e9a4f781c03d42bfa168925bc01e4a77.txt`
* **Ruta de Envío**: `https://glodaxia.com/indexnow`
* **Automatización**: Se dispara automáticamente al crearse o publicarse cualquier artículo (`slug_en` y `slug_es`).

---

## 6. Ably: WebSockets y Notificaciones en Tiempo Real

Proporciona comunicación por WebSockets para el panel de administración Filament y eventos en vivo.

* **Variables `.env`**:
  ```env
  BROADCAST_CONNECTION=ably
  BROADCAST_DRIVER=ably
  ABLY_KEY=ljcq8Q...
  ```

---

## 7. Buenas Prácticas de Seguridad y Repositorio

1. **Protección de Secretos en Git**:
   * Las claves reales deben colocarse en `.env` en el servidor Coolify.
   * En archivos de documentación `.md`, utilizar siempre marcadores seguros (`<tu_api_key>`) para evitar bloqueos por *GitHub Secret Scanning*.
2. **Rotación Periódica**:
   * Si cualquier credencial es reportada como comprometida, utilizar la función de rotación (*Roll*) en el panel del proveedor y actualizar el `.env`.