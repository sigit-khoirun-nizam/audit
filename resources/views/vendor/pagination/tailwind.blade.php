@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center justify-between py-3">

        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-500">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-300 dark:hover:bg-white/5 transition duration-150">
                    Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative ml-3 inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-300 dark:hover:bg-white/5 transition duration-150">
                    Berikutnya
                </a>
            @else
                <span class="relative ml-3 inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-500">
                    Berikutnya
                </span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan
                    @if ($paginator->firstItem())
                        <span class="font-semibold text-gray-800 dark:text-white">{{ $paginator->firstItem() }}</span>
                        sampai
                        <span class="font-semibold text-gray-800 dark:text-white">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    dari
                    <span class="font-semibold text-gray-800 dark:text-white">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-lg shadow-theme-xs -space-x-px">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Sebelumnya">
                            <span class="relative inline-flex items-center rounded-l-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-400 cursor-not-allowed dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-500" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center rounded-l-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/5 transition duration-150" aria-label="Sebelumnya">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative z-10 inline-flex items-center bg-brand-500 border border-brand-500 px-4 py-2 text-sm font-semibold text-white focus:z-20">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-300 dark:hover:bg-white/5 transition duration-150" aria-label="Halaman {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center rounded-r-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/5 transition duration-150" aria-label="Berikutnya">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Berikutnya">
                            <span class="relative inline-flex items-center rounded-r-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-400 cursor-not-allowed dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-500" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
