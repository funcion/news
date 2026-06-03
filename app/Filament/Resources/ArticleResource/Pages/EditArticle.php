<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Mail\ArticleStatusChanged;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
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

    /**
     * Before filling the form, do nothing extra — 
     * afterStateHydrated callbacks in the resource handle populating virtual fields.
     */

    /**
     * Before saving, map virtual EN/ES fields to their translatable counterparts.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        // Save translatable fields via Spatie's setTranslation
        foreach ([
            'title'            => ['en' => $data['title_en'] ?? null, 'es' => $data['title_es'] ?? null],
            'excerpt'          => ['en' => $data['excerpt_en'] ?? null, 'es' => $data['excerpt_es'] ?? null],
            'content'          => ['en' => $data['content_en'] ?? null, 'es' => $data['content_es'] ?? null],
            'meta_title'       => ['en' => $data['meta_title_en'] ?? null, 'es' => $data['meta_title_es'] ?? null],
            'meta_description' => ['en' => $data['meta_description_en'] ?? null, 'es' => $data['meta_description_es'] ?? null],
        ] as $field => $translations) {
            foreach ($translations as $locale => $value) {
                if ($value !== null) {
                    $record->setTranslation($field, $locale, $value);
                }
            }
        }

        $record->save();

        // Remove virtual keys from $data to avoid Filament trying to set them directly
        foreach (['title_en', 'title_es', 'slug_en', 'slug_es', 'excerpt_en', 'excerpt_es',
                  'content_en', 'content_es', 'meta_title_en', 'meta_title_es',
                  'meta_description_en', 'meta_description_es'] as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
