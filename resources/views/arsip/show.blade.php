<x-app-layout>
    @section('title', 'Detail Arsip ' . $arsip->judul)

    <div class="max-w-4xl mx-auto space-y-6 pb-12">
        <!-- Top Navigation & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                    <a href="{{ route('admin.arsip.index') }}" class="hover:text-emerald-700 transition">Arsip Dokumen</a>
                    <span>/</span>
                    <span class="text-emerald-800 font-semibold truncate max-w-xs">Detail Arsip</span>
                </nav>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">{{ $arsip->judul }}</h1>
                    <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-bold rounded-lg text-xs">
                        {{ $arsip->kategori }} &bull; {{ $arsip->tahun }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Unduh File -->
                <a href="{{ route('admin.arsip.unduh', $arsip) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-900 hover:bg-blue-950 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Unduh Berkas</span>
                </a>

                <!-- Ubah -->
                <a href="{{ route('admin.arsip.edit', $arsip) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-900 text-xs sm:text-sm font-bold rounded-xl border border-amber-300 shadow-2xs transition active:scale-95">
                    <svg class="w-4 h-4 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Ubah</span>
                </a>

                <!-- Hapus -->
                <form method="POST" action="{{ route('admin.arsip.destroy', $arsip) }}"
                      data-konfirmasi="Apakah Anda yakin ingin menghapus arsip dokumen ini secara permanen?"
                      data-judul="Hapus Arsip Dokumen"
                      data-tombol="Ya, Hapus Arsip"
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs sm:text-sm font-bold rounded-xl border border-rose-300 shadow-2xs transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Hapus</span>
                    </button>
                </form>

                <a href="{{ route('admin.arsip.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs sm:text-sm font-bold rounded-xl border border-rose-300 shadow-2xs transition active:scale-95">
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <!-- Detail Box -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-100 font-extrabold text-xs text-gray-800 uppercase tracking-wider">
                Informasi Dokumen Arsip
            </div>
            <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-xs sm:text-sm">
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Judul Dokumen</span>
                    <span class="block text-base font-extrabold text-gray-900 mt-0.5">{{ $arsip->judul }}</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Kategori & Tahun</span>
                    <span class="block text-sm font-semibold text-gray-900 mt-0.5">{{ $arsip->kategori }} (Tahun {{ $arsip->tahun }})</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Nama File Fisik</span>
                    <span class="block text-xs font-mono font-bold text-gray-800 mt-0.5 truncate">{{ basename($arsip->file) }}</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Tautan Surat Terkait</span>
                    @if ($arsip->suratMasuk)
                        <div class="mt-0.5">
                            <a href="{{ route('admin.surat-masuk.show', $arsip->suratMasuk) }}" class="text-emerald-800 font-bold hover:underline" target="_blank">
                                [Surat Masuk] #{{ $arsip->suratMasuk->no_agenda }} &mdash; {{ $arsip->suratMasuk->no_surat }}
                            </a>
                        </div>
                    @elseif ($arsip->suratKeluar)
                        <div class="mt-0.5">
                            <a href="{{ route('admin.surat-keluar.show', $arsip->suratKeluar) }}" class="text-blue-800 font-bold hover:underline" target="_blank">
                                [Surat Keluar] {{ $arsip->suratKeluar->no_surat }}
                            </a>
                        </div>
                    @else
                        <span class="block text-gray-400 italic mt-0.5">- Tidak ada surat yang ditautkan -</span>
                    @endif
                </div>

                <div class="md:col-span-2 pt-2 border-t border-gray-100">
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider mb-1.5">Keterangan / Catatan Arsip</span>
                    <p class="text-xs sm:text-sm text-gray-700 bg-gray-50/80 p-3.5 rounded-xl border border-gray-200 leading-relaxed font-normal">
                        {{ $arsip->keterangan ?: 'Tidak ada keterangan tambahan.' }}
                    </p>
                </div>

                <!-- Pratinjau Dokumen -->
                <div class="md:col-span-2 space-y-2 pt-2 border-t border-gray-100">
                    <span class="block text-xs font-bold uppercase tracking-wider text-emerald-900">📄 Pratinjau Berkas Dokumen</span>
                    @if ($arsip->is_image)
                        <div class="border border-gray-200 rounded-2xl p-3 bg-gray-100 flex items-center justify-center max-h-[600px] overflow-hidden">
                            <img src="{{ asset('storage/' . $arsip->file) }}" alt="{{ $arsip->judul }}" class="max-h-[580px] w-auto rounded-xl shadow-xs object-contain bg-white" />
                        </div>
                    @elseif ($arsip->is_pdf)
                        <div class="border border-gray-200 rounded-2xl overflow-hidden bg-gray-100 h-[650px]">
                            <iframe src="{{ asset('storage/' . $arsip->file) }}" class="w-full h-full border-0"></iframe>
                        </div>
                    @else
                        <div class="p-5 bg-gray-50/80 rounded-2xl border border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div>
                                <p class="font-bold text-gray-900 text-xs sm:text-sm">Berkas Format: {{ strtoupper($arsip->file_extension) }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">Pratinjau langsung tidak tersedia untuk format dokumen ini. Silakan unduh berkas untuk melihat.</p>
                            </div>
                            <a href="{{ route('admin.arsip.unduh', $arsip) }}"
                               class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-xs whitespace-nowrap">
                                Unduh Dokumen
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="px-6 sm:px-8 py-3 bg-gray-50/60 border-t border-gray-100 text-xs text-gray-500">
                Diunggah oleh: <span class="font-semibold text-gray-700">{{ $arsip->user ? $arsip->user->name : 'Sekretaris' }}</span> &bull; {{ $arsip->created_at->format('d-m-Y H:i') }} WIB
            </div>
        </div>
    </div>
</x-app-layout>
