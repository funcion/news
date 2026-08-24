<?php

namespace App\Filament\Resources\SourceResource\Pages;

use App\Filament\Resources\SourceResource;
use App\Filament\Widgets\SourcesGlossaryWidget;
use App\Models\Setting;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSources extends ListRecords
{
    protected static string $resource = SourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('toggleIngestion')
                ->label(fn () => Setting::get('ingestion_enabled', true) 
                    ? '🟢 Ingesta: ACTIVA (Pausar)' 
                    : '⏸️ Ingesta: PAUSADA (Reanudar)'
                )
                ->color(fn () => Setting::get('ingestion_enabled', true) ? 'success' : 'danger')
                ->icon(fn () => Setting::get('ingestion_enabled', true) ? 'heroicon-o-pause-circle' : 'heroicon-o-play-circle')
                ->requiresConfirmation()
                ->modalHeading(fn () => Setting::get('ingestion_enabled', true) 
                    ? '¿Pausar toda la Ingesta de Noticias?' 
                    : '¿Reanudar la Ingesta de Noticias?'
                )
                ->modalDescription(fn () => Setting::get('ingestion_enabled', true) 
                    ? 'El programador automático (Cron) dejará de consultar los feeds RSS y no se consumirán llamadas a la IA hasta que la reanudes.'
                    : 'El programador automático (Cron) volverá a consultar los feeds RSS activos y la IA comenzará a procesar noticias de nuevo.'
                )
                ->modalSubmitActionLabel(fn () => Setting::get('ingestion_enabled', true) ? 'Sí, Pausar Ingesta' : 'Sí, Reanudar Ingesta')
                ->action(function () {
                    $currentState = Setting::get('ingestion_enabled', true);
                    $newState = !$currentState;
                    Setting::set('ingestion_enabled', $newState, 'boolean', 'ingestion');
                    
                    Notification::make()
                        ->title($newState ? '✅ Ingesta de Noticias Reanudada' : '⏸️ Ingesta de Noticias Pausada')
                        ->body($newState 
                            ? 'El sistema ha reanudado la lectura automática de feeds RSS.' 
                            : 'El sistema ha pausado todas las consultas automáticas a los feeds RSS.'
                        )
                        ->color($newState ? 'success' : 'warning')
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            SourcesGlossaryWidget::class,
        ];
    }

    public function getMaxContentWidth(): string
    {
        return 'full';
    }
}