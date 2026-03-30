<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name']        = ['en' => $data['name_en'] ?? '', 'es' => $data['name_es'] ?? ''];
        $data['description'] = ['en' => $data['description_en'] ?? '', 'es' => $data['description_es'] ?? ''];
        $data['slug_en']     = $data['slug_en'] ?? Str::slug($data['name_en'] ?? '');
        $data['slug_es']     = $data['slug_es'] ?? Str::slug($data['name_es'] ?? $data['name_en'] ?? '');

        foreach (['name_en', 'name_es', 'description_en', 'description_es'] as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
