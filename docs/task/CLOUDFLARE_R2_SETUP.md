# ☁️ Guía Completa: Cloudflare R2 + CDN para Glodaxia

> **Objetivo**: Almacenar imágenes en Cloudflare R2 ($0 egress) servidas vía CDN global  
> **Ahorro**: ~$0/mes en egress (vs ~$0.09/GB en S3) gracias a cache CDN  
> **Fecha**: Junio 2026

---

## 📋 TABLA DE CONTENIDOS

1. [Cómo Funciona la Estrategia de Ahorro](#1-cómo-funciona-la-estrategia-de-ahorro)
2. [Paso 1: Crear Bucket R2](#2-paso-1-crear-bucket-r2)
3. [Paso 2: Crear API Token para R2](#3-paso-2-crear-api-token-para-r2)
4. [Paso 3: Configurar Dominio CDN](#4-paso-3-configurar-dominio-cdn)
5. [Paso 4: Configurar Reglas de Cache](#5-paso-4-configurar-reglas-de-cache)
6. [Paso 5: Crear API Token para Purge Cache](#6-paso-5-crear-api-token-para-purge-cache)
7. [Paso 6: Configurar .env](#7-paso-6-configurar-env)
8. [Paso 7: Migrar Imágenes a R2](#8-paso-7-migrar-imágenes-a-r2)
9. [Paso 8: Verificar y Cambiar a R2](#9-paso-8-verificar-y-cambiar-a-r2)
10. [Paso 9: Limpiar Almacenamiento Local](#10-paso-9-limpiar-almacenamiento-local)
11. [Diagrama de Flujo Completo](#11-diagrama-de-flujo-completo)
12. [Costos Estimados](#12-costos-estimados)
13. [Troubleshooting](#13-troubleshooting)

---

## 1. Cómo Funciona la Estrategia de Ahorro

```
┌──────────┐     ┌─────────────────┐     ┌──────────────────┐
│  Lector  │────▶│  Cloudflare CDN │────▶│  Cloudflare R2   │
│ (Browser)│     │  (Cache Global) │     │  (Object Storage)│
└──────────┘     └─────────────────┘     └──────────────────┘
      │                   │                        │
      │  1er request:     │  Cache MISS:           │  Lee de R2
      │  CDN no tiene     │  CDN pide a R2         │  (1 operación)
      │  la imagen         │  y guarda copia        │
      │                   │                        │
      │  Siguientes:      │  Cache HIT:            │  NO consulta
      │  CDN sirve        │  CDN sirve directo     │  R2 (0 ops)
      │  directo (0 R2)   │  desde su cache        │
```

**Resultados:**
- **1er visitante** de cada imagen: 1 operación Clase B en R2
- **Visitantes 2-∞**: 0 operaciones en R2 (sirve desde CDN)
- **10M operaciones gratis/mes** en R2 = cubre millones de visitas
- **Almacenamiento**: ~$0.015/GB/mes (una imagen promedio = 100KB)
- **Egress**: **$0 siempre** (la ventaja de R2 vs S3)

---

## 2. Paso 1: Crear Bucket R2

### 2.1 Ir a Cloudflare Dashboard
1. Ve a [dash.cloudflare.com](https://dash.cloudflare.com)
2. En el menú lateral izquierdo, clic en **"R2"**
3. Clic en **"Create bucket"**

### 2.2 Configurar el Bucket

| Campo | Valor | Nota |
|-------|-------|------|
| **Bucket name** | `glodaxia-media` | Nombre único global en Cloudflare |
| **Location** | `Automatic (ENAM)` | Cloudflare elige la más cercana |

4. Clic en **"Create bucket"**

### 2.3 Habilitar Acceso Público

1. Dentro del bucket recién creado, ve a la pestaña **"Settings"**
2. En **"Public Access"**, busca **"R2.dev subdomain"**
3. Clic en **"Allow Access"** y confirma
   - Esto te da una URL tipo: `https://pub-{hash}.r2.dev`
   - Esta URL es temporal — usaremos un dominio propio después

> ⚠️ **La URL `r2.dev` es solo para pruebas.** En producción usaremos un subdominio propio (paso 3).

---

## 3. Paso 2: Crear API Token para R2

### 3.1 Crear el Token

1. En R2 (menú lateral), clic en **"Manage R2 API Tokens"** (parte superior derecha)
2. Clic en **"Create API token"**

### 3.2 Configurar el Token

| Campo | Valor |
|-------|-------|
| **Token name** | `glodaxia-app-rw` |
| **Permissions** | `Object Read & Write` |
| **Specify bucket** | `glodaxia-media` (el bucket que creaste) |
| **TTL** | `Forever` (o la fecha que prefieras) |
| **IP address filtering** | (opcional) IP de tu VPS si quieres restringir |

3. Clic en **"Create API Token"**

### 3.3 Copiar las Credenciales

> ⚠️ **IMPORTANTE**: El `Secret Access Key` solo se muestra UNA vez. Cópialo inmediatamente.

Te van a dar:
```
Access Key ID:     a1b2c3d4e5f6...
Secret Access Key: x9y8z7w6v5u4...
Endpoint:          https://<account_id>.r2.cloudflarestorage.com
```

**Guarda estos valores** — los necesitas en el `.env`.

---

## 4. Paso 3: Configurar Dominio CDN

Esto es lo que hace la magia del ahorro. En vez de servir imágenes desde `r2.dev`, usamos un subdominio propio que pasa por la CDN de Cloudflare.

### 4.1 Configurar Custom Domain en R2

1. Ve a tu bucket **"glodaxia-media"** → **"Settings"**
2. En **"Custom Domains"**, clic en **"Connect Domain"**
3. Escribe: `media.glodaxia.com` (o el subdominio que prefieras)
4. Clic en **"Continue"** → **"Connect domain"**

Cloudflare automáticamente:
- Crea el registro DNS CNAME
- Activa el proxy (naranja) para que pase por la CDN
- Configura SSL/TLS

### 4.2 Verificar

Después de ~1-2 minutos:
- Ve a **DNS** → Records en tu dominio
- Deberías ver un registro CNAME: `media.glodaxia.com` → `{hash}.r2.cloudflarestorage.com` (proxied ✅)

### 4.3 Resultado

Ahora tus imágenes tendrán URLs como:
```
https://media.glodaxia.com/1/article-hero.webp
https://media.glodaxia.com/1/conversions/article-hero-thumb.webp
```

Y pasarán por la CDN de Cloudflare automáticamente (el proxy naranja lo hace).

---

## 5. Paso 4: Configurar Reglas de Cache

Para maximizar el ahorro, configuramos que las imágenes se cacheen agresivamente.

### 5.1 Crear Cache Rule

1. Ve a tu dominio en Cloudflare → **"Rules"** → **"Cache Rules"**
2. Clic en **"Create rule"**

### 5.2 Configurar

| Campo | Valor |
|-------|-------|
| **Rule name** | `Cache R2 media aggressively` |
| **When incoming requests match** | `Hostname equals` → `media.glodaxia.com` |
| **Then** | **Eligible for cache** = `true` |
| **Edge TTL** | `Override` → `1 year (31536000 seconds)` |
| **Browser TTL** | `Override` → `1 year (31536000 seconds)` |
| **Cache key** | (opcional) `Include query string = false` |

3. Clic en **"Deploy"**

### 5.3 ¿Por qué 1 año?

Las imágenes de artículos son inmutables (no cambian después de publicarse). Al cachear por 1 año:
- **Primer visitante**: 1 petición a R2
- **Siguientes visitantes durante 1 año**: 0 peticiones a R2
- Cuando necesites forzar una actualización, el `PurgeR2CacheJob` limpia la cache

### 5.4 Configurar Cache Level (Opcional pero recomendado)

1. Ve a **"Caching"** → **"Configuration"**
2. **Browser Cache TTL**: `Respect Existing Headers`
3. **Caching Level**: `Standard`

---

## 6. Paso 5: Crear API Token para Purge Cache

Este token permite a Laravel limpiar la cache de CDN cuando se elimina una imagen.

### 6.1 Crear Token

1. Ve a **"My Profile"** (esquina superior derecha) → **"API Tokens"**
2. Clic en **"Create Token"**
3. Busca la plantilla **"Custom token"** y clic en **"Get started"**

### 6.2 Configurar

| Campo | Valor |
|-------|-------|
| **Token name** | `glodaxia-cache-purge` |
| **Permissions** | `Zone` → `Cache Purge` → `Purge` |
| **Zone Resources** | `Include` → `Specific zone` → `glodaxia.com` |

4. Clic en **"Continue to summary"** → **"Create Token"**
5. **Copia el token** — solo se muestra una vez

### 6.3 Obtener Zone ID

1. Ve a tu dominio en Cloudflare
2. En **"Overview"** (panel principal), abajo a la derecha encontrarás **"Zone ID"**
3. Clic en el botón de copiar

---

## 7. Paso 6: Configurar .env

### 7.1 En Desarrollo (local)

Tu `.env` actual ya funciona con almacenamiento local. No cambies nada:
```env
MEDIA_DISK=public
FILESYSTEM_DISK=local
```

### 7.2 En Producción (VPS)

```env
# Usar R2 para media
MEDIA_DISK=r2

# Cloudflare R2
R2_ACCESS_KEY_ID=a1b2c3d4e5f6...
R2_SECRET_ACCESS_KEY=x9y8z7w6v5u4...
R2_BUCKET=glodaxia-media
R2_ENDPOINT=https://<tu_account_id>.r2.cloudflarestorage.com
R2_PUBLIC_URL=https://media.glodaxia.com

# Cloudflare CDN Cache Purge (opcional)
CLOUDFLARE_ZONE_ID=<tu_zone_id>
CLOUDFLARE_API_TOKEN=<token_de_purge_cache>
```

---

## 8. Paso 7: Migrar Imágenes a R2

### 8.1 Pre-check (dry run)

```bash
# Ver qué se va a migrar sin subir nada
docker compose exec app php artisan media:migrate-to-r2 --dry-run
```

### 8.2 Ejecutar migración

```bash
# Subir todas las imágenes a R2
docker compose exec app php artisan media:migrate-to-r2
```

Esto:
- Lee cada registro de la tabla `media`
- Sube el archivo original + conversiones (thumb, medium, large) a R2
- Usa `Cache-Control: public, max-age=31536000, immutable` (1 año de cache)
- Salta archivos que ya existen en R2 (idempotente)
- Muestra barra de progreso

### 8.3 Verificar

```bash
# Verificar que las imágenes están en R2
# Ve a Cloudflare → R2 → glodaxia-media → deberías ver carpetas 1/, 2/, etc.
```

---

## 9. Paso 8: Verificar y Cambiar a R2

### 9.1 Verificar que R2 funciona

1. Abre una URL de imagen directa de R2 en el navegador:
   ```
   https://media.glodaxia.com/1/nombre-imagen.webp
   ```
2. Debería cargar la imagen
3. En las DevTools del navegador → Network, verifica los headers:
   - `cf-cache-status: HIT` (después del primer request) = ✅ CDN funcionando
   - `cf-ray: ...` = ✅ Pasando por Cloudflare

### 9.2 Cambiar .env a R2

```bash
# En el VPS, editar .env
docker compose exec app sed -i 's/MEDIA_DISK=public/MEDIA_DISK=r2/' .env

# Limpiar cache de configuración
docker compose exec app php artisan config:clear
docker compose exec app php artisan config:cache

# Reiniciar workers
docker compose restart app horizon
```

### 9.3 Verificar que artículos nuevos usan R2

1. Espera a que se publique un artículo nuevo (o fuerza uno desde Filament)
2. Inspecciona la imagen en el frontend — la URL debería ser `https://media.glodaxia.com/...`
3. Si la URL sigue siendo local (`/storage/...`), verifica que `MEDIA_DISK=r2` en `.env`

---

## 10. Paso 9: Limpiar Almacenamiento Local

Una vez confirmado que R2 funciona correctamente:

```bash
# 1. Verificar que no hay archivos pendientes
docker compose exec app php artisan media:cleanup-orphan --dry-run

# 2. Eliminar archivos locales (ya están en R2)
docker compose exec app bash -c "rm -rf storage/app/public/media/*"

# 3. Eliminar archivos temporales
docker compose exec app bash -c "rm -rf storage/app/images-tmp/*"
```

> ⚠️ **Solo haz esto después de confirmar que R2 funciona al 100%**. Los archivos eliminados del disco local no se pueden recuperar.

---

## 11. Diagrama de Flujo Completo

### Subida de Imagen (cuando se publica un artículo)

```
SiliconFlow genera imagen
        ↓
Guarda en storage/app/images-tmp/
        ↓
Spatie MediaLibrary copia a MEDIA_DISK
        ↓
┌───────┴───────┐
│ MEDIA_DISK=?  │
├───────────────┤
│ public (dev)  │ → storage/app/public/media/{id}/
│ r2 (prod)     │ → R2: glodaxia-media/{id}/
└───────────────┘
        ↓
URLs guardadas en article.image_url + media table
        ↓
Frontend sirve imagen via CDN (Cloudflare cache)
```

### Eliminación de Artículo

```
Admin elimina artículo en Filament
        ↓
Article::booted() → deleted event
        ↓
Recopila URLs de todas las imágenes (EN + ES)
        ↓
clearMediaCollection() → Spatie elimina de MEDIA_DISK
        ↓
┌───────┴───────┐
│ MEDIA_DISK=?  │
├───────────────┤
│ public        │ → Elimina archivos locales
│ r2            │ → Elimina de R2 + PurgeR2CacheJob
└───────────────┘
        ↓
PurgeR2CacheJob → Cloudflare API → Limpia cache CDN
        ↓
Siguiente visitante no verá imagen 404 (cache limpia)
```

---

## 12. Costos Estimados

### Escenario: 50 artículos publicados

| Concepto | Cálculo | Costo |
|----------|---------|-------|
| Imágenes generadas | 50 arts × 4 imgs × 2 idiomas = 400 archivos | — |
| Tamaño promedio | 400 × 100KB × 4 tamaños = ~160MB | — |
| Almacenamiento R2 | 160MB × $0.015/GB/mes | **~$0.002/mes** |
| Operaciones R2 (con CDN) | ~400 primeras lecturas + uploads | **Gratis** (< 10M) |
| Egress | $0 (ventaja R2) | **$0** |
| **Total** | | **~$0.002/mes** |

### Escenario: 1,000 artículos publicados

| Concepto | Cálculo | Costo |
|----------|---------|-------|
| Almacenamiento | ~3.2GB | **~$0.05/mes** |
| Operaciones (con CDN) | ~8,000 primeras lecturas | **Gratis** |
| **Total** | | **~$0.05/mes** |

### Escenario: 10,000 artículos + 1M visitas/mes

| Concepto | Cálculo | Costo |
|----------|---------|-------|
| Almacenamiento | ~32GB | **~$0.48/mes** |
| Operaciones R2 (con CDN cache) | Solo ~32K (primeras lecturas) | **Gratis** |
| Egress | $0 | **$0** |
| **Total** | | **~$0.48/mes** |

> **Sin CDN cache**: 1M visitas × 4 imgs × 4 tamaños = 16M operaciones = ~$0.024/mes extra.
> **Con CDN cache**: las operaciones se reducen ~99%.

---

## 13. Troubleshooting

### "Images not loading from R2"

1. Verifica que `MEDIA_DISK=r2` en `.env`
2. Verifica que `R2_PUBLIC_URL` sea correcto
3. Ejecuta `php artisan config:clear`
4. Verifica que el dominio CDN tiene proxy naranja activado (DNS → Records)

### "Cache-Control headers missing"

1. Revisa la Cache Rule en Cloudflare → Rules → Cache Rules
2. Asegúrate que la regla matchea el hostname correcto
3. Verifica en DevTools → Network → Response Headers

### "PurgeR2CacheJob failing"

1. Verifica `CLOUDFLARE_ZONE_ID` y `CLOUDFLARE_API_TOKEN` en `.env`
2. Verifica que el token tiene permisos `Zone → Cache Purge → Purge`
3. Revisa logs: `tail -f storage/logs/laravel.log | grep Purge`

### "Upload timeout during migration"

```bash
# Subir en batches más pequeños
docker compose exec app php artisan media:migrate-to-r2 --batch-size=20
```

### "Mixed content warnings (HTTP/HTTPS)"

1. Verifica que `R2_PUBLIC_URL` use `https://`
2. En Cloudflare → SSL/TLS → Mode: `Full (strict)`

### "CDN cache serving stale images after update"

Esto no debería pasar porque las imágenes son inmutables (una vez creadas, no cambian). Pero si necesitas forzar:

```php
// En cualquier lugar del código:
use App\Jobs\PurgeR2CacheJob;
PurgeR2CacheJob::dispatch(['https://media.glodaxia.com/path/to/image.webp']);
```

---

## 📝 Comandos de Referencia

```bash
# Verificar estado de migración (dry run)
php artisan media:migrate-to-r2 --dry-run

# Ejecutar migración
php artisan media:migrate-to-r2

# Limpiar imágenes huérfanas
php artisan media:cleanup-orphan --dry-run
php artisan media:cleanup-orphan

# Verificar configuración
php artisan tinker
>>> config('media-library.disk_name')
>>> config('filesystems.disks.r2')
>>> Storage::disk('r2')->put('test.txt', 'hello')
>>> Storage::disk('r2')->delete('test.txt')

# Cambiar entre local y R2 (sin migrar)
# Solo cambia MEDIA_DISK en .env y ejecuta:
php artisan config:clear
```
