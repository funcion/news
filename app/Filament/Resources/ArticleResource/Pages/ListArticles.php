<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Models\Article;
use Filament\Actions;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    /**
     * Get a base query with active date & category table filters applied.
     */
    public function getFilteredBaseQuery(): Builder
    {
        $query = Article::query();

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

        // 3. Category filter
        $categoryId = $this->tableFilters['category_id']['value'] ?? null;
        if ($categoryId) {
            $query = $query->where('category_id', $categoryId);
        }

        return $query;
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos')
                ->badge(fn () => (string) $this->getFilteredBaseQuery()->count()),

            'published' => Tab::make('Publicados')
                ->badge(fn () => (string) $this->getFilteredBaseQuery()->where('status', 'published')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'published')),

            'scheduled' => Tab::make('Programados')
                ->badge(fn () => (string) $this->getFilteredBaseQuery()->where('status', 'scheduled')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'scheduled')),

            'draft' => Tab::make('Borradores')
                ->badge(fn () => (string) $this->getFilteredBaseQuery()->where('status', 'draft')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft')),

            'pending_review' => Tab::make('En Revisión')
                ->badge(fn () => (string) $this->getFilteredBaseQuery()->where('status', 'pending_review')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending_review')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getMaxContentWidth(): string
    {
        return 'full';
    }
}