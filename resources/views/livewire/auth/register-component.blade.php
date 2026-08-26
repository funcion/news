<div class="max-w-md mx-auto py-8 sm:py-12 px-4">
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-10 shadow-2xl shadow-cyan-500/5">
        
        @if ($registeredSuccess)
            <!-- Verification Pending State -->
            <div class="text-center py-4">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20 shadow-lg shadow-cyan-500/10 mb-5 animate-pulse">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                
                <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-2">
                    {{ __('ui.auth_verify_email_sent_title') }}
                </h2>
                
                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6">
                    {{ __('ui.auth_verify_email_sent_desc') }}
                    <span class="block font-bold text-cyan-600 dark:text-cyan-400 mt-1">{{ $registeredEmail }}</span>
                </p>

                <div class="p-4 rounded-2xl bg-cyan-500/5 dark:bg-cyan-500/10 border border-cyan-500/20 text-xs text-slate-600 dark:text-slate-300 text-left mb-6 space-y-2">
                    <p class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                        <span>💡</span> {{ __('ui.auth_verify_instruction_title') }}
                    </p>
                    <p>{{ __('ui.auth_verify_instruction_text') }}</p>
                </div>

                @if ($resendStatus)
                    <div class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-semibold">
                        ✓ {{ $resendStatus }}
                    </div>
                @endif

                <div class="space-y-3">
                    <button wire:click="resendVerification"
                            wire:loading.attr="disabled"
                            class="w-full py-3 px-4 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                        <span wire:loading.remove>{{ __('ui.auth_resend_verification_button') }}</span>
                        <span wire:loading>{{ __('ui.auth_processing') }}</span>
                    </button>

                    <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeUrl('/login') }}"
                       class="inline-block text-xs font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                        ← {{ __('ui.auth_back_to_login') }}
                    </a>
                </div>
            </div>
        @else
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 text-white shadow-lg shadow-cyan-500/25 mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ __('ui.auth_join_glodaxia') }}
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-2">
                    {{ __('ui.auth_register_subtitle') }}
                </p>
            </div>

            <!-- Social Login: Google -->
            <div class="mb-6">
                <a href="{{ route('auth.google') }}"
                   class="w-full flex items-center justify-center gap-3 px-4 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold text-sm transition-all duration-200 shadow-sm hover:shadow group">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24">
                        <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.4 9 5 12 5z"/>
                        <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
                        <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3s.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 10.8 0 12s.7 2.3 1.9 4.7l3.7-1.9z"/>
                        <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.4-6.4-5.2L1.9 16C3.7 19.7 7.5 23 12 23z"/>
                    </svg>
                    <span>{{ __('ui.auth_continue_with_google') }}</span>
                </a>
            </div>

            <!-- Divider -->
            <div class="relative flex items-center justify-center mb-6">
                <div class="border-t border-slate-200 dark:border-slate-800 w-full"></div>
                <span class="bg-white dark:bg-slate-900 px-3 text-xs uppercase tracking-widest text-slate-400 font-bold relative">
                    {{ __('ui.auth_or_email') }}
                </span>
            </div>

            <!-- Form -->
            <form wire:submit="register" class="space-y-4">
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        {{ __('ui.auth_name_label') }}
                    </label>
                    <input wire:model.defer="name"
                           type="text"
                           id="name"
                           required
                           autofocus
                           placeholder="Ada Lovelace"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm transition">
                    @error('name')
                        <p class="text-rose-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        {{ __('ui.auth_email_label') }}
                    </label>
                    <input wire:model.defer="email"
                           type="email"
                           id="email"
                           required
                           placeholder="you@example.com"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm transition">
                    @error('email')
                        <p class="text-rose-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        {{ __('ui.auth_password_label') }}
                    </label>
                    <input wire:model.defer="password"
                           type="password"
                           id="password"
                           required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm transition">
                    @error('password')
                        <p class="text-rose-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        {{ __('ui.auth_password_confirm_label') }}
                    </label>
                    <input wire:model.defer="password_confirmation"
                           type="password"
                           id="password_confirmation"
                           required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm transition">
                </div>

                <!-- Terms -->
                <div class="pt-1">
                    <label class="inline-flex items-start gap-2 cursor-pointer">
                        <input wire:model="terms"
                               type="checkbox"
                               class="w-4 h-4 mt-0.5 rounded border-slate-300 dark:border-slate-700 text-cyan-500 focus:ring-cyan-500/50">
                        <span class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed select-none">
                            {{ __('ui.auth_agree_terms') }}
                            <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeUrl('/terms') }}" class="text-cyan-600 dark:text-cyan-400 hover:underline" target="_blank">{{ __('ui.auth_terms_link') }}</a>
                            {{ __('ui.and') }}
                            <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeUrl('/privacy') }}" class="text-cyan-600 dark:text-cyan-400 hover:underline" target="_blank">{{ __('ui.auth_privacy_link') }}</a>.
                        </span>
                    </label>
                    @error('terms')
                        <p class="text-rose-500 text-xs font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button (Spacious & Uppercase with High Contrast) -->
                <button type="submit"
                        wire:loading.attr="disabled"
                        style="min-height: 54px; padding: 14px 28px;"
                        class="w-full min-h-[54px] py-4 px-6 rounded-2xl bg-cyan-600 hover:bg-cyan-500 active:bg-cyan-700 dark:bg-cyan-500 dark:hover:bg-cyan-400 dark:active:bg-cyan-600 text-white dark:text-slate-950 font-black text-sm uppercase tracking-widest shadow-lg shadow-cyan-600/25 dark:shadow-cyan-500/20 hover:shadow-xl hover:shadow-cyan-600/35 dark:hover:shadow-cyan-500/30 border border-cyan-500/40 dark:border-cyan-400/50 transition-all duration-200 ease-out flex items-center justify-center gap-3 cursor-pointer select-none active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed mt-4">
                    <svg wire:loading.remove class="w-5 h-5 text-white/90 dark:text-slate-950/90 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span wire:loading.remove>{{ __('ui.auth_create_account_button') }}</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white dark:text-slate-950" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ __('ui.auth_processing') }}</span>
                    </span>
                </button>
            </form>

            <!-- Footer link: Login -->
            <div class="text-center mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
                <span>{{ __('ui.auth_already_have_account') }}</span>
                <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeUrl('/login') }}" class="font-bold text-cyan-600 dark:text-cyan-400 hover:underline ml-1">
                    {{ __('ui.auth_sign_in_link') }}
                </a>
            </div>
        @endif

    </div>
</div>