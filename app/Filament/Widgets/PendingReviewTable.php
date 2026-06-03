<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PendingReviewTable extends TableWidget
{
    protected static ?int $sort = 1;
    protected static ?string $heading = '🟡 Artículos Pendientes de Revisión';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Article::query()
                    ->whereIn('status', ['draft', 'pending_review'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Artículo')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('title', 'en'))
                    ->limit(60)
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->formatStateUsing(fn ($record) => $record->category?->getTranslation('name', 'en') ?? '—'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'          => 'gray',
                        'pending_review' => 'warning',
                        default          => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Article $record) {
                        $old = $record->status;
                        $record->update(['status' => 'published', 'published_at' => now()]);
                        \App\Filament\Resources\ArticleResource::sendNotification($record, $old, 'published');
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Article $record) {
                        $old = $record->status;
                        $record->update(['status' => 'rejected']);
                        \App\Filament\Resources\ArticleResource::sendNotification($record, $old, 'rejected');
                    }),
            ])
            ->paginated([5]);
    }
}
