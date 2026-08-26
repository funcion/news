<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Audience';

    protected static ?string $navigationLabel = 'Comments & Moderation';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Comment Details')
                    ->schema([
                        TextInput::make('user_name')
                            ->label('Author')
                            ->formatStateUsing(fn ($record) => $record?->user?->name)
                            ->disabled(),
                        TextInput::make('article_title')
                            ->label('Article')
                            ->formatStateUsing(fn ($record) => $record?->article?->title)
                            ->disabled(),
                        Select::make('status')
                            ->label('Moderation Status')
                            ->options([
                                'approved' => 'Approved (Visible)',
                                'pending'  => 'Pending Moderation',
                                'spam'     => 'Marked as Spam',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                        TextInput::make('likes_count')
                            ->label('Likes Count')
                            ->numeric()
                            ->disabled(),
                        Textarea::make('content')
                            ->label('Comment Content')
                            ->rows(5)
                            ->columnSpanFull()
                            ->required(),
                        TextInput::make('ip_address')
                            ->label('IP Address')
                            ->disabled(),
                        TextInput::make('created_at')
                            ->label('Posted At')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('article.title')
                    ->label('Article')
                    ->limit(35)
                    ->searchable()
                    ->tooltip(fn ($record) => $record->article?->title),
                TextColumn::make('content')
                    ->label('Comment')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending'  => 'warning',
                        'spam'     => 'danger',
                        'rejected' => 'gray',
                        default    => 'gray',
                    }),
                TextColumn::make('likes_count')
                    ->label('Likes')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Posted')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'approved' => 'Approved',
                        'pending'  => 'Pending',
                        'spam'     => 'Spam',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                \Filament\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'approved')
                    ->action(fn ($record) => $record->update(['status' => 'approved'])),
                \Filament\Actions\Action::make('mark_spam')
                    ->label('Spam')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== 'spam')
                    ->action(fn ($record) => $record->update(['status' => 'spam'])),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'approved'])),
                    \Filament\Actions\BulkAction::make('bulk_spam')
                        ->label('Mark as Spam')
                        ->icon('heroicon-o-shield-exclamation')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'spam'])),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
        ];
    }
}