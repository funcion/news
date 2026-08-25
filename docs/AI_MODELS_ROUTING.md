# 🤖 Rotación y Failover Automático de Modelos de IA (OpenRouter)

Este documento detalla la arquitectura modular y la configuración de modelos de Inteligencia Artificial utilizados en la plataforma para la redacción bilingüe de artículos, clasificación semántica y asignación de etiquetas.

---

## 🎯 Arquitectura: Fuente Única de Verdad (`config/ai_models.php`)

Todo el flujo de IA se centraliza y controla exclusivamente desde [`config/ai_models.php`](../config/ai_models.php). Cero cadenas fijas o modelos hardcodeados en servicios o controladores.

### 🌟 Pool Oficial de Modelos Activos:

| Modelo | Proveedor | Costo Input / 1M | Costo Output / 1M | Propósito Principal |
|---|---|---|---|---|
| **`deepseek/deepseek-v4-flash-0731`** | DeepSeek | **$0.035 USD** | **$0.010 USD** | **Modelo Principal**: Máxima velocidad, economía extrema y redacción técnica impecable. |
| **`qwen/qwen3.7-flash`** | Alibaba Qwen | **$0.030 USD** | **$0.130 USD** | **Primer Respaldo**: Alta precisión analítica, diversidad léxica y razonamiento sintáctico. |
| **`deepseek/deepseek-chat`** (V3) | DeepSeek | **$0.250 USD** | **$1.290 USD** | **Segundo Respaldo**: Modelo editorial profundo con máxima solidez argumental. |

---

## ⚙️ Configuración en `.env`

Puedes ajustar el modelo principal y el orden del pool directamente en tu archivo `.env`:

```env
# Modelo principal por defecto
AI_DEFAULT_MODEL=deepseek/deepseek-v4-flash-0731

# Cadena de failover automático en orden de prioridad
AI_MODELS_POOL=deepseek/deepseek-v4-flash-0731,qwen/qwen3.7-flash,deepseek/deepseek-chat

# Límites de tokens seguros (evita sobrecostos)
AI_MAX_TOKENS=10000
AI_CLASSIFICATION_MAX_TOKENS=1500
AI_TAG_MAX_TOKENS=500
AI_TEMPERATURE=0.7
AI_TIMEOUT=180
```

---

## 🔄 Flujo de Failover Automático (`ModelRouterService`)

Cuando entra una noticia para procesamiento (`ProcessArticleWithAIJob`):

```
                       ┌─────────────────────────┐
                       │  Nueva Noticia Cruda    │
                       └────────────┬────────────┘
                                    │
                                    ▼
                 ┌──────────────────────────────────────┐
                 │ 1. Intento: DeepSeek V4 Flash        │
                 │    (Precio ultra-bajo: $0.035/$0.01) │
                 └──────────────────┬───────────────────┘
                                    │
                     ¿Éxito? ───────┴─────── No (Timeout/Error)
                        │                               │
                       Sí                               ▼
                        │                ┌──────────────────────────────────────┐
                        │                │ 2. Failover: Qwen 3.7 Flash          │
                        │                │    (Precio económico: $0.030/$0.13)  │
                        │                └──────────────────┬───────────────────┘
                        │                                   │
                        │                    ¿Éxito? ───────┴─────── No
                        │                       │                     │
                        │                      Sí                     ▼
                        │                       │      ┌───────────────────────────────┐
                        │                       │      │ 3. Failover: DeepSeek V3      │
                        │                       │      └──────────────┬────────────────┘
                        │                       │                     │
                        ▼                       ▼                     ▼
             ┌─────────────────────────────────────────────────────────────┐
             │       Artículo Bilingüe Creado y Guardado en DB             │
             │   (Se registra el modelo exacto utilizado en ai_metadata)   │
             └─────────────────────────────────────────────────────────────┘
```

---

## 📊 Medición Real de Consumo y Costos:
* **Tokens por Noticia Bilingüe Completa (ES + EN)**: ~6,970 tokens.
* **Costo por Noticia con DeepSeek V4 Flash**: **~$0.000139 USD**.
* **Rendimiento**: Más de **7,000 artículos completos por cada $1 USD**.
