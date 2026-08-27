<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
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

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Taxonomy';

    protected static ?string $navigationLabel = 'Categories';
    protected static ?string $modelLabel = 'Category';
    protected static ?string $pluralModelLabel = 'Categories';

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Gestión de Categoría')
                    ->description('Define la información editorial, traducciones y metadatos SEO en una sola interfaz organizada.')
                    ->columnSpanFull()
                    ->schema([
                        Tabs::make('Category_Tabs')
                            ->tabs([
                                // ─── 1. ENGLISH ──────────────────────────────────────────
                                Tabs\Tab::make('🇺🇸 English')
                                    ->schema([
                                        TextInput::make('name_en')
                                            ->label('Name (EN)')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('name', 'en'));
                                                }
                                            })
                                            ->afterStateUpdated(fn ($state, $set) => $set('slug_en', Str::slug($state ?? '')))
                                            ->suffixAction(
                                                Action::make('generate_ai_desc')
                                                    ->icon('heroicon-m-sparkles')
                                                    ->tooltip('✨ Generar automáticamente traducciones y SEO con IA')
                                                    ->action(function ($state, $set, OpenRouterService $ai) {
                                                        if (empty($state)) {
                                                            Notification::make()->warning()->title('Ingresa un Nombre en Inglés primero')->send();
                                                            return;
                                                        }
                                                        $appName = config('app.name', 'Glodaxia');
                                                        $prompt = "You are an elite bilingual SEO copywriter for {$appName} news. For the category '{$state}', generate Spanish name, descriptions, and high-CTR meta titles & descriptions. Response STRICTLY in JSON without markdown: { \"name_es\": \"...\", \"description_en\": \"...\", \"description_es\": \"...\", \"meta_title_en\": \"...\", \"meta_title_es\": \"...\", \"meta_description_en\": \"...\", \"meta_description_es\": \"...\" }";
                                                        
                                                        $response = $ai->complete([['role' => 'user', 'content' => $prompt]], config('ai_models.default'));
                                                        
                                                        if ($response) {
                                                            $clean = preg_replace('/```json|```/', '', $response);
                                                            $data = json_decode(trim($clean), true);
                                                            if (isset($data['name_es'])) $set('name_es', $data['name_es']);
                                                            if (isset($data['name_es'])) $set('slug_es', Str::slug($data['name_es']));
                                                            if (isset($data['description_en'])) $set('description_en', $data['description_en']);
                                                            if (isset($data['description_es'])) $set('description_es', $data['description_es']);
                                                            if (isset($data['meta_title_en'])) $set('meta_title_en', $data['meta_title_en']);
                                                            if (isset($data['meta_title_es'])) $set('meta_title_es', $data['meta_title_es']);
                                                            if (isset($data['meta_description_en'])) $set('meta_description_en', $data['meta_description_en']);
                                                            if (isset($data['meta_description_es'])) $set('meta_description_es', $data['meta_description_es']);
                                                            
                                                            Notification::make()->success()->title('✨ Contenido y SEO generados con éxito')->send();
                                                        }
                                                    })
                                            ),
                                        TextInput::make('slug_en')
                                            ->label('Slug (EN)')
                                            ->required()
                                            ->unique(Category::class, 'slug_en', ignoreRecord: true),
                                        Textarea::make('description_en')
                                            ->label('Description (EN)')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('description', 'en'));
                                                }
                                            }),
                                    ]),

                                // ─── 2. ESPAÑOL ──────────────────────────────────────────
                                Tabs\Tab::make('🇪🇸 Español')
                                    ->schema([
                                        TextInput::make('name_es')
                                            ->label('Nombre (ES)')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('name', 'es'));
                                                }
                                            })
                                            ->afterStateUpdated(fn ($state, $set) => $set('slug_es', Str::slug($state ?? ''))),
                                        TextInput::make('slug_es')
                                            ->label('Slug (ES)')
                                            ->required()
                                            ->unique(Category::class, 'slug_es', ignoreRecord: true),
                                        Textarea::make('description_es')
                                            ->label('Descripción (ES)')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('description', 'es'));
                                                }
                                            }),
                                    ]),

                                // ─── 3. SEO ──────────────────────────────────────────────
                                Tabs\Tab::make('🔍 SEO')
                                    ->schema([
                                        TextInput::make('meta_title_en')
                                            ->label('Meta Title (EN)')
                                            ->maxLength(70)
                                            ->helperText('Recommended: 50-60 characters for Google SERP.')
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('meta_title', 'en'));
                                                }
                                            }),
                                        TextInput::make('meta_title_es')
                                            ->label('Meta Título (ES)')
                                            ->maxLength(70)
                                            ->helperText('Recomendado: 50-60 caracteres para Google.')
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('meta_title', 'es'));
                                                }
                                            }),
                                        Textarea::make('meta_description_en')
                                            ->label('Meta Description (EN)')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->helperText('Recommended: 120-155 characters for high CTR.')
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('meta_description', 'en'));
                                                }
                                            }),
                                        Textarea::make('meta_description_es')
                                            ->label('Meta Descripción (ES)')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->helperText('Recomendado: 120-155 caracteres para maximizar el CTR.')
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('meta_description', 'es'));
                                                }
                                            }),
                                    ])->columns(2),
                            ])->columnSpanFull(),

                        TextInput::make('order')
                            ->label('Orden de Visualización')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Categoría Activa')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre EN / ES')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', 'en') ?: '—')
                    ->description(fn ($record) => $record->getTranslation('name', 'es') ?: '—')
                    ->searchable(query: function ($query, $search) {
                        $query->whereRaw("name->>'en' ILIKE ?", ["%{$search}%"])
                              ->orWhereRaw("name->>'es' ILIKE ?", ["%{$search}%"]);
                    })
                    ->sortable(),
                TextColumn::make('articles_count')
                    ->counts('articles')
                    ->label('Artículos')
                    ->sortable(),
                TextColumn::make('slug_en')
                    ->label('Slug EN')
                    ->copyable(),
                TextColumn::make('slug_es')
                    ->label('Slug ES')
                    ->copyable(),
                ToggleColumn::make('is_active')
                    ->label('Activo'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
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
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}