<x-layouts.app :trendingTags="$trendingTags">
    <x-slot:title>
        {{ __('ui.profile_title') }} | {{ config('app.name') }}
    </x-slot>

    <div class="max-w-4xl mx-auto" x-data="{ activeTab: 'info' }">
        
        <!-- Header Section -->
        <div class="mb-8 pb-6 border-b border-gray-100 dark:border-white/5">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20 mb-3">
                <span>👤 {{ __('ui.my_profile') }}</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-slate-900 dark:text-white leading-tight mb-2">
                {{ __('ui.profile_title') }}
            </h1>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                {{ __('ui.profile_subtitle') }}
            </p>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 sm:gap-4 border-b border-slate-200 dark:border-slate-800 mb-8 overflow-x-auto pb-1">
            <button @click="activeTab = 'info'" 
                    type="button"
                    :class="activeTab === 'info' ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400 bg-cyan-50/50 dark:bg-cyan-950/20' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="px-4 py-2.5 rounded-xl border text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer whitespace-nowrap">
                👤 {{ __('ui.tab_personal_info') }}
            </button>
            <button @click="activeTab = 'security'" 
                    type="button"
                    :class="activeTab === 'security' ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400 bg-cyan-50/50 dark:bg-cyan-950/20' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="px-4 py-2.5 rounded-xl border text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer whitespace-nowrap">
                🔒 {{ __('ui.tab_security') }}
            </button>
            <button @click="activeTab = 'comments'" 
                    type="button"
                    :class="activeTab === 'comments' ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400 bg-cyan-50/50 dark:bg-cyan-950/20' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="px-4 py-2.5 rounded-xl border text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer whitespace-nowrap flex items-center gap-2">
                💬 {{ __('ui.tab_comments') }}
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">{{ $comments->total() }}</span>
            </button>
        </div>

        <!-- TAB 1: Personal Information -->
        <div x-show="activeTab === 'info'" x-cloak class="space-y-8">
            <form method="POST" action="{{ app()->getLocale() === 'es' ? url('/es/perfil/info') : url('/profile/info') }}" enctype="multipart/form-data" class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-xs">
                @csrf
                
                <!-- Avatar Upload Section -->
                <div class="mb-8 pb-8 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center gap-6"
                     x-data="{ 
                         previewUrl: '{{ $user->avatar_url ? asset($user->avatar_url) : '' }}',
                         removeAvatar: false,
                         handleFileSelect(e) {
                             const file = e.target.files[0];
                             if (file) {
                                 this.previewUrl = URL.createObjectURL(file);
                                 this.removeAvatar = false;
                             }
                         }
                     }">
                    
                    <div class="relative group">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-cyan-500 to-blue-600 p-0.5 shadow-md">
                            <div class="w-full h-full rounded-full bg-white dark:bg-slate-900 overflow-hidden flex items-center justify-center">
                                <template x-if="previewUrl && !removeAvatar">
                                    <img :src="previewUrl" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!previewUrl || removeAvatar">
                                    <span class="text-2xl font-black text-cyan-600 dark:text-cyan-400">{{ $user->initials }}</span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 text-center sm:text-left space-y-2">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ __('ui.profile_avatar_label') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">JPG, PNG o WebP. Máximo 2MB.</p>
                        
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 pt-1">
                            <label for="avatar-input" class="px-4 py-2 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-400 border border-cyan-500/20 text-xs font-bold hover:bg-cyan-100 dark:hover:bg-cyan-900/40 transition cursor-pointer">
                                {{ __('ui.profile_change_photo') }}
                                <input id="avatar-input" name="avatar" type="file" accept="image/*" class="sr-only" @change="handleFileSelect($event)">
                            </label>
                            
                            <template x-if="previewUrl && !removeAvatar">
                                <button type="button" @click="removeAvatar = true; previewUrl = ''" class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 text-xs font-semibold transition">
                                    {{ __('ui.profile_remove_photo') }}
                                </button>
                            </template>
                            <input type="hidden" name="remove_avatar" :value="removeAvatar ? '1' : '0'">
                        </div>
                    </div>
                </div>

                <!-- Form Inputs -->
                <div class="space-y-6">
                    <!-- Name -->
                    <div class="space-y-2">
                        <label for="profile-name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            {{ __('ui.full_name') }} <span class="text-rose-500">*</span>
                        </label>
                        <input id="profile-name" name="name" type="text" required value="{{ old('name', $user->name) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 transition">
                        @error('name') <p class="text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="profile-email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            {{ __('ui.email_address') }} <span class="text-rose-500">*</span>
                        </label>
                        <input id="profile-email" name="email" type="email" required value="{{ old('email', $user->email) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 transition">
                        @error('email') <p class="text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Bio -->
                    <div class="space-y-2">
                        <label for="profile-bio" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            {{ __('ui.profile_bio') }}
                        </label>
                        <textarea id="profile-bio" name="bio" rows="3" class="w-full p-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 transition">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio') <p class="text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Save Button -->
                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white text-sm font-bold shadow-md hover:shadow-cyan-500/25 transition cursor-pointer">
                            💾 {{ __('ui.profile_save_changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- TAB 2: Security & Password -->
        <div x-show="activeTab === 'security'" x-cloak class="space-y-8">
            <form method="POST" action="{{ app()->getLocale() === 'es' ? url('/es/perfil/password') : url('/profile/password') }}" class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-xs">
                @csrf
                
                <div class="space-y-6">
                    <!-- Current Password -->
                    <div class="space-y-2">
                        <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            {{ __('ui.current_password') }} <span class="text-rose-500">*</span>
                        </label>
                        <input id="current_password" name="current_password" type="password" required class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 transition">
                        @error('current_password') <p class="text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- New Password -->
                    <div class="space-y-2">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            {{ __('ui.new_password') }} <span class="text-rose-500">*</span>
                        </label>
                        <input id="password" name="password" type="password" required class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 transition">
                        @error('password') <p class="text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            {{ __('ui.confirm_new_password') }} <span class="text-rose-500">*</span>
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 transition">
                    </div>

                    <!-- Save Password Button -->
                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white text-sm font-bold shadow-md hover:shadow-cyan-500/25 transition cursor-pointer">
                            🔒 {{ __('ui.password_updated_success') ? __('ui.profile_save_changes') : 'Actualizar Contraseña' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- TAB 3: Comments History -->
        <div x-show="activeTab === 'comments'" x-cloak class="space-y-6">
            <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-xs">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">{{ __('ui.comments_history_title') }}</h3>

                @if($comments->isEmpty())
                    <div class="text-center py-12 px-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                        <div class="w-12 h-12 rounded-full bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 mx-auto flex items-center justify-center text-2xl mb-3">
                            💬
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto">
                            {{ __('ui.comments_empty') }}
                        </p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($comments as $comment)
                            <div class="p-4 sm:p-5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800 transition-all hover:border-cyan-500/30">
                                <div class="flex items-center justify-between gap-4 mb-2">
                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                        {{ $comment->created_at->translatedFormat('d M Y · H:i') }}
                                    </span>
                                    
                                    @if($comment->status === 'approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            ✓ {{ __('ui.comment_status_approved') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                            ⏳ {{ __('ui.comment_status_pending') }}
                                        </span>
                                    @endif
                                </div>

                                @if($comment->article)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">
                                        {{ __('ui.comment_on') }}: 
                                        <a href="{{ $comment->article->url }}" class="font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                                            {{ $comment->article->title }}
                                        </a>
                                    </p>
                                @endif

                                <p class="text-sm text-slate-800 dark:text-slate-200 leading-relaxed font-normal bg-white dark:bg-slate-900/60 p-3 rounded-lg border border-slate-200/50 dark:border-slate-800">
                                    {{ $comment->content }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $comments->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Account Deletion & Privacy Notice Section (GDPR Compliance) -->
        <div class="mt-12 p-6 sm:p-8 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-800/80">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                <div class="space-y-1.5 max-w-xl">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>🛡️</span>
                        <span>{{ __('ui.delete_account_notice_title') }}</span>
                    </h4>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ __('ui.delete_account_notice_desc') }}
                    </p>
                </div>
                
                <a href="mailto:soporte@glodaxia.com?subject={{ urlencode('Solicitud de Eliminación de Cuenta - ' . $user->email) }}&body={{ urlencode('Hola equipo de Glodaxia, solicito la eliminación definitiva de mi cuenta y mis datos asociados a este correo electrónico: ' . $user->email) }}" 
                   class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-rose-500/30 bg-rose-500/10 hover:bg-rose-500/20 text-rose-700 dark:text-rose-400 text-xs font-bold transition cursor-pointer">
                    <span>✉️</span>
                    <span>{{ __('ui.delete_account_btn') }}</span>
                </a>
            </div>
        </div>

    </div>
</x-layouts.app>