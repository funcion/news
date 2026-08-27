<?php

namespace App\Filament\Resources\RawArticleResource\Pages;

use App\Filament\Resources\RawArticleResource;
use App\Models\RawArticle;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\RawArticlesGlossaryWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListRawArticles extends ListRecords
{
    protected static string $resource = RawArticleResource::class;

    /**
     * Get a base query with active date & model table filters applied.
     */
    public function getFilteredBaseQuery(): Builder
    {
        $query = RawArticle::query();

        // 1. Period filter (Hoy, Ayer, 1 semana, 1 mes)
        $periodo = $this->tableFilters['periodo']['value'] ?? null;
        if ($periodo) {
            $query = match ($periodo) {
                'today'      => $query->where(fn ($q) => $q->whereDate('published_at', now()->today())->orWhere(fn($sq)=>$sq->whereNull('published_at')->whereDate('created_at', now()->today()))),
                'yesterday'  => $query->where(fn ($q) => $q->whereDate('published_at', now()->yesterday())->orWhere(fn($sq)=>$sq->whereNull('published_at')->whereDate('created_at', now()->yesterday()))),
                'this_week'  => $query->where(fn ($q) => $q->where('published_at', '>=', now()->subDays(7))->orWhere(fn($sq)=>$sq->whereNull('published_at')->where('created_at', '>=', now()->subDays(7)))),
                'this_month' => $query->where(fn ($q) => $q->where('published_at', '>=', now()->subDays(30))->orWhere(fn($sq)=>$sq->whereNull('published_at')->where('created_at', '>=', now()->subDays(30)))),
                default      => $query,
            };
        }

        // 2. Custom date range filter (Desde / Hasta)
        $desde = $this->tableFilters['rango_personalizado']['desde'] ?? null;
        $hasta = $this->tableFilters['rango_personalizado']['hasta'] ?? null;
        if ($desde) {
            $query = $query->where(fn ($sq) => $sq->whereDate('published_at', '>=', $desde)->orWhere(fn($ssq) => $ssq->whereNull('published_at')->whereDate('created_at', '>=', $desde)));
        }
        if ($hasta) {
            $query = $query->where(fn ($sq) => $sq->whereDate('published_at', '<=', $hasta)->orWhere(fn($ssq) => $ssq->whereNull('published_at')->whereDate('created_at', '<=', $hasta)));
        }

        // 3. AI Model filter
        $aiModel = $this->tableFilters['ai_model']['value'] ?? null;
        if ($aiModel) {
            $query = $query->where('ai_model', $aiModel);
        }

        return $query;
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas')
                ->badge(fn () => (string) $this->getFilteredBaseQuery()->count()),

            'pending' => Tab::make('Pendientes')
                ->badge(fn () => (string) $this->getFilteredBaseQuery()->where('status', 'pending')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

            'processing' => Tab::make('Procesando')
                ->badge(fn () => (string) $this->getFilteredBaseQuery()->where('status', 'processing')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processing')),

            'processed' => Tab::make('Procesadas')
                ->badge(fn () => (string) $this->getFilteredBaseQuery()->where('status', 'processed')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processed')),

            'failed' => Tab::make('Fallidas')
                ->badge(fn () => (string) $this->getFilteredBaseQuery()->where('status', 'failed')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'failed')),

            'ignored' => Tab::make('Ignoradas')
                ->badge(fn () => (string) $this->getFilteredBaseQuery()->where('status', 'ignored')->count())
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

    protected function getFooterWidgets(): array
    {
        return [
            RawArticlesGlossaryWidget::class,
        ];
    }

    public function getMaxContentWidth(): string
    {
        return 'full';
    }
}