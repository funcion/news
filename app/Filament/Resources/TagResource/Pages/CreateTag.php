<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name']        = ['en' => $data['name_en'] ?? '', 'es' => $data['name_es'] ?? ''];
        $data['description'] = ['en' => $data['description_en'] ?? '', 'es' => $data['description_es'] ?? ''];
        $data['slug']        = $data['slug'] ?? Str::slug($data['name_en'] ?? '');

        foreach (['name_en', 'name_es', 'description_en', 'description_es'] as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
