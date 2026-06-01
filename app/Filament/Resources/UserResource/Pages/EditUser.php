<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Map virtual bilingual fields to Spatie HasTranslations JSON structure before saving.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        foreach ([
            'name' => ['en' => $data['name_en'] ?? null, 'es' => $data['name_es'] ?? null],
            'bio'  => ['en' => $data['bio_en'] ?? null, 'es' => $data['bio_es'] ?? null],
        ] as $field => $translations) {
            foreach ($translations as $locale => $value) {
                if ($value !== null) {
                    $record->setTranslation($field, $locale, $value);
                }
            }
        }

        $record->save();

        // Remove virtual fields from $data to prevent Filament from trying to set them directly
        unset($data['name_en'], $data['name_es'], $data['bio_en'], $data['bio_es']);

        return $data;
    }
}
