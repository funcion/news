<div class="max-w-md mx-auto py-8 sm:py-12 px-4">
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-10 shadow-2xl shadow-cyan-500/5">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 text-white shadow-lg shadow-cyan-500/25 mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                {{ __('ui.auth_forgot_password_title') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-2">
                {{ __('ui.auth_forgot_password_subtitle') }}
            </p>
        </div>

        @if ($status)
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm font-medium flex items-center gap-3">
                <span>✓</span>
                <span>{{ $status }}</span>
            </div>
        @endif

        <form wire:submit="sendResetLink" class="space-y-4">
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                    {{ __('ui.auth_email_label') }}
                </label>
                <input wire:model.defer="email"
                       type="email"
                       id="email"
                       required
                       autofocus
                       placeholder="you@example.com"
                       class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm transition">
                @error('email')
                    <p class="text-rose-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled" class="w-full py-3.5 px-6 rounded-xl bg-cyan-600 hover:bg-cyan-500 active:bg-cyan-700 dark:bg-cyan-500 dark:hover:bg-cyan-400 dark:active:bg-cyan-600 text-white dark:text-slate-950 font-extrabold text-sm tracking-wide shadow-lg shadow-cyan-600/30 dark:shadow-cyan-500/25 hover:shadow-xl hover:shadow-cyan-600/40 dark:hover:shadow-cyan-500/35 border border-cyan-500/30 dark:border-cyan-400/40 transition-all duration-200 ease-out flex items-center justify-center gap-2.5 cursor-pointer select-none active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed mt-4"><span wire:loading.remove>{{ __('ui.auth_send_reset_link_button') }}</span><span wire:loading class="flex items-center gap-2"><svg class="animate-spin h-4 w-4 text-white dark:text-slate-950" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>{{ __('ui.auth_processing') }}</span></span></button>
        </form>

        <div class="text-center mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
            <a href="{{ LaravelLocalization::localizeUrl('/login') }}" class="font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                ← {{ __('ui.auth_back_to_login') }}
            </a>
        </div>
    </div>
</div>