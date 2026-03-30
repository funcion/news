<?php

namespace App\Filament\Resources\RawArticleResource\Pages;

use App\Filament\Resources\RawArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRawArticles extends ListRecords
{
    protected static string $resource = RawArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
