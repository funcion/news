<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Before filling the form, do nothing extra — 
     * afterStateHydrated callbacks in the resource handle populating virtual fields.
     */

    /**
     * Before saving, map virtual EN/ES fields to their translatable counterparts.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        // Save translatable fields via Spatie's setTranslation
        foreach ([
            'title'            => ['en' => $data['title_en'] ?? null, 'es' => $data['title_es'] ?? null],
            'excerpt'          => ['en' => $data['excerpt_en'] ?? null, 'es' => $data['excerpt_es'] ?? null],
            'content'          => ['en' => $data['content_en'] ?? null, 'es' => $data['content_es'] ?? null],
            'meta_title'       => ['en' => $data['meta_title_en'] ?? null, 'es' => $data['meta_title_es'] ?? null],
            'meta_description' => ['en' => $data['meta_description_en'] ?? null, 'es' => $data['meta_description_es'] ?? null],
        ] as $field => $translations) {
            foreach ($translations as $locale => $value) {
                if ($value !== null) {
                    $record->setTranslation($field, $locale, $value);
                }
            }
        }

        $record->save();

        // Remove virtual keys from $data to avoid Filament trying to set them directly
        foreach (['title_en', 'title_es', 'slug_en', 'slug_es', 'excerpt_en', 'excerpt_es',
                  'content_en', 'content_es', 'meta_title_en', 'meta_title_es',
                  'meta_description_en', 'meta_description_es'] as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
