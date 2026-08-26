<section class="pt-10 border-t border-slate-200 dark:border-slate-800" id="comments" style="margin-top: 50px !important;">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-3">
            <span class="w-1.5 h-6 bg-cyan-500 rounded-full"></span>
            <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                {{ __('ui.comments_title') }}
            </h3>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                {{ $totalCount }}
            </span>
        </div>
    </div>

    <!-- Flash / Feedback Message -->
    @if ($feedbackMessage)
        <div class="mb-6 p-4 rounded-2xl {{ $feedbackType === 'success' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/10 border-rose-500/20 text-rose-700 dark:text-rose-300' }} border text-xs font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span>{{ $feedbackType === 'success' ? '✓' : '⚠️' }}</span>
                <span>{{ $feedbackMessage }}</span>
            </div>
            <button wire:click="$set('feedbackMessage', null)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm font-bold">×</button>
        </div>
    @endif

    <!-- New Comment Box -->
    <div class="mb-10 p-5 rounded-3xl bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 backdrop-blur-sm shadow-sm">
        @auth
            <form wire:submit="postComment">
                <!-- Honeypot -->
                <input type="text" wire:model="honeypot" class="hidden" tabindex="-1" autocomplete="off">

                <div class="flex items-start gap-3.5">
                    <!-- User Avatar -->
                    <div style="width: 36px; height: 36px; min-width: 36px; min-height: 36px;"
                         class="w-9 h-9 rounded-full bg-slate-200/70 dark:bg-slate-800 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold tracking-normal overflow-hidden flex-shrink-0 select-none">
                        @if (auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover rounded-full">
                        @else
                            <span class="leading-none text-center">{{ auth()->user()->initials }}</span>
                        @endif
                    </div>

                    <!-- Text Area Container -->
                    <div class="flex-1 min-w-0">
                        <textarea wire:model.defer="newComment"
                                  rows="3"
                                  placeholder="{{ __('ui.leave_a_comment') }}"
                                  required
                                  class="w-full px-4 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 text-sm font-medium transition resize-y shadow-xs"></textarea>
                        @error('newComment')
                            <p class="text-rose-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror

                        <div class="mt-3 flex items-center justify-end">
                            <button type="submit"
                                    wire:loading.attr="disabled"
                                    class="py-2.5 px-6 rounded-xl bg-cyan-600 hover:bg-cyan-500 active:bg-cyan-700 dark:bg-cyan-500 dark:hover:bg-cyan-400 text-white dark:text-slate-950 font-extrabold text-xs uppercase tracking-wider shadow-sm transition flex items-center gap-2 cursor-pointer disabled:opacity-50">
                                <span wire:loading.remove>{{ __('ui.post_comment') }}</span>
                                <span wire:loading class="flex items-center gap-1.5">
                                    <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span>{{ __('ui.auth_processing') }}</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @else
            <!-- Guest Call to Action -->
            <div class="text-center py-6 px-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 max-w-sm mx-auto mb-4">
                    {{ __('ui.login_to_join_discussion') }}
                </p>
                <div class="flex items-center justify-center gap-3 flex-wrap">
                    <a href="{{ route('auth.google') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-xs transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.4 9 5 12 5z"/>
                            <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
                            <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3s.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 10.8 0 12s.7 2.3 1.9 4.7l3.7-1.9z"/>
                            <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.4-6.4-5.2L1.9 16C3.7 19.7 7.5 23 12 23z"/>
                        </svg>
                        <span>{{ __('ui.auth_continue_with_google') }}</span>
                    </a>
                    <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeUrl('/login') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 dark:bg-cyan-500 dark:hover:bg-cyan-400 text-white dark:text-slate-950 text-xs font-extrabold uppercase tracking-wide transition shadow-xs">
                        <span>{{ __('ui.auth_sign_in_button') }}</span>
                    </a>
                </div>
            </div>
        @endauth
    </div>

    <!-- Comments List -->
    <div class="space-y-6">
        @forelse($comments as $comment)
            <div class="p-5 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs transition" id="comment-{{ $comment->id }}">
                
                <!-- Author Header -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div style="width: 36px; height: 36px; min-width: 36px; min-height: 36px;"
                             class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-xs font-bold tracking-normal overflow-hidden flex-shrink-0 select-none">
                            @if ($comment->user->avatar_url)
                                <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" class="w-full h-full object-cover rounded-full">
                            @else
                                <span class="leading-none text-center">{{ $comment->user->initials }}</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $comment->user->name }}</p>
                            <p class="text-[10px] font-medium text-slate-400 dark:text-slate-500">{{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->slug === 'admin' || auth()->user()->id === 1))
                        <button wire:click="deleteComment({{ $comment->id }})"
                                wire:confirm="{{ __('ui.delete_comment_confirm') }}"
                                class="text-slate-400 hover:text-rose-500 transition text-xs p-1"
                                title="Delete">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    @endif
                </div>

                <!-- Comment Body -->
                <div class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed pl-12 whitespace-pre-line mb-3">
                    {{ $comment->content }}
                </div>

                <!-- Actions Bar: Like & Reply -->
                <div class="flex items-center gap-4 pl-12 pt-1 border-t border-slate-100 dark:border-slate-800/60">
                    <!-- Like Button -->
                    <button wire:click="toggleLike({{ $comment->id }})"
                            class="inline-flex items-center gap-1.5 text-xs font-bold transition {{ $comment->isLikedBy(auth()->user()) ? 'text-rose-500' : 'text-slate-500 dark:text-slate-400 hover:text-rose-500' }}">
                        <svg class="w-4 h-4 {{ $comment->isLikedBy(auth()->user()) ? 'fill-rose-500 stroke-rose-500' : 'fill-none stroke-currentColor' }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span>{{ $comment->likes_count }}</span>
                    </button>

                    <!-- Reply Button -->
                    <button wire:click="startReply({{ $comment->id }})"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-cyan-500 dark:hover:text-cyan-400 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        <span>{{ __('ui.reply') }}</span>
                    </button>
                </div>

                <!-- Reply Form -->
                @if($replyingTo === $comment->id)
                    <div class="mt-4 pl-12 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <form wire:submit="postReply({{ $comment->id }})" class="space-y-2">
                            <textarea wire:model.defer="replyContent"
                                      rows="2"
                                      placeholder="{{ __('ui.replying_to') }} {{ $comment->user->name }}..."
                                      required
                                      class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 text-xs font-medium transition"></textarea>
                            @error('replyContent')
                                <p class="text-rose-500 text-[11px] font-medium">{{ $message }}</p>
                            @enderror
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" wire:click="cancelReply" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 text-[11px] font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                                    {{ __('ui.cancel') }}
                                </button>
                                <button type="submit" class="px-4 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-[11px] uppercase tracking-wide">
                                    {{ __('ui.post_reply') }}
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Nested Replies List -->
                @if($comment->replies->isNotEmpty())
                    <div class="mt-4 pl-8 sm:pl-12 space-y-3.5 border-l-2 border-slate-100 dark:border-slate-800 ml-4 sm:ml-6">
                        @foreach($comment->replies as $reply)
                            <div class="p-3.5 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60" id="reply-{{ $reply->id }}">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2.5">
                                        <div style="width: 28px; height: 28px; min-width: 28px; min-height: 28px;"
                                             class="w-7 h-7 rounded-full bg-slate-200/80 dark:bg-slate-700 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-[10px] font-bold overflow-hidden flex-shrink-0">
                                            @if ($reply->user->avatar_url)
                                                <img src="{{ $reply->user->avatar_url }}" alt="{{ $reply->user->name }}" class="w-full h-full object-cover rounded-full">
                                            @else
                                                <span class="leading-none">{{ $reply->user->initials }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-bold text-slate-900 dark:text-white">{{ $reply->user->name }}</p>
                                            <p class="text-[9px] font-medium text-slate-400">{{ $reply->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    @if(auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->slug === 'admin' || auth()->user()->id === 1))
                                        <button wire:click="deleteComment({{ $reply->id }})"
                                                wire:confirm="{{ __('ui.delete_comment_confirm') }}"
                                                class="text-slate-400 hover:text-rose-500 transition text-[11px]">
                                            ×
                                        </button>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-700 dark:text-slate-300 pl-9 whitespace-pre-line">
                                    {{ $reply->content }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        @empty
            <div class="text-center py-10 px-4 rounded-3xl border border-dashed border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-500">
                <p class="text-xs font-semibold">{{ __('ui.no_comments_yet') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Load More Comments Button -->
    @if($hasMore)
        <div class="mt-8 text-center">
            <button wire:click="loadMore"
                    class="py-2.5 px-6 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold transition">
                {{ __('ui.load_more_comments') }}
            </button>
        </div>
    @endif

</section>