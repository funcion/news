<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Models\Tag;
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

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Taxonomy';

    protected static ?string $navigationLabel = 'Tags';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Tag Details')
                    ->description('Define the tag name and description in both languages. Tags are used to classify articles.')
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
                                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state ?? ''))),
                                        Textarea::make('description_en')
                                            ->label('Description (EN)')
                                            ->rows(2)
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
                                            }),
                                        Textarea::make('description_es')
                                            ->label('Descripción (ES)')
                                            ->rows(2)
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('description', 'es'));
                                                }
                                            }),
                                    ]),
                            ])->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('Slug (shared)')
                            ->required()
                            ->unique(Tag::class, 'slug', ignoreRecord: true)
                            ->helperText('A single slug is used for tags (auto-generated from EN name)'),
                        Toggle::make('is_featured')
                            ->label('Featured Tag')
                            ->default(false),
                        TextInput::make('article_count')
                            ->label('Article Count')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ]),
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
                    ->label('Articles')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_featured')
                    ->label('Featured'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
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
            'index'  => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit'   => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
