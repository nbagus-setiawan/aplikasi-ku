<x-app-layout>
    @section('title', 'Surat Keluar')

    <div class="space-y-6 pb-12">
        <!-- Header Judul & Tombol Aksi -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                    <span>Administrasi Surat</span>
                    <span>/</span>
                    <span class="text-emerald-800 font-semibold">Surat Keluar</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Arsip & Registrasi Surat Keluar</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola pembuatan surat resmi pondok, nomor otomatis, tanda tangan digital, dan berkas verifikasi QR.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Unduh Buku Induk Surat Keluar (CSV) -->
                <a href="{{ route('admin.surat-keluar.export', request()->query()) }}"
                   class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs sm:text-sm font-bold rounded-xl border border-emerald-300 shadow-xs transition active:scale-95"
                   title="Unduh Buku Induk Surat Keluar (CSV)">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Unduh CSV</span>
                </a>

                <a href="{{ route('admin.surat-keluar.create') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-emerald-700/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Upload / Buat Surat Keluar</span>
                </a>
            </div>
        </div>

        <!-- 4 Kartu Ringkasan Administrasi Surat Keluar -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Total Surat Keluar -->
            <div class="bg-white rounded-2xl border border-gray-200/90 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Total Surat</span>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 mt-0.5">{{ $statistik['total'] ?? 0 }}</div>
                    <span class="text-[11px] text-gray-500 font-medium">Dokumen keluar</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200/60 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </div>
            </div>

            <!-- Draft -->
            <div class="bg-white rounded-2xl border border-amber-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Draft</span>
                    <div class="text-2xl sm:text-3xl font-black text-amber-950 mt-0.5">{{ $statistik['draft'] ?? 0 }}</div>
                    <span class="text-[11px] text-amber-800 font-medium">Belum final</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 border border-amber-300 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
            </div>

            <!-- Final (Terkunci & Ber-QR) -->
            <div class="bg-white rounded-2xl border border-emerald-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Final (Terkunci)</span>
                    <div class="text-2xl sm:text-3xl font-black text-emerald-950 mt-0.5">{{ $statistik['final'] ?? 0 }}</div>
                    <span class="text-[11px] text-emerald-800 font-medium">Sah ber-QR Code</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>

            <!-- Terkirim -->
            <div class="bg-white rounded-2xl border border-blue-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Terkirim</span>
                    <div class="text-2xl sm:text-3xl font-black text-blue-950 mt-0.5">{{ $statistik['terkirim'] ?? 0 }}</div>
                    <span class="text-[11px] text-blue-800 font-medium">Sudah disampaikan</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filter & Pencarian Lengkap (Live & Collapsible) -->
        <div x-data="liveFilter({
            hasActiveFilters: {{ request()->hasAny(['kategori_id', 'status', 'tgl_mulai', 'tgl_selesai']) ? 'true' : 'false' }},
            activeCount: {{ (request('kategori_id') ? 1 : 0) + (request('status') ? 1 : 0) + (request('tgl_mulai') ? 1 : 0) + (request('tgl_selesai') ? 1 : 0) }}
        })" class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-4 sm:p-5 space-y-4">
                <form x-ref="filterForm" method="GET" action="{{ route('admin.surat-keluar.index') }}" @submit.prevent="applyLive()">
                    <!-- Baris Pencarian Utama: Global Search + Filter Lanjutan + Reset -->
                    <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
                        <div class="relative flex-1">
                            <input type="text" name="q" value="{{ request('q') }}"
                                   placeholder="Cari perihal, tujuan, nomor surat keluar, atau penandatangan..."
                                   @input="applyLive()"
                                   class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 pl-10 pr-10 py-2.5 shadow-2xs" />
                            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <div x-show="loading" x-cloak class="absolute right-3.5 top-3">
                                <svg class="animate-spin w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Tombol Toggle Filter Lanjutan -->
                        <button type="button" @click="showFilters = !showFilters"
                                :class="showFilters || activeCount > 0 ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border font-bold text-xs sm:text-sm shadow-2xs transition shrink-0">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span>Filter Lanjutan</span>
                            <template x-if="activeCount > 0">
                                <span class="px-1.5 py-0.5 text-[11px] font-black rounded-full bg-emerald-700 text-white" x-text="activeCount"></span>
                            </template>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Tombol Reset Filter -->
                        <button type="button" @click="resetFilters()"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs sm:text-sm shadow-2xs transition shrink-0"
                                title="Bersihkan Semua Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="hidden sm:inline">Reset</span>
                        </button>
                    </div>

                    <!-- Baris Filter Lanjutan (Kategori, Status, Tanggal) -->
                    <div x-show="showFilters" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3 mt-3 border-t border-gray-100">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Status Dokumen</label>
                            <select name="status" @change="applyLive()" class="w-full text-xs rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2 font-medium">
                                <option value="">Semua Status</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft (Konsep)</option>
                                <option value="final" {{ request('status') === 'final' ? 'selected' : '' }}>Final (Terkunci & Ber-QR)</option>
                                <option value="terkirim" {{ request('status') === 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Kategori Surat</label>
                            <select name="kategori_id" @change="applyLive()" class="w-full text-xs rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2 font-medium">
                                <option value="">Semua Kategori</option>
                                @foreach ($kategoriList as $kat)
                                    <option value="{{ $kat->id }}" {{ (string) request('kategori_id') === (string) $kat->id ? 'selected' : '' }}>
                                        {{ $kat->nama }} ({{ $kat->kode }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Tanggal Mulai</label>
                            <input type="date" name="tgl_mulai" value="{{ request('tgl_mulai') }}" @change="applyLive()"
                                   class="w-full text-xs rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2" />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Tanggal Selesai</label>
                            <input type="date" name="tgl_selesai" value="{{ request('tgl_selesai') }}" @change="applyLive()"
                                   class="w-full text-xs rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2" />
                        </div>
                    </div>
                </form>
            </div>

            <div id="data-container" class="relative space-y-4">
                <!-- Overlay Indikator Saat Memuat Data -->
                <div x-show="loading" x-cloak class="absolute inset-0 bg-white/60 z-10 backdrop-blur-[1px] flex items-center justify-center transition-opacity rounded-2xl">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white shadow-md border border-gray-200 text-xs font-bold text-emerald-800">
                        <svg class="animate-spin w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Menyaring surat keluar...</span>
                    </div>
                </div>

        <!-- TAMPILAN DESKTOP: TABEL -->
        <div class="hidden md:block bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-700">
                    <thead class="bg-gray-50/90 border-b border-gray-200 text-gray-800 font-extrabold uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5 w-16 text-center">No</th>
                            <th class="px-5 py-3.5">Perihal & Tujuan</th>
                            <th class="px-5 py-3.5">No. Surat & Status</th>
                            <th class="px-5 py-3.5">Lampiran</th>
                            <th class="px-5 py-3.5 text-center w-44">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($suratKeluars as $idx => $item)
                            <tr class="hover:bg-gray-50/70 transition">
                                <!-- No -->
                                <td class="px-5 py-4 text-center font-bold text-gray-500">
                                    {{ $suratKeluars->firstItem() + $idx }}
                                </td>

                                <!-- Perihal & Tujuan -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.surat-keluar.show', $item) }}" class="font-extrabold text-gray-900 hover:text-emerald-700 transition">
                                            {{ $item->perihal }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Kepada Yth: <span class="font-semibold text-gray-800">{{ $item->tujuan }}</span>
                                        &bull; {{ $item->tempat_surat ?: 'Putak' }}, {{ $item->formatted_tgl_surat }}
                                    </div>
                                </td>

                                <!-- No. Surat & Status -->
                                <td class="px-5 py-4">
                                    <div class="flex flex-col items-start gap-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-mono font-bold text-emerald-900 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 text-xs">
                                                {{ $item->no_surat }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                                🏷️ {{ $item->kategori?->nama ?? 'Surat' }}
                                            </span>
                                        </div>
                                        <div>
                                            @if ($item->status === 'draft')
                                                <span class="px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 font-bold rounded-full text-[10px]">Draft</span>
                                            @elseif ($item->status === 'final')
                                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold rounded-full text-[10px]">Final (Terkunci)</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 font-bold rounded-full text-[10px]">Terkirim</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Lampiran -->
                                <td class="px-5 py-4">
                                    @php
                                        $lampTeks = $item->lampiran_teks ?: '-';
                                        $adaLamp = ($lampTeks !== '-' && $lampTeks !== '') || !empty($item->file);
                                    @endphp
                                    @if ($adaLamp)
                                        <div class="flex flex-col items-start gap-1">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-blue-50 text-blue-800 border border-blue-200 text-xs font-bold">
                                                📎 {{ $lampTeks !== '-' ? $lampTeks : '1 Berkas' }}
                                            </span>
                                            @if ($item->file)
                                                <a href="{{ asset('storage/' . $item->file) }}" target="_blank" class="text-[11px] text-blue-700 hover:underline font-semibold">
                                                    Lihat Berkas Lampiran &rarr;
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 font-medium">- (Tidak Ada)</span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Lihat / Detail (Biru) -->
                                        <a href="{{ route('admin.surat-keluar.show', $item) }}" title="Lihat Detail & Tanda Tangan"
                                           class="p-2 text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200/60 rounded-xl transition shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        <!-- Buka Dokumen Asli Ber-QR / PDF (Pink Merah Jambu) -->
                                        <a href="{{ route('admin.surat-keluar.cetak', $item) }}" target="_blank" title="Lihat Dokumen Cetak Ber-QR"
                                           class="p-2 text-pink-700 bg-pink-50 hover:bg-pink-100 border border-pink-200/60 rounded-xl transition shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </a>

                                        @if ($item->file_surat_asli)
                                            <!-- Unduh File Word Asli (.docx) (Biru Tua) -->
                                            <a href="{{ route('admin.surat-keluar.cetak', [$item, 'format' => 'docx']) }}" title="Unduh File Word Asli (.docx) Ber-QR"
                                               class="p-2 text-blue-900 bg-blue-100/90 hover:bg-blue-200 border border-blue-300 rounded-xl transition shadow-2xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                        @endif

                                        @if (! $item->isFinal())
                                            <!-- Ubah Data (Kuning) -->
                                            <a href="{{ route('admin.surat-keluar.edit', $item) }}" title="Ubah Data"
                                               class="p-2 text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-xl transition shadow-2xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @endif

                                        <!-- Hapus (Merah) -->
                                        <form method="POST" action="{{ route('admin.surat-keluar.destroy', $item) }}"
                                              data-konfirmasi="Apakah Anda yakin ingin menghapus surat keluar ini?"
                                              data-judul="Hapus Surat Keluar"
                                              data-tombol="Ya, Hapus Surat"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                    class="p-2 text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 rounded-xl transition shadow-2xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        <p class="font-medium text-sm text-gray-500">Belum ada dokumen surat keluar terdaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAMPILAN MOBILE: CARDS -->
        <div class="block md:hidden space-y-3">
            @forelse ($suratKeluars as $item)
                <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 mb-1">
                                @if ($item->status === 'draft')
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 font-bold rounded-full text-[10px]">Draft</span>
                                @elseif ($item->status === 'final')
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold rounded-full text-[10px]">Final (Terkunci)</span>
                                @else
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 font-bold rounded-full text-[10px]">Terkirim</span>
                                @endif
                                <span class="text-[10px] text-gray-500 font-mono">{{ $item->formatted_tgl_surat }}</span>
                            </div>
                            <a href="{{ route('admin.surat-keluar.show', $item) }}" class="font-extrabold text-sm text-gray-900 block leading-tight">
                                {{ $item->perihal }}
                            </a>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400 text-[10px] uppercase font-bold">Jenis Surat:</span>
                            <span class="font-bold text-purple-800 bg-purple-50 px-2 py-0.5 rounded border border-purple-200 text-[11px]">{{ $item->kategori?->nama ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400 text-[10px] uppercase font-bold">No. Surat:</span>
                            <span class="font-mono font-bold text-emerald-900 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-[11px] truncate max-w-[200px]">
                                {{ $item->no_surat }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400 text-[10px] uppercase font-bold">Tujuan:</span>
                            <span class="font-semibold text-gray-800 text-right truncate max-w-[200px]">{{ $item->tujuan }}</span>
                        </div>
                        @if ($item->lampiran_teks)
                            <div class="flex items-center justify-between pt-1 border-t border-gray-200/60">
                                <span class="text-gray-400 text-[10px] uppercase font-bold">Lampiran:</span>
                                <span class="font-medium text-blue-800">📎 {{ $item->lampiran_teks }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Aksi Mobile -->
                    <div class="flex items-center justify-end gap-1.5 pt-2 border-t border-gray-100 flex-wrap">
                        <a href="{{ route('admin.surat-keluar.show', $item) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 rounded-lg transition shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>Detail</span>
                        </a>

                        <a href="{{ route('admin.surat-keluar.cetak', $item) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-pink-700 bg-pink-50 border border-pink-300 hover:bg-pink-100 rounded-lg transition shadow-2xs">
                            <span>Cetak PDF</span>
                        </a>

                        @if ($item->file_surat_asli)
                            <a href="{{ route('admin.surat-keluar.cetak', [$item, 'format' => 'docx']) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-blue-900 bg-blue-100/90 border border-blue-300 hover:bg-blue-200 rounded-lg transition shadow-2xs">
                                <span>Word (.docx)</span>
                            </a>
                        @endif

                        @if (! $item->isFinal())
                            <a href="{{ route('admin.surat-keluar.edit', $item) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-amber-900 bg-amber-100 border border-amber-300 hover:bg-amber-200 rounded-lg transition shadow-2xs">
                                <span>Ubah</span>
                            </a>
                        @endif

                        <form method="POST" action="{{ route('admin.surat-keluar.destroy', $item) }}"
                              data-konfirmasi="Apakah Anda yakin ingin menghapus surat keluar ini?"
                              data-judul="Hapus Surat Keluar"
                              data-tombol="Ya, Hapus Surat"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 border border-rose-300 hover:bg-rose-100 rounded-lg transition shadow-2xs">
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center text-gray-400">
                    <p class="font-medium text-sm text-gray-500">Belum ada dokumen surat keluar terdaftar.</p>
                </div>
            @endforelse
        </div>

        @if ($suratKeluars->hasPages())
            <div class="p-4 bg-white rounded-2xl border border-gray-200 shadow-xs">
                {{ $suratKeluars->links() }}
            </div>
        @endif
            </div>
        </div>
    </div>
</x-app-layout>
