<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Display the bilingual contact page.
     */
    public function show()
    {
        $locale = app()->getLocale();
        $title = $locale === 'es' ? 'Contacto y Soporte Editorial' : 'Contact Us & Editorial Inquiries';
        $metaDescription = $locale === 'es' 
            ? 'Ponte en contacto con el equipo de Glodaxia. Consultas editoriales, publicidad, soporte y avisos legales en hi@glodaxia.com.'
            : 'Get in touch with the Glodaxia team. Editorial inquiries, sponsorships, technical support, and legal notices at hi@glodaxia.com.';

        return view('contact', compact('title', 'metaDescription'));
    }

    /**
     * Process contact form submission with anti-spam honeypot and email dispatch.
     */
    public function submit(Request $request)
    {
        // 1. Anti-spam honeypot check (Bots fill hidden fields)
        if ($request->filled('website_hp')) {
            Log::info("Contact form spam blocked by honeypot from IP: " . $request->ip());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('ui.message_sent_success'),
                ]);
            }
            return back()->with('success', __('ui.message_sent_success'));
        }

        // 2. Validate user input
        $validated = $request->validate([
            'name'    => 'required|string|min:2|max:100',
            'email'   => 'required|email|max:150',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|min:10|max:5000',
        ], [
            'name.required'    => app()->getLocale() === 'es' ? 'Por favor introduce tu nombre.' : 'Please enter your name.',
            'email.required'   => app()->getLocale() === 'es' ? 'Por favor introduce un correo válido.' : 'Please enter a valid email address.',
            'email.email'      => app()->getLocale() === 'es' ? 'El formato del correo es inválido.' : 'The email address is invalid.',
            'subject.required' => app()->getLocale() === 'es' ? 'Por favor selecciona o indica un asunto.' : 'Please enter a subject.',
            'message.required' => app()->getLocale() === 'es' ? 'Por favor escribe tu mensaje.' : 'Please enter your message.',
            'message.min'      => app()->getLocale() === 'es' ? 'El mensaje debe contener al menos 10 caracteres.' : 'The message must be at least 10 characters.',
        ]);

        // 3. Store contact message in database
        $contactMessage = ContactMessage::create([
            'name'       => strip_tags(trim($validated['name'])),
            'email'      => strtolower(trim($validated['email'])),
            'subject'    => strip_tags(trim($validated['subject'])),
            'message'    => trim($validated['message']),
            'locale'     => app()->getLocale(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_read'    => false,
        ]);

        // 4. Send email notification to official address
        try {
            Mail::to('hi@glodaxia.com')->send(new ContactMessageMail($contactMessage));
        } catch (\Throwable $e) {
            Log::warning("Contact form email dispatch failed: " . $e->getMessage());
        }

        $successMsg = __('ui.message_sent_success');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
            ]);
        }

        return back()->with('success', $successMsg);
    }
}