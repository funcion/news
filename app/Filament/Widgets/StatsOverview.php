<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\RawArticle;
use App\Models\Source;
use App\Models\Tag;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $published = Article::where('status', 'published')->count();
        $pending   = Article::where('status', 'pending_review')->count();
        $rejected  = Article::where('status', 'rejected')->count();
        $today     = Article::where('status', 'published')->whereDate('published_at', today())->count();

        return [
            Stat::make('Artículos Publicados', $published)
                ->description('Total en el sitio')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 12, 8, 15, 20, $published]),

            Stat::make('Pendientes de Revisión', $pending)
                ->description($pending > 0 ? 'Requieren atención' : 'Todo al día')
                ->descriptionIcon($pending > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($pending > 0 ? 'warning' : 'success'),

            Stat::make('Publicados Hoy', $today)
                ->description('Artículos de hoy')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Rechazados', $rejected)
                ->description('Total rechazados')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Fuentes RSS', Source::where('is_active', true)->count())
                ->description(Source::count() . ' totales')
                ->descriptionIcon('heroicon-m-rss')
                ->color('primary'),

            Stat::make('Tags', Tag::count())
                ->description('Tags únicos')
                ->descriptionIcon('heroicon-m-tag')
                ->color('gray'),
        ];
    }
}
