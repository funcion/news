<?php

namespace App\Filament\Resources\CustomCodeResource\Pages;

use App\Filament\Resources\CustomCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomCode extends EditRecord
{
    protected static string $resource = CustomCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}