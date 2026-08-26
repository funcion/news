<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Category Details')
                    ->description('Define the category name and description in both languages.')
                    ->schema([
                        Tabs::make('Languages')
                            ->tabs([
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
                                                    ->tooltip('✨ Generar nombre ES y descripciones con IA')
                                                    ->action(function ($state, $set, OpenRouterService $ai) {
                                                        if (empty($state)) {
                                                            Notification::make()->warning()->title('Ingresa un Nombre en Inglés primero')->send();
                                                            return;
                                                        }
                                                        
                                                        $prompt = "You are an elite bilingual SEO copywriter. Generate a Spanish name and 30-70 word SEO descriptions in both English and Spanish for the category: '{$state}'. Response STRICTLY in JSON: { \"name_es\": \"...\", \"description_en\": \"...\", \"description_es\": \"...\" } without markdown.";
                                                        
                                                        $response = $ai->complete([['role' => 'user', 'content' => $prompt]], config('ai_models.default'));
                                                        
                                                        $clean = preg_replace('/```json|```/', '', $response ?? '');
                                                        $data = json_decode(trim($clean), true);
                                                        
                                                        if ($data && isset($data['name_es'])) {
                                                            $set('name_es', $data['name_es']);
                                                            $set('slug_es', Str::slug($data['name_es']));
                                                            $set('description_en', $data['description_en'] ?? '');
                                                            $set('description_es', $data['description_es'] ?? '');
                                                            Notification::make()->success()->title('✨ ¡Contenido generado!')->send();
                                                        } else {
                                                            Notification::make()->danger()->title('La IA no pudo procesar la solicitud.')->send();
                                                        }
                                                    })
                                            ),
                                        TextInput::make('slug_en')
                                            ->label('Slug (EN)')
                                            ->required()
                                            ->unique(Category::class, 'slug_en', ignoreRecord: true)
                                            ->helperText('Used in URL: /category/your-slug'),
                                        Textarea::make('description_en')
                                            ->label('Description (EN)')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('description', 'en'));
                                                }
                                            }),
                                        Placeholder::make('image_en')
                                            ->label('Cover Image (EN) - 100% AI Generated')
                                            ->content(function ($record) {
                                                if (!$record) {
                                                    return new \Illuminate\Support\HtmlString('<p class="text-xs text-slate-500">Guarda la categoría primero para habilitar la generación de portada con IA.</p>');
                                                }
                                                $media = $record->getFirstMedia('images_en');
                                                if (!$media) {
                                                    return new \Illuminate\Support\HtmlString('
                                                        <div class="flex items-center gap-3 p-4 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                                                            <span class="text-2xl">🖼️</span>
                                                            <div>
                                                                <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">Aún no se ha generado una portada con IA.</p>
                                                                <p class="text-[11px] text-slate-500">Haz clic en el botón superior <strong>"🖼️ Generar Portada IA"</strong> para crearla automáticamente.</p>
                                                            </div>
                                                        </div>
                                                    ');
                                                }
                                                $url = $media->getUrl();
                                                return new \Illuminate\Support\HtmlString('
                                                    <div class="space-y-3">
                                                        <div class="relative group rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-md bg-slate-900 max-w-md aspect-video">
                                                            <img src="' . e($url) . '" alt="' . e($record->getTranslation('name', 'en')) . '" class="w-full h-full object-cover">
                                                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                                                <a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/95 text-slate-900 text-xs font-bold hover:bg-white transition shadow">
                                                                    <span>🔍</span> Ver original en Cloudflare R2
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-mono text-[11px]">
                                                                ☁️ Cloudflare R2
                                                            </span>
                                                            <a href="' . e($url) . '" target="_blank" class="truncate hover:underline text-cyan-600 dark:text-cyan-400 max-w-sm font-mono text-[11px]">' . e($url) . '</a>
                                                        </div>
                                                    </div>
                                                ');
                                            })
                                            ->columnSpanFull(),
                                    ]),

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
                                            ->afterStateUpdated(fn ($state, $set) => $set('slug_es', Str::slug($state ?? '')))
                                            ->suffixAction(
                                                Action::make('generate_ai_desc_es')
                                                    ->icon('heroicon-m-sparkles')
                                                    ->tooltip('✨ Generar nombre EN y descripciones con IA')
                                                    ->action(function ($state, $set, OpenRouterService $ai) {
                                                        if (empty($state)) {
                                                            Notification::make()->warning()->title('Ingresa un Nombre en Español primero')->send();
                                                            return;
                                                        }
                                                        
                                                        $prompt = "You are an elite bilingual SEO copywriter. Generate an English name and 30-70 word SEO descriptions in both English and Spanish for the Spanish category: '{$state}'. Response STRICTLY in JSON: { \"name_en\": \"...\", \"description_en\": \"...\", \"description_es\": \"...\" } without markdown.";
                                                        
                                                        $response = $ai->complete([['role' => 'user', 'content' => $prompt]], config('ai_models.default'));
                                                        
                                                        $clean = preg_replace('/```json|```/', '', $response ?? '');
                                                        $data = json_decode(trim($clean), true);
                                                        
                                                        if ($data && isset($data['name_en'])) {
                                                            $set('name_en', $data['name_en']);
                                                            $set('slug_en', Str::slug($data['name_en']));
                                                            $set('description_en', $data['description_en'] ?? '');
                                                            $set('description_es', $data['description_es'] ?? '');
                                                            Notification::make()->success()->title('✨ ¡Contenido generado desde Español!')->send();
                                                        } else {
                                                            Notification::make()->danger()->title('La IA no pudo procesar la solicitud.')->send();
                                                        }
                                                    })
                                            ),
                                        TextInput::make('slug_es')
                                            ->label('Slug (ES)')
                                            ->required()
                                            ->unique(Category::class, 'slug_es', ignoreRecord: true)
                                            ->helperText('Usado en URL: /es/categoria/tu-slug'),
                                        Textarea::make('description_es')
                                            ->label('Descripción (ES)')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('description', 'es'));
                                                }
                                            }),
                                        Placeholder::make('image_es')
                                            ->label('Imagen de Portada (ES) - 100% IA')
                                            ->content(function ($record) {
                                                if (!$record) {
                                                    return new \Illuminate\Support\HtmlString('<p class="text-xs text-slate-500">Guarda la categoría primero para habilitar la generación de portada con IA.</p>');
                                                }
                                                $media = $record->getFirstMedia('images_es');
                                                if (!$media) {
                                                    return new \Illuminate\Support\HtmlString('
                                                        <div class="flex items-center gap-3 p-4 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                                                            <span class="text-2xl">🖼️</span>
                                                            <div>
                                                                <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">Aún no se ha generado una portada con IA.</p>
                                                                <p class="text-[11px] text-slate-500">Haz clic en el botón superior <strong>"🖼️ Generar Portada IA"</strong> para crearla automáticamente.</p>
                                                            </div>
                                                        </div>
                                                    ');
                                                }
                                                $url = $media->getUrl();
                                                return new \Illuminate\Support\HtmlString('
                                                    <div class="space-y-3">
                                                        <div class="relative group rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-md bg-slate-900 max-w-md aspect-video">
                                                            <img src="' . e($url) . '" alt="' . e($record->getTranslation('name', 'es')) . '" class="w-full h-full object-cover">
                                                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                                                <a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/95 text-slate-900 text-xs font-bold hover:bg-white transition shadow">
                                                                    <span>🔍</span> Ver original en Cloudflare R2
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-mono text-[11px]">
                                                                ☁️ Cloudflare R2
                                                            </span>
                                                            <a href="' . e($url) . '" target="_blank" class="truncate hover:underline text-cyan-600 dark:text-cyan-400 max-w-sm font-mono text-[11px]">' . e($url) . '</a>
                                                        </div>
                                                    </div>
                                                ');
                                            })
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Active / Activa')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name (EN)')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', 'en') ?: $record->getTranslation('name', 'es'))
                    ->description(fn ($record) => $record->getTranslation('name', 'es'))
                    ->searchable(query: function ($query, $search) {
                        $query->whereRaw("name->>'en' ILIKE ?", ["%{$search}%"])
                              ->orWhereRaw("name->>'es' ILIKE ?", ["%{$search}%"]);
                    })
                    ->sortable(),
                TextColumn::make('slug_en')
                    ->label('Slug EN')
                    ->copyable(),
                TextColumn::make('slug_es')
                    ->label('Slug ES')
                    ->copyable(),
                ToggleColumn::make('is_active')
                    ->label('Active'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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
