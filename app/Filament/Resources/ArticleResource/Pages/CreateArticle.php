<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Models\Article;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    /**
     * Before creating, map virtual EN/ES fields to proper translatable JSON.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Build translatable JSON values for Spatie HasTranslations
        $data['title']            = ['en' => $data['title_en'] ?? '', 'es' => $data['title_es'] ?? ''];
        $data['excerpt']          = ['en' => $data['excerpt_en'] ?? '', 'es' => $data['excerpt_es'] ?? ''];
        $data['content']          = ['en' => $data['content_en'] ?? '', 'es' => $data['content_es'] ?? ''];
        $data['meta_title']       = ['en' => $data['meta_title_en'] ?? '', 'es' => $data['meta_title_es'] ?? ''];
        $data['meta_description'] = ['en' => $data['meta_description_en'] ?? '', 'es' => $data['meta_description_es'] ?? ''];

        // Slugs
        $data['slug_en'] = $data['slug_en'] ?? Str::slug($data['title_en'] ?? '');
        $data['slug_es'] = $data['slug_es'] ?? Str::slug($data['title_es'] ?? $data['title_en'] ?? '');

        // Remove virtual keys
        foreach (['title_en', 'title_es', 'excerpt_en', 'excerpt_es',
                  'content_en', 'content_es', 'meta_title_en', 'meta_title_es',
                  'meta_description_en', 'meta_description_es'] as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
