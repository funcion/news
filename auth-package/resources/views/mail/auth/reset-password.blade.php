<x-mail::message>
# {{ __('Reset Password Notification') }}

{{ __('You are receiving this email because we received a password reset request for your account.') }}

<x-mail::button :url="$url" color="primary">
{{ __('Reset Password') }}
</x-mail::button>

{{ __('This password reset link will expire in :count minutes.', ['count' => $count]) }}

{{ __('If you did not request a password reset, no further action is required.') }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>
