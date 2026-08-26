<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Livewire\Attributes\Layout;

class ForgotPasswordComponent extends Component
{
    public string $email = '';
    public ?string $status = null;

    public function sendResetLink()
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->status = __($status);
            $this->reset('email');
        } else {
            $this->addError('email', __($status));
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.auth.forgot-password-component', [
            'title'           => __('ui.auth_forgot_password_title') . ' — Glodaxia',
            'metaDescription' => __('ui.auth_forgot_password_subtitle'),
        ]);
    }
}