<?php

namespace App\Filament\Resources\RawArticleResource\Pages;

use App\Filament\Resources\RawArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRawArticle extends EditRecord
{
    protected static string $resource = RawArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
