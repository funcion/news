@extends('layouts.auth')

@section('content')
<div class="">
    <x-card class="glass-card shadow-2xl rounded-3xl" title="{{ __('Register') }}">
        @include('partials.alerts')
        <form action="{{ request()->url() }}" method="POST">
            @csrf

            <x-input
                name="name"
                label="{{ __('Name') }}"
                type="text"
                required
                autofocus
                autocomplete="name"
                :value="old('name')"
                error="{{ $errors->first('name') }}"
            />

            <x-input
                name="email"
                label="{{ __('Email') }}"
                type="email"
                required
                autocomplete="username"
                :value="old('email')"
                error="{{ $errors->first('email') }}"
            />

            <x-input
                name="password"
                label="{{ __('Password') }}"
                type="password"
                required
                autocomplete="new-password"
                error="{{ $errors->first('password') }}"
            />

            <x-input
                name="password_confirmation"
                label="{{ __('Confirm Password') }}"
                type="password"
                required
                autocomplete="new-password"
                error="{{ $errors->first('password_confirmation') }}"
            />

            <div class="mt-6">
                <x-button type="submit" class="btn-primary w-full" label="{{ __('Register') }}" />
            </div>
        </form>

        {{-- OAuth Google --}}
        @if (Route::has('auth.google'))
            <div class="divider my-4">{{ __('or') }}</div>
            <a href="{{ route('auth.google') }}" class="btn btn-outline w-full gap-2">
                <x-icon name="o-globe-alt" class="w-5 h-5" />
                {{ __('Continue with Google') }}
            </a>
        @endif

        <div class="mt-4 text-center text-sm">
            <span class="text-base-content/60">{{ __('Already registered?') }}</span>
            <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(null, route('login')) }}" class="text-primary hover:underline ml-1">
                {{ __('Login') }}
            </a>
        </div>
    </x-card>
</div>
@endsection
