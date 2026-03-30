<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class SiliconFlowImageService
{
    protected string $apiKey;
    protected string $model;
    protected string $apiUrl = 'https://api.siliconflow.com/v1/images/generations';

    public function __construct()
    {
        $this->apiKey = env('SILICONFLOW_API_KEY');
        $this->model = env('SILICONFLOW_IMAGE_MODEL', 'black-forest-labs/FLUX.1-schnell');

        if (empty($this->apiKey)) {
            Log::error("SiliconFlow API Key is missing in .env (SILICONFLOW_API_KEY)");
        }
    }

    /**
     * Generates an image based on a prompt, saves it as WebP and returns the path.
     * 
     * @param string $prompt The visual prompt for FLUX.
     * @param string $slug The article short slug to base the filename on.
     * @param int $index Index number for gallery images (e.g., 1, 2, 3)
     * @return string|null Path to the saved image or null on failure.
     */
    public function generateAndSave(string $prompt, string $slug, int $index = 1): ?string
    {
        try {
            Log::info("Requesting image from SiliconFlow (FLUX.1) for slug: {$slug}-{$index}");

            $response = Http::withToken($this->apiKey)
                ->timeout(60) // Image generation might take a bit
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'prompt' => $prompt,
                    'image_size' => '1280x720',
                    'batch_size' => 1,
                    // 'num_inference_steps' => 4, // schnell usually requires 4 steps, but API defaults are fine
                ]);

            if ($response->failed()) {
                Log::error("SiliconFlow API Error", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();
            $imageUrl = $data['images'][0]['url'] ?? null;

            if (!$imageUrl) {
                // Sometimes OpenAI format is used: $data['data'][0]['url']
                $imageUrl = $data['data'][0]['url'] ?? null;
            }

            if (!$imageUrl) {
                Log::error("SiliconFlow API returned no image URL", ['response' => $data]);
                return null;
            }

            // Download the image
            $imageContents = Http::timeout(30)->get($imageUrl)->body();

            if (!$imageContents) {
                Log::error("Failed to download generated image from url: {$imageUrl}");
                return null;
            }

            // Process image with Intervention
            // Ensure exact 1280x720 and convert to WebP
            $filename = "{$slug}-{$index}.webp";
            
            $img = Image::make($imageContents)
                        ->fit(1280, 720, function ($constraint) {
                            $constraint->upsize();
                        })
                        ->encode('webp', 85);

            // Save to temp storage absolute path
            $tempPath = storage_path('app/images-tmp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            $absolutePath = $tempPath . '/' . $filename;
            
            file_put_contents($absolutePath, (string) $img);

            Log::info("Image successfully generated and saved to temp: {$absolutePath}");

            // Return absolute path for Spatie Media Library
            return $absolutePath;

        } catch (\Exception $e) {
            Log::error("SiliconFlowImageService Exception: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}
