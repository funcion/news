<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Mail\ArticleStatusChanged;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';

    /**
     * The form uses a custom EN/ES tab layout instead of the Filament
     * Translatable plugin (which requires the plugin to be installed).
     * Each translatable field has explicit _en / _es virtual inputs that
     * read/write via getState/setMutatedAttributeValue hooks.
     */
    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Production Wall')
                    ->description('Manage the final article content in both languages, adjust SEO settings, and publish.')
                    ->columnSpanFull()
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
                                                    $content = $record->getTranslation('content', 'en');
                                                    $component->state(blank($content) ? '<p></p>' : $content);
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
                                                    $content = $record->getTranslation('content', 'es');
                                                    $component->state(blank($content) ? '<p></p>' : $content);
                                                }
                                            }),
                                    ]),

                                // ─── METADATA ────────────────────────────────
                                Tabs\Tab::make('Metadata')
                                    ->schema([
                                        Select::make('user_id')
                                            ->relationship('user', 'name')
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
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->formatStateUsing(fn ($record) => $record->user?->getTranslation('name', 'es') ?: $record->user?->getTranslation('name', 'en') ?: '—')
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
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Article $record) => in_array($record->status, ['draft', 'pending_review']))
                    ->requiresConfirmation()
                    ->action(function (Article $record) {
                        $old = $record->status;
                        $record->update(['status' => 'published', 'published_at' => now()]);
                        static::sendNotification($record, $old, 'published');
                        \App\Http\Controllers\SitemapController::flushCache();
                        \App\Http\Controllers\IndexNowController::ping(url('/' . $record->slug_en));
                    }),
                \Filament\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Article $record) => in_array($record->status, ['draft', 'pending_review']))
                    ->requiresConfirmation()
                    ->action(function (Article $record) {
                        $old = $record->status;
                        $record->update(['status' => 'rejected']);
                        static::sendNotification($record, $old, 'rejected');
                    }),
                \Filament\Actions\Action::make('review')
                    ->label('Enviar a Revisión')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->visible(fn (Article $record) => $record->status === 'draft')
                    ->action(function (Article $record) {
                        $old = $record->status;
                        $record->update(['status' => 'pending_review']);
                        static::sendNotification($record, $old, 'pending_review');
                    }),
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
            'index'  => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit'   => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    public static function sendNotification(Article $article, string $oldStatus, string $newStatus): void
    {
        try {
            $editors = \App\Models\User::where('is_active', true)->pluck('email')->filter();
            if ($editors->isEmpty()) {
                return;
            }
            $changedBy = auth()->user()?->name ?? 'Sistema';
            Mail::to($editors)->send(new ArticleStatusChanged($article, $oldStatus, $newStatus, $changedBy));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send article status notification: ' . $e->getMessage());
        }
    }
}
