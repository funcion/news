<?php

namespace App\Filament\Resources\RawArticleResource\Pages;

use App\Filament\Resources\RawArticleResource;
use App\Models\RawArticle;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListRawArticles extends ListRecords
{
    protected static string $resource = RawArticleResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas')
                ->badge(RawArticle::count()),
            'pending' => Tab::make('Pendientes')
                ->badge(RawArticle::where('status', 'pending')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
            'processing' => Tab::make('Procesando')
                ->badge(RawArticle::where('status', 'processing')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processing')),
            'processed' => Tab::make('Procesadas')
                ->badge(RawArticle::where('status', 'processed')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processed')),
            'failed' => Tab::make('Fallidas')
                ->badge(RawArticle::where('status', 'failed')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'failed')),
            'ignored' => Tab::make('Ignoradas')
                ->badge(RawArticle::where('status', 'ignored')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'ignored')),
        ];
    }

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