<?php

namespace App\Livewire\Auth;

use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ResetPasswordComponent extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token)
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword(ResetUserPassword $resetter)
    {
        $this->validate([
            'token'    => ['required'],
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', trans(Password::INVALID_USER));
            return;
        }

        $status = Password::reset(
            [
                'email'                 => $this->email,
                'password'              => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token'                 => $this->token,
            ],
            function (User $user, string $password) use ($resetter) {
                $resetter->reset($user, [
                    'password'              => $password,
                    'password_confirmation' => $password,
                ]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', __($status));
            return redirect(LaravelLocalization::localizeUrl('/login'));
        }

        $this->addError('email', __($status));
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.auth.reset-password-component', [
            'title'           => __('ui.auth_reset_password_title') . ' — Glodaxia',
            'metaDescription' => __('ui.auth_reset_password_subtitle'),
        ]);
    }
}