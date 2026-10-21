<x-app-layout>
    @section('title', 'Upload & Catat Surat Masuk')

    <div class="max-w-3xl mx-auto space-y-6" x-data="{
        step: 1,
        progress1: 0,
        progress2: 0,
        isAnalyzing: false,
        fileSuratName: '',
        fileLampiranName: '',
        errorMsg: '',

        // Hasil deteksi otomatis dari dokumen surat masuk (semua dapat dikoreksi manual bila perlu)
        detected: {
            no_surat: '',
            saran_no_surat: '',
            is_nomor_lengkap: true,
            kode_jenis: 'SPm.01',
            perihal: '',
            lampiran_teks: '-',
            ada_lampiran: false,
            pengirim: '',
            tujuan: '',
            tgl_surat: '{{ date('Y-m-d') }}',
            tgl_diterima: '{{ date('Y-m-d') }}',
            tempat_surat: 'Putak'
        },

        inputNomorManual: '',
        noAgenda: '{{ $suggestedAgenda ?? '001/SM/' . date('Y') }}',
        showConfirmModal: false,
        isSubmitting: false,

        evaluasiLampiran() {
            const raw = (this.detected.lampiran_teks || '').trim().toLowerCase();
            if (raw === '' || raw === '-' || raw === '0' || raw === 'nihil' || raw.includes('tidak ada') || raw.includes('tanpa lampiran')) {
                this.detected.ada_lampiran = false;
            } else {
                this.detected.ada_lampiran = true;
            }
        },

        setLampiranCepat(val) {
            this.detected.lampiran_teks = val;
            this.evaluasiLampiran();
        },

        async handleFileSurat(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.errorMsg = '';
            this.fileSuratName = file.name;
            this.isAnalyzing = true;
            this.progress1 = 15;
            this.progress2 = 0;
            this.showConfirmModal = false;

            const timer = setInterval(() => {
                if (this.progress1 < 85) {
                    this.progress1 += 15;
                }
            }, 150);

            try {
                const formData = new FormData();
                formData.append('file_surat', file);
                formData.append('_token', '{{ csrf_token() }}');

                const resp = await fetch('{{ route('admin.surat-masuk.parse-docx') }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData
                });

                const json = await resp.json();
                clearInterval(timer);

                if (!resp.ok || !json.success) {
                    this.progress1 = 0;
                    this.isAnalyzing = false;
                    this.errorMsg = json.message || 'Gagal membaca dokumen surat masuk. Pastikan format file benar (.docx, .pdf, atau gambar).';
                    return;
                }

                this.progress1 = 100;
                this.isAnalyzing = false;
                this.detected = Object.assign({
                    tgl_diterima: '{{ date('Y-m-d') }}',
                    pengirim: ''
                }, json.data);
                this.evaluasiLampiran();

                if (this.detected.no_surat) {
                    this.inputNomorManual = this.detected.no_surat;
                } else {
                    this.inputNomorManual = '';
                }

                // Jika tidak ada lampiran tertulis, dan nomor + pengirim sudah terbaca -> langsung tampilkan pop-up konfirmasi
                if (!this.detected.ada_lampiran && this.detected.is_nomor_lengkap && this.inputNomorManual.trim() !== '') {
                    setTimeout(() => {
                        this.showConfirmModal = true;
                    }, 350);
                }
            } catch (e) {
                clearInterval(timer);
                this.progress1 = 0;
                this.isAnalyzing = false;
                this.errorMsg = 'Terjadi kesalahan saat memproses dokumen surat masuk.';
            }
        },

        handleFileLampiran(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.fileLampiranName = file.name;
            this.progress2 = 25;

            const timer = setInterval(() => {
                if (this.progress2 < 100) {
                    this.progress2 += 25;
                } else {
                    clearInterval(timer);
                    if (this.inputNomorManual.trim() !== '') {
                        setTimeout(() => {
                            this.showConfirmModal = true;
                        }, 300);
                    }
                }
            }, 120);
        },

        lanjutKonfirmasi() {
            if (!this.fileSuratName) {
                this.errorMsg = 'Silakan upload file dokumen surat masuk terlebih dahulu.';
                return;
            }
            if (!this.inputNomorManual.trim()) {
                this.errorMsg = 'Nomor surat asal wajib diisi terlebih dahulu.';
                return;
            }
            if (!this.detected.pengirim || !this.detected.pengirim.trim()) {
                this.errorMsg = 'Pengirim / Instansi Asal wajib diisi terlebih dahulu.';
                return;
            }
            this.evaluasiLampiran();
            if (this.detected.ada_lampiran && this.progress2 < 100) {
                this.errorMsg = 'Karena pada surat tertulis ada lampiran (' + this.detected.lampiran_teks + '), silakan upload berkas lampirannya pada Tahap 2.';
                return;
            }
            this.errorMsg = '';
            this.showConfirmModal = true;
        },

        simpanSekarang() {
            this.isSubmitting = true;
            document.getElementById('formUploadSuratMasuk').submit();
        }
    }">
        <!-- HEADER -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-4">
            <div>
                <a href="{{ route('admin.surat-masuk.index') }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-bold inline-flex items-center gap-1 mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span>Kembali ke Daftar Surat Masuk</span>
                </a>
                <h1 class="text-2xl font-extrabold text-gray-900">Upload & Catat Surat Masuk Otomatis</h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    Unggah file surat masuk (Word <span class="font-semibold">.docx</span>, <span class="font-semibold">.pdf</span>, atau <span class="font-semibold">Foto/Scan</span>). Sistem otomatis mendeteksi Nomor Surat, Perihal, Lampiran, Pengirim, serta Tempat & Tanggal, dan menyimpannya 100% asli tanpa merubah tampilan dokumen.
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 text-xs space-y-1">
                <div class="font-bold">Gagal menyimpan surat masuk:</div>
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

        <form method="POST" action="{{ route('admin.surat-masuk.store') }}" enctype="multipart/form-data" id="formUploadSuratMasuk" class="space-y-6">
            @csrf
            <!-- Hidden fields yang terhubung langsung ke hasil deteksi (dan otomatis ikut berubah jika dikoreksi manual) -->
            <input type="hidden" name="no_surat" :value="inputNomorManual">
            <input type="hidden" name="perihal" :value="detected.perihal">
            <input type="hidden" name="lampiran_teks" :value="detected.lampiran_teks">
            <input type="hidden" name="pengirim" :value="detected.pengirim">
            <input type="hidden" name="tgl_surat" :value="detected.tgl_surat">
            <input type="hidden" name="tgl_diterima" :value="detected.tgl_diterima">
            <input type="hidden" name="tempat_surat" :value="detected.tempat_surat">
            <input type="hidden" name="status_disposisi" value="baru">

            <!-- TAHAP 1: UPLOAD FILE SURAT MASUK & BAR PROGRES 1 -->
            <div class="bg-white rounded-2xl border-2 transition p-6 shadow-xs space-y-4"
                 :class="progress1 === 100 ? 'border-emerald-500' : 'border-gray-200'">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-extrabold"
                              :class="progress1 === 100 ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-800'">
                            1
                        </span>
                        <div>
                            <h2 class="text-sm font-extrabold text-gray-900">Tahap 1: Upload File Dokumen Surat Masuk</h2>
                            <p class="text-[11px] text-gray-500">Pilih file surat masuk (.docx, .pdf, atau gambar scan/foto) — disimpan 100% asli</p>
                        </div>
                    </div>
                    <span x-show="progress1 === 100" class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold">
                        ✓ Terbaca Otomatis (100%)
                    </span>
                </div>

                <!-- Kotak Pilih File Surat Masuk -->
                <label class="block cursor-pointer border-2 border-dashed rounded-2xl p-6 text-center transition"
                       :class="progress1 === 100 ? 'border-emerald-400 bg-emerald-50/40' : 'border-gray-300 hover:border-emerald-500 bg-gray-50/60 hover:bg-emerald-50/20'">
                    <input type="file" name="file_surat_asli" accept=".docx,.doc,.pdf,.jpg,.jpeg,.png" required class="hidden" @change="handleFileSurat($event)">
                    <div class="space-y-2">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <div x-show="!fileSuratName" class="text-sm font-bold text-gray-800">
                            Klik untuk memilih file Surat Masuk (<span class="text-emerald-700">.docx / .pdf / .jpg / .png</span>)
                        </div>
                        <div x-show="fileSuratName" class="text-sm font-extrabold text-emerald-900" x-text="fileSuratName"></div>
                        <p class="text-xs text-gray-500">Sistem otomatis mendeteksi Nomor Surat, Perihal, Lampiran, Pengirim, dan Tanggal Surat.</p>
                    </div>
                </label>

                <!-- BAR PROGRES 1 -->
                <div x-show="progress1 > 0" x-cloak class="space-y-1.5">
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-emerald-800" x-text="isAnalyzing ? 'Menganalisis & mencatat isi surat masuk...' : 'Pencatatan otomatis selesai'"></span>
                        <span class="text-emerald-700" x-text="progress1 + '%'"></span>
                    </div>
                    <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-600 transition-all duration-300 rounded-full" :style="'width: ' + progress1 + '%'"></div>
                    </div>
                </div>

                <!-- HASIL DETEKSI OTOMATIS (DILENGKAPI INPUT KOREKSI MANUAL JIKA SISTEM SALAH BACA) -->
                <div x-show="progress1 === 100" x-cloak class="bg-gray-50 rounded-xl border border-gray-200 p-4 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 pb-3">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-900">📋 Hasil Pencatatan Otomatis Sistem</span>
                            <p class="text-[11px] text-gray-500">Terisi otomatis dari dokumen Anda. Jika ada yang kurang tepat, Anda dapat langsung mengoreksinya pada kolom di bawah.</p>
                        </div>
                        <div class="flex items-center gap-2 bg-white border border-emerald-200 px-3 py-1.5 rounded-xl shadow-xs">
                            <div class="text-[11px] font-mono text-gray-700 leading-tight">
                                <span class="block text-[10px] text-gray-400 font-sans font-semibold uppercase">No. Agenda Otomatis:</span>
                                <strong class="text-emerald-700 text-xs">#<span x-text="noAgenda"></span></strong>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <!-- 1. Nomor Surat Asal (Otomatis terdeteksi atau input jika kosong/belum lengkap) -->
                        <div class="sm:col-span-2 p-3.5 rounded-xl border"
                             :class="detected.is_nomor_lengkap ? 'bg-white border-gray-200' : 'bg-amber-50 border-amber-300'">
                            <div class="flex flex-wrap items-center justify-between gap-1 mb-1.5">
                                <label class="text-gray-600 font-bold text-[11px] uppercase tracking-wider">Nomor Surat Asal</label>
                                <span x-show="!detected.is_nomor_lengkap" class="text-[11px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded">
                                    ⚠️ Nomor surat kosong/belum lengkap — Silakan lengkapi nomor surat asal di bawah
                                </span>
                                <span x-show="detected.is_nomor_lengkap" class="text-[11px] font-bold text-emerald-700">
                                    ✓ Terdeteksi Otomatis (Dapat dikoreksi jika perlu)
                                </span>
                            </div>

                            <input type="text" x-model="inputNomorManual"
                                   placeholder="Contoh: B-102/Kk.06.05/PP.00/10/2026"
                                   class="w-full text-sm font-mono font-bold rounded-lg py-2 px-3 bg-white focus:border-emerald-600 focus:ring-emerald-600"
                                   :class="detected.is_nomor_lengkap ? 'border-gray-300 text-gray-900' : 'border-amber-400 text-gray-900'" />
                        </div>

                        <!-- 2. Perihal Terdeteksi (Otomatis + Bisa Dikoreksi Manual) -->
                        <div class="p-3.5 bg-white rounded-xl border border-gray-200 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-gray-500 font-bold text-[11px] uppercase tracking-wider">Perihal Terdeteksi</label>
                                <span class="text-[10px] text-emerald-700 font-semibold">Otomatis / Edit</span>
                            </div>
                            <input type="text" x-model="detected.perihal"
                                   placeholder="Masukkan perihal surat..."
                                   class="w-full text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2 px-2.5" />
                        </div>

                        <!-- 3. Lampiran Terdeteksi (Otomatis + Bisa Dikoreksi Manual: Tanpa Lampiran, 1 Lembar, 2 Lembar, 1 Berkas, dst) -->
                        <div class="p-3.5 bg-white rounded-xl border border-gray-200 space-y-1.5">
                            <div class="flex items-center justify-between gap-1">
                                <label class="block text-gray-500 font-bold text-[11px] uppercase tracking-wider">Lampiran Terdeteksi</label>
                                <span x-show="detected.ada_lampiran" class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-[10px] font-bold">
                                    Wajib Upload Lampiran (Tahap 2)
                                </span>
                                <span x-show="!detected.ada_lampiran" class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-semibold">
                                    Tanpa Lampiran
                                </span>
                            </div>
                            <input type="text" x-model="detected.lampiran_teks" @input="evaluasiLampiran()"
                                   placeholder="Contoh: - atau 1 Lembar atau 1 Berkas"
                                   class="w-full text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2 px-2.5" />
                            <div class="flex flex-wrap items-center gap-1 pt-0.5">
                                <span class="text-[10px] text-gray-400 mr-1">Pilih Cepat:</span>
                                <button type="button" @click="setLampiranCepat('-')"
                                        class="px-2 py-0.5 rounded border text-[10px] font-semibold transition"
                                        :class="!detected.ada_lampiran ? 'bg-gray-800 text-white border-gray-800' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100'">
                                    Tanpa Lampiran (-)
                                </button>
                                <button type="button" @click="setLampiranCepat('1 Lembar')"
                                        class="px-2 py-0.5 rounded border text-[10px] font-semibold transition"
                                        :class="detected.lampiran_teks === '1 Lembar' ? 'bg-blue-700 text-white border-blue-700' : 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100'">
                                    1 Lembar
                                </button>
                                <button type="button" @click="setLampiranCepat('2 Lembar')"
                                        class="px-2 py-0.5 rounded border text-[10px] font-semibold transition"
                                        :class="detected.lampiran_teks === '2 Lembar' ? 'bg-blue-700 text-white border-blue-700' : 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100'">
                                    2 Lembar
                                </button>
                                <button type="button" @click="setLampiranCepat('1 Berkas')"
                                        class="px-2 py-0.5 rounded border text-[10px] font-semibold transition"
                                        :class="detected.lampiran_teks === '1 Berkas' ? 'bg-blue-700 text-white border-blue-700' : 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100'">
                                    1 Berkas
                                </button>
                            </div>
                        </div>

                        <!-- 4. Pengirim / Instansi Asal Surat -->
                        <div class="p-3.5 bg-white rounded-xl border border-gray-200 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-gray-500 font-bold text-[11px] uppercase tracking-wider">Pengirim / Instansi Asal</label>
                                <span class="text-[10px] text-emerald-700 font-semibold">Otomatis / Edit</span>
                            </div>
                            <input type="text" x-model="detected.pengirim"
                                   placeholder="Contoh: Kementerian Agama Kab. Muara Enim..."
                                   class="w-full text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2 px-2.5" />
                        </div>

                        <!-- 5. Tempat, Tanggal Surat, & Tanggal Diterima -->
                        <div class="p-3.5 bg-white rounded-xl border border-gray-200 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-gray-500 font-bold text-[11px] uppercase tracking-wider">Tempat & Tanggal Surat</label>
                                <span class="text-[10px] text-emerald-700 font-semibold">Otomatis / Edit</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" x-model="detected.tempat_surat"
                                       placeholder="Tempat"
                                       class="w-full text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2 px-2.5" />
                                <input type="date" x-model="detected.tgl_surat"
                                       title="Tanggal Surat"
                                       class="w-full text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2 px-2.5" />
                            </div>
                            <div class="pt-1 flex items-center justify-between gap-2">
                                <span class="text-[10px] font-semibold text-gray-500">Tanggal Diterima:</span>
                                <input type="date" x-model="detected.tgl_diterima"
                                       class="text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-1 px-2" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAHAP 2: BAR PROGRES KEDUA UPLOAD LAMPIRAN (HANYA MUNCUL JIKA ADA LAMPIRAN TERTULIS DI SURAT) -->
            <div x-show="progress1 === 100 && detected.ada_lampiran" x-cloak
                 class="bg-white rounded-2xl border-2 p-6 shadow-xs space-y-4 transition"
                 :class="progress2 === 100 ? 'border-emerald-500' : 'border-blue-400'">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-extrabold"
                              :class="progress2 === 100 ? 'bg-emerald-600 text-white' : 'bg-blue-600 text-white'">
                            2
                        </span>
                        <div>
                            <h2 class="text-sm font-extrabold text-gray-900">
                                Tahap 2: Upload Berkas Lampiran (<span class="text-blue-700" x-text="detected.lampiran_teks"></span>)
                            </h2>
                            <p class="text-[11px] text-gray-500">
                                Karena tertulis lampiran <strong x-text="detected.lampiran_teks"></strong>, silakan unggah file lampirannya.
                            </p>
                        </div>
                    </div>
                    <span x-show="progress2 === 100" class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold">
                        ✓ Lampiran Siap (100%)
                    </span>
                </div>

                <label class="block cursor-pointer border-2 border-dashed rounded-2xl p-5 text-center transition"
                       :class="progress2 === 100 ? 'border-emerald-400 bg-emerald-50/40' : 'border-blue-300 hover:border-blue-500 bg-blue-50/30'">
                    <input type="file" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden" @change="handleFileLampiran($event)">
                    <div class="space-y-1.5">
                        <div class="text-xs font-bold text-blue-900" x-show="!fileLampiranName">
                            📎 Klik untuk memilih berkas lampiran (Word / Gambar / PDF)
                        </div>
                        <div class="text-sm font-extrabold text-emerald-900" x-show="fileLampiranName" x-text="fileLampiranName"></div>
                    </div>
                </label>

                <!-- BAR PROGRES 2 -->
                <div x-show="progress2 > 0" class="space-y-1.5">
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-blue-900" x-text="progress2 < 100 ? 'Memuat berkas lampiran...' : 'Berkas lampiran siap disertakan'"></span>
                        <span class="text-blue-700" x-text="progress2 + '%'"></span>
                    </div>
                    <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-600 transition-all duration-300 rounded-full" :style="'width: ' + progress2 + '%'"></div>
                    </div>
                </div>
            </div>

            <!-- TOMBOL LANJUT SIMPAN (JIKA POPUP DITUTUP ATAU HABIS MENGEDIT KOLOM MANUAL) -->
            <div x-show="progress1 === 100" x-cloak class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.surat-masuk.index') }}" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl text-xs font-bold hover:bg-gray-50">
                    Batal
                </a>
                <button type="button" @click="lanjutKonfirmasi()"
                        class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-extrabold shadow-md transition">
                    ✓ Lanjutkan & Simpan Dokumen Surat Masuk
                </button>
            </div>
        </form>

        <!-- POP-UP KONFIRMASI PENYIMPANAN DOKUMEN SURAT MASUK -->
        <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-5 shadow-2xl border border-gray-100" @click.away="if (!isSubmitting) showConfirmModal = false">
                <div class="text-center space-y-2">
                    <div class="w-14 h-14 mx-auto rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl font-bold">
                        📥
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900">
                        Apakah Anda yakin ingin menyimpan dokumen surat masuk ini?
                    </h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Dokumen disimpan <strong>100% asli sesuai file yang Anda upload</strong> (tanpa mengubah tampilan, header, maupun footer) dan dicatat otomatis ke Buku Agenda Surat Masuk.
                    </p>
                </div>

                <!-- Ringkasan Dokumen yang Akan Disimpan -->
                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-200 space-y-2 text-xs">
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">No. Agenda:</span>
                        <span class="font-mono font-bold text-emerald-700 text-right">#<span x-text="noAgenda"></span></span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">No. Surat Asal:</span>
                        <span class="font-mono font-bold text-gray-900 text-right" x-text="inputNomorManual"></span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">Perihal:</span>
                        <span class="font-bold text-gray-900 text-right" x-text="detected.perihal"></span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">Lampiran:</span>
                        <span class="font-bold text-gray-900 text-right" x-text="detected.lampiran_teks"></span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">Pengirim:</span>
                        <span class="font-bold text-gray-900 text-right" x-text="detected.pengirim"></span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">Tempat, Tgl Surat:</span>
                        <span class="font-bold text-gray-900 text-right" x-text="(detected.tempat_surat || 'Putak') + ', ' + detected.tgl_surat"></span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button" @click="showConfirmModal = false" :disabled="isSubmitting"
                            class="px-4 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl text-xs font-bold transition">
                        Periksa / Edit Manual
                    </button>
                    <button type="button" @click="simpanSekarang()" :disabled="isSubmitting"
                            class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-extrabold shadow-md transition flex items-center gap-1.5">
                        <span x-show="!isSubmitting">✓ Ya, Simpan Surat Masuk</span>
                        <span x-show="isSubmitting">Menyimpan Dokumen...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
