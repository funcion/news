<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTag extends EditRecord
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        foreach ([
            'name'             => ['en' => $data['name_en'] ?? null, 'es' => $data['name_es'] ?? null],
            'description'      => ['en' => $data['description_en'] ?? null, 'es' => $data['description_es'] ?? null],
            'meta_title'       => ['en' => $data['meta_title_en'] ?? null, 'es' => $data['meta_title_es'] ?? null],
            'meta_description' => ['en' => $data['meta_description_en'] ?? null, 'es' => $data['meta_description_es'] ?? null],
        ] as $field => $translations) {
            foreach ($translations as $locale => $value) {
                if ($value !== null) {
                    $record->setTranslation($field, $locale, $value);
                }
            }
        }

        if (!empty($data['slug_en'])) {
            $record->slug_en = $data['slug_en'];
            $record->slug    = $data['slug_en'];
        }
        if (!empty($data['slug_es'])) {
            $record->slug_es = $data['slug_es'];
        }

        $record->save();

        foreach ([
            'name_en', 'name_es', 'description_en', 'description_es',
            'meta_title_en', 'meta_title_es', 'meta_description_en', 'meta_description_es'
        ] as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}