<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RawArticleResource\Pages;
use App\Models\RawArticle;
use App\Jobs\ProcessArticleWithAIJob;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class RawArticleResource extends Resource
{
    protected static ?string $model = RawArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';
    
    protected static ?string $navigationLabel = 'Noticias Crudas';
    
    protected static ?string $pluralLabel = 'Noticias Crudas';

    protected static ?string $navigationGroup = 'Gestión de Contenido';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Laboratorio de Ideas y Entradas')
                    ->description('Aquí llegan las noticias automáticas del RSS o puedes "Sembrar una Idea" para que la IA la procese.')
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
                    ])->columns(2),
                
                Forms\Components\Section::make('Contenido para Procesar')
                    ->description('Escribe un título o un pequeño resumen. Si solo pones un título y le das a "Procesar con IA", el sistema investigará el tema.')
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
                    ]),
                
                Forms\Components\Section::make('Estado y Metadatos')
                    ->schema([
                        Forms\Components\Placeholder::make('ai_status_alert')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 rounded-lg bg-blue-50 border-l-4 border-blue-500 flex items-center gap-3">
                                    <div class="text-blue-500">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <div class="text-sm text-blue-700">
                                        <strong>Nota de IA:</strong> Para que la IA pueda trabajar, el estado siempre debe ser <strong>"Pendiente" (Pending)</strong>.
                                    </div>
                                </div>
                            '))
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label('Estado de Procesamiento')
                            ->options([
                                'pending' => 'Pendiente',
                                'processed' => 'Procesada',
                                'ignored' => 'Ignorada',
                                'failed' => 'Fallida',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Fecha de Publicación'),
                    ])->columns(2),
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListRawArticles::route('/'),
            'create' => Pages\CreateRawArticle::route('/create'),
            'edit' => Pages\EditRawArticle::route('/{record}/edit'),
        ];
    }
}
