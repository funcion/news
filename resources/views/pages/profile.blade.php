<x-layouts.app :trendingTags="$trendingTags">
    <x-slot:metaDescription>{{ __('ui.my_profile') }} - {{ auth()->user()->name }} en {{ config('app.name', 'Glodaxia') }}.</x-slot>
    <x-slot:robots>noindex, nofollow</x-slot>
    <x-slot:title>
        {{ __('ui.profile_title') }} | {{ config('app.name') }}
    </x-slot>

    <div class="max-w-4xl mx-auto" x-data="{ activeTab: 'info' }">
        <!-- Accessible Breadcrumbs (ADA / WCAG Compliant) -->
        <nav aria-label="{{ __('ui.breadcrumbs') }}" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 mb-8">
            <a href="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500 rounded">{{ __('ui.home') }}</a>
            <svg class="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">{{ __('ui.my_profile') }}</span>
        </nav>

        <!-- Header Section -->
        <div class="mb-8 pb-6 border-b border-gray-100 dark:border-white/5">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20 mb-3">
                <svg aria-hidden="true" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>{{ __('ui.my_profile') }}</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-slate-900 dark:text-white leading-tight mb-2">
                {{ __('ui.profile_title') }}
            </h1>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                {{ __('ui.profile_subtitle') }}
            </p>
        </div>

        <!-- Navigation Tabs (ADA / WCAG 2.1 AA Tablist Pattern) -->
        <div role="tablist" aria-label="{{ __('ui.profile_title') }}" class="flex items-center gap-2 sm:gap-4 border-b border-slate-200 dark:border-slate-800 mb-8 overflow-x-auto pb-1">
            <button @click="activeTab = 'info'" 
                    role="tab"
                    id="tab-info"
                    aria-controls="panel-info"
                    :aria-selected="activeTab === 'info' ? 'true' : 'false'"
                    type="button"
                    :class="activeTab === 'info' ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400 bg-cyan-50/50 dark:bg-cyan-950/20' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="px-4 py-2.5 rounded-xl border text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer whitespace-nowrap inline-flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500">
                <svg aria-hidden="true" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>{{ __('ui.tab_personal_info') }}</span>
            </button>

            <button @click="activeTab = 'security'" 
                    role="tab"
                    id="tab-security"
                    aria-controls="panel-security"
                    :aria-selected="activeTab === 'security' ? 'true' : 'false'"
                    type="button"
                    :class="activeTab === 'security' ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400 bg-cyan-50/50 dark:bg-cyan-950/20' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="px-4 py-2.5 rounded-xl border text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer whitespace-nowrap inline-flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500">
                <svg aria-hidden="true" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>{{ __('ui.tab_security') }}</span>
            </button>

            <button @click="activeTab = 'comments'" 
                    role="tab"
                    id="tab-comments"
                    aria-controls="panel-comments"
                    :aria-selected="activeTab === 'comments' ? 'true' : 'false'"
                    type="button"
                    :class="activeTab === 'comments' ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400 bg-cyan-50/50 dark:bg-cyan-950/20' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="px-4 py-2.5 rounded-xl border text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer whitespace-nowrap inline-flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500">
                <svg aria-hidden="true" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>{{ __('ui.tab_comments') }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">{{ $comments->total() }}</span>
            </button>
        </div>

        <!-- TAB 1: Personal Information Panel -->
        <div id="panel-info" role="tabpanel" aria-labelledby="tab-info" x-show="activeTab === 'info'" x-cloak class="space-y-8 focus:outline-none" tabindex="-1">
            <form method="POST" action="{{ app()->getLocale() === 'es' ? url('/es/perfil/info') : url('/profile/info') }}" enctype="multipart/form-data" class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-xs">
                @csrf
                
                <!-- Avatar Upload Section (Fixed Explicit Dimensions 96x96px) -->
                <div class="mb-8 pb-8 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center gap-6"
                     x-data="{ 
                         previewUrl: '{{ $user->avatar_url ?? '' }}',
                         removeAvatar: false,
                         handleFileSelect(e) {
                             const file = e.target.files[0];
                             if (file) {
                                 this.previewUrl = URL.createObjectURL(file);
                                 this.removeAvatar = false;
                             }
                         }
                     }">
                    
                    <!-- Fixed Dimensions Avatar Frame (Primary Blue + White Initials in Light & Dark Mode) -->
                    <div class="relative shrink-0" style="width: 96px; height: 96px; min-width: 96px; min-height: 96px; max-width: 96px; max-height: 96px;">
                        <div class="w-full h-full rounded-full bg-cyan-600 text-white flex items-center justify-center shadow-lg overflow-hidden select-none" style="width: 96px; height: 96px;">
                            <template x-if="previewUrl && !removeAvatar">
                                <img :src="previewUrl" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-full" style="width: 96px; height: 96px; object-fit: cover;">
                            </template>
                            <template x-if="!previewUrl || removeAvatar">
                                <span class="text-3xl font-black text-white leading-none tracking-widest select-none drop-shadow-xs">{{ $user->initials }}</span>
                            </template>
                        </div>
                    </div>

                    <div class="flex-1 text-center sm:text-left space-y-2">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ __('ui.profile_avatar_label') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">JPG, PNG o WebP. Máximo 2MB.</p>
                        
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 pt-1">
                            <label for="avatar-input" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-400 border border-cyan-500/20 text-xs font-bold hover:bg-cyan-100 dark:hover:bg-cyan-900/40 transition cursor-pointer focus-within:ring-2 focus-within:ring-cyan-500">
                                <svg aria-hidden="true" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ __('ui.profile_change_photo') }}</span>
                                <input id="avatar-input" name="avatar" type="file" accept="image/*" class="sr-only" @change="handleFileSelect($event)">
                            </label>
                            
                            <template x-if="previewUrl && !removeAvatar">
                                <button type="button" @click="removeAvatar = true; previewUrl = ''" class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 text-xs font-semibold transition cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500">
                                    <svg aria-hidden="true" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span>{{ __('ui.profile_remove_photo') }}</span>
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
                            {{ __('ui.full_name') }} <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        <input id="profile-name" name="name" type="text" required aria-required="true" value="{{ old('name', $user->name) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 transition focus-visible:ring-2 focus-visible:ring-cyan-500">
                        @error('name') <p role="alert" class="text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="profile-email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            {{ __('ui.email_address') }} <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        <input id="profile-email" name="email" type="email" required aria-required="true" value="{{ old('email', $user->email) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 transition focus-visible:ring-2 focus-visible:ring-cyan-500">
                        @error('email') <p role="alert" class="text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Bio -->
                    <div class="space-y-2">
                        <label for="profile-bio" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            {{ __('ui.profile_bio') }}
                        </label>
                        <textarea id="profile-bio" name="bio" rows="3" class="w-full p-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 transition focus-visible:ring-2 focus-visible:ring-cyan-500">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio') <p role="alert" class="text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Save Button -->
                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white text-sm font-bold shadow-md hover:shadow-cyan-500/25 transition cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900">
                            <svg aria-hidden="true" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            <span>{{ __('ui.profile_save_changes') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- TAB 2: Security & Password Panel -->
        <div id="panel-security" role="tabpanel" aria-labelledby="tab-security" x-show="activeTab === 'security'" x-cloak class="space-y-8 focus:outline-none" tabindex="-1">
            <form method="POST" action="{{ app()->getLocale() === 'es' ? url('/es/perfil/password') : url('/profile/password') }}" class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-xs">
                @csrf
                
                <div class="space-y-6">
                    <!-- Current Password -->
                    <div class="space-y-2">
                        <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            {{ __('ui.current_password') }} <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        <input id="current_password" name="current_password" type="password" required aria-required="true" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 transition focus-visible:ring-2 focus-visible:ring-cyan-500">
                        @error('current_password') <p role="alert" class="text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- New Password -->
                    <div class="space-y-2">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            {{ __('ui.new_password') }} <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        <input id="password" name="password" type="password" required aria-required="true" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 transition focus-visible:ring-2 focus-visible:ring-cyan-500">
                        @error('password') <p role="alert" class="text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            {{ __('ui.confirm_new_password') }} <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required aria-required="true" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-cyan-500 focus:bg-white dark:focus:bg-slate-800 transition focus-visible:ring-2 focus-visible:ring-cyan-500">
                    </div>

                    <!-- Save Password Button -->
                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white text-sm font-bold shadow-md hover:shadow-cyan-500/25 transition cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900">
                            <svg aria-hidden="true" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>{{ __('ui.profile_save_changes') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- TAB 3: Comments History Panel -->
        <div id="panel-comments" role="tabpanel" aria-labelledby="tab-comments" x-show="activeTab === 'comments'" x-cloak class="space-y-6 focus:outline-none" tabindex="-1">
            <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-xs">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg aria-hidden="true" class="w-5 h-5 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span>{{ __('ui.comments_history_title') }}</span>
                </h3>

                @if($comments->isEmpty())
                    <div class="text-center py-12 px-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                        <div class="w-12 h-12 rounded-full bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 mx-auto flex items-center justify-center mb-3">
                            <svg aria-hidden="true" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
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

        <!-- Account Deletion & Privacy Notice Section (Generous Top Spacing & Full ADA/WCAG Compliance) -->
        <div class="mt-16 sm:mt-24 pt-8 border-t border-slate-200/80 dark:border-slate-800/80">
            <div class="p-6 sm:p-8 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-800/80">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                    <div class="space-y-1.5 max-w-xl">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg aria-hidden="true" class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>{{ __('ui.delete_account_notice_title') }}</span>
                        </h4>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-normal">
                            {{ __('ui.delete_account_notice_desc') }}
                        </p>
                    </div>
                    
                    <a href="mailto:soporte@glodaxia.com?subject={{ urlencode('Solicitud de Eliminación de Cuenta - ' . $user->email) }}&body={{ urlencode('Hola equipo de Glodaxia, solicito la eliminación definitiva de mi cuenta y mis datos asociados a este correo electrónico: ' . $user->email) }}" 
                       aria-label="{{ __('ui.delete_account_btn') }}"
                       class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-rose-500/30 bg-rose-500/10 hover:bg-rose-500/20 text-rose-700 dark:text-rose-400 text-xs font-bold transition cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500">
                        <svg aria-hidden="true" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ __('ui.delete_account_btn') }}</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>