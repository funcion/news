<?php

namespace App\Modules\User\Actions;

use App\Core\Actions\BaseAction;
use App\Models\User;
use App\Modules\User\Data\UserData;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class SocialiteAuthAction extends BaseAction
{
    public function __construct(
        protected RegisterUserAction $registerAction
    ) {}

    /**
     * Procesa la autenticación social.
     * 
     * @param SocialiteUser $socialUser
     * @return User
     */
    public function execute(mixed ...$args): User
    {
        /** @var SocialiteUser $socialUser */
        $socialUser = $args[0];

        $user = User::where('google_id', $socialUser->getId())
                    ->orWhere('email', $socialUser->getEmail())
                    ->first();

        if ($user) {
            // Vincular google_id si no lo tenía y actualizar avatar
            $user->update([
                'google_id' => $socialUser->getId(),
                'avatar_url' => $socialUser->getAvatar(),
            ]);
            return $user;
        }

        // Crear nuevo usuario si no existe
        $userData = UserData::from([
            'name'      => $socialUser->getName(),
            'email'     => $socialUser->getEmail(),
            'google_id' => $socialUser->getId(),
            'avatar_url' => $socialUser->getAvatar(),
        ]);

        $user = $this->registerAction->execute($userData);

        // Marcar email como verificado automáticamente para registros sociales
        $user->markEmailAsVerified();

        return $user;
    }
}
