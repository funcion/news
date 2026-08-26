<?php

namespace App\Modules\User\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformationAction implements UpdatesUserProfileInformation
{
    /**
     * Valida y actualiza la información del perfil del usuario.
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'identification_number' => ['required', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            
            // Teléfonos (Opcionales excepto WhatsApp)
            'phone' => ['nullable', 'string', 'max:25'],
            'office_phone' => ['nullable', 'string', 'max:25'],
            'home_phone' => ['nullable', 'string', 'max:25'],
            'whatsapp' => ['required', 'string', 'max:25'],

            // Redes Sociales (Nombres de usuario)
            'facebook_url' => ['nullable', 'string', 'max:255'],
            'instagram_url' => ['nullable', 'string', 'max:255'],
            'pinterest_url' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'string', 'max:255'],
            'tiktok_url' => ['nullable', 'string', 'max:255'],
        ])->validateWithBag('updateProfileInformation');

        $user->forceFill([
            'name' => $input['name'],
            'surname' => $input['surname'] ?? $user->surname,
            'identification_number' => $input['identification_number'],
            'company' => $input['company'],
            'phone' => $input['phone'],
            'office_phone' => $input['office_phone'],
            'home_phone' => $input['home_phone'],
            'whatsapp' => $input['whatsapp'],
            'facebook_url' => $input['facebook_url'],
            'instagram_url' => $input['instagram_url'],
            'pinterest_url' => $input['pinterest_url'],
            'linkedin_url' => $input['linkedin_url'],
            'tiktok_url' => $input['tiktok_url'],
        ])->save();
    }
}
