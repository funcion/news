<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterVerificationMail;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    /**
     * Handle incoming subscription request (Double Opt-In)
     */
    public function subscribe(Request $request)
    {
        // 1. Honeypot anti-spam verification
        if (!empty($request->input('website_hp'))) {
            return response()->json([
                'success' => true,
                'message' => __('Subscription request processed.'),
            ]);
        }

        // 2. Validate email and locale
        $validated = $request->validate([
            'email'  => ['required', 'string', 'email:rfc', 'max:255'],
            'source' => ['nullable', 'string', 'max:50'],
            'locale' => ['nullable', 'string', 'max:10'],
        ]);

        $email = strtolower(trim($validated['email']));
        $locale = $request->input('locale') ?: app()->getLocale() ?: 'es';
        $source = $validated['source'] ?? 'footer';

        app()->setLocale($locale);

        $subscriber = Subscriber::where('email', $email)->first();

        if ($subscriber) {
            // Already verified and active
            if ($subscriber->isActive()) {
                $msg = $locale === 'es'
                    ? '¡Ya estás suscrito a nuestro boletín de noticias!'
                    : 'You are already actively subscribed to our newsletter!';

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'already_active' => true, 'message' => $msg]);
                }
                return back()->with('success', $msg);
            }

            // If was unsubscribed, clear unsubscribed_at
            if ($subscriber->unsubscribed_at) {
                $subscriber->unsubscribed_at = null;
            }

            // Regenerate token for verification
            $subscriber->token = Str::random(64);
            $subscriber->locale = $locale;
            $subscriber->source = $source;
            $subscriber->ip_address = $request->ip();
            $subscriber->user_agent = $request->userAgent();
            $subscriber->save();
        } else {
            // Create new pending subscriber
            $subscriber = Subscriber::create([
                'email'       => $email,
                'locale'      => $locale,
                'token'       => Str::random(64),
                'verified_at' => null,
                'source'      => $source,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        }

        // Dispatch verification email with explicit locale
        try {
            Mail::to($subscriber->email)->queue(new NewsletterVerificationMail($subscriber));
        } catch (\Throwable $e) {
            report($e);
        }

        $successMsg = $locale === 'es'
            ? 'Te hemos enviado un enlace de confirmación a tu correo. Por favor revísalo para activar tu suscripción.'
            : 'We sent a verification link to your email. Please check your inbox to activate your subscription.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
            ]);
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Verify subscriber email via double opt-in link
     */
    public function verify(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->first();

        if (!$subscriber) {
            $isEs = app()->getLocale() === 'es';
            $errorMsg = $isEs
                ? 'El enlace de verificación no es válido o ya ha expirado.'
                : 'The verification link is invalid or has already expired.';

            $redirectUrl = $isEs ? url('/es') : url('/');
            return redirect($redirectUrl)->with('error', $errorMsg);
        }

        $subscriber->verified_at = now();
        $subscriber->unsubscribed_at = null;
        $subscriber->save();

        $isEs = $subscriber->locale === 'es';
        $successMsg = $isEs
            ? '🎉 ¡Ya estás suscrito oficialmente a Glodaxia Magazine! Bienvenido.'
            : '🎉 You are now officially subscribed to Glodaxia Magazine! Welcome.';

        $redirectUrl = $isEs ? url('/es') : url('/');
        return redirect($redirectUrl)->with('success', $successMsg);
    }

    /**
     * Unsubscribe from newsletter (GDPR 1-Click requirement)
     */
    public function unsubscribe(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->first();

        $isEs = true;
        if ($subscriber) {
            $isEs = $subscriber->locale === 'es';
            $subscriber->unsubscribed_at = now();
            $subscriber->save();
        }

        $msg = $isEs
            ? 'Te has dado de baja de nuestro boletín correctamente.'
            : 'You have been successfully unsubscribed from our newsletter.';

        $redirectUrl = $isEs ? url('/es') : url('/');
        return redirect($redirectUrl)->with('info', $msg);
    }
}