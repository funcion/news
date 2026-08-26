<?php

use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Fortify + Multi-language i18n)
|--------------------------------------------------------------------------
|
| Estas rutas deben ser cargadas dentro del grupo de rutas localizadas
| de Mcamara LaravelLocalization en tu routes/web.php.
|
*/

\ = LaravelLocalization::setLocale();

// ============================================
// Autenticación (Fortify) - Vistas Guest
// ============================================
Route::middleware('guest')->group(function () use (\) {
    Route::get(LaravelLocalization::transRoute('routes.login', \), function () {
        return view('auth.login');
    })->name('login');

    Route::get(LaravelLocalization::transRoute('routes.register', \), function () {
        return view('auth.register');
    })->name('register');

    Route::get(LaravelLocalization::transRoute('routes.forgot-password', \), function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::get(LaravelLocalization::transRoute('routes.reset-password', \) . '/{token}', function (\Illuminate\Http\Request \) {
        return view('auth.reset-password', ['request' => \]);
    })->name('password.reset');
});

// ============================================
// Autenticación (Fortify) - Endpoints POST
// ============================================
\ = config('fortify.limiters.login');

Route::post(LaravelLocalization::transRoute('routes.login', \), [AuthenticatedSessionController::class, 'store'])
    ->middleware(array_filter([
        'guest:'.config('fortify.guard'),
        \ ? 'throttle:'.\ : null,
    ]));

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// Registration
Route::post(LaravelLocalization::transRoute('routes.register', \), [RegisteredUserController::class, 'store'])
    ->middleware('guest:'.config('fortify.guard'));

// Password Reset
Route::post(LaravelLocalization::transRoute('routes.forgot-password', \), [PasswordResetLinkController::class, 'store'])
    ->middleware('guest:'.config('fortify.guard'))
    ->name('password.email');

Route::post(LaravelLocalization::transRoute('routes.reset-password', \), [NewPasswordController::class, 'store'])
    ->middleware('guest:'.config('fortify.guard'))
    ->name('password.update');

// Email Verification
Route::middleware('auth:'.config('fortify.guard'))->group(function () use (\) {
    Route::get(LaravelLocalization::transRoute('routes.verify-email', \), [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Verificación de Email - Personalizada para Auto-login
    Route::get(LaravelLocalization::transRoute('routes.verify-email', \) . '/{id}/{hash}', function (\, \) use (\) {
        \ = \App\Models\User::findOrFail(\);
        
        if (! hash_equals((string) \, sha1(\->getEmailForVerification()))) {
            throw new \Illuminate\Auth\Access\AuthorizationException;
        }

        if (\->hasVerifiedEmail()) {
            auth()->login(\);
            \ = LaravelLocalization::transRoute('routes.dashboard', \);
            return redirect()->to(url(\ . '/' . \));
        }

        if (\->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified(\));
        }

        auth()->login(\);
        \ = LaravelLocalization::transRoute('routes.dashboard', \);
        return redirect()->to(url(\ . '/' . \));
    })->middleware(['signed', 'throttle:6,1'])->name(\ . '.verification.verify');
});

// Two-Factor Authentication
Route::middleware('guest:'.config('fortify.guard'))->group(function () {
    \ = config('fortify.limiters.two-factor');

    Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
        ->name('two-factor.login')
        ->middleware(array_filter([
            \ ? 'throttle:'.\ : null,
        ]));
});

// Password Confirmation & 2FA Management
Route::middleware('auth:'.config('fortify.guard'))->group(function () {
    Route::get('/confirm-password', function () {
        return view('auth.confirm-password');
    })->name('password.confirm');

    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);

    // 2FA Management endpoints
    if (Features::enabled(Features::twoFactorAuthentication())) {
        Route::post('/user/two-factor-authentication', [\Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController::class, 'store'])
            ->name('two-factor.enable');

        Route::post('/user/confirmed-two-factor-authentication', [\Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController::class, 'store'])
            ->name('two-factor.confirm');

        Route::delete('/user/two-factor-authentication', [\Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController::class, 'destroy'])
            ->name('two-factor.disable');

        Route::get('/user/two-factor-qr-code', [\Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController::class, 'show'])
            ->name('two-factor.qr-code');

        Route::get('/user/two-factor-secret-key', [\Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController::class, 'show'])
            ->name('two-factor.secret-key');

        Route::get('/user/two-factor-recovery-codes', [\Laravel\Fortify\Http\Controllers\RecoveryCodeController::class, 'index'])
            ->name('two-factor.recovery-codes');

        Route::post('/user/two-factor-recovery-codes', [\Laravel\Fortify\Http\Controllers\RecoveryCodeController::class, 'store']);
    }

    // Profile Information
    if (Features::enabled(Features::updateProfileInformation())) {
        Route::put('/user/profile-information', [\Laravel\Fortify\Http\Controllers\ProfileInformationController::class, 'update'])
            ->name('user-profile-information.update');
    }

    // Passwords
    if (Features::enabled(Features::updatePasswords())) {
        Route::put('/user/password', [\Laravel\Fortify\Http\Controllers\PasswordController::class, 'update'])
            ->name('user-password.update');
    }
});
