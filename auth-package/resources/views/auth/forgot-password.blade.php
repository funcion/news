@extends('layouts.auth')

@section('content')
<div class="">
    <x-card class="glass-card shadow-2xl rounded-3xl" title="{{ __('Reset Password') }}">
        <p class="text-sm text-base-content/60 mb-4">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
        </p>

        @include('partials.alerts')

        <form action="{{ request()->url() }}" method="POST">
            @csrf

            <x-input
                name="email"
                label="{{ __('Email') }}"
                type="email"
                required
                autofocus
                :value="old('email')"
                error="{{ $errors->first('email') }}"
            />

            <div class="mt-6">
                <x-button type="submit" class="btn-primary w-full" label="{{ __('Send Password Reset Link') }}" />
            </div>
        </form>

        <div class="mt-4 text-center text-sm">
            <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(null, route('login')) }}" class="text-primary hover:underline">
                {{ __('Back to Login') }}
            </a>
        </div>
    </x-card>
</div>
@endsection
