<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SourceResource\Pages;
use App\Models\Source;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SourceResource extends Resource
{
    protected static ?string $model = Source::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rss';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Ingesta';

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                TextInput::make('name')
                    ->label('Nombre de la Fuente')
                    ->placeholder('Ej: TechCrunch, HackerNoon')
                    ->required(),
                TextInput::make('url')
                    ->label('Feed URL (RSS / Atom)')
                    ->helperText('URL pública del archivo feed XML / RSS')
                    ->required()
                    ->url(),
                Select::make('type')
                    ->label('Tipo de Ingesta')
                    ->options([
                        'rss' => 'RSS (2.0)',
                        'atom' => 'Atom',
                        'json' => 'JSON Feed',
                        'scraping' => 'Scraping',
                    ])
                    ->required()
                    ->default('rss'),
                TextInput::make('category')
                    ->label('Categoría Sugerida')
                    ->helperText('Categoría predeterminada asignada a esta fuente'),
                TextInput::make('frequency')
                    ->label('Frecuencia (minutos)')
                    ->helperText('Intervalo de consulta (ej. 60 = cada hora, 120 = cada 2 horas)')
                    ->numeric()
                    ->default(60)
                    ->required(),
                TextInput::make('fetch_limit')
                    ->label('Límite de Ingesta (Posts)')
                    ->helperText('Máx. noticias a extraer por escaneo (0 = Sin límite / ilimitado)')
                    ->numeric()
                    ->default(3)
                    ->required(),
                TextInput::make('score')
                    ->label('Puntuación de Salud')
                    ->helperText('Aumenta (+2) con noticias nuevas, disminuye (-5) si falla la conexión')
                    ->numeric()
                    ->default(100)
                    ->disabled(),
                Toggle::make('is_active')
                    ->label('Activa')
                    ->helperText('Activar o pausar la sincronización de este feed')
                    ->default(true),
                Toggle::make('trusted')
                    ->label('Fuente Verificada')
                    ->helperText('Las fuentes verificadas tienen prioridad en la cola de procesamiento')
                    ->default(false),
                Toggle::make('is_default')
                    ->label('Fuente Predeterminada (Sistema)')
                    ->helperText('Identifica las fuentes nucleares preconfiguradas del sistema')
                    ->default(false),
                TextInput::make('max_age_days')
                    ->label('Máx. Antigüedad (días)')
                    ->numeric()
                    ->default(1)
                    ->helperText('Descartar automáticamente noticias publicadas hace más de X días'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('url')
                    ->label('URL')
                    ->limit(25)
                    ->tooltip(fn (Source $record): string => $record->url),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('frequency')
                    ->label('Freq (min)')
                    ->numeric()
                    ->sortable()
                    ->tooltip('Intervalo en minutos entre cada consulta al feed por el programador.'),
                TextColumn::make('fetch_limit')
                    ->label('Límite')
                    ->badge()
                    ->formatStateUsing(fn ($state) => (int)$state === 0 ? 'Sin límite (0)' : "{$state} posts")
                    ->color(fn ($state) => (int)$state === 0 ? 'info' : 'primary')
                    ->sortable()
                    ->tooltip('Número de noticias a extraer en cada escaneo. 0 = Ilimitado.'),
                TextColumn::make('score')
                    ->label('Score (Salud)')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    })
                    ->tooltip('Salud del feed: Sube (+2) con noticias nuevas, baja (-5) con errores.'),
                ToggleColumn::make('is_active')
                    ->label('Activa')
                    ->tooltip('Activar o pausar la lectura de este feed.'),
                ToggleColumn::make('trusted')
                    ->label('Verificada')
                    ->tooltip('Fuente oficial prioritaria en la cola de procesamiento editorial.'),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-badge')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable()
                    ->tooltip('Fuente nuclear predeterminada del sistema.'),
                TextColumn::make('max_age_days')
                    ->label('Máx. Días')
                    ->numeric()
                    ->sortable()
                    ->tooltip('Filtro de frescura: Rechaza noticias con más de X días de antigüedad.'),
                TextColumn::make('last_fetched_at')
                    ->label('Última Ingesta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->tooltip('Fecha y hora del último escaneo completado con éxito.'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado Activo'),
                Tables\Filters\TernaryFilter::make('trusted')
                    ->label('Verificadas'),
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Predeterminadas (Default)'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
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