<?php

namespace App\Livewire\Auth;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Livewire\Component;
use Livewire\Attributes\Layout;

class RegisterComponent extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terms = false;

    public bool $registeredSuccess = false;
    public string $registeredEmail = '';
    public ?string $resendStatus = null;

    public function register(CreateNewUser $creator)
    {
        $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms'    => ['accepted'],
        ], [
            'terms.accepted' => __('ui.auth_terms_accepted_required'),
        ]);

        $user = $creator->create([
            'name'                  => $this->name,
            'email'                 => $this->email,
            'password'              => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        // Send Email Verification Link
        event(new Registered($user));

        $this->registeredEmail = $this->email;
        $this->registeredSuccess = true;
    }

    public function resendVerification()
    {
        $user = User::where('email', $this->registeredEmail)->first();

        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            $this->resendStatus = __('ui.auth_verification_link_sent');
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.auth.register-component', [
            'title'           => __('ui.auth_register_title') . ' — Glodaxia',
            'metaDescription' => __('ui.auth_register_subtitle'),
        ]);
    }
}