<?php

namespace App\Livewire\Profile;

use App\Modules\User\Actions\UpdateUserPasswordAction;
use App\Modules\User\Actions\UpdateUserProfileInformationAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

class EditProfile extends Component
{
    use Toast;

    // Profile Info
    public string $name = '';
    public string $surname = '';
    public string $email = '';
    public string $identification_number = '';
    public string $company = '';

    // Contact
    public string $phone = '';
    public string $office_phone = '';
    public string $home_phone = '';
    public string $whatsapp = '';

    // Social
    public string $facebook_url = '';
    public string $instagram_url = '';
    public string $pinterest_url = '';
    public string $linkedin_url = '';
    public string $tiktok_url = '';

    // Password Update
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->surname = $user->surname ?? '';
        $this->email = $user->email;
        $this->identification_number = $user->identification_number ?? '';
        $this->company = $user->company ?? '';
        
        $this->phone = $user->phone ?? '';
        $this->office_phone = $user->office_phone ?? '';
        $this->home_phone = $user->home_phone ?? '';
        $this->whatsapp = $user->whatsapp ?? '';

        $this->facebook_url = $user->facebook_url ?? '';
        $this->instagram_url = $user->instagram_url ?? '';
        $this->pinterest_url = $user->pinterest_url ?? '';
        $this->linkedin_url = $user->linkedin_url ?? '';
        $this->tiktok_url = $user->tiktok_url ?? '';
    }

    /**
     * Actualiza la información del perfil completo.
     */
    public function updateProfile(UpdateUserProfileInformationAction $updater): void
    {
        $this->resetErrorBag();

        $updater->update(Auth::user(), [
            'name' => $this->name,
            'surname' => $this->surname,
            'identification_number' => $this->identification_number,
            'company' => $this->company,
            'phone' => $this->phone,
            'office_phone' => $this->office_phone,
            'home_phone' => $this->home_phone,
            'whatsapp' => $this->whatsapp,
            'facebook_url' => $this->facebook_url,
            'instagram_url' => $this->instagram_url,
            'pinterest_url' => $this->pinterest_url,
            'linkedin_url' => $this->linkedin_url,
            'tiktok_url' => $this->tiktok_url,
        ]);

        $this->success(__('Profile updated successfully.'));
    }

    /**
     * Actualiza la contraseña del usuario.
     */
    public function updatePassword(UpdateUserPasswordAction $updater): void
    {
        $this->resetErrorBag();

        $updater->update(Auth::user(), [
            'current_password' => $this->current_password,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';

        $this->success(__('Password updated successfully.'));
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.profile.edit-profile');
    }
}
