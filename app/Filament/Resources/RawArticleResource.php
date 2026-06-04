<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RawArticleResource\Pages;
use App\Models\RawArticle;
use App\Jobs\ProcessArticleWithAIJob;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class RawArticleResource extends Resource
{
    protected static ?string $model = RawArticle::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-magnifying-glass';
    
    protected static ?string $navigationLabel = 'Noticias Crudas';
    
    protected static ?string $pluralLabel = 'Noticias Crudas';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión de Contenido';

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Laboratorio de Ideas y Entradas')
                    ->description('Aquí llegan las noticias automáticas del RSS o puedes "Sembrar una Idea" para que la IA la procese.')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Select::make('source_id')
                            ->relationship('source', 'name')
                            ->label('Fuente / Origen')
                            ->helperText('Si es una idea manual, puedes dejarlo en blanco o elegir una fuente general.')
                            ->nullable(),
                        Forms\Components\TextInput::make('url')
                            ->label('URL de Referencia (Opcional)')
                            ->placeholder('https://ejemplo.com/noticia-interesante')
                            ->columnSpanFull(),
                    ])->columns(1),
                
                Section::make('Contenido para Procesar')
                    ->description('Escribe un título o un pequeño resumen. Si solo pones un título y le das a "Procesar con IA", el sistema investigará el tema.')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título o Semilla de la Noticia')
                            ->placeholder('Ej: OpenAI lanza Sora para el público general este lunes')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('summary')
                            ->label('Resumen o Notas Rápidas')
                            ->placeholder('Escribe aquí puntos clave que quieras que la IA incluya...')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
                            ->label('Contenido Crudo (Opcional)')
                            ->helperText('Contenido original que la IA usará como base para redactar.')
                            ->columnSpanFull(),
                    ])->columns(1),
                
                Section::make('Estado y Metadatos')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado de Procesamiento')
                            ->options([
                                'pending' => 'Pendiente',
                                'processing' => 'Procesando',
                                'processed' => 'Procesada',
                                'ignored' => 'Ignorada',
                                'failed' => 'Fallida',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Fecha de Publicación')
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('source.name')
                    ->label('Fuente')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'processed' => 'success',
                        'ignored' => 'warning',
                        'failed' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pendiente',
                        'processing' => 'Procesando',
                        'processed' => 'Procesada',
                        'ignored' => 'Ignorada',
                        'failed' => 'Fallida',
                    ]),
            ])
            ->actions([
                Action::make('procesar_ia')
                    ->label(fn (RawArticle $record) => in_array($record->status, ['ignored', 'failed']) ? 'Forzar Re-procesamiento IA' : 'Procesar con IA')
                    ->icon('heroicon-o-sparkles')
                    ->color(fn (RawArticle $record) => in_array($record->status, ['ignored', 'failed']) ? 'warning' : 'info')
                    ->requiresConfirmation()
                    ->visible(fn (RawArticle $record) => in_array($record->status, ['pending', 'ignored', 'failed']))
                    ->action(function (RawArticle $record) {
                        $record->update(['status' => 'pending']);
                        ProcessArticleWithAIJob::dispatch($record);
                        
                        Notification::make()
                            ->title('Procesamiento iniciado')
                            ->body('La noticia se ha puesto en cola para redacción profesional.')
                            ->success()
                            ->send();
                    }),
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRawArticles::route('/'),
            'create' => Pages\CreateRawArticle::route('/create'),
            'edit' => Pages\EditRawArticle::route('/{record}/edit'),
        ];
    }
}
