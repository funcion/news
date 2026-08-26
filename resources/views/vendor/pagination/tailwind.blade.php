@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between py-6">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-not-allowed rounded-xl">
                    {{ app()->getLocale() === 'es' ? 'Anterior' : 'Previous' }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    {{ app()->getLocale() === 'es' ? 'Anterior' : 'Previous' }}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    {{ app()->getLocale() === 'es' ? 'Siguiente' : 'Next' }}
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-not-allowed rounded-xl">
                    {{ app()->getLocale() === 'es' ? 'Siguiente' : 'Next' }}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-5">
                    @if (app()->getLocale() === 'es')
                        Mostrando
                        @if ($paginator->firstItem())
                            <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->firstItem() }}</span>
                            a
                            <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->lastItem() }}</span>
                        @else
                            {{ $paginator->count() }}
                        @endif
                        de
                        <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->total() }}</span>
                        resultados
                    @else
                        Showing
                        @if ($paginator->firstItem())
                            <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->firstItem() }}</span>
                            to
                            <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->lastItem() }}</span>
                        @else
                            {{ $paginator->count() }}
                        @endif
                        of
                        <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->total() }}</span>
                        results
                    @endif
                </p>
            </div>

            <div>
                <span class="inline-flex shadow-xs rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-400 bg-slate-50 dark:bg-slate-900 cursor-not-allowed" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 transition" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-500 bg-slate-50 dark:bg-slate-900 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center px-4 py-2 text-sm font-bold text-cyan-600 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-950/40 border-y border-cyan-500/30 cursor-default">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 transition" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 transition" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-400 bg-slate-50 dark:bg-slate-900 cursor-not-allowed" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif