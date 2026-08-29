<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Mail\ArticleStatusChanged;
use App\Services\AI\SiliconFlowImageService;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
use Illuminate\Database\Eloquent\Builder;
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
                                            ->relationship('user', 'name', fn ($query) => $query->role(['redactor', 'admin', 'super_admin'])->where('is_active', true))
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Select::make('category_id')
                                            ->relationship('category', 'name')
                                            ->required(),
                                        Select::make('status')
                                            ->options([
                                                'draft'          => '⬜ Borrador (Draft)',
                                                'scheduled'      => '🟣 Programado (Scheduled)',
                                                'pending_review' => '🟡 En Revisión (Pending Review)',
                                                'published'      => '✅ Publicado (Published)',
                                                'rejected'       => '🔴 Rechazado (Rejected)',
                                            ])
                                            ->required()
                                            ->default('draft'),
                                        DateTimePicker::make('published_at')
                                            ->label('Publish Date'),
                                        TextInput::make('image_url')
                                            ->label('Featured Image URL')
                                            ->url()
                                            ->columnSpanFull(),
                                        Placeholder::make('image_preview')
                                            ->label('Vista Previa de la Portada')
                                            ->content(fn ($record) => $record?->image_url ? new \Illuminate\Support\HtmlString('<div class="mt-2"><img src="' . e($record->image_url) . '" class="w-full max-w-md h-auto rounded-xl shadow-md border border-gray-300 dark:border-gray-700" alt="Portada" /></div>') : 'Sin portada asignada')
                                            ->columnSpanFull(),
                                    ]),

                                // ─── SEO ─────────────────────────────────────
                                Tabs\Tab::make('SEO')
                                    ->schema([
                                        \Filament\Schemas\Components\Grid::make(2)
                                            ->schema([
                                                Toggle::make('is_indexable')
                                                    ->label('¿Permitir Indexación en Motores de Búsqueda? (index / noindex)')
                                                    ->helperText('Activado: Los buscadores como Google indexarán este artículo en sus resultados.')
                                                    ->default(true),
                                                Toggle::make('is_followable')
                                                    ->label('¿Permitir Rastreo de Enlaces? (follow / nofollow)')
                                                    ->helperText('Activado: Los rastreadores seguirán los enlaces dentro de este contenido.')
                                                    ->default(true),
                                            ])->columnSpanFull(),
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
            ->poll('5s')
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Portada')
                    ->circular(false)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover shadow-sm']),

                TextColumn::make('title')
                    ->label('Titular')
                    ->formatStateUsing(function (Article $record) {
                        return $record->getTranslation('title', 'es') ?: $record->getTranslation('title', 'en') ?: '—';
                    })
                    ->description(function (Article $record) {
                        $titleEs = $record->getTranslation('title', 'es') ?: $record->getTranslation('title', 'en') ?: '';
                        $len = mb_strlen($titleEs);
                        $words = count(preg_split('/\s+/u', trim($titleEs), -1, PREG_SPLIT_NO_EMPTY));
                        
                        $minChars = (int) config('global.editorial.limits.title.min', 50);
                        $maxChars = (int) config('global.editorial.limits.title.max', 130);
                        $minWords = (int) config('global.editorial.limits.title.min_words', 7);
                        
                        $isProcessing = \Illuminate\Support\Facades\Cache::has("article_title_processing_{$record->id}");

                        if ($isProcessing) {
                            $statusBadge = "⏳ <span class='text-amber-500 dark:text-amber-400 font-semibold animate-pulse'>Regenerando titular con IA en segundo plano...</span>";
                        } elseif ($len === 0) {
                            $statusBadge = 'Sin titular';
                        } elseif ($len < $minChars || $words < $minWords) {
                            $statusBadge = "🟡 {$len} caracteres • {$words} palabras (Corto / Incompleto)";
                        } elseif ($len > $maxChars) {
                            $statusBadge = "🔴 {$len} caracteres • {$words} palabras (Excedido)";
                        } else {
                            $statusBadge = "🟢 {$len} caracteres • {$words} palabras (Óptimo)";
                        }

                        $authorName = $record->user?->getTranslation('name', 'es') ?: $record->user?->getTranslation('name', 'en') ?: $record->user?->name ?: 'Sin autor';
                        $categoryName = $record->category?->getTranslation('name', 'es') ?: $record->category?->getTranslation('name', 'en') ?: 'Sin categoría';

                        return new \Illuminate\Support\HtmlString(
                            "<div class='flex flex-col gap-1 mt-1'>" .
                            "<div class='text-xs font-medium'>{$statusBadge}</div>" .
                            "<div class='flex items-center gap-2.5 text-[11.5px] font-normal mt-0.5' style='color: #909090;'>" .
                                "<span class='inline-flex items-center gap-1'>👤 {$authorName}</span>" .
                                "<span style='opacity: 0.5;'>•</span>" .
                                "<span class='inline-flex items-center gap-1'>📁 {$categoryName}</span>" .
                            "</div>" .
                            "</div>"
                        );
                    })
                    ->searchable(query: function ($query, $search) {
                        $query->whereRaw("title->>'en' ILIKE ?", ["%{$search}%"])
                              ->orWhereRaw("title->>'es' ILIKE ?", ["%{$search}%"]);
                    })
                    ->wrap(),

                TextColumn::make('ai_model')
                    ->label('Modelo IA')
                    ->badge()
                    ->placeholder('—')
                    ->getStateUsing(fn (Article $record) => $record->ai_metadata['model_used'] ?? $record->rawArticle?->ai_model ?? null)
                    ->color(fn (?string $state): string => config("ai_models.models.{$state}.color") ?? ($state ? 'primary' : 'gray'))
                    ->formatStateUsing(fn (?string $state): string => config("ai_models.models.{$state}.name") ?? ($state ? (basename($state) ?: $state) : '—'))
                    ->tooltip(fn (Article $record) => $record->ai_metadata['model_used'] ?? $record->rawArticle?->ai_model ?? 'Sin modelo registrado'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'          => 'Borrador',
                        'scheduled'      => 'Programado',
                        'pending_review' => 'En Revisión',
                        'published'      => 'Publicado',
                        'rejected'       => 'Rechazado',
                        default          => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft'          => 'gray',
                        'scheduled'      => 'info',
                        'pending_review' => 'warning',
                        'published'      => 'success',
                        'rejected'       => 'danger',
                        default          => 'gray',
                    }),

                TextColumn::make('published_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('views')
                    ->label('Vistas')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('periodo')
                    ->label('Período')
                    ->options([
                        'today'      => '📅 Hoy',
                        'yesterday'  => '📅 Ayer',
                        'this_week'  => '🗓️ Última semana (7 días)',
                        'this_month' => '🗓️ Último mes (30 días)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'today'      => $query->where(fn ($q) => $q->whereDate('published_at', now()->today())->orWhere(fn($sq)=>$sq->whereNull('published_at')->whereDate('created_at', now()->today()))),
                            'yesterday'  => $query->where(fn ($q) => $q->whereDate('published_at', now()->yesterday())->orWhere(fn($sq)=>$sq->whereNull('published_at')->whereDate('created_at', now()->yesterday()))),
                            'this_week'  => $query->where(fn ($q) => $q->where('published_at', '>=', now()->subDays(7))->orWhere(fn($sq)=>$sq->whereNull('published_at')->where('created_at', '>=', now()->subDays(7)))),
                            'this_month' => $query->where(fn ($q) => $q->where('published_at', '>=', now()->subDays(30))->orWhere(fn($sq)=>$sq->whereNull('published_at')->where('created_at', '>=', now()->subDays(30)))),
                            default      => $query,
                        };
                    }),

                Tables\Filters\Filter::make('rango_personalizado')
                    ->label('Rango Personalizado')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Fecha Desde'),
                        Forms\Components\DatePicker::make('hasta')->label('Fecha Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'] ?? null, fn (Builder $q, $date) => $q->where(fn ($sq) => $sq->whereDate('published_at', '>=', $date)->orWhere(fn($ssq) => $ssq->whereNull('published_at')->whereDate('created_at', '>=', $date))))
                            ->when($data['hasta'] ?? null, fn (Builder $q, $date) => $q->where(fn ($sq) => $sq->whereDate('published_at', '<=', $date)->orWhere(fn($ssq) => $ssq->whereNull('published_at')->whereDate('created_at', '<=', $date))));
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'published'      => 'Publicado',
                        'scheduled'      => 'Programado',
                        'draft'          => 'Borrador',
                        'pending_review' => 'En Revisión',
                        'rejected'       => 'Rechazado',
                    ]),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name'),
            ])
            ->actionsColumnLabel('Acciones')
            ->actions([
                \Filament\Actions\Action::make('regenerate_title')
                    ->label(fn (Article $record) => \Illuminate\Support\Facades\Cache::has("article_title_processing_{$record->id}") ? 'Regenerando Titular...' : 'Regenerar Título')
                    ->icon(fn (Article $record) => \Illuminate\Support\Facades\Cache::has("article_title_processing_{$record->id}") ? 'heroicon-o-arrow-path' : 'heroicon-o-sparkles')
                    ->iconButton()
                    ->color(fn (Article $record) => \Illuminate\Support\Facades\Cache::has("article_title_processing_{$record->id}") ? 'warning' : 'success')
                    ->extraAttributes(fn (Article $record) => \Illuminate\Support\Facades\Cache::has("article_title_processing_{$record->id}") ? ['class' => 'animate-spin opacity-80 pointer-events-none cursor-not-allowed'] : [])
                    ->disabled(fn (Article $record) => \Illuminate\Support\Facades\Cache::has("article_title_processing_{$record->id}"))
                    ->tooltip(fn (Article $record) => \Illuminate\Support\Facades\Cache::has("article_title_processing_{$record->id}") ? '⏳ Regenerando titular con IA en segundo plano...' : '🪄 Regenerar Título IA (1 Clic directo)')
                    ->action(function (Article $record) {
                        \Illuminate\Support\Facades\Cache::put("article_title_processing_{$record->id}", true, now()->addMinutes(3));
                        \App\Jobs\RegenerateArticleTitleJob::dispatch($record);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('🪄 Titular enviado a la cola de IA')
                            ->body('Se está regenerando en segundo plano.')
                            ->success()
                            ->send();
                    }),

                \Filament\Actions\Action::make('regenerate_image')
                    ->label('Regenerar Portada')
                    ->icon('heroicon-o-photo')
                    ->iconButton()
                    ->color('primary')
                    ->tooltip('🖼️ Regenerar Portada con IA (FLUX.1)')
                    ->form([
                        Textarea::make('prompt')
                            ->label('Prompt Visual para FLUX.1 (En inglés)')
                            ->helperText('Puedes personalizar las instrucciones visuales para la IA o dejar el prompt generado automáticamente.')
                            ->default(fn (Article $record) => $record->ai_metadata['image_prompts'][0]['prompt_en'] ?? ($record->getTranslation('image_alt', 'en') ?: ('Editorial photojournalism style, high quality photography: ' . $record->getTranslation('title', 'en'))))
                            ->rows(3)
                            ->required(),
                    ])
                    ->modalHeading('🎨 Regenerar Portada con IA (FLUX.1)')
                    ->modalDescription('Se enviará una solicitud a SiliconFlow para generar una nueva portada de alta calidad y reemplazar la imagen actual.')
                    ->modalSubmitActionLabel('Generar Portada')
                    ->action(function (Article $record, array $data) {
                        $imageService = app(SiliconFlowImageService::class);
                        $success = $imageService->regenerateHeroForArticle($record, $data['prompt']);

                        if ($success) {
                            \Filament\Notifications\Notification::make()
                                ->title('Imagen generada y actualizada con éxito')
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Error al generar la imagen con SiliconFlow')
                                ->body('Revisa los logs o tu saldo de API en SiliconFlow.')
                                ->danger()
                                ->send();
                        }
                    }),

                \Filament\Actions\Action::make('reprocess_article')
                    ->label('Reprocesar Noticia')
                    ->icon('heroicon-o-arrow-path')
                    ->iconButton()
                    ->color('danger')
                    ->tooltip('🔄 Reprocesar Noticia Completa (En Cola Asíncrona)')
                    ->action(function (Article $record) {
                        if ($record->rawArticle) {
                            $record->rawArticle->update(['status' => 'pending']);
                            \App\Jobs\ProcessArticleWithAIJob::dispatch($record->rawArticle);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Noticia enviada a la cola de IA')
                                ->body('El artículo y sus imágenes se están reescribiendo en segundo plano.')
                                ->info()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('No se encontró el registro original (RawArticle)')
                                ->danger()
                                ->send();
                        }
                    }),

                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('view_live')
                        ->label('Ver en la Web')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('info')
                        ->url(fn (Article $record) => $record->url)
                        ->openUrlInNewTab(),

                    \Filament\Actions\Action::make('change_author')
                        ->label('Cambiar Autor')
                        ->icon('heroicon-o-user-circle')
                        ->color('warning')
                        ->form([
                            Select::make('user_id')
                                ->label('Nuevo Autor / Redactor Asignado')
                                ->options(fn () => \App\Models\User::role(['redactor', 'admin', 'super_admin'])->where('is_active', true)->pluck('name', 'id'))
                                ->default(fn (Article $record) => $record->user_id)
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (Article $record, array $data) {
                            $record->update(['user_id' => $data['user_id']]);
                            \Filament\Notifications\Notification::make()
                                ->title('Autor reasignado exitosamente')
                                ->success()
                                ->send();
                        }),

                    \Filament\Actions\EditAction::make()
                        ->label('Editar Artículo')
                        ->icon('heroicon-o-pencil-square'),

                    \Filament\Actions\Action::make('publish_now')
                        ->label('Publicar Ahora')
                        ->icon('heroicon-o-bolt')
                        ->color('success')
                        ->visible(fn (Article $record) => $record->status === 'scheduled')
                        ->requiresConfirmation()
                        ->action(function (Article $record) {
                            $record->update(['status' => 'published', 'published_at' => now()]);
                            try {
                                event(new \App\Events\ArticlePublished($record));
                                \App\Http\Controllers\SitemapController::flushCache();
                                if ($record->slug_en) {
                                    \App\Http\Controllers\IndexNowController::ping(url('/' . $record->slug_en));
                                }
                                if ($record->slug_es) {
                                    \App\Http\Controllers\IndexNowController::ping(url('/es/' . $record->slug_es));
                                }
                            } catch (\Throwable $e) {
                                // ignore ping error
                            }
                        }),

                    \Filament\Actions\Action::make('approve')
                        ->label('Aprobar y Publicar')
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
                        ->label('Rechazar Noticia')
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
                ->label('Opciones')
                ->icon('heroicon-m-ellipsis-vertical')
                ->iconButton()
                ->color('gray')
                ->tooltip('Más opciones'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('regenerate_titles_bulk')
                        ->label('🪄 Regenerar Títulos Seleccionados')
                        ->icon('heroicon-o-sparkles')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $count = $records->count();
                            foreach ($records as $record) {
                                \Illuminate\Support\Facades\Cache::put("article_title_processing_{$record->id}", true, now()->addMinutes(3));
                                \App\Jobs\RegenerateArticleTitleJob::dispatch($record);
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title("🪄 {$count} artículos enviados a la cola de IA")
                                ->body("Los titulares se están regenerando en segundo plano de forma concurrente.")
                                ->success()
                                ->send();
                        }),
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
