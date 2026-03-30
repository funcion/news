<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_image')
                ->label('🖼️ Generar Portada IA')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('¿Generar nueva portada con IA?')
                ->modalDescription('Esta acción se tomará unos segundos. Usará el Título y Descripción que tienes actualmente guardados (Asegúrate de darle a "Guardar" abajo antes de continuar).')
                ->action(function (\App\Services\AI\OpenRouterService $ai, \App\Services\AI\SiliconFlowImageService $imageService) {
                    $category = $this->getRecord();
                    
                    if (empty($category->getTranslation('name', 'en'))) {
                        \Filament\Notifications\Notification::make()->warning()->title('El nombre en inglés está vacío.')->send();
                        return;
                    }

                    $titleEn = $category->getTranslation('name', 'en');
                    $descEn = $category->getTranslation('description', 'en') ?? $titleEn;
                    $titleEs = $category->getTranslation('name', 'es') ?? $titleEn;

                    // 1. Generate prompt with OpenRouter
                    $promptToAi = "You are an elite creative director. Write a highly detailed, professional, photorealistic image prompt for a news category named '{$titleEn}'. Context: {$descEn}. The prompt must be exclusively in English. Only return the prompt text, no markdown, no quotes.";
                    $imagePrompt = $ai->complete([['role' => 'user', 'content' => $promptToAi]], \App\Services\AI\OpenRouterService::MODEL_GEMINI_LATEST);
                    $imagePrompt = "Photorealistic, highly aesthetic, minimalist editorial style, 8k resolution, " . trim($imagePrompt) . ", no text, no watermarks, professional focus";

                    // 2. Clear old images
                    $category->clearMediaCollection('images_en');
                    $category->clearMediaCollection('images_es');

                    // 3. Generate with SiliconFlow
                    $path = $imageService->generateAndSave($imagePrompt, $category->slug_en, rand(100, 999));

                    if ($path && file_exists($path)) {
                        $imgNum = rand(10, 99);
                        $slugEn = $category->slug_en;
                        $slugEs = $category->slug_es ?? $slugEn;
                        
                        // Insert dual
                        $fileNameEn = "{$slugEn}-cover-{$imgNum}.webp";
                        $category->addMedia($path)
                            ->usingFileName($fileNameEn)
                            ->usingName(\Illuminate\Support\Str::limit($titleEn, 70))
                            ->withCustomProperties([
                                'lang' => 'en',
                                'alt' => "Artistic cover representing {$titleEn} category",
                                'title' => $titleEn,
                                'caption' => "Category: {$titleEn}",
                            ])
                            ->preservingOriginal()
                            ->toMediaCollection('images_en');

                        $fileNameEs = "{$slugEs}-portada-{$imgNum}.webp";
                        $category->addMedia($path)
                            ->usingFileName($fileNameEs)
                            ->usingName(\Illuminate\Support\Str::limit($titleEs, 70))
                            ->withCustomProperties([
                                'lang' => 'es',
                                'alt' => "Portada artística que representa la categoría {$titleEs}",
                                'title' => $titleEs,
                                'caption' => "Categoría: {$titleEs}",
                            ])
                            ->toMediaCollection('images_es');

                        \Filament\Notifications\Notification::make()->success()->title('✅ ¡Imagen bilingüe renderizada exitosamente!')->send();
                        $this->redirect($this->getResource()::getUrl('edit', ['record' => $category]));
                    } else {
                        \Filament\Notifications\Notification::make()->danger()->title('❌ Falló la generación de la imagen.')->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        foreach ([
            'name'        => ['en' => $data['name_en'] ?? null, 'es' => $data['name_es'] ?? null],
            'description' => ['en' => $data['description_en'] ?? null, 'es' => $data['description_es'] ?? null],
        ] as $field => $translations) {
            foreach ($translations as $locale => $value) {
                if ($value !== null) {
                    $record->setTranslation($field, $locale, $value);
                }
            }
        }

        $record->save();

        foreach (['name_en', 'name_es', 'slug_en', 'slug_es', 'description_en', 'description_es'] as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
