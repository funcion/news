<?php

namespace App\Livewire\Auth;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class RegisterComponent extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terms = false;

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

        Auth::login($user);

        return redirect()->intended(LaravelLocalization::localizeUrl('/'));
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