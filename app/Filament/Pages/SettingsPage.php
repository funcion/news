<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class SettingsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 1;

    // ─── Data ────────────────────────────────────────────────

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill();

        $this->data = [
            // Rate Limits
            'max_articles_per_day'              => Setting::get('rate_limits.max_articles_per_day', 8),
            'max_articles_per_hour'             => Setting::get('rate_limits.max_articles_per_hour', 2),
            'max_articles_per_category_per_day' => Setting::get('rate_limits.max_articles_per_category_per_day', 3),
            'publish_hour_start'                => Setting::get('rate_limits.publish_hour_start', 7),
            'publish_hour_end'                  => Setting::get('rate_limits.publish_hour_end', 22),

            // Editorial
            'site_name'   => Setting::get('editorial.site_name', config('global.site_name', 'Glodaxia')),
            'tagline'     => Setting::get('editorial.tagline', config('global.tagline', 'Tech & News Magazine')),
            'footer_text' => Setting::get('editorial.footer_text', config('global.footer_text', 'Glodaxia Digital Media')),
        ];
    }

    // ─── Schema (Filament v5 — Schema reemplaza a Form) ─────

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Límites de Publicación (Rate Limiting)')
                    ->description('Controla cuántos artículos se publican por día/hora. Esto evita patrones que Google podría detectar como automatizados.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('data.max_articles_per_day')
                            ->label('Artículos por día')
                            ->helperText('Máximo de artículos publicados por día (7 AM - 10 PM)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->live(onBlur: true)
                            ->suffix('arts/día'),

                        TextInput::make('data.max_articles_per_hour')
                            ->label('Artículos por hora')
                            ->helperText('Máximo por hora — evita bursts sospechosos')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->live(onBlur: true)
                            ->suffix('arts/hora'),

                        TextInput::make('data.max_articles_per_category_per_day')
                            ->label('Por categoría/día')
                            ->helperText('Máximo de artículos de una misma categoría por día')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20)
                            ->live(onBlur: true)
                            ->suffix('arts/cat'),

                        TextInput::make('data.publish_hour_start')
                            ->label('Hora de inicio')
                            ->helperText('No publicar antes de esta hora (0-23)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(23)
                            ->live(onBlur: true)
                            ->suffix(':00'),

                        TextInput::make('data.publish_hour_end')
                            ->label('Hora de fin')
                            ->helperText('No publicar después de esta hora (0-23)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(23)
                            ->live(onBlur: true)
                            ->suffix(':00'),
                    ]),

                Section::make('Marca e Identidad')
                    ->description('Configuración general del sitio')
                    ->columns(2)
                    ->schema([
                        TextInput::make('data.site_name')
                            ->label('Nombre del sitio')
                            ->helperText('Ej: Glodaxia')
                            ->live(onBlur: true)
                            ->maxLength(255),

                        TextInput::make('data.tagline')
                            ->label('Tagline')
                            ->helperText('Ej: Tech & News Magazine')
                            ->live(onBlur: true)
                            ->maxLength(255),

                        TextInput::make('data.footer_text')
                            ->label('Texto del footer')
                            ->helperText('Ej: Glodaxia Digital Media')
                            ->live(onBlur: true)
                            ->maxLength(255),
                    ]),
            ]);
    }

    // ─── Actions ─────────────────────────────────────────────

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar Configuración')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        // Rate limits
        Setting::set('rate_limits.max_articles_per_day', (int) ($this->data['max_articles_per_day'] ?? 8), 'integer', 'rate_limits');
        Setting::set('rate_limits.max_articles_per_hour', (int) ($this->data['max_articles_per_hour'] ?? 2), 'integer', 'rate_limits');
        Setting::set('rate_limits.max_articles_per_category_per_day', (int) ($this->data['max_articles_per_category_per_day'] ?? 3), 'integer', 'rate_limits');
        Setting::set('rate_limits.publish_hour_start', (int) ($this->data['publish_hour_start'] ?? 7), 'integer', 'rate_limits');
        Setting::set('rate_limits.publish_hour_end', (int) ($this->data['publish_hour_end'] ?? 22), 'integer', 'rate_limits');

        // Editorial
        Setting::set('editorial.site_name', $this->data['site_name'] ?? 'Glodaxia', 'string', 'editorial');
        Setting::set('editorial.tagline', $this->data['tagline'] ?? 'Tech & News Magazine', 'string', 'editorial');
        Setting::set('editorial.footer_text', $this->data['footer_text'] ?? 'Glodaxia Digital Media', 'string', 'editorial');

        // Clear all cached settings so next request reads fresh values
        Cache::tags(['settings'])->flush();

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->body('Los cambios se aplicarán en los próximos artículos.')
            ->send();
    }
}