# 🤖 Arquitectura de Enrutamiento Dinámico & Pool de Modelos de IA (Glodaxia)

Este documento describe la arquitectura de **Rotación Dinámica, Tolerancia a Fallos (*Smart Failover*) y Control de Tokens** implementada en el motor editorial de **Glodaxia**.

---

## 🌟 1. Objetivos del Sistema

1. **Riqueza y Variedad Editorial**: Evita la monotonía estilística rotando dinámicamente entre distintos modelos de lenguaje líderes del sector.
2. **Alta Disponibilidad 99.99% (*Zero-Downtime Failover*)**: Si un proveedor o modelo en OpenRouter experimenta sobrecarga o caída, el sistema salta automáticamente al siguiente modelo de respaldo en milisegundos sin interrumpir la publicación.
3. **Eficiencia y Blindaje de Presupuesto**: Control estricto de `max_tokens = 10000`, impidiendo que OpenRouter reserve búferes de saldo inflados (65k+ tokens).
4. **100% Modular y Configurable**: Puedes añadir o quitar modelos en cualquier momento desde el archivo `.env` o `config/ai_models.php` sin tocar código.

---

## 🏆 2. Modelos Activos en el Pool por Defecto

| Modelo en OpenRouter | Especialidad Editorial | Calidad de Prosa |
|---|---|:---:|
| **`deepseek/deepseek-chat`** *(DeepSeek V3)* | Prosa periodística humana, riqueza de vocabulario bilingüe, cero muletillas de bot. | ⭐⭐⭐⭐⭐ (9.9/10) |
| **`qwen/qwen3.7-flash`** *(Alibaba Cloud)* | Precisión técnica impecable, código, ciberseguridad, frameworks y estructura Markdown/JSON. | ⭐⭐⭐⭐⭐ (9.6/10) |
| **`google/gemini-2.5-flash`** *(Google DeepMind)* | Velocidad instantánea, noticias de última hora y síntesis ágil. | ⭐⭐⭐⭐☆ (9.1/10) |
| **`minimax/minimax-m3`** *(MiniMax)* | Narrativa literaria, redacción creativa y tono periodístico envolvente. | ⭐⭐⭐⭐⭐ (9.5/10) |

---

## ⚙️ 3. Lógica de Funcionamiento (Paso a Paso)

```
[ Ingesta RSS: Noticia Cruda ]
             │
             ▼
[ ModelRouterService::completeWithFailover() ]
             │
             ├─► 1. Selecciona un modelo al azar del Pool (ej. DeepSeek V3)
             ├─► 2. Envía la solicitud con max_tokens: 10000 y temperature: 0.7
             │
      ¿Éxito? ──► SÍ ──► Guarda artículo bilingüe (EN/ES) y registra modelo en ai_metadata.
             │
             ▼ NO (Timeout / Error 429 / Error 500)
   [ Failover Automático ]
             │
             ├─► Captura el error de inmediato en 200ms
             ├─► Selecciona el siguiente modelo disponible (ej. Qwen 3.7 Flash)
             ├─► Reintenta transparentemente hasta completar la redacción
```

---

## 🛠️ 4. Cómo Agregar o Modificar Modelos Manualmente

Puedes gestionar el pool de modelos de dos formas:

### Opción A: Desde `.env` (Recomendada para Producción)
Edita la variable `AI_MODELS_POOL` en tu archivo `.env` separando los slugs de OpenRouter con comas:

```env
# Pool de modelos activos para rotación y failover
AI_MODELS_POOL=deepseek/deepseek-chat,qwen/qwen3.7-flash,google/gemini-2.5-flash,minimax/minimax-m3

# Límite máximo de tokens de salida (Espacio holgado para artículos de 6,000+ caracteres)
AI_MAX_TOKENS=10000

# Límite para la clasificación previa rápida
AI_CLASSIFICATION_MAX_TOKENS=1500
```

### Opción B: En `config/ai_models.php`
Puedes editar directamente el array del archivo de configuración:

```php
return [
    'pool' => [
        'deepseek/deepseek-chat',
        'qwen/qwen3.7-flash',
        'google/gemini-2.5-flash',
        'minimax/minimax-m3',
        // 'anthropic/claude-3.5-haiku', // <-- Ejemplo: Puedes añadir nuevos modelos aquí
    ],
    'max_tokens' => 10000,
    'temperature' => 0.7,
];
```

---

## 📊 5. Trazabilidad Editorial en Base de Datos & Filament

Cada artículo generado guarda el modelo exacto que completó su redacción en el campo `ai_metadata`:

```json
{
  "origin_url": "https://feeds.arstechnica.com/...",
  "model_used": "deepseek/deepseek-chat",
  "today_date": "Monday, August 24, 2026",
  "temperature": 0.7
}
```

Esto permite al equipo editorial auditar en todo momento qué IA generó cada publicación desde el panel de administración de Filament.