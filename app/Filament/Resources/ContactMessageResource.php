<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Audience';

    protected static ?string $navigationLabel = 'Contact Inquiries';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Inquiry Details')
                    ->schema([
                        TextInput::make('name')
                            ->disabled(),
                        TextInput::make('email')
                            ->email()
                            ->disabled(),
                        TextInput::make('subject')
                            ->disabled(),
                        TextInput::make('locale')
                            ->disabled(),
                        Textarea::make('message')
                            ->rows(6)
                            ->disabled()
                            ->columnSpanFull(),
                        TextInput::make('ip_address')
                            ->disabled(),
                        Toggle::make('is_read')
                            ->label('Mark as Read'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Sender')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(35),
                TextColumn::make('locale')
                    ->label('Lang')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'es' => 'warning',
                        'en' => 'info',
                        default => 'gray',
                    }),
                IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Received At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_read')
                    ->label('Status')
                    ->placeholder('All Messages')
                    ->trueLabel('Read Only')
                    ->falseLabel('Unread Only'),
                SelectFilter::make('locale')
                    ->options([
                        'en' => 'English',
                        'es' => 'Español',
                    ]),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view'  => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}