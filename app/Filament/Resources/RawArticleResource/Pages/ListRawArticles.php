<?php

namespace App\Filament\Resources\RawArticleResource\Pages;

use App\Filament\Resources\RawArticleResource;
use App\Models\RawArticle;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListRawArticles extends ListRecords
{
    protected static string $resource = RawArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cancel_all_pending')
                ->label('Cancelar Todo en Cola')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('¿Cancelar todas las generaciones de noticias en cola?')
                ->modalDescription('Esta acción limpiará la cola de trabajos y marcará todas las noticias crudas pendientes como "Ignoradas" para detener su procesamiento con IA.')
                ->modalSubmitActionLabel('Sí, cancelar todo')
                ->action(function () {
                    $jobsCount = DB::table('jobs')->delete();
                    $failedCount = DB::table('failed_jobs')->delete();
                    $rawCount = RawArticle::whereIn('status', ['pending', 'processing'])
                        ->update(['status' => 'ignored']);

                    Notification::make()
                        ->title('Cola cancelada con éxito')
                        ->body("Se limpiaron {$jobsCount} trabajos en cola y se ignoraron {$rawCount} noticias pendientes.")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }

    public function getMaxContentWidth(): string
    {
        return 'full';
    }
}