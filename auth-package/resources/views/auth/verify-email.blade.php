@extends('layouts.auth')

@section('content')
<div class="">
    <x-card class="glass-card shadow-2xl rounded-3xl text-center" title="{{ __('Verify Email') }}">
        <x-icon name="o-envelope" class="w-16 h-16 mx-auto text-primary mb-4" />

        <p class="text-sm text-base-content/60 mb-6">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?') }}
        </p>

        @include('partials.alerts')

        <div class="flex flex-col gap-3">
            <form action="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeUrl(route('verification.send')) }}" method="POST">
                @csrf
                <x-button type="submit" class="btn-outline w-full" label="{{ __('Resend Verification Email') }}" />
            </form>

            <form action="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeUrl(route('logout')) }}" method="POST">
                @csrf
                <x-button type="submit" class="btn-ghost w-full" label="{{ __('Logout') }}" />
            </form>
        </div>
    </x-card>
</div>
@endsection
