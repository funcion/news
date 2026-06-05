# Referencia Técnica: Lógica de Ingesta y Control de Fuentes (Sources)

Este documento detalla todas las reglas de negocio aplicadas al control, activación e ingesta de feeds desde fuentes externas. Corresponde al módulo visual de **Fuentes** en el panel de administración (`/admin/sources`).

---

## 1. Filtrado Dinámico de Antigüedad (Ingestion Max Age)

### 1.1 Regla de Negocio
Para asegurar la frescura de los contenidos y evitar saturar la base de datos con artículos obsoletos, el sistema no descarga noticias cuya fecha de publicación supere el límite establecido en la columna `max_age_days` de la fuente (por defecto `1` día para la mayoría de portales).

### 1.2 Implementación Basada en Unix Timestamps
Durante el parseo de los canales RSS/Atom, la clase `RssService` convierte los datos de fecha de las publicaciones a Unix Timestamps (`get_date('U')`) para evitar desajustes debido a zonas horarias diferentes entre el servidor y el origen:
```php
$maxAgeDays = (int) ($source->max_age_days ?? 1);
$maxAgeThreshold = now()->subDays($maxAgeDays)->timestamp;

// ... en el bucle de lectura de artículos ...
$pubDate = $item->get_date('U');

if ($pubDate && $pubDate < $maxAgeThreshold) {
    Log::info("Skipping RSS item from {$source->name}: older than {$maxAgeDays} day(s).");
    continue;
}
```

---

## 2. Red de Seguridad en Cola (*Fail-Fast*)

### 2.1 Regla de Negocio
Las descargas de feeds se ejecutan de manera asíncrona a través de tareas en cola (`FetchRssFeedJob`). Para evitar que un job en cola procese una fuente que fue desactivada (`is_active = false`) o eliminada por un administrador del panel mientras la tarea esperaba turno en Redis, el Job implementa un chequeo rápido de estado al arrancar.

### 2.2 Implementación en el Job
En `FetchRssFeedJob.php`:
```php
public function handle(RssService $rssService): void
{
    // Cargar la fuente fresca de la base de datos
    $source = \App\Models\Source::find($this->sourceId);

    // Fail-fast: Abortar si la fuente fue eliminada o desactivada
    if (!$source || !$source->is_active) {
        Log::warning("FetchRssFeedJob aborted: Source ID {$this->sourceId} is inactive or does not exist.");
        return;
    }

    $rssService->fetchSource($source);
}
```
Esto reduce la sobrecarga del procesador del servidor y detiene el procesamiento innecesario inmediatamente.
