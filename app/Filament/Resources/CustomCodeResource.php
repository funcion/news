<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomCodeResource\Pages;
use App\Models\CustomCode;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomCodeResource extends Resource
{
    protected static ?string $model = CustomCode::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-code-bracket';

    protected static ?string $navigationLabel = 'Código Personalizado';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Select::make('location')
                    ->label('Ubicación')
                    ->options([
                        'header_head' => 'Header - dentro de <head> (GA, GTM, meta tags)',
                        'header_body' => 'Header - después de <body> (GTM noscript, JS)',
                        'footer'      => 'Footer - antes de </body> (analytics, scripts)',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->label('Descripción')
                    ->maxLength(255)
                    ->columnStart(1),
                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
                Forms\Components\Textarea::make('content')
                    ->label('Código')
                    ->rows(12)
                    ->helperText('Pega aquí el código JS, CSS o HTML que se inyectará en la ubicación seleccionada.')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('location')
                    ->label('Ubicación')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'header_head' => 'Header <head>',
                        'header_body' => 'Header <body>',
                        'footer'      => 'Footer </body>',
                        default       => $state,
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('location')
                    ->options([
                        'header_head' => 'Header <head>',
                        'header_body' => 'Header <body>',
                        'footer'      => 'Footer </body>',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomCodes::route('/'),
            'create' => Pages\CreateCustomCode::route('/create'),
            'edit'   => Pages\EditCustomCode::route('/{record}/edit'),
        ];
    }
}