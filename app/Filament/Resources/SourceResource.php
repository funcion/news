<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SourceResource\Pages;
use App\Models\Source;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SourceResource extends Resource
{
    protected static ?string $model = Source::class;

    protected static ?string $navigationIcon = 'heroicon-o-rss';
    
    protected static ?string $navigationGroup = 'Ingesta';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('url')
                    ->label('Feed URL')
                    ->required()
                    ->url(),
                Select::make('type')
                    ->options([
                        'rss' => 'RSS',
                        'atom' => 'Atom',
                        'json' => 'JSON Feed',
                        'scraping' => 'Scraping',
                    ])
                    ->required()
                    ->default('rss'),
                TextInput::make('category')
                    ->helperText('Categoría principal de la fuente'),
                TextInput::make('frequency')
                    ->label('Frecuencia (minutos)')
                    ->numeric()
                    ->default(60)
                    ->required(),
                TextInput::make('score')
                    ->numeric()
                    ->default(100)
                    ->disabled(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('url')
                    ->limit(30),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('frequency')
                    ->label('Freq (min)')
                    ->numeric(),
                TextColumn::make('score')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_active'),
                TextColumn::make('last_fetched_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSources::route('/'),
            'create' => Pages\CreateSource::route('/create'),
            'edit' => Pages\EditSource::route('/{record}/edit'),
        ];
    }
}
