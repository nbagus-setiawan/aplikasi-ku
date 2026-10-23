@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center justify-between gap-2 w-full text-xs">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center gap-1.5 px-3 py-2 font-bold text-gray-400 bg-gray-100 border border-gray-200 rounded-xl cursor-not-allowed select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                <span>Sebelumnya</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1.5 px-3 py-2 font-bold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-xl shadow-2xs transition active:scale-95">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                <span>Sebelumnya</span>
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1.5 px-3 py-2 font-bold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-xl shadow-2xs transition active:scale-95">
                <span>Berikutnya</span>
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-2 font-bold text-gray-400 bg-gray-100 border border-gray-200 rounded-xl cursor-not-allowed select-none">
                <span>Berikutnya</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </span>
        @endif
    </nav>
@endif
