<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SiliconFlow Image Generation Configuration (STRICTLY IMAGES ONLY)
    |--------------------------------------------------------------------------
    |
    | REGLA ARQUITECTÓNICA ESTRICTA:
    | La API de SiliconFlow se utiliza 100% EXCLUSIVAMENTE para la generación
    | de imágenes fotorrealistas con FLUX.1 (black-forest-labs/FLUX.1-schnell).
    |
    | Queda ESTRICTAMENTE PROHIBIDO utilizar SiliconFlow para modelos de texto,
    | chat, o embeddings. Todos los modelos de texto deben cursar por OpenRouter.
    |
    */

    'api_key' => env('SILICONFLOW_API_KEY', ''),

    'image_model' => env('SILICONFLOW_IMAGE_MODEL', 'black-forest-labs/FLUX.1-schnell'),

];