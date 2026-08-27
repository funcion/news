<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Models\Tag;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Services\AI\OpenRouterService;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'Taxonomy';

    protected static ?string $navigationLabel = 'Tags';
    protected static ?string $modelLabel = 'Tag';
    protected static ?string $pluralModelLabel = 'Tags';

        public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Información del Tag / Etiqueta')
                    ->columnSpanFull()
                    ->schema([
                        Tabs::make('Translations')
                            ->tabs([
                                // ─── 1. ENGLISH ──────────────────────────────────────────
                                Tabs\Tab::make('🇬🇧 English')
                                    ->schema([
                                        TextInput::make('name_en')
                                            ->label('Tag Name (EN)')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug_en', Str::slug($state)))
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('name', 'en'));
                                                }
                                            }),
                                        TextInput::make('slug_en')
                                            ->label('Slug EN (Sin prefijo de idioma)')
                                            ->required()
                                            ->helperText('Identificador en inglés: /tag/artificial-intelligence')
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->slug_en ?: $record->slug);
                                                }
                                            }),
                                        Textarea::make('description_en')
                                            ->label('Description (EN)')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('description', 'en'));
                                                }
                                            }),
                                    ])->columns(2),

                                // ─── 2. ESPAÑOL ──────────────────────────────────────────
                                Tabs\Tab::make('🇪🇸 Español')
                                    ->schema([
                                        TextInput::make('name_es')
                                            ->label('Nombre (ES)')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug_es', Str::slug($state)))
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('name', 'es'));
                                                }
                                            }),
                                        TextInput::make('slug_es')
                                            ->label('Slug ES (Con prefijo de idioma /es/)')
                                            ->required()
                                            ->helperText('Identificador en español: /es/tag/inteligencia-artificial')
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->slug_es ?: $record->slug);
                                                }
                                            }),
                                        Textarea::make('description_es')
                                            ->label('Descripción (ES)')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('description', 'es'));
                                                }
                                            }),
                                    ])->columns(2),

                                // ─── 3. SEO ──────────────────────────────────────────────
                                Tabs\Tab::make('🔍 SEO')
                                    ->schema([
                                        \Filament\Schemas\Components\Grid::make(2)
                                            ->schema([
                                                Toggle::make('is_indexable')
                                                    ->label('¿Permitir Indexación en Motores de Búsqueda? (index / noindex)')
                                                    ->helperText('Activado: Los buscadores como Google indexarán esta etiqueta en sus resultados.')
                                                    ->default(true),
                                                Toggle::make('is_followable')
                                                    ->label('¿Permitir Rastreo de Enlaces? (follow / nofollow)')
                                                    ->helperText('Activado: Los rastreadores seguirán los enlaces de esta etiqueta.')
                                                    ->default(true),
                                            ])->columnSpanFull(),
                                        TextInput::make('meta_title_en')
                                            ->label('Meta Title (EN)')
                                            ->maxLength(70)
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('meta_title', 'en'));
                                                }
                                            }),
                                        TextInput::make('meta_title_es')
                                            ->label('Meta Título (ES)')
                                            ->maxLength(70)
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('meta_title', 'es'));
                                                }
                                            }),
                                        Textarea::make('meta_description_en')
                                            ->label('Meta Description (EN)')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('meta_description', 'en'));
                                                }
                                            }),
                                        Textarea::make('meta_description_es')
                                            ->label('Meta Descripción (ES)')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('meta_description', 'es'));
                                                }
                                            }),
                                    ])->columns(2),
                            ])->columnSpanFull(),

                        Toggle::make('is_featured')
                            ->label('Tag Destacado')
                            ->default(false),
                        TextInput::make('article_count')
                            ->label('Total Artículos')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tag EN / ES')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', 'en') ?: '—')
                    ->description(fn ($record) => $record->getTranslation('name', 'es') ?: '—')
                    ->searchable(query: function ($query, $search) {
                        $query->whereRaw("name->>'en' ILIKE ?", ["%{$search}%"])
                              ->orWhereRaw("name->>'es' ILIKE ?", ["%{$search}%"]);
                    })
                    ->sortable(),
                TextColumn::make('slug')
                    ->copyable(),
                TextColumn::make('article_count')
                    ->label('Artículos')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_featured')
                    ->label('Destacado'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacado'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit'   => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}