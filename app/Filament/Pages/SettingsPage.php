<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class SettingsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Configuración Global';

    protected static ?string $navigationLabel = 'Configuración';

    protected string $view = 'filament.pages.settings';

    // ─── Data ────────────────────────────────────────────────

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill();

        $this->data = [
            // Editorial & Marca
            'site_name'   => Setting::get('editorial.site_name', config('global.site_name', 'Glodaxia')),
            'tagline'     => Setting::get('editorial.tagline', config('global.tagline', 'Tech & News Magazine')),
            'footer_text' => Setting::get('editorial.footer_text', config('global.footer_text', 'Glodaxia Digital Media')),

            // Cadencia de Publicación Escalonada
            'staggered_enabled' => (bool) Setting::get('publishing.staggered_enabled', false),
            'delay_min_minutes' => (int) Setting::get('publishing.delay_min_minutes', 1),
            'delay_max_minutes' => (int) Setting::get('publishing.delay_max_minutes', 60),
        ];
    }

    // ─── Schema ──────────────────────────────────────────────

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('⏱️ Cadencia y Publicación Escalonada de Noticias')
                    ->description('Controla el intervalo de tiempo aleatorio entre cada publicación automática para simular un ritmo humano orgánico.')
                    ->columns(3)
                    ->schema([
                        Toggle::make('data.staggered_enabled')
                            ->label('Activar Publicación Escalonada')
                            ->helperText('Si se desactiva, las noticias se publicarán de inmediato al redactarse.')
                            ->default(false)
                            ->columnSpanFull(),

                        TextInput::make('data.delay_min_minutes')
                            ->label('Tiempo Mínimo (Minutos)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1440)
                            ->default(1)
                            ->helperText('Ejemplo: 1 minuto.')
                            ->live(onBlur: true)
                            ->required(),

                        TextInput::make('data.delay_max_minutes')
                            ->label('Tiempo Máximo (Minutos)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1440)
                            ->default(60)
                            ->helperText('Ejemplo: 60 minutos o 120 minutos.')
                            ->live(onBlur: true)
                            ->required(),
                    ]),

                Section::make('🏷️ Marca e Identidad')
                    ->description('Configuración general del sitio y metadatos globales')
                    ->columns(3)
                    ->schema([
                        TextInput::make('data.site_name')
                            ->label('Nombre del sitio')
                            ->helperText('Ej: Glodaxia')
                            ->live(onBlur: true)
                            ->maxLength(255),

                        TextInput::make('data.tagline')
                            ->label('Tagline')
                            ->helperText('Ej: Periodismo Tecnológico de Vanguardia')
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
                ->icon('heroicon-o-check')
                ->color('primary')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        // Editorial
        Setting::set('editorial.site_name', $this->data['site_name'] ?? 'Glodaxia', 'string', 'editorial');
        Setting::set('editorial.tagline', $this->data['tagline'] ?? 'Tech & News Magazine', 'string', 'editorial');
        Setting::set('editorial.footer_text', $this->data['footer_text'] ?? 'Glodaxia Digital Media', 'string', 'editorial');

        // Publishing Cadence
        $minDelay = max(0, (int) ($this->data['delay_min_minutes'] ?? 1));
        $maxDelay = max($minDelay, (int) ($this->data['delay_max_minutes'] ?? 60));

        Setting::set('publishing.staggered_enabled', (bool) ($this->data['staggered_enabled'] ?? true), 'boolean', 'publishing');
        Setting::set('publishing.delay_min_minutes', $minDelay, 'integer', 'publishing');
        Setting::set('publishing.delay_max_minutes', $maxDelay, 'integer', 'publishing');

        // Clear all cached settings so next request reads fresh values
        Cache::forget('setting.publishing.staggered_enabled');
        Cache::forget('setting.publishing.delay_min_minutes');
        Cache::forget('setting.publishing.delay_max_minutes');
        Cache::forget('setting.editorial.site_name');
        Cache::forget('setting.editorial.tagline');
        Cache::forget('setting.editorial.footer_text');

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->body('Los parámetros de publicación y marca se han actualizado correctamente.')
            ->send();
    }
}