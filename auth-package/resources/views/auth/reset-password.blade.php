@extends('layouts.auth')

@section('content')
<div class="">
    <x-card class="glass-card shadow-2xl rounded-3xl" title="{{ __('Reset Password') }}">
        @include('partials.alerts')
        <form action="{{ request()->url() }}" method="POST">
            @csrf

            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <x-input
                name="email"
                label="{{ __('Email') }}"
                type="email"
                required
                autocomplete="username"
                :value="old('email', request()->query('email'))"
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
                <x-button type="submit" class="btn-primary w-full" label="{{ __('Reset Password') }}" />
            </div>
        </form>
    </x-card>
</div>
@endsection
