<div class="p-6 lg:p-10 max-w-4xl mx-auto space-y-10">
    <x-header title="{{ __('Edit Profile') }}" subtitle="{{ __('Manage your personal, contact, and social information') }}" separator progress-indicator />

    <x-form wire:submit="updateProfile">
        
        {{-- Sección: Identidad --}}
        <div class="space-y-8">
            <div class="flex items-center gap-2 mb-6">
                <x-icon name="o-identification" class="w-6 h-6 text-primary" />
                <h2 class="text-2xl font-black tracking-tight">{{ __('Identity Information') }}</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input label="{{ __('Name') }}" wire:model="name" icon="o-user" required />
                <x-input label="{{ __('Surname') }}" wire:model="surname" icon="o-user" />
                <x-input label="{{ __('Identification Number') }}" wire:model="identification_number" icon="o-identification" required hint="{{ __('ID, Passport, or National ID') }}" />
                <x-input label="{{ __('Company') }}" wire:model="company" icon="o-building-office" />
                <div class="md:col-span-2">
                    <x-input label="{{ __('Email Address') }}" wire:model="email" icon="o-envelope" readonly hint="{{ __('Email cannot be changed.') }}" />
                </div>
            </div>
        </div>

        {{-- Sección: Contacto (2 Columnas) --}}
        <div class="space-y-8 pt-7">
            <div class="flex items-center gap-2 mb-6">
                <x-icon name="o-phone" class="w-6 h-6 text-primary" />
                <h2 class="text-2xl font-black tracking-tight">{{ __('Contact Information') }}</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input label="{{ __('Personal Phone') }}" wire:model="phone" icon="o-phone" />
                <x-input label="{{ __('Office Phone') }}" wire:model="office_phone" icon="o-phone" />
                <x-input label="{{ __('Home Phone') }}" wire:model="home_phone" icon="o-home" />
                <x-input label="{{ __('WhatsApp Number') }}" wire:model="whatsapp" icon="o-chat-bubble-left-right" required />
            </div>
        </div>

        {{-- Sección: Redes Sociales --}}
        <div class="space-y-8 pt-7">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <x-icon name="o-share" class="w-6 h-6 text-primary" />
                    <h2 class="text-2xl font-black tracking-tight">{{ __('Social Networks') }}</h2>
                </div>
                <p class="text-sm text-base-content/60 mb-6">
                    {{ __('Please enter only your username (e.g., @username).') }}
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <x-input label="Facebook" wire:model="facebook_url" icon="o-link" placeholder="@username" />
                <x-input label="Instagram" wire:model="instagram_url" icon="o-link" placeholder="@username" />
                <x-input label="LinkedIn" wire:model="linkedin_url" icon="o-link" placeholder="@username" />
                <x-input label="Pinterest" wire:model="pinterest_url" icon="o-link" placeholder="@username" />
                <x-input label="TikTok" wire:model="tiktok_url" icon="o-link" placeholder="@username" />
            </div>
        </div>

        <div class="flex justify-start pt-12">
            <x-button label="{{ __('Save All Changes') }}" type="submit" icon="o-check" class="btn-primary w-full md:w-auto btn-lg" spinner="updateProfile" />
        </div>
    </x-form>

    <x-menu-separator />

    {{-- Sección: Seguridad (Full Width / 3 Filas) --}}
    <div class="space-y-8 pt-7">
        <div class="flex items-center gap-2 mb-6">
            <x-icon name="o-key" class="w-6 h-6 text-error" />
            <h2 class="text-2xl font-black tracking-tight text-error">{{ __('Security') }}</h2>
        </div>
        
        <x-card shadow separator border class="bg-base-200/50">
            <x-form wire:submit="updatePassword">
                <div class="grid grid-cols-1 gap-6 max-w-2xl">
                    <x-input label="{{ __('Current Password') }}" wire:model="current_password" type="password" icon="o-key" required />
                    <x-input label="{{ __('New Password') }}" wire:model="password" type="password" icon="o-lock-closed" required />
                    <x-input label="{{ __('Confirm Password') }}" wire:model="password_confirmation" type="password" icon="o-lock-closed" required />
                </div>

                <x-slot:actions>
                    <div class="flex justify-start w-full">
                        <x-button label="{{ __('Update Password') }}" type="submit" icon="o-arrow-path" class="btn-error btn-outline" spinner="updatePassword" />
                    </div>
                </x-slot:actions>
            </x-form>
        </x-card>
    </div>
</div>
