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
use Filament\Forms\Form;
use Filament\Resources\Resource;
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
                                            ->afterStateUpdated(fn ($state, $set) => $set('slug_en', Str::slug($state ?? ''))),
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
                                            ->afterStateUpdated(fn ($state, $set) => $set('slug_es', Str::slug($state ?? ''))),
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
