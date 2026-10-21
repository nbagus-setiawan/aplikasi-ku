<x-app-layout>
    @section('title', 'Ubah Surat Masuk')

    <div class="max-w-5xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.surat-masuk.show', $suratMasuk) }}" class="text-sm text-emerald-700 hover:text-emerald-800 font-semibold flex items-center gap-1.5 mb-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Detail Surat</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Ubah Surat Masuk #{{ $suratMasuk->no_agenda }}</h1>
                <p class="text-sm text-gray-500 mt-1">Perbarui rincian naskah surat masuk, status disposisi pimpinan, atau berkas pindaian.</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm p-6 sm:p-10">
            <form method="POST" action="{{ route('admin.surat-masuk.update', $suratMasuk) }}" enctype="multipart/form-data" class="space-y-10">
                @csrf
                @method('PUT')

                <!-- SEKSI 1: NOMOR AGENDA & DATA SURAT ASAL -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-200">
                        <span class="flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">1</span>
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Nomor Agenda & Asal Surat</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50/80 p-5 sm:p-6 rounded-xl border border-gray-200">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Nomor Agenda (Baku)
                            </label>
                            <input type="text" value="{{ $suratMasuk->no_agenda }}" disabled
                                   class="w-full text-sm rounded-lg border-gray-300 bg-gray-100 text-gray-700 font-bold font-mono py-2.5 px-3.5 cursor-not-allowed shadow-xs" />
                            <p class="text-xs text-gray-500 mt-1.5">Nomor urut agenda buku surat masuk.</p>
                        </div>

                        <div>
                            <label for="no_surat" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                Nomor Surat Asal <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_surat" id="no_surat" value="{{ old('no_surat', $suratMasuk->no_surat) }}" required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 bg-white py-2.5 px-3.5 shadow-xs" />
                            <p class="text-xs text-gray-500 mt-1.5">Nomor dari instansi/lembaga pengirim.</p>
                            @error('no_surat')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="pengirim" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                Pengirim / Instansi Asal <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="pengirim" id="pengirim" value="{{ old('pengirim', $suratMasuk->pengirim) }}" required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 bg-white py-2.5 px-3.5 shadow-xs" />
                            <p class="text-xs text-gray-500 mt-1.5">Lembaga asal pembuat surat.</p>
                            @error('pengirim')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SEKSI 2: TANGGAL & KLASIFIKASI -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-200">
                        <span class="flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">2</span>
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Tanggal, Tempat & Klasifikasi Surat</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 bg-white p-5 sm:p-6 rounded-xl border border-gray-200">
                        <div>
                            <label for="tempat_surat" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                Kota / Tempat Asal Surat
                            </label>
                            <input type="text" name="tempat_surat" id="tempat_surat"
                                   value="{{ old('tempat_surat', $suratMasuk->tempat_surat ?: 'Putak') }}"
                                   placeholder="Contoh: Putak, Muara Enim"
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5 px-3.5 shadow-xs" />
                            <p class="text-xs text-gray-500 mt-1.5">Kota penetapan surat asal.</p>
                            @error('tempat_surat')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tgl_surat" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                Tanggal Surat Asal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tgl_surat" id="tgl_surat"
                                   value="{{ old('tgl_surat', $suratMasuk->tgl_surat ? $suratMasuk->tgl_surat->format('Y-m-d') : '') }}" required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5 px-3.5 shadow-xs" />
                            <p class="text-xs text-gray-500 mt-1.5">Tanggal tertera pada naskah surat asal.</p>
                            @error('tgl_surat')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tgl_diterima" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                Tanggal Diterima di Pesantren <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tgl_diterima" id="tgl_diterima"
                                   value="{{ old('tgl_diterima', $suratMasuk->tgl_diterima ? $suratMasuk->tgl_diterima->format('Y-m-d') : '') }}" required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5 px-3.5 shadow-xs" />
                            <p class="text-xs text-gray-500 mt-1.5">Waktu fisik surat tiba di sekretariat.</p>
                            @error('tgl_diterima')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-kategori-surat-select
                                :kategori-list="$kategoriList"
                                :selected-id="old('kategori_id', $suratMasuk->kategori_id)"
                                :required="false"
                                label="Jenis / Kategori Surat"
                                helper-text="Klasifikasi arsip surat masuk (opsional)."
                            />
                            @error('kategori_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SEKSI 3: PERIHAL & LAMPIRAN -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-200">
                        <span class="flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">3</span>
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Perihal & Keterangan Lampiran</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-white p-5 sm:p-6 rounded-xl border border-gray-200">
                        <div class="md:col-span-2">
                            <label for="perihal" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                Perihal Surat <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="perihal" id="perihal" value="{{ old('perihal', $suratMasuk->perihal) }}" required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5 px-3.5 shadow-xs" />
                            <p class="text-xs text-gray-500 mt-1.5">Ringkasan maksud isi surat yang diterima.</p>
                            @error('perihal')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="lampiran_teks" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                Keterangan Lampiran
                            </label>
                            <input type="text" name="lampiran_teks" id="lampiran_teks" value="{{ old('lampiran_teks', $suratMasuk->lampiran_teks ?: '-') }}"
                                   placeholder="Contoh: 1 Berkas / 1 Lembar / -"
                                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5 px-3.5 shadow-xs" />
                            <p class="text-xs text-gray-500 mt-1.5">Rincian teks lampiran pada naskah asal.</p>
                            @error('lampiran_teks')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SEKSI 4: STATUS DISPOSISI & BERKAS DOKUMEN -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-200">
                        <span class="flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">4</span>
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Disposisi & Berkas Dokumen</h3>
                    </div>

                    <div class="bg-gray-50/80 p-5 sm:p-6 rounded-xl border border-gray-200 space-y-6">
                        <div>
                            <label for="status_disposisi" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                Status Tindak Lanjut Disposisi <span class="text-red-500">*</span>
                            </label>
                            <select name="status_disposisi" id="status_disposisi" required
                                    class="w-full max-w-md text-sm rounded-lg border-gray-300 bg-white focus:border-emerald-600 focus:ring-emerald-600 py-2.5 px-3.5 shadow-xs">
                                <option value="baru" {{ old('status_disposisi', $suratMasuk->status_disposisi) === 'baru' ? 'selected' : '' }}>Baru (Belum Ditindaklanjuti)</option>
                                <option value="diproses" {{ old('status_disposisi', $suratMasuk->status_disposisi) === 'diproses' ? 'selected' : '' }}>Diproses (Sedang Berjalan)</option>
                                <option value="selesai" {{ old('status_disposisi', $suratMasuk->status_disposisi) === 'selesai' ? 'selected' : '' }}>Selesai (Sudah Tuntas)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1.5">Tentukan tahapan tindak lanjut surat.</p>
                            @error('status_disposisi')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-gray-200">
                            <div>
                                <label for="file" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                    Ganti Berkas Dokumen Surat Asli (Opsional)
                                </label>
                                @if ($suratMasuk->file)
                                    <div class="mb-2 p-2.5 bg-white border border-gray-200 rounded-lg text-xs text-gray-700 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            <span class="truncate max-w-[200px]">Dokumen: <strong>{{ basename($suratMasuk->file) }}</strong></span>
                                        </span>
                                        <a href="{{ asset('storage/' . $suratMasuk->file) }}" target="_blank" class="text-emerald-700 font-bold hover:underline shrink-0">
                                            Lihat ↗
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="file" id="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                       class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-300 rounded-lg cursor-pointer bg-white shadow-xs" />
                                <p class="text-xs text-gray-500 mt-1.5">Format: PDF, JPG, PNG, DOCX (Maks 5 MB).</p>
                                @error('file')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="file_lampiran" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                    Ganti Berkas Lampiran Tambahan (Opsional)
                                </label>
                                @if ($suratMasuk->file_lampiran)
                                    <div class="mb-2 p-2.5 bg-white border border-blue-200 rounded-lg text-xs text-blue-900 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            <span class="truncate max-w-[200px]">Lampiran: <strong>{{ basename($suratMasuk->file_lampiran) }}</strong></span>
                                        </span>
                                        <a href="{{ asset('storage/' . $suratMasuk->file_lampiran) }}" target="_blank" class="text-blue-700 font-bold hover:underline shrink-0">
                                            Lihat ↗
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="file_lampiran" id="file_lampiran" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                       class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg cursor-pointer bg-white shadow-xs" />
                                <p class="text-xs text-gray-500 mt-1.5">Berkas tambahan seperti lembar jadwal/lampiran (Maks 5 MB).</p>
                                @error('file_lampiran')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="disposisi" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                Catatan / Instruksi Disposisi Pimpinan
                            </label>
                            <textarea name="disposisi" id="disposisi" rows="3"
                                      class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 bg-white py-2.5 px-3.5 shadow-xs">{{ old('disposisi', $suratMasuk->disposisi) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1.5">Petunjuk tertulis dari pimpinan pondok terkait disposisi surat.</p>
                            @error('disposisi')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- FOOTER TOMBOL AKSI -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.surat-masuk.show', $suratMasuk) }}"
                       class="px-5 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold rounded-xl transition shadow-xs">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-7 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-bold rounded-xl shadow-sm transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
