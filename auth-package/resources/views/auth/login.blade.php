@extends('layouts.auth')

@section('content')
<div class="">
    <x-card class="glass-card shadow-2xl rounded-3xl" title="{{ __('Login') }}">

        @include('partials.alerts')

        <form action="{{ request()->url() }}" method="POST">
            @csrf

            <x-input
                name="email"
                label="{{ __('Email') }}"
                type="email"
                required
                autofocus
                autocomplete="username"
                :value="old('email')"
                error="{{ $errors->first('email') }}"
            />

            <x-input
                name="password"
                label="{{ __('Password') }}"
                type="password"
                required
                autocomplete="current-password"
                error="{{ $errors->first('password') }}"
            />

            <div class="flex items-center justify-between mt-4">
                <x-checkbox
                    name="remember"
                    label="{{ __('Remember me') }}"
                />

                @if (Route::has('password.request'))
                    <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(null, route('password.request')) }}" class="text-sm text-primary hover:underline">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <div class="mt-6">
                <x-button type="submit" class="btn-primary w-full" label="{{ __('Login') }}" />
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

        @if (Route::has('register'))
            <div class="mt-4 text-center text-sm">
                <span class="text-base-content/60">{{ __("Don't have an account?") }}</span>
                <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(null, route('register')) }}" class="text-primary hover:underline ml-1">
                    {{ __('Register') }}
                </a>
            </div>
        @endif
    </x-card>
</div>
@endsection
