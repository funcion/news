{{-- resources/views/partials/alerts.blade.php --}}

{{-- Mensajes de estado de sesión (Éxito) --}}
@if (session('status'))
    <x-alert icon="o-check-circle" class="alert-success mb-4" dismissible>
        {{-- Algunos mensajes de Fortify son keys, intentamos traducirlos --}}
        {{ __(session('status')) }}
    </x-alert>
@endif

{{-- Alerta de errores de validación globales --}}
@if ($errors->any())
    <x-alert icon="o-exclamation-triangle" class="alert-error mb-4" dismissible>
        <div class="font-medium text-sm">{{ __('Whoops! Something went wrong.') }}</div>
        <ul class="mt-1 list-disc list-inside text-xs">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif
