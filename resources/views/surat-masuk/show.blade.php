<x-app-layout>
    @section('title', 'Detail Surat Masuk #' . $suratMasuk->no_agenda)

    <div class="max-w-5xl mx-auto space-y-6 pb-12">
        <!-- Top Navigation & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                    <a href="{{ route('admin.surat-masuk.index') }}" class="hover:text-emerald-700 transition">Surat Masuk</a>
                    <span>/</span>
                    <span class="text-emerald-800 font-semibold">Agenda #{{ $suratMasuk->no_agenda }}</span>
                </nav>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Surat Masuk #{{ $suratMasuk->no_agenda }}</h1>
                    @if ($suratMasuk->status_disposisi === 'baru')
                        <span class="px-2.5 py-0.5 bg-amber-50 text-amber-800 border border-amber-300 font-bold rounded-full text-xs">Baru</span>
                    @elseif ($suratMasuk->status_disposisi === 'diproses')
                        <span class="px-2.5 py-0.5 bg-blue-50 text-blue-800 border border-blue-300 font-bold rounded-full text-xs">Diproses</span>
                    @else
                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-300 font-bold rounded-full text-xs">Selesai</span>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.surat-masuk.edit', $suratMasuk) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 text-xs sm:text-sm font-semibold rounded-xl border border-gray-300 shadow-2xs transition active:scale-95">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Ubah Data</span>
                </a>

                <form method="POST" action="{{ route('admin.surat-masuk.destroy', $suratMasuk) }}"
                      data-konfirmasi="Apakah Anda yakin ingin menghapus surat masuk ini?"
                      data-judul="Hapus Surat Masuk"
                      data-tombol="Ya, Hapus Surat">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 text-red-700 hover:bg-red-100 text-xs sm:text-sm font-semibold rounded-xl border border-red-200 shadow-2xs transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Hapus</span>
                    </button>
                </form>

                <a href="{{ route('admin.surat-masuk.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl transition active:scale-95">
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <!-- Detail Data Surat -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-100 flex items-center justify-between">
                <span class="font-extrabold text-xs text-gray-800 uppercase tracking-wider">Informasi Dokumen Surat Masuk</span>
                <span class="text-xs font-mono font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                    Agenda: #{{ $suratMasuk->no_agenda }}
                </span>
            </div>
            
            <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-xs sm:text-sm">
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Nomor Agenda Surat</span>
                    <span class="block text-base font-extrabold text-emerald-900 mt-0.5">#{{ $suratMasuk->no_agenda }}</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Nomor Surat Asal</span>
                    <span class="block text-base font-mono font-bold text-gray-900 mt-0.5">{{ $suratMasuk->no_surat }}</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Pengirim / Instansi Asal</span>
                    <span class="block text-sm font-bold text-gray-900 mt-0.5">{{ $suratMasuk->pengirim }}</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Keterangan Lampiran</span>
                    <span class="block text-sm font-semibold text-gray-800 mt-0.5">{{ $suratMasuk->lampiran_teks ?: '- (Tidak Ada)' }}</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Tempat & Tanggal Surat</span>
                    <span class="block text-sm font-semibold text-gray-800 mt-0.5">{{ $suratMasuk->tempat_surat ?: 'Putak' }}, {{ $suratMasuk->formatted_tgl_surat }}</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Tanggal Diterima Sekretariat</span>
                    <span class="block text-sm font-semibold text-gray-800 mt-0.5">{{ $suratMasuk->formatted_tgl_diterima }}</span>
                </div>
                <div class="md:col-span-2 pt-2 border-t border-gray-100">
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Perihal Dokumen</span>
                    <p class="text-sm sm:text-base font-extrabold text-gray-900 mt-1 leading-relaxed">{{ $suratMasuk->perihal }}</p>
                </div>

                <!-- Dokumen Surat Masuk Asli (Tetap Tepat Seperti Aslinya yang Diupload) -->
                <div class="md:col-span-2 space-y-3 pt-4 border-t border-gray-100">
                    <span class="block text-xs font-bold uppercase tracking-wider text-emerald-900">📄 Berkas Dokumen Surat Masuk Asli</span>
                    @if ($suratMasuk->file)
                        @php
                            $extFile = strtolower(pathinfo($suratMasuk->file, PATHINFO_EXTENSION));
                            $isImage = in_array($extFile, ['png', 'jpg', 'jpeg', 'webp']);
                        @endphp
                        <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-gray-50/80 rounded-2xl border border-gray-200">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ basename($suratMasuk->file) }}</p>
                                    <p class="text-[11px] text-gray-500">Berkas surat masuk tersimpan asli di sistem</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ asset('storage/' . $suratMasuk->file) }}" target="_blank"
                                   class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                                    Buka Dokumen
                                </a>
                                @if ($suratMasuk->file_surat_asli && $suratMasuk->file_surat_asli !== $suratMasuk->file)
                                    <a href="{{ asset('storage/' . $suratMasuk->file_surat_asli) }}" download
                                       class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                                        Unduh File Word Asli (.docx)
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if ($isImage)
                            <div class="p-3 bg-gray-100 rounded-2xl border border-gray-200 flex justify-center">
                                <img src="{{ asset('storage/' . $suratMasuk->file) }}" alt="Dokumen Surat Masuk Asli" class="max-h-[750px] w-auto rounded-xl shadow-xs border border-gray-300 bg-white" />
                            </div>
                        @elseif ($extFile === 'pdf')
                            <div class="w-full h-[650px] rounded-2xl overflow-hidden border border-gray-200 bg-gray-100">
                                <iframe src="{{ asset('storage/' . $suratMasuk->file) }}" class="w-full h-full" title="Dokumen Surat Masuk Asli"></iframe>
                            </div>
                        @endif
                    @else
                        <p class="text-gray-400 italic text-xs">Tidak ada berkas surat masuk yang diunggah.</p>
                    @endif
                </div>

                @if ($suratMasuk->file_lampiran)
                    <div class="md:col-span-2 space-y-2 pt-2">
                        <span class="block text-xs font-bold uppercase tracking-wider text-blue-900">📎 Berkas Lampiran Tambahan ({{ $suratMasuk->lampiran_teks ?: '1 Berkas' }})</span>
                        <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-blue-50/60 rounded-2xl border border-blue-200">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-bold text-blue-950 truncate">{{ basename($suratMasuk->file_lampiran) }}</p>
                                    <p class="text-[11px] text-blue-700">Lampiran pendukung surat masuk</p>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $suratMasuk->file_lampiran) }}" target="_blank"
                               class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                                Buka Lampiran
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="px-6 sm:px-8 py-3 bg-gray-50/60 border-t border-gray-100 text-xs text-gray-500">
                Dicatat oleh: <span class="font-semibold text-gray-700">{{ $suratMasuk->user ? $suratMasuk->user->name : 'Sekretaris' }}</span> &bull; {{ $suratMasuk->created_at->format('d-m-Y H:i') }} WIB
            </div>
        </div>

        <!-- Disposisi Pimpinan -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6 sm:p-8 space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-gray-900">Disposisi & Instruksi Tindak Lanjut</h2>
                    <p class="text-xs text-gray-500">Instruksi pimpinan pondok dan pembagian tugas divisi atas surat masuk ini.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.surat-masuk.disposisi', $suratMasuk) }}" class="space-y-4 pt-2">
                @csrf
                @method('PATCH')

                <div>
                    <label for="status_disposisi" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Status Disposisi <span class="text-red-500">*</span>
                    </label>
                    <select name="status_disposisi" id="status_disposisi" required
                            class="w-full sm:w-64 text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs font-medium">
                        <option value="baru" {{ old('status_disposisi', $suratMasuk->status_disposisi) === 'baru' ? 'selected' : '' }}>Baru</option>
                        <option value="diproses" {{ old('status_disposisi', $suratMasuk->status_disposisi) === 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="selesai" {{ old('status_disposisi', $suratMasuk->status_disposisi) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div>
                    <label for="disposisi" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Catatan Disposisi / Instruksi Tindak Lanjut
                    </label>
                    <textarea name="disposisi" id="disposisi" rows="4"
                              placeholder="Ketik instruksi tindak lanjut pimpinan atau pembagian tugas divisi..."
                              class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs leading-relaxed">{{ old('disposisi', $suratMasuk->disposisi) }}</textarea>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-emerald-700/20 hover:scale-[1.01] active:scale-[0.98] transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Perbarui Disposisi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
