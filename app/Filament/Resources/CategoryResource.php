<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Taxonomy';

    protected static ?string $navigationLabel = 'Categories';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                                                        
                                                        $response = $ai->complete([['role' => 'user', 'content' => $prompt]], OpenRouterService::MODEL_GEMINI_LATEST);
                                                        
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
                                        SpatieMediaLibraryFileUpload::make('image_en')
                                            ->label('Cover Image (EN) - 100% AI Generated')
                                            ->collection('images_en')
                                            ->image()
                                            ->disabled() // Impide subida manual, solo visualización
                                            ->deletable(false)
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
                                                        
                                                        $response = $ai->complete([['role' => 'user', 'content' => $prompt]], OpenRouterService::MODEL_GEMINI_LATEST);
                                                        
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
                                        SpatieMediaLibraryFileUpload::make('image_es')
                                            ->label('Imagen de Portada (ES) - 100% IA')
                                            ->collection('images_es')
                                            ->image()
                                            ->disabled() // Impide subida manual
                                            ->deletable(false)
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
