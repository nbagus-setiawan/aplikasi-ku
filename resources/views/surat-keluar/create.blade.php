<x-app-layout>
    @section('title', 'Upload & Terbitkan Surat Keluar')

    <div class="max-w-3xl mx-auto space-y-6" x-data="{
        uploadBerhasil: false,
        fileSuratName: '',
        fileLampiranName: '',
        errorMsg: '',

        kategoriList: {{ Js::from($kategoriList->map(fn($k) => ['nama' => $k->nama, 'kode' => $k->kode])) }},
        kodeVerifikasi: '{{ $suggestedKode ?? '' }}',
        showConfirmModal: false,
        isSubmitting: false,

        // Data input manual kosongan sesuai permintaan
        formData: {
            jenis_surat: '',
            perihal: '',
            tujuan: '',
            no_surat: '',
            lampiran_teks: '-',
            status: 'final',
            tempat_surat: 'Putak',
            tgl_surat: '{{ date('Y-m-d') }}'
        },

        handleFileSurat(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.errorMsg = '';
            this.fileSuratName = file.name;
            this.uploadBerhasil = true;
        },

        handleFileLampiran(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.fileLampiranName = file.name;
        },

        setLampiranCepat(val) {
            this.formData.lampiran_teks = val;
        },

        lanjutKonfirmasi() {
            if (!this.fileSuratName) {
                this.errorMsg = 'Silakan upload file surat Word (.docx) terlebih dahulu.';
                return;
            }
            if (!this.formData.jenis_surat.trim()) {
                this.errorMsg = 'Jenis surat wajib diisi.';
                return;
            }
            if (!this.formData.perihal.trim()) {
                this.errorMsg = 'Perihal surat wajib diisi.';
                return;
            }
            if (!this.formData.tujuan.trim()) {
                this.errorMsg = 'Tujuan surat wajib diisi.';
                return;
            }
            this.errorMsg = '';
            this.showConfirmModal = true;
        },

        terbitkanSekarang() {
            this.isSubmitting = true;
            document.getElementById('formUploadSurat').submit();
        }
    }">
        <!-- HEADER -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-4">
            <div>
                <a href="{{ route('admin.surat-keluar.index') }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-bold inline-flex items-center gap-1 mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span>Kembali ke Daftar Surat Keluar</span>
                </a>
                <h1 class="text-2xl font-extrabold text-gray-900">Upload & Terbitkan Surat Keluar (.docx)</h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    Unggah dokumen surat asli Microsoft Word Anda, lalu lengkapi rincian informasi surat secara manual.
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 text-xs space-y-1">
                <div class="font-bold">Gagal menerbitkan surat:</div>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div x-show="errorMsg" x-cloak class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 text-xs font-semibold flex items-center justify-between">
            <span x-text="errorMsg"></span>
            <button type="button" @click="errorMsg = ''" class="text-rose-600 font-bold">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.surat-keluar.store') }}" enctype="multipart/form-data" id="formUploadSurat" class="space-y-6">
            @csrf
            <input type="hidden" name="aksi_simpan" value="terbitkan_word">
            <input type="hidden" name="kode_verifikasi" :value="kodeVerifikasi">
            <input type="hidden" name="nama_penandatangan" value="-">
            <input type="hidden" name="jabatan_penandatangan" value="-">

            <!-- TAHAP 1: UPLOAD SURAT ANDA -->
            <div class="bg-white rounded-2xl border-2 transition p-6 shadow-xs space-y-4"
                 :class="uploadBerhasil ? 'border-emerald-500 bg-emerald-50/20' : 'border-gray-200'">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-extrabold"
                              :class="uploadBerhasil ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-800'">
                            1
                        </span>
                        <div>
                            <h2 class="text-sm font-extrabold text-gray-900">Upload Surat Anda (.docx)</h2>
                            <p class="text-[11px] text-gray-500">Pilih berkas surat Microsoft Word yang akan diterbitkan dan ditempel QR Code verifikasi</p>
                        </div>
                    </div>
                    <span x-show="uploadBerhasil" class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold flex items-center gap-1">
                        ✓ Berhasil Diupload
                    </span>
                </div>

                <!-- Kotak Pilih File Word -->
                <label class="block cursor-pointer border-2 border-dashed rounded-2xl p-6 text-center transition"
                       :class="uploadBerhasil ? 'border-emerald-400 bg-emerald-50/50' : 'border-gray-300 hover:border-emerald-500 bg-gray-50/60 hover:bg-emerald-50/20'">
                    <input type="file" name="file_surat_asli" accept=".docx,.doc" required class="hidden" @change="handleFileSurat($event)">
                    <div class="space-y-2">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <div x-show="!fileSuratName" class="text-sm font-bold text-gray-800">
                            Klik untuk memilih file surat Word (<span class="text-emerald-700">.docx</span>)
                        </div>
                        <div x-show="fileSuratName" class="text-sm font-extrabold text-emerald-900" x-text="'✓ ' + fileSuratName"></div>
                        <p class="text-xs text-gray-500" x-show="!uploadBerhasil">
                            Setelah berkas berhasil diupload, formulir rincian surat akan otomatis muncul di bawah.
                        </p>
                        <p class="text-xs text-emerald-700 font-semibold" x-show="uploadBerhasil">
                            Berkas siap. Silakan lengkapi informasi surat pada formulir di bawah.
                        </p>
                    </div>
                </label>
            </div>

            <!-- TAHAP 2: FORMULIR INPUT MANUAL (MUNCUL SETELAH SURAT BERHASIL DIUPLOAD) -->
            <div x-show="uploadBerhasil" x-cloak class="bg-white rounded-2xl border-2 border-gray-200 p-6 shadow-xs space-y-5 transition">
                <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-extrabold">
                            2
                        </span>
                        <div>
                            <h2 class="text-sm font-extrabold text-gray-900">Rincian Informasi Dokumen Surat</h2>
                            <p class="text-[11px] text-gray-500">Silakan isi formulir secara manual sesuai dengan isi surat Anda</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-xl">
                        <span class="text-[10px] text-emerald-800 font-bold uppercase tracking-wider">Kode QR:</span>
                        <strong class="font-mono text-xs text-emerald-900" x-text="kodeVerifikasi"></strong>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- 1. JENIS SURAT -->
                    <div class="sm:col-span-2 space-y-1.5 p-3.5 bg-gray-50/70 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between">
                            <label class="block text-gray-700 font-bold text-[11px] uppercase tracking-wider">
                                1. Jenis Surat <span class="text-rose-600">*</span>
                            </label>
                            <span class="text-[10px] text-emerald-700 font-semibold">Ketik Manual / Pilih Rekomendasi</span>
                        </div>
                        <input type="text" name="jenis_surat" list="list-jenis-surat-keluar" x-model="formData.jenis_surat" required
                               placeholder="Ketik jenis surat (contoh: Surat Permohonan, Surat Undangan, Surat Tugas, dll)..."
                               class="w-full text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5 px-3 bg-white" />
                        <datalist id="list-jenis-surat-keluar">
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->nama }}">{{ $kat->nama }} ({{ $kat->kode }})</option>
                            @endforeach
                        </datalist>
                    </div>

                    <!-- 2. PERIHAL -->
                    <div class="sm:col-span-2 space-y-1.5 p-3.5 bg-gray-50/70 rounded-xl border border-gray-200">
                        <label class="block text-gray-700 font-bold text-[11px] uppercase tracking-wider">
                            2. Perihal Surat <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="perihal" x-model="formData.perihal" required
                               placeholder="Masukkan perihal surat..."
                               class="w-full text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5 px-3 bg-white" />
                    </div>

                    <!-- 3. TUJUAN -->
                    <div class="sm:col-span-2 space-y-1.5 p-3.5 bg-gray-50/70 rounded-xl border border-gray-200">
                        <label class="block text-gray-700 font-bold text-[11px] uppercase tracking-wider">
                            3. Tujuan Surat (Kepada Yth.) <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="tujuan" x-model="formData.tujuan" required
                               placeholder="Contoh: Kepala Kantor Kemenag / Wali Santri / Instansi..."
                               class="w-full text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5 px-3 bg-white" />
                    </div>

                    <!-- 4. NOMOR SURAT -->
                    <div class="sm:col-span-2 space-y-1.5 p-3.5 bg-gray-50/70 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between">
                            <label class="block text-gray-700 font-bold text-[11px] uppercase tracking-wider">
                                4. Nomor Surat (Manual)
                            </label>
                            <span class="text-[10px] text-emerald-700 font-semibold">Bisa ketik angka urut (misal: 001) atau nomor lengkap</span>
                        </div>
                        <input type="text" name="no_surat_manual" x-model="formData.no_surat"
                               placeholder="Contoh: 001 atau 001/SPm.01/AF-PA/X/2026 (kosongkan jika ingin nomor otomatis)"
                               class="w-full font-mono text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5 px-3 bg-white" />
                    </div>

                    <!-- 5. LAMPIRAN -->
                    <div class="space-y-1.5 p-3.5 bg-gray-50/70 rounded-xl border border-gray-200">
                        <label class="block text-gray-700 font-bold text-[11px] uppercase tracking-wider">
                            5. Keterangan Lampiran
                        </label>
                        <input type="text" name="lampiran_teks" x-model="formData.lampiran_teks"
                               placeholder="Contoh: - atau 1 Lembar / 1 Berkas"
                               class="w-full text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2 px-2.5 bg-white" />
                        <div class="flex flex-wrap items-center gap-1 pt-1">
                            <button type="button" @click="setLampiranCepat('-')" class="px-2 py-0.5 rounded border text-[10px] font-semibold bg-white text-gray-700 hover:bg-gray-100 border-gray-300">Tanpa (-)</button>
                            <button type="button" @click="setLampiranCepat('1 Lembar')" class="px-2 py-0.5 rounded border text-[10px] font-semibold bg-white text-blue-700 hover:bg-blue-50 border-blue-200">1 Lembar</button>
                            <button type="button" @click="setLampiranCepat('2 Lembar')" class="px-2 py-0.5 rounded border text-[10px] font-semibold bg-white text-blue-700 hover:bg-blue-50 border-blue-200">2 Lembar</button>
                            <button type="button" @click="setLampiranCepat('1 Berkas')" class="px-2 py-0.5 rounded border text-[10px] font-semibold bg-white text-blue-700 hover:bg-blue-50 border-blue-200">1 Berkas</button>
                        </div>
                    </div>

                    <!-- 6. STATUS -->
                    <div class="space-y-1.5 p-3.5 bg-gray-50/70 rounded-xl border border-gray-200">
                        <label class="block text-gray-700 font-bold text-[11px] uppercase tracking-wider">
                            6. Status Dokumen Surat
                        </label>
                        <select name="status" x-model="formData.status"
                                class="w-full text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2 px-2.5 bg-white">
                            <option value="final">Final (Terkunci & Diberi QR Code Resmi)</option>
                            <option value="draft">Draft (Konsep Sementara)</option>
                            <option value="terkirim">Terkirim (Sudah Didistribusikan)</option>
                        </select>
                        <p class="text-[10px] text-gray-500">Pilih status penerbitan dokumen surat keluar ini.</p>
                    </div>

                    <!-- 7. TEMPAT & TANGGAL SURAT -->
                    <div class="sm:col-span-2 space-y-1.5 p-3.5 bg-gray-50/70 rounded-xl border border-gray-200">
                        <label class="block text-gray-700 font-bold text-[11px] uppercase tracking-wider">
                            7. Tempat & Tanggal Surat
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" name="tempat_surat" x-model="formData.tempat_surat" placeholder="Tempat (Putak)"
                                   class="w-full text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2 px-2.5 bg-white" />
                            <input type="date" name="tgl_surat" x-model="formData.tgl_surat"
                                   class="w-full text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2 px-2.5 bg-white" />
                        </div>
                    </div>

                    <!-- 8. UPLOAD BERKAS LAMPIRAN TAMBAHAN (OPSIONAL) -->
                    <div class="sm:col-span-2 space-y-1.5 p-3.5 bg-blue-50/40 rounded-xl border border-blue-200">
                        <label class="block text-blue-900 font-bold text-[11px] uppercase tracking-wider">
                            8. Berkas Lampiran Tambahan (Opsional)
                        </label>
                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" @change="handleFileLampiran($event)"
                               class="w-full text-xs text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-100 file:text-blue-800 hover:file:bg-blue-200 cursor-pointer" />
                        <p class="text-[10px] text-blue-700" x-show="fileLampiranName" x-text="'Lampiran dipilih: ' + fileLampiranName"></p>
                    </div>
                </div>

                <!-- TOMBOL SIMPAN / TERBITKAN -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.surat-keluar.index') }}" class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 rounded-xl text-xs font-bold transition shadow-2xs">
                        Batal
                    </a>
                    <button type="button" @click="lanjutKonfirmasi()"
                            class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-extrabold shadow-md transition flex items-center gap-1.5">
                        <span>✓ Simpan & Terbitkan Surat</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- POP-UP KONFIRMASI PENERBITAN DOKUMEN -->
        <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-5 shadow-2xl border border-gray-100" @click.away="if (!isSubmitting) showConfirmModal = false">
                <div class="text-center space-y-2">
                    @if (!empty($qrPreviewDataUri))
                        <div class="inline-flex flex-col items-center p-2.5 bg-white rounded-xl border-2 border-emerald-200 shadow-xs mx-auto">
                            <img src="{{ $qrPreviewDataUri }}" alt="QR Code Verifikasi" class="w-20 h-20" />
                            <span class="text-[10px] font-mono font-extrabold text-emerald-800 mt-1">Kode: <span x-text="kodeVerifikasi"></span></span>
                        </div>
                    @else
                        <div class="w-14 h-14 mx-auto rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl font-bold">
                            📄
                        </div>
                    @endif
                    <h3 class="text-base font-extrabold text-gray-900">
                        Konfirmasi Penerbitan Surat Keluar
                    </h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Pastikan seluruh rincian informasi di bawah sudah sesuai sebelum disimpan ke sistem.
                    </p>
                </div>

                <!-- Ringkasan Dokumen yang Akan Diterbitkan -->
                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-200 space-y-2 text-xs">
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">Jenis Surat:</span>
                        <span class="font-bold text-emerald-800 text-right" x-text="formData.jenis_surat || '-'"></span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">Perihal:</span>
                        <span class="font-bold text-gray-900 text-right" x-text="formData.perihal || '-'"></span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">Tujuan:</span>
                        <span class="font-bold text-gray-900 text-right" x-text="formData.tujuan || '-'"></span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">No. Surat:</span>
                        <span class="font-mono font-bold text-gray-900 text-right" x-text="formData.no_surat || '(Otomatis oleh sistem)'"></span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">Lampiran:</span>
                        <span class="font-bold text-gray-900 text-right" x-text="formData.lampiran_teks || '-'"></span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">Status:</span>
                        <span class="font-bold text-emerald-800 uppercase text-right" x-text="formData.status"></span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">Tempat, Tgl:</span>
                        <span class="font-bold text-gray-900 text-right" x-text="(formData.tempat_surat || 'Putak') + ', ' + formData.tgl_surat"></span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">Kode Verifikasi:</span>
                        <span class="font-mono font-bold text-emerald-700 text-right" x-text="kodeVerifikasi"></span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button" @click="showConfirmModal = false" :disabled="isSubmitting"
                            class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 rounded-xl text-xs font-bold transition shadow-2xs">
                        Periksa Kembali
                    </button>
                    <button type="button" @click="terbitkanSekarang()" :disabled="isSubmitting"
                            class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-extrabold shadow-md transition flex items-center gap-1.5">
                        <span x-show="!isSubmitting">✓ Ya, Simpan & Terbitkan</span>
                        <span x-show="isSubmitting">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
