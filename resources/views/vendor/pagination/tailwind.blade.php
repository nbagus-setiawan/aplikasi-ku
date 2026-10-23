@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
        {{-- Tampilan Layar HP (Mobile) --}}
        <div class="flex items-center justify-between gap-2 sm:hidden w-full">
            {{-- Tombol Sebelumnya --}}
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

            {{-- Indikator Halaman Mobile --}}
            <span class="text-xs text-gray-500 font-medium">
                Hal <strong class="text-gray-900">{{ $paginator->currentPage() }}</strong> / <strong class="text-gray-900">{{ $paginator->lastPage() }}</strong>
            </span>

            {{-- Tombol Berikutnya --}}
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
        </div>

        {{-- Tampilan Layar Desktop & Tablet (sm ke atas) --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between w-full gap-4">
            {{-- Keterangan Total Data --}}
            <div>
                <p class="text-xs text-gray-600">
                    Menampilkan
                    @if ($paginator->firstItem())
                        <span class="font-bold text-gray-900">{{ $paginator->firstItem() }}</span>
                        sampai
                        <span class="font-bold text-gray-900">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    dari
                    <span class="font-bold text-gray-900">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            {{-- Daftar Nomor Halaman --}}
            <div>
                <div class="inline-flex items-center gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" class="p-2 text-gray-300 bg-gray-50 border border-gray-200 rounded-xl cursor-not-allowed select-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="p-2 text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-xl shadow-2xs transition active:scale-95" title="Halaman Sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </a>
                    @endif

                    {{-- Elemen Nomor Halaman --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="px-2.5 py-1.5 text-xs font-bold text-gray-400 select-none">{{ $element }}</span>
                        @endif

                        {{-- Array Tautan Halaman --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="px-3.5 py-1.5 text-xs font-bold text-white bg-emerald-700 border border-emerald-700 rounded-xl shadow-xs select-none">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="px-3.5 py-1.5 text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-xl shadow-2xs transition active:scale-95">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="p-2 text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-xl shadow-2xs transition active:scale-95" title="Halaman Berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    @else
                        <span aria-disabled="true" class="p-2 text-gray-300 bg-gray-50 border border-gray-200 rounded-xl cursor-not-allowed select-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </nav>
@endif
