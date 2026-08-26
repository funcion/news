@extends('layouts.auth')

@section('content')
<div class="">
    <x-card class="glass-card shadow-2xl rounded-3xl" title="{{ __('Confirm Password') }}">
        @include('partials.alerts')
        <p class="text-sm text-base-content/60 mb-4">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>

        <form action="{{ route('password.confirm') }}" method="POST">
            @csrf

            <x-input
                name="password"
                label="{{ __('Password') }}"
                type="password"
                required
                autofocus
                autocomplete="current-password"
                error="{{ $errors->first('password') }}"
            />

            <div class="mt-6">
                <x-button type="submit" class="btn-primary w-full" label="{{ __('Confirm') }}" />
            </div>
        </form>
    </x-card>
</div>
@endsection
