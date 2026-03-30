<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Contenido';

    /**
     * The form uses a custom EN/ES tab layout instead of the Filament
     * Translatable plugin (which requires the plugin to be installed).
     * Each translatable field has explicit _en / _es virtual inputs that
     * read/write via getState/setMutatedAttributeValue hooks.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Production Wall')
                    ->description('Manage the final article content in both languages, adjust SEO settings, and publish.')
                    ->schema([
                        Tabs::make('Sections')
                            ->tabs([

                                // ─── CONTENT ─────────────────────────────────
                                Tabs\Tab::make('🇺🇸 English')
                                    ->schema([
                                        TextInput::make('title_en')
                                            ->label('Title (EN)')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('title', 'en'));
                                                }
                                            })
                                            ->afterStateUpdated(fn ($state, $set) => $set('slug_en', Str::slug($state ?? ''))),
                                        TextInput::make('slug_en')
                                            ->label('Slug (EN)')
                                            ->required()
                                            ->unique(Article::class, 'slug_en', ignoreRecord: true)
                                            ->helperText('URL: /news/your-slug'),
                                        Textarea::make('excerpt_en')
                                            ->label('Excerpt (EN)')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('excerpt', 'en'));
                                                }
                                            }),
                                        RichEditor::make('content_en')
                                            ->label('Content (EN)')
                                            ->required()
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('content', 'en'));
                                                }
                                            }),
                                    ]),

                                Tabs\Tab::make('🇪🇸 Español')
                                    ->schema([
                                        TextInput::make('title_es')
                                            ->label('Título (ES)')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('title', 'es'));
                                                }
                                            })
                                            ->afterStateUpdated(fn ($state, $set) => $set('slug_es', Str::slug($state ?? ''))),
                                        TextInput::make('slug_es')
                                            ->label('Slug (ES)')
                                            ->required()
                                            ->unique(Article::class, 'slug_es', ignoreRecord: true)
                                            ->helperText('URL: /es/noticias/tu-slug'),
                                        Textarea::make('excerpt_es')
                                            ->label('Extracto (ES)')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('excerpt', 'es'));
                                                }
                                            }),
                                        RichEditor::make('content_es')
                                            ->label('Contenido (ES)')
                                            ->required()
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('content', 'es'));
                                                }
                                            }),
                                    ]),

                                // ─── METADATA ────────────────────────────────
                                Tabs\Tab::make('Metadata')
                                    ->schema([
                                        Select::make('author_id')
                                            ->relationship('author', 'name')
                                            ->required(),
                                        Select::make('category_id')
                                            ->relationship('category', 'name')
                                            ->required(),
                                        Select::make('status')
                                            ->options([
                                                'draft'          => '⬜ Draft',
                                                'pending_review' => '🟡 Pending Review',
                                                'published'      => '✅ Published',
                                                'rejected'       => '🔴 Rejected',
                                            ])
                                            ->required()
                                            ->default('draft'),
                                        DateTimePicker::make('published_at')
                                            ->label('Publish Date'),
                                        TextInput::make('image_url')
                                            ->label('Featured Image URL')
                                            ->url()
                                            ->columnSpanFull(),
                                    ]),

                                // ─── SEO ─────────────────────────────────────
                                Tabs\Tab::make('SEO')
                                    ->schema([
                                        TextInput::make('meta_title_en')
                                            ->label('Meta Title (EN)')
                                            ->maxLength(70)
                                            ->helperText('Max 70 characters')
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('meta_title', 'en'));
                                                }
                                            }),
                                        TextInput::make('meta_title_es')
                                            ->label('Meta Title (ES)')
                                            ->maxLength(70)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('meta_title', 'es'));
                                                }
                                            }),
                                        Textarea::make('meta_description_en')
                                            ->label('Meta Description (EN)')
                                            ->rows(2)
                                            ->maxLength(160)
                                            ->helperText('Max 160 characters')
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('meta_description', 'en'));
                                                }
                                            }),
                                        Textarea::make('meta_description_es')
                                            ->label('Meta Description (ES)')
                                            ->rows(2)
                                            ->maxLength(160)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $component->state($record->getTranslation('meta_description', 'es'));
                                                }
                                            }),
                                        TextInput::make('seo_score')
                                            ->label('SEO Score (0-100)')
                                            ->numeric()
                                            ->disabled(),
                                    ]),

                            ])->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Handle saving all virtual translation fields back to the model.
     */
    public static function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Image'),
                TextColumn::make('title')
                    ->label('Title (EN)')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('title', 'en'))
                    ->searchable(query: function ($query, $search) {
                        $query->whereRaw("title->>'en' ILIKE ?", ["%{$search}%"])
                              ->orWhereRaw("title->>'es' ILIKE ?", ["%{$search}%"]);
                    })
                    ->limit(50),
                TextColumn::make('author.name')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->formatStateUsing(fn ($record) => $record->category?->getTranslation('name', 'en') ?? '—')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'          => 'gray',
                        'pending_review' => 'warning',
                        'published'      => 'success',
                        'rejected'       => 'danger',
                        default          => 'gray',
                    }),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('views')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'          => 'Draft',
                        'pending_review' => 'Pending Review',
                        'published'      => 'Published',
                    ]),
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
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
            'index'  => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit'   => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
