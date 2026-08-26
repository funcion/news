<?php

namespace App\Providers;

use App\Modules\User\Actions\SocialiteAuthAction;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\PasswordResetResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Fortify::ignoreRoutes();

        // Personalizar respuesta de registro
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    // Construir la URL localizada manualmente sin depender de nombres de ruta.
                    // Esto extrae el slug traducido (ej: 'verificar-correo-electronico') y lo une al prefijo del idioma ('es').
                    $locale = app()->getLocale();
                    $slug = LaravelLocalization::transRoute('routes.verify-email');
                    $url = url($locale . '/' . $slug);
                    
                    return redirect()->to($url);
                }
            };
        });

        // Asegurar que el Login redirija al idioma y slug correcto
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    $locale = app()->getLocale();
                    $slug = LaravelLocalization::transRoute('routes.dashboard');
                    $url = url($locale . '/' . $slug);
                    
                    return redirect()->intended($url);
                }
            };
        });

        // Asegurar que el Reset Password redirija al login en el idioma correcto
        $this->app->singleton(PasswordResetResponse::class, function () {
            return new class implements PasswordResetResponse {
                public function toResponse($request)
                {
                    $locale = app()->getLocale();
                    $slug = LaravelLocalization::transRoute('routes.login');
                    $url = url($locale . '/' . $slug);
                    
                    return redirect()->to($url)->with('status', trans('passwords.reset'));
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registro de vistas para resolver los contratos de Fortify
        Fortify::loginView(fn() => view('auth.login'));
        Fortify::registerView(fn() => view('auth.register'));
        Fortify::requestPasswordResetLinkView(fn() => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn($request) => view('auth.reset-password', ['request' => $request]));
        Fortify::verifyEmailView(fn() => view('auth.verify-email'));
        Fortify::twoFactorChallengeView(fn() => view('auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn() => view('auth.confirm-password'));

        Fortify::createUsersUsing(\App\Modules\User\Actions\RegisterUserAction::class);
        Fortify::updateUserProfileInformationUsing(\App\Modules\User\Actions\UpdateUserProfileInformationAction::class);
        Fortify::updateUserPasswordsUsing(\App\Modules\User\Actions\UpdateUserPasswordAction::class);
        Fortify::resetUserPasswordsUsing(\App\Modules\User\Actions\ResetUserPasswordAction::class);
    }
}
