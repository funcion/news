<x-filament-panels::page>
    <x-filament-panels::form
        id="form"
        :wire:key="'settings-form'"
    >
        {{ $this->form }}

        <div class="flex justify-end gap-4 mt-6">
            <x-filament::button
                type="submit"
                form="form"
                wire:click="save"
                color="primary"
            >
                Guardar Configuración
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>