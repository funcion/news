<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth provider.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        // Mock for local development if credentials not provided
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            Log::info('Google OAuth: client_id not set in .env. Mocking local login with reader demo account.');
            $mockUser = User::firstOrCreate(
                ['email' => 'reader.demo@glodaxia.com'],
                [
                    'name'              => ['en' => 'Demo Reader', 'es' => 'Lector Demo'],
                    'google_id'         => 'mock_google_123456',
                    'slug'              => 'demo-reader',
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );
            Auth::login($mockUser, true);
            return redirect(LaravelLocalization::localizeUrl('/'));
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            $avatarUrl = $googleUser->getAvatar();
            $name = $googleUser->getName() ?: 'Glodaxia Reader';

            if ($user) {
                $user->update([
                    'google_id'         => $googleUser->getId(),
                    'avatar_url'        => $user->avatar_url ?: $avatarUrl,
                    'email_verified_at' => $user->email_verified_at ?: now(),
                ]);
            } else {
                $user = User::create([
                    'name'              => ['en' => $name, 'es' => $name],
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'avatar_url'        => $avatarUrl,
                    'slug'              => Str::slug($name) . '-' . Str::lower(Str::random(4)),
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user, true);

            return redirect()->intended(LaravelLocalization::localizeUrl('/'));
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback failed: ' . $e->getMessage());
            return redirect(LaravelLocalization::localizeUrl('/login'))->with('error', __('auth.oauth_failed'));
        }
    }
}