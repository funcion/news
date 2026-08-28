<?php

namespace App\Services\AI;

use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class SiliconFlowImageService
{
    protected string $apiKey = '';
    protected string $model;
    protected string $apiUrl = 'https://api.siliconflow.com/v1/images/generations';

    public function __construct()
    {
        $this->apiKey = config('siliconflow.api_key', '');
        $this->model = config('siliconflow.image_model', 'black-forest-labs/FLUX.1-schnell');

        if (empty($this->apiKey)) {
            Log::warning('SiliconFlow API Key is missing (SILICONFLOW_API_KEY) — image generation will be disabled for this job.');
        }
    }

    public function generateAndSave(string $prompt, string $slug, int $index = 1): ?string
    {
        try {
            Log::info('Requesting image from SiliconFlow (FLUX.1) for slug: ' . $slug . '-' . $index);

            $response = retry(3, function () use ($prompt) {
                return Http::withToken($this->apiKey)
                    ->timeout(75)
                    ->connectTimeout(15)
                    ->post($this->apiUrl, [
                        'model' => $this->model,
                        'prompt' => $prompt,
                        'image_size' => '1280x720',
                        'batch_size' => 1,
                ]);
            }, 2000);

            if ($response->failed()) {
                Log::error('SiliconFlow API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();
            $imageUrl = $data['images'][0]['url'] ?? $data['data'][0]['url'] ?? null;

            if (!$imageUrl) {
                Log::error('SiliconFlow API returned no image URL', ['response' => $data]);
                return null;
            }

            $imageContents = retry(3, function () use ($imageUrl) {
                $res = Http::timeout(45)->connectTimeout(15)->get($imageUrl);
                return $res->successful() ? $res->body() : null;
            }, 2000);

            if (!$imageContents) {
                Log::error('Failed to download generated image from url: ' . $imageUrl);
                return null;
            }

            $filename = "{$slug}-{$index}.webp";
            $img = Image::make($imageContents)
                ->fit(1280, 720, function ($constraint) {
                    $constraint->upsize();
                })
                ->encode('webp', 85);

            $tempPath = storage_path('app/images-tmp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            $absolutePath = $tempPath . '/' . $filename;
            file_put_contents($absolutePath, (string) $img);

            Log::info("Image successfully generated and saved to temp: {$absolutePath}");
            return $absolutePath;

        } catch (\Exception $e) {
            Log::error("SiliconFlowImageService Exception: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function regenerateHeroForArticle(Article $article, ?string $customPrompt = null): bool
    {
        $slugEn = $article->slug_en ?: Str::slug($article->getTranslation('title', 'en') ?: 'article');
        $slugEs = $article->slug_es ?: Str::slug($article->getTranslation('title', 'es') ?: 'articulo');

        $prompt = $customPrompt;
        if (empty($prompt)) {
            $prompt = $article->ai_metadata['image_prompts'][0]['prompt_en'] ?? null;
        }
        if (empty($prompt)) {
            $altEn = $article->getTranslation('image_alt', 'en');
            $titleEn = $article->getTranslation('title', 'en');
            $excerptEn = $article->getTranslation('excerpt', 'en');
            $prompt = 'Editorial photojournalism style, high quality photography: ' . ($altEn ?: ($titleEn . ($excerptEn ? '. ' . $excerptEn : '')));
        }

        $path = $this->generateAndSave($prompt, $slugEn, 1);
        if (!$path || !file_exists($path)) {
            Log::error("regenerateHeroForArticle failed for Article #{$article->id}");
            return false;
        }

        try {
            $altEn = $article->getTranslation('image_alt', 'en') ?: $article->getTranslation('title', 'en');
            $altEs = $article->getTranslation('image_alt', 'es') ?: $article->getTranslation('title', 'es');
            $titleEn = Str::limit($article->getTranslation('title', 'en') ?? 'Hero', 70);
            $titleEs = Str::limit($article->getTranslation('title', 'es') ?? 'Hero', 70);
            $captionEn = $article->ai_metadata['image_prompts'][0]['caption_en'] ?? $altEn;
            $captionEs = $article->ai_metadata['image_prompts'][0]['caption_es'] ?? $altEs;

            // 1. Delete all existing hero media (-1.webp or placeholders)
            foreach ($article->getMedia('images_en') as $media) {
                if (str_contains($media->file_name, '-1.webp') || str_contains($media->file_name, 'placeholder')) {
                    $media->delete();
                }
            }
            foreach ($article->getMedia('images_es') as $media) {
                if (str_contains($media->file_name, '-1.webp') || str_contains($media->file_name, 'placeholder')) {
                    $media->delete();
                }
            }

            // 2. Attach new hero media
            $fileNameEn = "{$slugEn}-1.webp";
            $mediaEn = $article->addMedia($path)
                ->usingFileName($fileNameEn)
                ->usingName($titleEn)
                ->withCustomProperties([
                    'lang'    => 'en',
                    'alt'     => $altEn,
                    'title'   => $titleEn,
                    'caption' => $captionEn,
                ])
                ->preservingOriginal()
                ->toMediaCollection('images_en');

            $fileNameEs = "{$slugEs}-1.webp";
            $mediaEs = $article->addMedia($path)
                ->usingFileName($fileNameEs)
                ->usingName($titleEs)
                ->withCustomProperties([
                    'lang'    => 'es',
                    'alt'     => $altEs,
                    'title'   => $titleEs,
                    'caption' => $captionEs,
                ])
                ->toMediaCollection('images_es');

            // 3. Ensure hero order_column is 1
            $mediaEn ->order_column = 1;
            $mediaEn->save();

            $mediaEs->order_column = 1;
            $mediaEs->save();

            // 4. Update article image_url directly
            $article->image_url = $mediaEn->getUrl('large') ?: $mediaEn->getUrl();
            $article->save();
            $article->unsetRelation('media');

            if (file_exists($path)) {
                @unlink($path);
            }

            Log::info("Successfully regenerated hero image for Article #{$article->id} ({$article->image_url})");
            return true;
        } catch (\Exception $e) {
            Log::error("Error attaching regenerated media to Article #{$article->id}: " . $e->getMessage());
            if (file_exists($path)) {
                @unlink($path);
            }
            return false;
        }
    }
}
