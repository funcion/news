<?php

namespace App\Modules\User\Actions;

use App\Core\Actions\BaseAction;
use App\Models\User;
use App\Modules\User\Data\UserData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\Validator;

class RegisterUserAction extends BaseAction implements CreatesNewUsers
{
    /**
     * Requerido por Fortify: Crea el usuario validando primero los datos.
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        $data = UserData::from([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'preferred_locale' => app()->getLocale(),
        ]);

        return $this->execute($data);
    }

    /**
     * Registra un nuevo usuario en el sistema.
     * 
     * @param UserData $data
     * @return User
     */
    public function execute(mixed ...$args): User
    {
        /** @var UserData $data */
        $data = $args[0];

        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'             => $data->name,
                'email'            => $data->email,
                'password'         => $data->password ? Hash::make($data->password) : null,
                'google_id'        => $data->google_id,
                'avatar_url'       => $data->avatar_url,
                'preferred_locale' => $data->preferred_locale,
                'timezone'         => $data->timezone,
            ]);

            // Asignar rol por defecto
            $user->assignRole('panel_user');

            // La Wallet se inicializa automáticamente con bavix al primer acceso,
            // pero podemos forzar la creación aquí si es necesario.
            $user->createWallet([
                'name' => __('Default Wallet'),
                'slug' => 'default',
            ]);

            return $user;
        });
    }
}
