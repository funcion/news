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

    protected static string|\UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.settings';

    // ─── Data ────────────────────────────────────────────────

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill();

        $this->data = [
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
                Section::make('Marca e Identidad')
                    ->description('Configuración general del sitio y metadatos globales')
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
        // Editorial
        Setting::set('editorial.site_name', $this->data['site_name'] ?? 'Glodaxia', 'string', 'editorial');
        Setting::set('editorial.tagline', $this->data['tagline'] ?? 'Tech & News Magazine', 'string', 'editorial');
        Setting::set('editorial.footer_text', $this->data['footer_text'] ?? 'Glodaxia Digital Media', 'string', 'editorial');

        // Clear all cached settings so next request reads fresh values
        Cache::tags(['settings'])->flush();

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->body('Los cambios se han aplicado correctamente.')
            ->send();
    }
}