<x-app-layout>
    @section('title', 'Surat Masuk')

    <div class="space-y-6 pb-12">
        <!-- Header Judul & Tombol Aksi -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                    <span>Administrasi Surat</span>
                    <span>/</span>
                    <span class="text-emerald-800 font-semibold">Surat Masuk</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Buku Agenda Surat Masuk</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola arsip surat masuk, disposisi pimpinan, dan berkas asli dokumen pesantren.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.surat-masuk.create') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-emerald-700/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Upload / Catat Surat Masuk</span>
                </a>
            </div>
        </div>

        <!-- Filter & Pencarian -->
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.surat-masuk.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Cari Surat Masuk</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Cari berdasarkan Perihal, No. Surat, Pengirim, atau No. Agenda..."
                               class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 pl-10 py-2.5 shadow-2xs" />
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit"
                            class="flex-1 sm:flex-none py-2.5 px-5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm rounded-xl transition-all shadow-xs">
                        Cari
                    </button>
                    @if (request()->hasAny(['q', 'kategori_id', 'status_disposisi', 'tgl_mulai', 'tgl_selesai']))
                        <a href="{{ route('admin.surat-masuk.index') }}"
                           class="py-2.5 px-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs sm:text-sm rounded-xl transition" title="Reset Filter">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- TAMPILAN DESKTOP: TABEL -->
        <div class="hidden md:block bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-700">
                    <thead class="bg-gray-50/90 border-b border-gray-200 text-gray-800 font-extrabold uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5 w-16 text-center">No</th>
                            <th class="px-5 py-3.5">Perihal & Pengirim</th>
                            <th class="px-5 py-3.5">No. Surat & Agenda</th>
                            <th class="px-5 py-3.5">Lampiran</th>
                            <th class="px-5 py-3.5 text-center w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($suratMasuks as $idx => $item)
                            <tr class="hover:bg-gray-50/70 transition">
                                <!-- No -->
                                <td class="px-5 py-4 text-center font-bold text-gray-500">
                                    {{ $suratMasuks->firstItem() + $idx }}
                                </td>

                                <!-- Perihal & Pengirim -->
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('admin.surat-masuk.show', $item) }}" class="font-extrabold text-gray-900 hover:text-emerald-700 transition">
                                            {{ $item->perihal }}
                                        </a>
                                        @if ($item->status_disposisi === 'baru')
                                            <span class="px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-300 font-bold rounded-full text-[10px]">Baru</span>
                                        @elseif ($item->status_disposisi === 'diproses')
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-300 font-bold rounded-full text-[10px]">Diproses</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-300 font-bold rounded-full text-[10px]">Selesai</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Pengirim: <span class="font-semibold text-gray-800">{{ $item->pengirim }}</span>
                                        &bull; Tgl: {{ $item->formatted_tgl_surat }} (Diterima: {{ $item->formatted_tgl_diterima }})
                                    </div>
                                    @if ($item->disposisi)
                                        <div class="text-xs text-emerald-900 truncate mt-1.5 bg-emerald-50/80 px-2.5 py-1 rounded-lg border border-emerald-200/60 inline-flex items-center gap-1.5 max-w-lg">
                                            <span class="font-bold shrink-0">Disposisi:</span>
                                            <span class="truncate">{{ $item->disposisi }}</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- No. Surat -->
                                <td class="px-5 py-4">
                                    <div class="flex flex-col items-start gap-1">
                                        <span class="font-mono font-bold text-emerald-900 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 text-xs">
                                            {{ $item->no_surat }}
                                        </span>
                                        <span class="text-[11px] font-bold text-gray-500">
                                            Agenda: #{{ $item->no_agenda }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Lampiran -->
                                <td class="px-5 py-4">
                                    @php
                                        $lampTeks = $item->lampiran_teks ?: '-';
                                        $adaLamp = ($lampTeks !== '-' && $lampTeks !== '') || !empty($item->file_lampiran);
                                    @endphp
                                    @if ($adaLamp)
                                        <div class="flex flex-col items-start gap-1">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-blue-50 text-blue-800 border border-blue-200 text-xs font-bold">
                                                📎 {{ $lampTeks !== '-' ? $lampTeks : '1 Berkas' }}
                                            </span>
                                            @if ($item->file_lampiran)
                                                <a href="{{ asset('storage/' . $item->file_lampiran) }}" target="_blank" class="text-[11px] text-blue-700 hover:underline font-semibold">
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
                                        <!-- Lihat & Disposisi -->
                                        <a href="{{ route('admin.surat-masuk.show', $item) }}" title="Lihat Detail & Disposisi"
                                           class="p-2 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/60 rounded-xl transition shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        @if ($item->file)
                                            <!-- Buka Dokumen Asli -->
                                            <a href="{{ asset('storage/' . $item->file) }}" target="_blank" title="Lihat Dokumen Surat Masuk Asli"
                                               class="p-2 text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 rounded-xl transition shadow-2xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                            </a>
                                        @endif

                                        <!-- Ubah -->
                                        <a href="{{ route('admin.surat-masuk.edit', $item) }}" title="Ubah Data"
                                           class="p-2 text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200/60 rounded-xl transition shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <!-- Hapus -->
                                        <form method="POST" action="{{ route('admin.surat-masuk.destroy', $item) }}"
                                              data-konfirmasi="Apakah Anda yakin ingin menghapus surat masuk ini?"
                                              data-judul="Hapus Surat Masuk"
                                              data-tombol="Ya, Hapus Surat"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                    class="p-2 text-gray-500 hover:text-red-700 hover:bg-red-50 border border-transparent hover:border-red-200/60 rounded-xl transition">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="font-medium text-sm text-gray-500">Tidak ada data surat masuk yang ditemukan.</p>
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
            @forelse ($suratMasuks as $item)
                <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="font-mono text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                    Agenda #{{ $item->no_agenda }}
                                </span>
                                @if ($item->status_disposisi === 'baru')
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-300 font-bold rounded-full text-[10px]">Baru</span>
                                @elseif ($item->status_disposisi === 'diproses')
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-300 font-bold rounded-full text-[10px]">Diproses</span>
                                @else
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-300 font-bold rounded-full text-[10px]">Selesai</span>
                                @endif
                            </div>
                            <a href="{{ route('admin.surat-masuk.show', $item) }}" class="font-extrabold text-sm text-gray-900 block leading-tight">
                                {{ $item->perihal }}
                            </a>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400 text-[10px] uppercase font-bold">No. Surat:</span>
                            <span class="font-mono font-bold text-emerald-900 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-[11px] truncate max-w-[200px]">
                                {{ $item->no_surat }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400 text-[10px] uppercase font-bold">Pengirim:</span>
                            <span class="font-semibold text-gray-800 text-right truncate max-w-[200px]">{{ $item->pengirim }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400 text-[10px] uppercase font-bold">Tanggal:</span>
                            <span class="text-gray-600">{{ $item->formatted_tgl_surat }}</span>
                        </div>
                        @if ($item->lampiran_teks)
                            <div class="flex items-center justify-between pt-1 border-t border-gray-200/60">
                                <span class="text-gray-400 text-[10px] uppercase font-bold">Lampiran:</span>
                                <span class="font-medium text-blue-800">📎 {{ $item->lampiran_teks }}</span>
                            </div>
                        @endif
                    </div>

                    @if ($item->disposisi)
                        <div class="bg-emerald-50/70 p-2.5 rounded-xl border border-emerald-200/60 text-xs text-emerald-950">
                            <span class="font-bold block text-[10px] uppercase tracking-wider text-emerald-800 mb-0.5">Disposisi Pimpinan:</span>
                            <p class="text-xs leading-relaxed">{{ $item->disposisi }}</p>
                        </div>
                    @endif

                    <!-- Tombol Aksi Mobile -->
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.surat-masuk.show', $item) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>Detail</span>
                        </a>

                        @if ($item->file)
                            <a href="{{ asset('storage/' . $item->file) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200 rounded-lg">
                                <span>Surat Asli</span>
                            </a>
                        @endif

                        <a href="{{ route('admin.surat-masuk.edit', $item) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg">
                            <span>Ubah</span>
                        </a>

                        <form method="POST" action="{{ route('admin.surat-masuk.destroy', $item) }}"
                              data-konfirmasi="Apakah Anda yakin ingin menghapus surat masuk ini?"
                              data-judul="Hapus Surat Masuk"
                              data-tombol="Ya, Hapus Surat"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-red-700 bg-red-50 border border-red-200 rounded-lg">
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center text-gray-400">
                    <p class="font-medium text-sm text-gray-500">Tidak ada data surat masuk yang ditemukan.</p>
                </div>
            @endforelse
        </div>

        @if ($suratMasuks->hasPages())
            <div class="p-4 bg-white rounded-2xl border border-gray-200 shadow-xs">
                {{ $suratMasuks->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
