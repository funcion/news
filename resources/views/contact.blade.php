<x-layouts.app :title="$title" :metaDescription="$metaDescription">
    <div class="max-w-6xl mx-auto">
        <!-- Header Section -->
        <div class="mb-10 text-center md:text-left">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 text-xs font-black uppercase tracking-widest border border-cyan-500/20 mb-3">
                <span>✉️ {{ __('ui.contact') }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-3">
                {{ __('ui.contact_title') }}
            </h1>
            <p class="text-slate-600 dark:text-slate-300 text-base md:text-lg max-w-2xl leading-relaxed">
                {{ __('ui.contact_subtitle') }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left: Contact Form (8 cols) -->
            <div class="lg:col-span-8">
                <div x-data="{
                        name: '',
                        email: '',
                        subject: '',
                        message: '',
                        website_hp: '',
                        loading: false,
                        submitted: false,
                        errorMessage: '',
                        errors: {},
                        async submitForm() {
                            this.loading = true;
                            this.errorMessage = '';
                            this.errors = {};
                            try {
                                const res = await axios.post('{{ route('contact.submit') }}', {
                                    name: this.name,
                                    email: this.email,
                                    subject: this.subject,
                                    message: this.message,
                                    website_hp: this.website_hp,
                                    locale: '{{ app()->getLocale() }}'
                                });
                                if (res.data.success) {
                                    this.submitted = true;
                                    this.name = '';
                                    this.email = '';
                                    this.subject = '';
                                    this.message = '';
                                }
                            } catch (err) {
                                if (err.response?.status === 422) {
                                    this.errors = err.response.data.errors || {};
                                } else {
                                    this.errorMessage = err.response?.data?.message || '{{ app()->getLocale() === 'es' ? 'Ocurrió un error al enviar el mensaje. Inténtalo nuevamente.' : 'An error occurred while sending your message. Please try again.' }}';
                                }
                            } finally {
                                this.loading = false;
                            }
                        }
                     }"
                     class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8 md:p-10">

                    <!-- Success State -->
                    <div x-show="submitted" x-cloak role="status" aria-live="polite" class="text-center py-10 space-y-4">
                        <div class="w-16 h-16 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center border border-emerald-500/20 text-3xl">
                            ✓
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">
                            {{ app()->getLocale() === 'es' ? '¡Mensaje Enviado con Éxito!' : 'Message Sent Successfully!' }}
                        </h3>
                        <p class="text-slate-600 dark:text-slate-300 max-w-md mx-auto text-sm leading-relaxed">
                            {{ __('ui.message_sent_success') }}
                        </p>
                        <div class="pt-4">
                            <button @click="submitted = false" type="button" class="px-6 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-sm font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                {{ app()->getLocale() === 'es' ? 'Enviar otro mensaje' : 'Send another message' }}
                            </button>
                        </div>
                    </div>

                    <!-- Form State -->
                    <form x-show="!submitted" @submit.prevent="submitForm()" method="POST" action="{{ route('contact.submit') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Honeypot Anti-Spam (Hidden from real users) -->
                        <input type="text" name="website_hp" x-model="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">

                        <!-- Global Server Error -->
                        <div x-show="errorMessage" x-cloak role="alert" aria-live="assertive" class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-300 text-sm">
                            <span x-text="errorMessage"></span>
                        </div>

                        <!-- Name & Email Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div class="space-y-2">
                                <label for="contact-name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                    {{ __('ui.full_name') }} <span class="text-rose-500">*</span>
                                </label>
                                <input id="contact-name" 
                                       x-model="name" 
                                       name="name" 
                                       type="text" 
                                       required 
                                       placeholder="{{ app()->getLocale() === 'es' ? 'Ej. Alex Morgan' : 'e.g. Alex Morgan' }}"
                                       class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 outline-none focus:border-cyan-500 dark:focus:border-cyan-400 focus:bg-white dark:focus:bg-slate-800 transition-all">
                                <p x-show="errors.name" x-text="errors.name ? errors.name[0] : ''" class="text-xs text-rose-500 font-medium"></p>
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label for="contact-email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                    {{ __('ui.email_address') }} <span class="text-rose-500">*</span>
                                </label>
                                <input id="contact-email" 
                                       x-model="email" 
                                       name="email" 
                                       type="email" 
                                       required 
                                       placeholder="{{ app()->getLocale() === 'es' ? 'tu@correo.com' : 'you@domain.com' }}"
                                       class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 outline-none focus:border-cyan-500 dark:focus:border-cyan-400 focus:bg-white dark:focus:bg-slate-800 transition-all">
                                <p x-show="errors.email" x-text="errors.email ? errors.email[0] : ''" class="text-xs text-rose-500 font-medium"></p>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="space-y-2">
                            <label for="contact-subject" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                {{ __('ui.subject_label') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="contact-subject" 
                                    x-model="subject" 
                                    name="subject" 
                                    required 
                                    class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-cyan-500 dark:focus:border-cyan-400 focus:bg-white dark:focus:bg-slate-800 transition-all">
                                <option value="" disabled selected>{{ __('ui.subject_select') }}</option>
                                <option value="{{ __('ui.subject_editorial') }}">{{ __('ui.subject_editorial') }}</option>
                                <option value="{{ __('ui.subject_advertising') }}">{{ __('ui.subject_advertising') }}</option>
                                <option value="{{ __('ui.subject_technical') }}">{{ __('ui.subject_technical') }}</option>
                                <option value="{{ __('ui.subject_dmca') }}">{{ __('ui.subject_dmca') }}</option>
                                <option value="{{ __('ui.subject_other') }}">{{ __('ui.subject_other') }}</option>
                            </select>
                            <p x-show="errors.subject" x-text="errors.subject ? errors.subject[0] : ''" class="text-xs text-rose-500 font-medium"></p>
                        </div>

                        <!-- Message Body -->
                        <div class="space-y-2">
                            <label for="contact-message" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                {{ __('ui.message_label') }} <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="contact-message" 
                                      x-model="message" 
                                      name="message" 
                                      rows="6" 
                                      required 
                                      placeholder="{{ __('ui.message_placeholder') }}"
                                      class="w-full p-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 outline-none focus:border-cyan-500 dark:focus:border-cyan-400 focus:bg-white dark:focus:bg-slate-800 transition-all leading-relaxed"></textarea>
                            <p x-show="errors.message" x-text="errors.message ? errors.message[0] : ''" class="text-xs text-rose-500 font-medium"></p>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                :disabled="loading" 
                                class="w-full sm:w-auto min-w-[200px] h-12 px-8 rounded-xl bg-cyan-500 hover:bg-cyan-600 active:scale-[0.99] disabled:opacity-50 text-white text-sm font-bold tracking-wide shadow-md shadow-cyan-500/20 transition-all flex items-center justify-center gap-2">
                            <span x-show="!loading" class="flex items-center gap-2">
                                <span>{{ __('ui.send_message') }}</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>{{ __('ui.sending') }}</span>
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Direct Channels & Office Info (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Official Email Card -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-lg border border-cyan-500/20">
                        📬
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ __('ui.official_channel') }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        {{ app()->getLocale() === 'es' ? 'Canal centralizado para cualquier comunicación directa:' : 'Centralized channel for direct communications:' }}
                    </p>
                    <a href="mailto:hi@glodaxia.com" class="inline-block text-base font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                        hi@glodaxia.com
                    </a>
                </div>

                <!-- Response Time -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg border border-blue-500/20">
                        ⚡
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ __('ui.response_time_title') }}
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        {{ __('ui.response_time_desc') }}
                    </p>
                </div>

                <!-- Editorial Policy & DMCA Notice -->
                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/60 p-6 space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        {{ __('ui.editorial_office') }}
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        {{ __('ui.editorial_office_desc') }}
                    </p>
                    <div class="pt-2 border-t border-slate-200 dark:border-slate-700">
                        <a href="{{ route('legal.editorial') }}" class="text-xs font-bold text-cyan-600 dark:text-cyan-400 hover:underline inline-flex items-center gap-1">
                            <span>{{ __('ui.editorial_policy') }}</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>