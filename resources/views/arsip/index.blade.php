<x-app-layout>
    @section('title', 'Arsip Dokumen')

    <div class="space-y-6 pb-12">
        <!-- Header Judul & Tombol Aksi -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                    <span>Master Data & Dokumen</span>
                    <span>/</span>
                    <span class="text-emerald-800 font-semibold">Arsip Dokumen</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Arsip Digital Dokumen Pesantren</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Penyimpanan terpusat dokumen resmi, SK, piagam, berkas legalitas, dan tautan surat dinas.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.arsip.create') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-emerald-700/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Unggah Dokumen Arsip</span>
                </a>
            </div>
        </div>

        <!-- 4 Kartu Ringkasan Arsip Dokumen -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Total Dokumen -->
            <div class="bg-white rounded-2xl border border-gray-200/90 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Total Arsip</span>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 mt-0.5">{{ $statistik['total'] ?? 0 }}</div>
                    <span class="text-[11px] text-gray-500 font-medium">Dokumen digital</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200/60 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
            </div>

            <!-- Kategori -->
            <div class="bg-white rounded-2xl border border-purple-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-purple-700">Kategori</span>
                    <div class="text-2xl sm:text-3xl font-black text-purple-950 mt-0.5">{{ $statistik['kategori'] ?? 0 }}</div>
                    <span class="text-[11px] text-purple-800 font-medium">Klasifikasi arsip</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
            </div>

            <!-- Tahun Ini -->
            <div class="bg-white rounded-2xl border border-amber-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Tahun Ini</span>
                    <div class="text-2xl sm:text-3xl font-black text-amber-950 mt-0.5">{{ $statistik['tahun_ini'] ?? 0 }}</div>
                    <span class="text-[11px] text-amber-800 font-medium">Tahun {{ date('Y') }}</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <!-- Tertaut Surat -->
            <div class="bg-white rounded-2xl border border-blue-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Tertaut Surat</span>
                    <div class="text-2xl sm:text-3xl font-black text-blue-950 mt-0.5">{{ $statistik['tertaut'] ?? 0 }}</div>
                    <span class="text-[11px] text-blue-800 font-medium">Surat masuk / keluar</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filter & Pencarian -->
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.arsip.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 sm:gap-4 items-end">
                <!-- Kata Kunci -->
                <div class="lg:col-span-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Cari Dokumen</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Judul arsip, kategori, keterangan..."
                               class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 pl-10 py-2.5 shadow-2xs" />
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Kategori -->
                <div class="lg:col-span-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Kategori Dokumen</label>
                    <select name="kategori" class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>
                                {{ $kat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tahun -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Tahun Dokumen</label>
                    <select name="tahun" class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5 font-mono">
                        <option value="">Semua Tahun</option>
                        @foreach ($tahunList as $th)
                            <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>
                                {{ $th }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Filter -->
                <div class="lg:col-span-2 flex items-center gap-2">
                    <button type="submit"
                            class="flex-1 py-2.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm rounded-xl transition shadow-xs">
                        Saring
                    </button>
                    @if (request()->hasAny(['q', 'kategori', 'tahun']))
                        <a href="{{ route('admin.arsip.index') }}"
                           class="py-2.5 px-3 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold border border-rose-300 text-xs sm:text-sm rounded-xl transition shadow-2xs" title="Reset Filter">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- TAMPILAN DESKTOP: TABEL ARSIP -->
        <div class="hidden md:block bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-700">
                    <thead class="bg-gray-50/90 border-b border-gray-200 text-gray-800 font-extrabold uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Judul Dokumen</th>
                            <th class="px-5 py-3.5">Kategori & Tahun</th>
                            <th class="px-5 py-3.5">Tautan Surat Terkait</th>
                            <th class="px-5 py-3.5 text-center">Tipe Berkas</th>
                            <th class="px-5 py-3.5 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($arsips as $item)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.arsip.show', $item) }}" class="font-extrabold text-gray-900 hover:text-emerald-700 transition">
                                        {{ $item->judul }}
                                    </a>
                                    @if ($item->keterangan)
                                        <p class="text-xs text-gray-500 truncate max-w-sm mt-0.5 font-normal">{{ $item->keterangan }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200/70 font-bold rounded-lg text-xs">
                                        {{ $item->kategori }}
                                    </span>
                                    <span class="text-gray-500 ml-1.5 font-mono font-bold text-xs">({{ $item->tahun }})</span>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($item->suratMasuk)
                                        <div class="text-xs">
                                            <span class="text-emerald-700 font-bold">[Surat Masuk]</span>
                                            <a href="{{ route('admin.surat-masuk.show', $item->suratMasuk) }}" class="underline font-semibold text-gray-800 ml-1 hover:text-emerald-800" target="_blank">
                                                #{{ $item->suratMasuk->no_agenda }} ({{ $item->suratMasuk->no_surat }})
                                            </a>
                                        </div>
                                    @elseif ($item->suratKeluar)
                                        <div class="text-xs">
                                            <span class="text-blue-700 font-bold">[Surat Keluar]</span>
                                            <a href="{{ route('admin.surat-keluar.show', $item->suratKeluar) }}" class="underline font-semibold text-gray-800 ml-1 hover:text-blue-800" target="_blank">
                                                {{ $item->suratKeluar->no_surat }}
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs font-medium">- Tidak Ditautkan -</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <span class="uppercase px-2.5 py-1 rounded-lg text-xs font-black tracking-wider {{ $item->is_pdf ? 'bg-rose-100 text-rose-800 border border-rose-300' : ($item->is_image ? 'bg-sky-100 text-sky-800 border border-sky-300' : 'bg-amber-100 text-amber-800 border border-amber-300') }}">
                                        {{ $item->file_extension }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Lihat Detail -->
                                        <a href="{{ route('admin.arsip.show', $item) }}" title="Lihat Detail & Pratinjau"
                                           class="p-2 text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200/60 rounded-xl transition shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        <!-- Unduh File -->
                                        <a href="{{ route('admin.arsip.unduh', $item) }}" title="Unduh Berkas"
                                           class="p-2 text-white bg-blue-900 hover:bg-blue-950 rounded-xl transition shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>

                                        <!-- Ubah -->
                                        <a href="{{ route('admin.arsip.edit', $item) }}" title="Ubah Data"
                                           class="p-2 text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-xl transition shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <!-- Hapus -->
                                        <form method="POST" action="{{ route('admin.arsip.destroy', $item) }}"
                                              data-konfirmasi="Apakah Anda yakin ingin menghapus arsip dokumen ini secara permanen?"
                                              data-judul="Hapus Arsip Dokumen"
                                              data-tombol="Ya, Hapus Arsip"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                    class="p-2 text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl transition shadow-2xs">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                        <p class="font-medium text-sm text-gray-500">Tidak ada dokumen arsip yang ditemukan.</p>
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
            @forelse ($arsips as $item)
                <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200/70 font-bold rounded-lg text-[10px]">
                                    {{ $item->kategori }}
                                </span>
                                <span class="text-[10px] text-gray-500 font-mono font-bold">{{ $item->tahun }}</span>
                            </div>
                            <a href="{{ route('admin.arsip.show', $item) }}" class="font-extrabold text-sm text-gray-900 block leading-tight">
                                {{ $item->judul }}
                            </a>
                        </div>
                        <span class="uppercase px-2 py-0.5 rounded-lg text-[10px] font-black tracking-wider shrink-0 {{ $item->is_pdf ? 'bg-rose-100 text-rose-800 border border-rose-300' : ($item->is_image ? 'bg-sky-100 text-sky-800 border border-sky-300' : 'bg-amber-100 text-amber-800 border border-amber-300') }}">
                            {{ $item->file_extension }}
                        </span>
                    </div>

                    @if ($item->suratMasuk || $item->suratKeluar)
                        <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100 text-xs">
                            <span class="text-gray-400 text-[10px] uppercase font-bold block mb-0.5">Tautan Surat:</span>
                            @if ($item->suratMasuk)
                                <a href="{{ route('admin.surat-masuk.show', $item->suratMasuk) }}" class="text-emerald-800 font-bold hover:underline" target="_blank">
                                    [Surat Masuk] #{{ $item->suratMasuk->no_agenda }} - {{ $item->suratMasuk->no_surat }}
                                </a>
                            @elseif ($item->suratKeluar)
                                <a href="{{ route('admin.surat-keluar.show', $item->suratKeluar) }}" class="text-blue-800 font-bold hover:underline" target="_blank">
                                    [Surat Keluar] {{ $item->suratKeluar->no_surat }}
                                </a>
                            @endif
                        </div>
                    @endif

                    <!-- Tombol Aksi Mobile -->
                    <div class="flex items-center justify-end gap-1.5 pt-2 border-t border-gray-100 flex-wrap">
                        <a href="{{ route('admin.arsip.show', $item) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100">
                            <span>Detail</span>
                        </a>
                        <a href="{{ route('admin.arsip.unduh', $item) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-blue-900 hover:bg-blue-950 rounded-lg shadow-2xs">
                            <span>Unduh</span>
                        </a>
                        <a href="{{ route('admin.arsip.edit', $item) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-amber-900 bg-amber-100 border border-amber-300 rounded-lg hover:bg-amber-200">
                            <span>Ubah</span>
                        </a>
                        <form method="POST" action="{{ route('admin.arsip.destroy', $item) }}"
                              data-konfirmasi="Apakah Anda yakin ingin menghapus arsip dokumen ini secara permanen?"
                              data-judul="Hapus Arsip Dokumen"
                              data-tombol="Ya, Hapus Arsip"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 border border-rose-300 rounded-lg hover:bg-rose-100">
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center text-gray-400">
                    <p class="font-medium text-sm text-gray-500">Tidak ada dokumen arsip yang ditemukan.</p>
                </div>
            @endforelse
        </div>

        @if ($arsips->hasPages())
            <div class="p-4 bg-white rounded-2xl border border-gray-200 shadow-xs">
                {{ $arsips->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
