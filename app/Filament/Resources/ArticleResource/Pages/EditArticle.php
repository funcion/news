<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Mail\ArticleStatusChanged;
use App\Services\AI\SiliconFlowImageService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_live')
                ->label('Ver en la Web')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('info')
                ->url(fn () => $this->record->url)
                ->openUrlInNewTab(),
            Actions\Action::make('regenerate_image')
                ->label('Regenerar Portada IA')
                ->icon('heroicon-o-sparkles')
                ->color('primary')
                ->form([
                    Forms\Components\Textarea::make('prompt')
                        ->label('Prompt Visual para FLUX.1 (En inglés)')
                        ->helperText('Instrucciones visuales para la IA.')
                        ->default(fn () => $this->record->ai_metadata['image_prompts'][0]['prompt_en'] ?? ($this->record->getTranslation('image_alt', 'en') ?: ('Editorial photojournalism style, high quality photography: ' . $this->record->getTranslation('title', 'en'))))
                        ->rows(3)
                        ->required(),
                ])
                ->modalHeading('🎨 Regenerar Portada con IA (FLUX.1)')
                ->modalDescription('Se generará una nueva portada con SiliconFlow FLUX.1 y se actualizarán las colecciones de medios.')
                ->modalSubmitActionLabel('Generar Imagen')
                ->action(function (array $data) {
                    $imageService = app(SiliconFlowImageService::class);
                    $success = $imageService->regenerateHeroForArticle($this->record, $data['prompt']);

                    if ($success) {
                        Notification::make()
                            ->title('Portada generada y actualizada con éxito')
                            ->success()
                            ->send();
                        $this->record->refresh();
                        $this->fillForm();
                } else {
                        Notification::make()
                            ->title('Error al generar la imagen con SiliconFlow')
                            ->danger()
                            ->send();
                }
                }),
            Actions\DeleteAction::make(),
            Actions\Action::make('approve')
                ->label('Aprobar')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => in_array($this->record->status, ['draft', 'pending_review']))
                ->requiresConfirmation()
                ->action(function () {
                    $old = $this->record->status;
                    $this->record->update(['status' => 'published', 'published_at' => now()]);
                    $this->sendNotification($old, 'published');
                    \App\Http\Controllers\SitemapController::flushCache();
                    \App\Http\Controllers\IndexNowController::ping(url('/' . $this->record->slug_en));
                    $this->refreshFormData(['status']);
                }),
            Actions\Action::make('reject')
                ->label('Rechazar')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => in_array($this->record->status, ['draft', 'pending_review']))
                ->requiresConfirmation()
                ->action(function () {
                    $old = $this->record->status;
                    $this->record->update(['status' => 'rejected']);
                    $this->sendNotification($old, 'rejected');
                    $this->refreshFormData(['status']);
                }),
            Actions\Action::make('review')
                ->label('Enviar a Revisión')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'draft')
                ->action(function () {
                    $old = $this->record->status;
                    $this->record->update(['status' => 'pending_review']);
                    $this->sendNotification($old, 'pending_review');
                    $this->refreshFormData(['status']);
                }),
        ];
    }

    protected function sendNotification(string $oldStatus, string $newStatus): void
    {
        try {
            $editors = \App\Models\User::where('is_active', true)->pluck('email')->filter();
            if ($editors->isEmpty()) return;
            $changedBy = auth()->user()?->name ?? 'Sistema';
            Mail::to($editors)->send(new ArticleStatusChanged($this->record, $oldStatus, $newStatus, $changedBy));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send article status notification: ' . $e->getMessage());
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();
        $metaTitleMax = (int) config('global.editorial.limits.meta_title.max', 70);
        $metaDescMax  = (int) config('global.editorial.limits.meta_description.max', 160);

        foreach ([
            'title'            => ['en' => $data['title_en'] ?? null, 'es' => $data['title_es'] ?? null],
            'excerpt'          => ['en' => $data['excerpt_en'] ?? null, 'es' => $data['excerpt_es'] ?? null],
            'content'          => ['en' => $data['content_en'] ?? null, 'es' => $data['content_es'] ?? null],
            'meta_title'       => [
                'en' => isset($data['meta_title_en']) ? \Illuminate\Support\Str::limit(trim($data['meta_title_en']), $metaTitleMax, '') : null,
                'es' => isset($data['meta_title_es']) ? \Illuminate\Support\Str::limit(trim($data['meta_title_es']), $metaTitleMax, '') : null,
            ],
            'meta_description' => [
                'en' => isset($data['meta_description_en']) ? \Illuminate\Support\Str::limit(trim($data['meta_description_en']), $metaDescMax, '') : null,
                'es' => isset($data['meta_description_es']) ? \Illuminate\Support\Str::limit(trim($data['meta_description_es']), $metaDescMax, '') : null,
            ],
        ] as $field => $translations) {
            foreach ($translations as $locale => $value) {
                if ($value !== null) {
                    $record->setTranslation($field, $locale, $value);
                }
            }
        }

        if (!empty($data['slug_en'])) {
            $record->slug_en = $data['slug_en'];
        }
        if (!empty($data['slug_es'])) {
            $record->slug_es = $data['slug_es'];
        }
        if (!empty($data['user_id'])) {
            $record->user_id = $data['user_id'];
        }

        $record->save();

        foreach (['title_en', 'title_es', 'slug_en', 'slug_es', 'excerpt_en', 'excerpt_es',
                  'content_en', 'content_es', 'meta_title_en', 'meta_title_es',
                  'meta_description', 'meta_description_es'] as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
