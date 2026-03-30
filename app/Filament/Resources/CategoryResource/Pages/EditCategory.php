<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

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
            'name'        => ['en' => $data['name_en'] ?? null, 'es' => $data['name_es'] ?? null],
            'description' => ['en' => $data['description_en'] ?? null, 'es' => $data['description_es'] ?? null],
        ] as $field => $translations) {
            foreach ($translations as $locale => $value) {
                if ($value !== null) {
                    $record->setTranslation($field, $locale, $value);
                }
            }
        }

        $record->save();

        foreach (['name_en', 'name_es', 'slug_en', 'slug_es', 'description_en', 'description_es'] as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
