@extends('layouts.auth')

@section('content')
<div class="">
    <x-card class="glass-card shadow-2xl rounded-3xl" title="{{ __('Two Factor Authentication') }}">
        @include('partials.alerts')
        <div x-data="{ recovery: false }">

            {{-- Authentication Code Form --}}
            <div x-show="!recovery">
                <p class="text-sm text-base-content/60 mb-4">
                    {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
                </p>

                <form action="{{ request()->url() }}" method="POST">
                    @csrf

                    <x-input
                        name="code"
                        label="{{ __('Two Factor Code') }}"
                        type="text"
                        required
                        autofocus
                        autocomplete="one-time-code"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        error="{{ $errors->first('code') }}"
                    />

                    <div class="mt-6">
                        <x-button type="submit" class="btn-primary w-full" label="{{ __('Confirm') }}" />
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <button type="button" class="text-sm text-primary hover:underline" @click="recovery = true">
                        {{ __('Use a recovery code') }}
                    </button>
                </div>
            </div>

            {{-- Recovery Code Form --}}
            <div x-show="recovery" x-cloak>
                <p class="text-sm text-base-content/60 mb-4">
                    {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
                </p>

                <form action="{{ request()->url() }}" method="POST">
                    @csrf

                    <x-input
                        name="recovery_code"
                        label="{{ __('Recovery Code') }}"
                        type="text"
                        required
                        autocomplete="off"
                        error="{{ $errors->first('recovery_code') }}"
                    />

                    <div class="mt-6">
                        <x-button type="submit" class="btn-primary w-full" label="{{ __('Confirm') }}" />
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <button type="button" class="text-sm text-primary hover:underline" @click="recovery = false">
                        {{ __('Use an authentication code') }}
                    </button>
                </div>
            </div>

        </div>
    </x-card>
</div>
@endsection
