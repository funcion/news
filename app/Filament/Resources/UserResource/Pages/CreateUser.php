<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Map virtual bilingual fields to Spatie HasTranslations JSON structure before creating.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = [
            'en' => $data['name_en'] ?? '',
            'es' => $data['name_es'] ?? '',
        ];

        $data['bio'] = [
            'en' => $data['bio_en'] ?? '',
            'es' => $data['bio_es'] ?? '',
        ];

        // Remove virtual fields
        unset($data['name_en'], $data['name_es'], $data['bio_en'], $data['bio_es']);

        return $data;
    }
}
