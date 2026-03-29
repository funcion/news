<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuthorResource\Pages;
use App\Models\Author;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'Administración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                TextInput::make('slug')
                    ->required()
                    ->unique(Author::class, 'slug', ignoreRecord: true),
                Select::make('type')
                    ->options([
                        'human' => 'Humano',
                        'ai' => 'IA',
                    ])
                    ->required()
                    ->default('ai'),
                Select::make('voice_style')
                    ->options([
                        'El Analista' => 'El Analista',
                        'El Divulgador' => 'El Divulgador',
                        'El Cronista' => 'El Cronista',
                        'El Crítico' => 'El Crítico',
                    ])
                    ->required()
                    ->default('El Analista'),
                TextInput::make('avatar_url')
                    ->url()
                    ->columnSpanFull(),
                Textarea::make('bio')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->circular(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'human' => 'success',
                        'ai' => 'info',
                    }),
                TextColumn::make('voice_style'),
                ToggleColumn::make('is_active'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'human' => 'Humano',
                        'ai' => 'IA',
                    ]),
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
            'index' => Pages\ListAuthors::route('/'),
            'create' => Pages\CreateAuthor::route('/create'),
            'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }
}
