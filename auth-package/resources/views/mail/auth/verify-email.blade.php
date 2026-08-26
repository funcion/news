<x-mail::message>
# {{ __('Verify Email Address') }}

{{ __('Please click the button below to verify your email address.') }}

<x-mail::button :url="$url" color="primary">
{{ __('Verify Email Address') }}
</x-mail::button>

{{ __('If you did not create an account, no further action is required.') }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>
