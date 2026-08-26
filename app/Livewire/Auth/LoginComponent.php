<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class LoginComponent extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public bool $showResendOption = false;
    public ?string $resendSuccess = null;

    protected function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function login()
    {
        $this->validate();

        $throttleKey = Str::transliterate(Str::lower($this->email) . '|' . request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        $user = User::where('email', $this->email)->first();

        // Check if user credentials match but email is not yet verified
        if ($user && Hash::check($this->password, $user->password) && !$user->hasVerifiedEmail()) {
            $this->showResendOption = true;
            throw ValidationException::withMessages([
                'email' => __('ui.auth_email_unverified_warning'),
            ]);
        }

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($throttleKey);
        session()->regenerate();

        return redirect()->intended(LaravelLocalization::localizeUrl('/'));
    }

    public function resendVerification()
    {
        $user = User::where('email', $this->email)->first();

        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            $this->resendSuccess = __('ui.auth_verification_link_sent');
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.auth.login-component', [
            'title'           => __('ui.auth_login_title') . ' — Glodaxia',
            'metaDescription' => __('ui.auth_login_subtitle'),
        ]);
    }
}