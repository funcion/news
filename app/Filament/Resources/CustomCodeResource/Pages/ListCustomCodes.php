<?php

namespace App\Filament\Resources\CustomCodeResource\Pages;

use App\Filament\Resources\CustomCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomCodes extends ListRecords
{
    protected static string $resource = CustomCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
