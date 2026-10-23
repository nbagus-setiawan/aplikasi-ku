<x-app-layout>
    @section('title', 'Unggah Dokumen Arsip')

    <div class="max-w-4xl mx-auto space-y-6 pb-12">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                    <a href="{{ route('admin.arsip.index') }}" class="hover:text-emerald-700 transition">Arsip Dokumen</a>
                    <span>/</span>
                    <span class="text-emerald-800 font-semibold">Unggah Baru</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Unggah Dokumen Arsip Baru</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Simpan berkas digital dokumen penting pesantren dan tautkan ke surat masuk/keluar jika relevan.</p>
            </div>
            <div>
                <a href="{{ route('admin.arsip.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-gray-100 bg-gradient-to-r from-emerald-50/60 via-white to-emerald-50/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-bold shadow-md shadow-emerald-700/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Formulir Pengarsipan Dokumen</h2>
                        <p class="text-xs text-gray-500">Tanda bintang merah (<span class="text-red-500 font-bold">*</span>) wajib diisi dengan benar.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.arsip.store') }}" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Judul Arsip -->
                    <div class="sm:col-span-2">
                        <label for="judul" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Judul Dokumen Arsip <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required
                               placeholder="Contoh: SK Pengurus Pondok Pesantren Periode 2026-2028"
                               class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs font-semibold text-gray-900" />
                        @error('judul')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kategori Dokumen -->
                    <div>
                        <label for="kategori" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Kategori Dokumen <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="kategori" id="kategori" value="{{ old('kategori') }}" required list="listKategori"
                               placeholder="Ketik atau pilih kategori"
                               class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs" />
                        <datalist id="listKategori">
                            <option value="SK & Legalitas"></option>
                            <option value="Perjanjian & MoU"></option>
                            <option value="Sertifikat & Piagam"></option>
                            <option value="Laporan & SPJ"></option>
                            <option value="Kurikulum & Kitab"></option>
                            <option value="Berita Acara"></option>
                            <option value="Dokumentasi"></option>
                        </datalist>
                        @error('kategori')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tahun Dokumen -->
                    <div>
                        <label for="tahun" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Tahun Dokumen <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="tahun" id="tahun" value="{{ old('tahun', date('Y')) }}" required min="1950" max="2100"
                               class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs font-mono font-bold" />
                        @error('tahun')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Berkas File Dokumen -->
                    <div class="sm:col-span-2">
                        <label for="file" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Unggah Berkas Dokumen (PDF, JPG, PNG, DOC, DOCX Maks 5 MB) <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="file" id="file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                               class="w-full text-xs file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100 border border-gray-300 rounded-xl cursor-pointer bg-white shadow-2xs" />
                        <p class="text-[11px] text-gray-400 mt-1.5">File tersimpan aman dalam pangkalan data digital pesantren.</p>
                        @error('file')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tautan ke Surat Masuk (Opsional) -->
                    <div>
                        <label for="surat_masuk_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Tautkan ke Surat Masuk (Opsional)
                        </label>
                        <select name="surat_masuk_id" id="surat_masuk_id"
                                class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs font-medium">
                            <option value="">-- Tidak Ditautkan --</option>
                            @foreach ($suratMasukList as $sm)
                                <option value="{{ $sm->id }}" {{ old('surat_masuk_id') == $sm->id ? 'selected' : '' }}>
                                    #{{ $sm->no_agenda }} &mdash; {{ $sm->no_surat }} ({{ $sm->pengirim }})
                                </option>
                            @endforeach
                        </select>
                        @error('surat_masuk_id')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tautan ke Surat Keluar (Opsional) -->
                    <div>
                        <label for="surat_keluar_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Tautkan ke Surat Keluar (Opsional)
                        </label>
                        <select name="surat_keluar_id" id="surat_keluar_id"
                                class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs font-medium">
                            <option value="">-- Tidak Ditautkan --</option>
                            @foreach ($suratKeluarList as $sk)
                                <option value="{{ $sk->id }}" {{ old('surat_keluar_id') == $sk->id ? 'selected' : '' }}>
                                    {{ $sk->no_surat }} &mdash; {{ $sk->perihal }}
                                </option>
                            @endforeach
                        </select>
                        @error('surat_keluar_id')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Keterangan Tambahan -->
                    <div class="sm:col-span-2">
                        <label for="keterangan" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Keterangan / Lokasi Simpan Fisik
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                                  placeholder="Catatan mengenai nomor lemari/bundel fisik, asal arsip, atau keterangan penting lainnya..."
                                  class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs leading-relaxed">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('admin.arsip.index') }}"
                       class="px-5 py-2.5 text-xs sm:text-sm font-bold text-rose-700 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl transition text-center shadow-2xs">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-emerald-700/20 hover:scale-[1.01] active:scale-[0.98] transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>Simpan Dokumen Arsip</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
