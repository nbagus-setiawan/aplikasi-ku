<x-app-layout>
    @section('title', 'Ubah Surat Keluar')

    @php
        $penandatangansList = $suratKeluar->penandatangans ?? [];
        $p1 = $penandatangansList[0] ?? ['nama' => $suratKeluar->nama_penandatangan, 'jabatan' => $suratKeluar->jabatan_penandatangan];
        $p2 = $penandatangansList[1] ?? null;
        $p3 = $penandatangansList[2] ?? null;

        $ttdMap = $suratKeluar->tandaTangans->keyBy('urutan');

        $templatesJson = $templateList->map(function ($t) {
            return [
                'id' => $t->id,
                'nama' => $t->nama,
                'kode' => $t->kode_jenis_surat ?: ($t->kategori?->kode ?? 'SPm.01'),
                'tujuan' => $t->tujuan_default ?? '',
                'perihal' => $t->perihal_default ?? '',
                'isi' => $t->isi_template ?? '',
            ];
        })->values();

        $pengurusJson = $pengurusList->map(function ($p) {
            return [
                'nama' => $p->nama,
                'jabatan' => $p->jabatan,
            ];
        })->values();
    @endphp

    <div class="max-w-[1520px] mx-auto space-y-4" x-data="{
        templates: @js($templatesJson),
        pengurusList: @js($pengurusJson),
        selectedTemplateId: '',
        kertas: '{{ old('ukuran_kertas', $suratKeluar->ukuran_kertas ?? 'a4') }}',
        margin: '{{ old('margin', $suratKeluar->margin ?? 'standar') }}',
        jmlTtd: {{ (int) old('jumlah_penandatangan', $suratKeluar->jumlah_penandatangan ?? 1) }},
        metodeTtd: '{{ old('metode_ttd_pilihan', $suratKeluar->metode_ttd ?? 'digital') }}',
        showTemplateModal: false,
        templateModalMode: 'buat',
        tplNama: '',
        tplKode: '{{ $suratKeluar->kategori->kode ?? 'SPm.01' }}',
        tplTujuan: '',
        tplPerihal: '',
        tplIsi: '',
        kodeVerifikasi: '{{ old('kode_verifikasi', $suratKeluar->kode_verifikasi) }}',
        kodeJenis: '{{ old('kode_jenis_surat', $suratKeluar->kategori->kode ?? 'SPm.01') }}',
        tempatSurat: @js(old('tempat_surat', $suratKeluar->tempat_surat ?: 'Putak')),
        tglSurat: '{{ old('tgl_surat', $suratKeluar->tgl_surat ? $suratKeluar->tgl_surat->format('Y-m-d') : date('Y-m-d')) }}',
        lampiranTeks: @js(old('lampiran_teks', $suratKeluar->lampiran_teks ?: '-')),
        tujuan: @js(old('tujuan', $suratKeluar->tujuan)),
        perihal: @js(old('perihal', $suratKeluar->perihal)),
        isiSurat: @js(old('isi', $suratKeluar->isi)),
        modeHtmlRaw: false,
        namaTtd1: @js(old('nama_penandatangan', $p1['nama'] ?? $suratKeluar->nama_penandatangan)),
        jabatanTtd1: @js(old('jabatan_penandatangan', $p1['jabatan'] ?? $suratKeluar->jabatan_penandatangan)),
        namaTtd2: @js(old('penandatangan_2_nama', $p2['nama'] ?? '')),
        jabatanTtd2: @js(old('penandatangan_2_jabatan', $p2['jabatan'] ?? 'Ketua Panitia')),
        namaTtd3: @js(old('penandatangan_3_nama', $p3['nama'] ?? '')),
        jabatanTtd3: @js(old('penandatangan_3_jabatan', $p3['jabatan'] ?? 'Pimpinan Pondok Pesantren')),
        sig1: @js(old('data_ttd_langsung', $ttdMap[1]?->data_ttd ?? '')),
        sig2: @js(old('data_ttd_2', $ttdMap[2]?->data_ttd ?? '')),
        sig3: @js(old('data_ttd_3', $ttdMap[3]?->data_ttd ?? '')),
        signModalOpen: false,
        signTargetUrutan: 1,
        aksiSimpan: 'draft',

        init() {
            this.$nextTick(() => {
                if (this.$refs.visualEditor) {
                    this.$refs.visualEditor.innerHTML = this.isiSurat;
                }
            });
        },

        terapkanTemplate(tpl) {
            this.selectedTemplateId = tpl.id;
            if (tpl.kode) this.kodeJenis = tpl.kode;
            if (tpl.tujuan) this.tujuan = tpl.tujuan;
            if (tpl.perihal) this.perihal = tpl.perihal;
            if (tpl.isi) {
                this.isiSurat = tpl.isi;
                if (this.$refs.visualEditor) {
                    this.$refs.visualEditor.innerHTML = tpl.isi;
                }
            }
        },

        syncFromVisual() {
            if (this.$refs.visualEditor) {
                this.isiSurat = this.$refs.visualEditor.innerHTML;
            }
        },

        syncFromRaw() {
            if (this.$refs.visualEditor) {
                this.$refs.visualEditor.innerHTML = this.isiSurat;
            }
        },

        execCmd(cmd, val = null) {
            if (this.modeHtmlRaw) return;
            this.$refs.visualEditor.focus();
            document.execCommand(cmd, false, val);
            this.syncFromVisual();
        },

        insertSnippet(type) {
            let html = '';
            if (type === 'salam') {
                html = `<p><em>Assalamu'alaikum Warahmatullahi Wabarakatuh,</em></p><p style='text-indent: 28px;'>Segala puji bagi Allah Subhanahu wa Ta'ala, shalawat serta salam semoga senantiasa tercurah kepada Rasulullah Muhammad Shallallahu 'Alaihi wa Sallam.</p><p style='text-indent: 28px;'>Demikian surat ini kami sampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.</p><p><em>Wassalamu'alaikum Warahmatullahi Wabarakatuh.</em></p>`;
            } else if (type === 'paragraf') {
                html = `<p style='text-indent: 28px;'>Ketik isi paragraf baru di sini...</p>`;
            } else if (type === 'jadwal') {
                html = `<table style='border-collapse: collapse; width: 100%; border: none; margin: 8px 0;'>
                    <tr><td style='border: none; width: 28%; padding: 2px 0;'>Hari / Tanggal</td><td style='border: none; width: 3%;'>:</td><td style='border: none; padding: 2px 0;'><strong>Sabtu, 24 Oktober 2026</strong></td></tr>
                    <tr><td style='border: none; padding: 2px 0;'>Waktu</td><td style='border: none;'>:</td><td style='border: none; padding: 2px 0;'>08.30 WIB s.d. Selesai</td></tr>
                    <tr><td style='border: none; padding: 2px 0;'>Tempat</td><td style='border: none;'>:</td><td style='border: none; padding: 2px 0;'>Aula Pondok Pesantren Al Falah Putra</td></tr>
                </table>`;
            } else if (type === 'biodata') {
                html = `<table style='border-collapse: collapse; width: 100%; border: none; margin: 8px 0;'>
                    <tr><td style='border: none; width: 28%; padding: 2px 0;'>Nama Lengkap</td><td style='border: none; width: 3%;'>:</td><td style='border: none; padding: 2px 0;'><strong>[Nama Santri]</strong></td></tr>
                    <tr><td style='border: none; padding: 2px 0;'>NIS</td><td style='border: none;'>:</td><td style='border: none; padding: 2px 0;'>[NIS]</td></tr>
                    <tr><td style='border: none; padding: 2px 0;'>Kelas / Kamar</td><td style='border: none;'>:</td><td style='border: none; padding: 2px 0;'>[Kelas] / [Kamar]</td></tr>
                </table>`;
            } else if (type === 'tabel') {
                html = `<table style='border-collapse: collapse; width: 100%; margin: 10px 0;'>
                    <thead><tr><th style='border: 1px solid #333; padding: 5px; background: #f3f4f6;'>No</th><th style='border: 1px solid #333; padding: 5px; background: #f3f4f6;'>Nama / Uraian</th><th style='border: 1px solid #333; padding: 5px; background: #f3f4f6;'>Keterangan</th></tr></thead>
                    <tbody><tr><td style='border: 1px solid #333; padding: 5px; text-align: center;'>1</td><td style='border: 1px solid #333; padding: 5px;'>Contoh Uraian</td><td style='border: 1px solid #333; padding: 5px;'>-</td></tr></tbody>
                </table>`;
            }

            if (this.modeHtmlRaw) {
                this.isiSurat += '\n' + html;
            } else if (this.$refs.visualEditor) {
                this.$refs.visualEditor.focus();
                document.execCommand('insertHTML', false, html);
                this.syncFromVisual();
            }
        },

        pilihPengurus(urutan, idx) {
            if (idx === '' || !this.pengurusList[idx]) return;
            const p = this.pengurusList[idx];
            if (urutan === 1) {
                this.namaTtd1 = p.nama;
                this.jabatanTtd1 = p.jabatan;
            } else if (urutan === 2) {
                this.namaTtd2 = p.nama;
                this.jabatanTtd2 = p.jabatan;
            } else if (urutan === 3) {
                this.namaTtd3 = p.nama;
                this.jabatanTtd3 = p.jabatan;
            }
        },

        bukaModalTtd(urutan) {
            this.signTargetUrutan = urutan;
            this.signModalOpen = true;
            this.$nextTick(() => {
                window.initModalSignaturePad();
            });
        },

        simpanGoresanModal() {
            const dataUrl = window.getModalSignatureData();
            if (!dataUrl) {
                alert('Silakan goreskan tanda tangan terlebih dahulu di kotak kanvas.');
                return;
            }
            if (this.signTargetUrutan === 1) this.sig1 = dataUrl;
            else if (this.signTargetUrutan === 2) this.sig2 = dataUrl;
            else if (this.signTargetUrutan === 3) this.sig3 = dataUrl;
            this.metodeTtd = 'digital';
            this.signModalOpen = false;
        },

        hapusTtd(urutan) {
            if (urutan === 1) this.sig1 = '';
            else if (urutan === 2) this.sig2 = '';
            else if (urutan === 3) this.sig3 = '';
        },

        submitSurat(mode) {
            this.syncFromVisual();
            this.aksiSimpan = mode;
            this.$nextTick(() => {
                document.getElementById('formSuratKeluar').submit();
            });
        },

        bukaSimpanSebagaiTemplate() {
            this.syncFromVisual();
            this.templateModalMode = 'simpan_sekarang';
            this.tplNama = this.perihal || 'Template Surat Baru';
            this.tplKode = this.kodeJenis || 'SPm.01';
            this.tplTujuan = this.tujuan;
            this.tplPerihal = this.perihal;
            this.tplIsi = this.isiSurat;
            this.showTemplateModal = true;
        },

        formatTanggalIndo(tglStr) {
            if (!tglStr) return '';
            const bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const parts = tglStr.split('-');
            if (parts.length === 3) {
                return `${parseInt(parts[2], 10)} ${bulanIndo[parseInt(parts[1], 10) - 1] || parts[1]} ${parts[0]}`;
            }
            return tglStr;
        }
    }">
        <!-- TOP ACTION BAR (STICKY) -->
        <div class="sticky top-0 z-30 bg-white/95 backdrop-blur border border-gray-200 rounded-2xl px-4 py-3 shadow-sm flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.surat-keluar.show', $suratKeluar) }}" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 transition shadow-2xs" title="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-base sm:text-lg font-extrabold text-gray-900">Ubah Surat Keluar</h1>
                        <span class="px-2 py-0.5 text-[11px] font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-lg">
                            {{ $suratKeluar->no_surat }}
                        </span>
                    </div>
                    <p class="text-[11px] text-gray-500">Klik dan sunting langsung di atas lembar kertas sebelah kanan.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" @click="submitSurat('draft')"
                        class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    💾 Simpan Perubahan
                </button>
                <button type="button" @click="submitSurat('cetak')"
                        class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-xl text-xs font-extrabold transition shadow-sm flex items-center gap-1.5">
                    <span>🖨️ Simpan & Langsung Cetak PDF</span>
                </button>
                <button type="button" @click="submitSurat('siap_kirim')"
                        class="px-4 py-2 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-xs font-extrabold transition shadow-sm flex items-center gap-1.5">
                    <span>🚀 Simpan & Siap Kirim (Final)</span>
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.surat-keluar.update', $suratKeluar) }}" enctype="multipart/form-data" id="formSuratKeluar">
            @csrf
            @method('PUT')
            <input type="hidden" name="aksi_simpan" :value="aksiSimpan">
            <input type="hidden" name="jumlah_penandatangan" :value="jmlTtd">
            <input type="hidden" name="metode_ttd_pilihan" :value="metodeTtd">
            <input type="hidden" name="data_ttd_langsung" :value="sig1">
            <input type="hidden" name="data_ttd_2" :value="sig2">
            <input type="hidden" name="data_ttd_3" :value="sig3">
            <textarea name="isi" x-model="isiSurat" class="hidden"></textarea>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

                <!-- PANEL KIRI (4 KOLOM) -->
                <div class="lg:col-span-4 space-y-4 lg:sticky lg:top-20">
                    <!-- 1. Pilih Cepat Template -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-900">1. Template Cepat</span>
                            <button type="button" @click="bukaSimpanSebagaiTemplate()" class="text-[11px] font-bold text-emerald-700 hover:underline px-1.5 py-0.5 rounded bg-emerald-50">
                                + Simpan Jadi Template
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto pr-1">
                            <template x-for="tpl in templates" :key="tpl.id">
                                <button type="button" @click="terapkanTemplate(tpl)"
                                        :class="String(selectedTemplateId) === String(tpl.id) ? 'border-emerald-600 bg-emerald-50/90 ring-2 ring-emerald-500/20' : 'border-gray-200 hover:border-emerald-400 bg-gray-50/60'"
                                        class="text-left rounded-xl border p-2 transition">
                                    <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800" x-text="tpl.kode"></span>
                                    <div class="font-bold text-[11px] text-gray-900 mt-1 line-clamp-1" x-text="tpl.nama"></div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- 2. Jenis & Tanggal -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-2xs space-y-3">
                        <span class="block text-xs font-extrabold uppercase tracking-wider text-emerald-900">2. Jenis & Tanggal Surat</span>
                        <div class="grid grid-cols-2 gap-2.5">
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Kode Jenis Surat</label>
                                <input type="text" name="kode_jenis_surat" x-model="kodeJenis" required
                                       class="w-full text-xs font-mono font-bold uppercase rounded-xl border-gray-300 py-1.5 px-2.5" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Tempat Surat</label>
                                <input type="text" name="tempat_surat" x-model="tempatSurat"
                                       class="w-full text-xs rounded-xl border-gray-300 py-1.5 px-2.5" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Tanggal Surat</label>
                                <input type="date" name="tgl_surat" x-model="tglSurat" required
                                       class="w-full text-xs rounded-xl border-gray-300 py-1.5 px-2.5" />
                            </div>
                        </div>
                    </div>

                    <!-- 3. Penandatangan & Metode TTD -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-900">3. Penandatangan & TTD</span>
                            <div class="inline-flex rounded-lg border border-gray-300 bg-gray-50 p-0.5">
                                <button type="button" @click="jmlTtd = 1" :class="jmlTtd === 1 ? 'bg-emerald-700 text-white font-bold' : 'text-gray-600'" class="px-2 py-0.5 rounded text-[11px]">1 Org</button>
                                <button type="button" @click="jmlTtd = 2" :class="jmlTtd === 2 ? 'bg-emerald-700 text-white font-bold' : 'text-gray-600'" class="px-2 py-0.5 rounded text-[11px]">2 Org</button>
                                <button type="button" @click="jmlTtd = 3" :class="jmlTtd === 3 ? 'bg-emerald-700 text-white font-bold' : 'text-gray-600'" class="px-2 py-0.5 rounded text-[11px]">3 Org</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-1.5 bg-gray-100 p-1 rounded-xl">
                            <button type="button" @click="metodeTtd = 'digital'"
                                    :class="metodeTtd === 'digital' ? 'bg-white text-emerald-900 font-bold shadow-2xs' : 'text-gray-600'"
                                    class="py-1.5 px-2 rounded-lg text-[11px] transition text-center">
                                ✍️ TTD Digital di Layar
                            </button>
                            <button type="button" @click="metodeTtd = 'basah'"
                                    :class="metodeTtd === 'basah' ? 'bg-white text-gray-900 font-bold shadow-2xs' : 'text-gray-600'"
                                    class="py-1.5 px-2 rounded-lg text-[11px] transition text-center">
                                🖨️ TTD Basah (Cetak)
                            </button>
                        </div>

                        <div class="space-y-2.5">
                            <!-- Penandatangan 1 -->
                            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-emerald-900">Penandatangan 1</span>
                                    @if ($pengurusList->isNotEmpty())
                                        <select @change="pilihPengurus(1, $event.target.value); $event.target.value = ''"
                                                class="text-[10px] rounded-lg border-gray-300 py-0.5 pl-1.5 pr-5 bg-white text-gray-700">
                                            <option value="">⚡ Pilih Pengurus</option>
                                            @foreach ($pengurusList as $idx => $p)
                                                <option value="{{ $idx }}">{{ $p->nama }} ({{ $p->jabatan }})</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <input type="text" name="nama_penandatangan" x-model="namaTtd1" required class="w-full text-xs rounded-lg border-gray-300 py-1 px-2 bg-white" />
                                    <input type="text" name="jabatan_penandatangan" x-model="jabatanTtd1" required class="w-full text-xs rounded-lg border-gray-300 py-1 px-2 bg-white" />
                                </div>
                                <div x-show="metodeTtd === 'digital'" class="flex items-center justify-between pt-1">
                                    <span class="text-[10px]" :class="sig1 ? 'text-emerald-700 font-bold' : 'text-gray-500'" x-text="sig1 ? '✓ Sudah Tanda Tangan' : 'Belum TTD'"></span>
                                    <button type="button" @click="bukaModalTtd(1)" class="px-2 py-0.5 bg-emerald-600 text-white rounded text-[10px] font-bold">✍️ Gambar TTD 1</button>
                                </div>
                            </div>

                            <!-- Penandatangan 2 -->
                            <div x-show="jmlTtd >= 2" x-cloak class="p-2.5 rounded-xl bg-gray-50 border border-gray-200 space-y-1.5">
                                <span class="text-[11px] font-bold text-blue-900">Penandatangan 2</span>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <input type="text" name="penandatangan_2_nama" x-model="namaTtd2" class="w-full text-xs rounded-lg border-gray-300 py-1 px-2 bg-white" />
                                    <input type="text" name="penandatangan_2_jabatan" x-model="jabatanTtd2" class="w-full text-xs rounded-lg border-gray-300 py-1 px-2 bg-white" />
                                </div>
                                <div x-show="metodeTtd === 'digital'" class="flex items-center justify-between pt-1">
                                    <span class="text-[10px]" :class="sig2 ? 'text-emerald-700 font-bold' : 'text-gray-500'" x-text="sig2 ? '✓ Sudah Tanda Tangan' : 'Belum TTD'"></span>
                                    <button type="button" @click="bukaModalTtd(2)" class="px-2 py-0.5 bg-blue-600 text-white rounded text-[10px] font-bold">✍️ Gambar TTD 2</button>
                                </div>
                            </div>

                            <!-- Penandatangan 3 -->
                            <div x-show="jmlTtd >= 3" x-cloak class="p-2.5 rounded-xl bg-gray-50 border border-gray-200 space-y-1.5">
                                <span class="text-[11px] font-bold text-purple-900">Penandatangan 3</span>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <input type="text" name="penandatangan_3_nama" x-model="namaTtd3" class="w-full text-xs rounded-lg border-gray-300 py-1 px-2 bg-white" />
                                    <input type="text" name="penandatangan_3_jabatan" x-model="jabatanTtd3" class="w-full text-xs rounded-lg border-gray-300 py-1 px-2 bg-white" />
                                </div>
                                <div x-show="metodeTtd === 'digital'" class="flex items-center justify-between pt-1">
                                    <span class="text-[10px]" :class="sig3 ? 'text-emerald-700 font-bold' : 'text-gray-500'" x-text="sig3 ? '✓ Sudah Tanda Tangan' : 'Belum TTD'"></span>
                                    <button type="button" @click="bukaModalTtd(3)" class="px-2 py-0.5 bg-purple-600 text-white rounded text-[10px] font-bold">✍️ Gambar TTD 3</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Kertas & Lampiran -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-2xs space-y-3">
                        <span class="block text-xs font-extrabold uppercase tracking-wider text-emerald-900">4. Kertas & Lampiran</span>
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Ukuran Kertas</label>
                                <select name="ukuran_kertas" x-model="kertas" class="w-full text-xs rounded-xl border-gray-300 py-1.5 px-2">
                                    <option value="a4">A4</option>
                                    <option value="f4">F4 / Folio</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Margin</label>
                                <select name="margin" x-model="margin" class="w-full text-xs rounded-xl border-gray-300 py-1.5 px-2">
                                    <option value="standar">Standar Dinas</option>
                                    <option value="ringkas">Ringkas</option>
                                    <option value="lebar">Lebar</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Ganti Berkas Lampiran</label>
                                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                       class="w-full text-xs file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-800 border border-gray-300 rounded-xl" />
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Kode QR Verifikasi</label>
                                <input type="text" name="kode_verifikasi" x-model="kodeVerifikasi" maxlength="8"
                                       class="w-full text-xs font-mono font-bold uppercase rounded-xl border-gray-300 py-1.5 px-2.5" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KANAN: KERTAS INTERAKTIF WYSIWYG (8 KOLOM) -->
                <div class="lg:col-span-8 space-y-2.5">
                    <div class="bg-slate-800 text-white rounded-2xl p-2.5 shadow-md flex flex-wrap items-center justify-between gap-2 border border-slate-700">
                        <div class="flex flex-wrap items-center gap-1">
                            <button type="button" @click="execCmd('bold')" class="px-2.5 py-1 bg-slate-700 hover:bg-slate-600 rounded-lg font-bold text-xs">B</button>
                            <button type="button" @click="execCmd('italic')" class="px-2.5 py-1 bg-slate-700 hover:bg-slate-600 rounded-lg italic font-serif text-xs">I</button>
                            <button type="button" @click="execCmd('underline')" class="px-2.5 py-1 bg-slate-700 hover:bg-slate-600 rounded-lg underline text-xs">U</button>
                            <span class="h-4 w-px bg-slate-600 mx-1"></span>
                            <button type="button" @click="execCmd('justifyLeft')" class="px-2 py-1 bg-slate-700 hover:bg-slate-600 rounded-lg text-[11px]">⬅ Kiri</button>
                            <button type="button" @click="execCmd('justifyCenter')" class="px-2 py-1 bg-slate-700 hover:bg-slate-600 rounded-lg text-[11px]">⬌ Tengah</button>
                            <button type="button" @click="execCmd('justifyFull')" class="px-2 py-1 bg-slate-700 hover:bg-slate-600 rounded-lg text-[11px]">☰ Rata Kanan-Kiri</button>
                        </div>
                        <div class="flex flex-wrap items-center gap-1">
                            <button type="button" @click="insertSnippet('jadwal')" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-bold">+ Jadwal</button>
                            <button type="button" @click="insertSnippet('biodata')" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-bold">+ Santri</button>
                            <button type="button" @click="insertSnippet('tabel')" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-bold">+ Tabel</button>
                            <button type="button" @click="modeHtmlRaw = !modeHtmlRaw; if (!modeHtmlRaw) $nextTick(() => syncFromRaw())"
                                    class="px-2 py-1 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg text-[11px] font-semibold">
                                <span x-text="modeHtmlRaw ? 'Visual' : 'HTML'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="bg-slate-200/80 rounded-2xl p-4 sm:p-8 border border-slate-300 shadow-inner overflow-x-auto flex justify-center">
                        <div class="bg-white text-gray-900 shadow-2xl w-full max-w-[21cm] min-h-[29.7cm] p-[2cm_2.5cm] flex flex-col justify-between relative"
                             style="font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.45;">
                            <div>
                                <!-- KOP SURAT -->
                                <div class="border-b-4 border-double border-black pb-2.5 mb-4 text-center">
                                    <div class="text-sm font-bold uppercase tracking-wider">{{ $pengaturan['kop_yayasan'] ?? 'YAYASAN PONDOK PESANTREN AL-FALAH PUTAK' }}</div>
                                    <div class="text-lg font-extrabold uppercase tracking-wide">{{ $pengaturan['kop_nama'] ?? 'PONDOK PESANTREN AL-FALAH PUTRA' }}</div>
                                    <div class="text-xs text-gray-700">{{ $pengaturan['kop_alamat'] ?? 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171' }}</div>
                                </div>

                                <!-- META SURAT -->
                                <table class="w-full mb-4 text-[11pt]" style="border-collapse: collapse;">
                                    <tr>
                                        <td class="w-[15%] align-top py-0.5">Nomor</td>
                                        <td class="w-[2%] align-top py-0.5">:</td>
                                        <td class="w-[46%] align-top py-0.5 font-bold">{{ $suratKeluar->no_surat }}</td>
                                        <td class="w-[37%] align-top py-0.5 text-right whitespace-nowrap">
                                            <input type="text" x-model="tempatSurat"
                                                   class="inline-block w-24 text-right bg-amber-50/70 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0 text-[11pt]"
                                                   style="font-family: inherit;" />,
                                            <span x-text="formatTanggalIndo(tglSurat)"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0.5">Lampiran</td>
                                        <td class="align-top py-0.5">:</td>
                                        <td class="align-top py-0.5" colspan="2">
                                            <input type="text" name="lampiran_teks" x-model="lampiranTeks"
                                                   class="w-full max-w-xs bg-amber-50/70 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0 text-[11pt]"
                                                   style="font-family: inherit;" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0.5">Perihal</td>
                                        <td class="align-top py-0.5">:</td>
                                        <td class="align-top py-0.5 font-bold" colspan="2">
                                            <input type="text" name="perihal" x-model="perihal" required
                                                   class="w-full font-bold bg-amber-50/70 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0 text-[11pt]"
                                                   style="font-family: inherit;" />
                                        </td>
                                    </tr>
                                </table>

                                <!-- TUJUAN SURAT -->
                                <div class="mb-4 text-[11pt]">
                                    <div>Kepada Yth.</div>
                                    <div class="my-0.5">
                                        <input type="text" name="tujuan" x-model="tujuan" required
                                               class="w-full max-w-md font-bold bg-amber-50/70 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0.5 text-[11pt]"
                                               style="font-family: inherit;" />
                                    </div>
                                    <div>di - Tempat</div>
                                </div>

                                <!-- ISI SURAT -->
                                <div x-show="!modeHtmlRaw"
                                     x-ref="visualEditor"
                                     contenteditable="true"
                                     @input="syncFromVisual()"
                                     @blur="syncFromVisual()"
                                     class="w-full min-h-[320px] p-2 -mx-2 rounded-lg hover:ring-1 hover:ring-emerald-400/70 focus:ring-2 focus:ring-emerald-600 focus:outline-none text-[11pt] leading-relaxed text-justify">
                                </div>

                                <textarea x-show="modeHtmlRaw" x-model="isiSurat" @input="syncFromRaw()" rows="14"
                                          class="w-full font-mono text-xs border border-emerald-500 rounded-lg p-3 bg-slate-50"></textarea>
                            </div>

                            <!-- PENANDATANGAN -->
                            <div class="mt-6 pt-3 pb-12">
                                <template x-if="jmlTtd === 1">
                                    <div class="grid grid-cols-2 text-center text-[11pt]">
                                        <div></div>
                                        <div class="flex flex-col items-center">
                                            <input type="text" x-model="jabatanTtd1" class="w-full text-center bg-amber-50/60 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0" style="font-family: inherit;" />
                                            <div @click="if (metodeTtd === 'digital') bukaModalTtd(1)"
                                                 class="w-[3.9cm] h-[2.6cm] my-1.5 flex items-center justify-center rounded-lg border border-dashed border-emerald-400 cursor-pointer">
                                                <template x-if="sig1"><img :src="sig1" class="max-w-[3.9cm] max-h-[2.6cm] object-contain" /></template>
                                                <template x-if="!sig1"><span class="text-[10px] font-sans text-emerald-700 font-semibold">✍️ Klik TTD</span></template>
                                            </div>
                                            <input type="text" x-model="namaTtd1" class="w-full text-center font-bold underline bg-amber-50/60 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0" style="font-family: inherit;" />
                                        </div>
                                    </div>
                                </template>
                                <template x-if="jmlTtd === 2">
                                    <div class="grid grid-cols-2 gap-6 text-center text-[11pt]">
                                        <div class="flex flex-col items-center">
                                            <input type="text" x-model="jabatanTtd1" class="w-full text-center bg-amber-50/60 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0" style="font-family: inherit;" />
                                            <div @click="if (metodeTtd === 'digital') bukaModalTtd(1)" class="w-[3.9cm] h-[2.6cm] my-1.5 flex items-center justify-center rounded-lg border border-dashed border-emerald-400 cursor-pointer">
                                                <template x-if="sig1"><img :src="sig1" class="max-w-[3.9cm] max-h-[2.6cm] object-contain" /></template>
                                                <template x-if="!sig1"><span class="text-[10px] font-sans text-emerald-700 font-semibold">✍️ Klik TTD 1</span></template>
                                            </div>
                                            <input type="text" x-model="namaTtd1" class="w-full text-center font-bold underline bg-amber-50/60 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0" style="font-family: inherit;" />
                                        </div>
                                        <div class="flex flex-col items-center">
                                            <input type="text" x-model="jabatanTtd2" class="w-full text-center bg-amber-50/60 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0" style="font-family: inherit;" />
                                            <div @click="if (metodeTtd === 'digital') bukaModalTtd(2)" class="w-[3.9cm] h-[2.6cm] my-1.5 flex items-center justify-center rounded-lg border border-dashed border-blue-400 cursor-pointer">
                                                <template x-if="sig2"><img :src="sig2" class="max-w-[3.9cm] max-h-[2.6cm] object-contain" /></template>
                                                <template x-if="!sig2"><span class="text-[10px] font-sans text-blue-700 font-semibold">✍️ Klik TTD 2</span></template>
                                            </div>
                                            <input type="text" x-model="namaTtd2" class="w-full text-center font-bold underline bg-amber-50/60 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0" style="font-family: inherit;" />
                                        </div>
                                    </div>
                                </template>
                                <template x-if="jmlTtd === 3">
                                    <div class="space-y-4 text-center text-[10.5pt]">
                                        <div class="flex flex-col items-center max-w-xs mx-auto">
                                            <div class="italic text-[10pt]">Mengetahui,</div>
                                            <input type="text" x-model="jabatanTtd1" class="w-full text-center bg-amber-50/60 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0" style="font-family: inherit;" />
                                            <div @click="if (metodeTtd === 'digital') bukaModalTtd(1)" class="w-[3.9cm] h-[2.4cm] my-1 flex items-center justify-center rounded-lg border border-dashed border-emerald-400 cursor-pointer">
                                                <template x-if="sig1"><img :src="sig1" class="max-w-[3.9cm] max-h-[2.4cm] object-contain" /></template>
                                                <template x-if="!sig1"><span class="text-[10px] font-sans text-emerald-700 font-semibold">✍️ Klik TTD 1</span></template>
                                            </div>
                                            <input type="text" x-model="namaTtd1" class="w-full text-center font-bold underline bg-amber-50/60 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0" style="font-family: inherit;" />
                                        </div>
                                        <div class="grid grid-cols-2 gap-6">
                                            <div class="flex flex-col items-center">
                                                <input type="text" x-model="jabatanTtd2" class="w-full text-center bg-amber-50/60 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0" style="font-family: inherit;" />
                                                <div @click="if (metodeTtd === 'digital') bukaModalTtd(2)" class="w-[3.9cm] h-[2.4cm] my-1 flex items-center justify-center rounded-lg border border-dashed border-blue-400 cursor-pointer">
                                                    <template x-if="sig2"><img :src="sig2" class="max-w-[3.9cm] max-h-[2.4cm] object-contain" /></template>
                                                    <template x-if="!sig2"><span class="text-[10px] font-sans text-blue-700 font-semibold">✍️ Klik TTD 2</span></template>
                                                </div>
                                                <input type="text" x-model="namaTtd2" class="w-full text-center font-bold underline bg-amber-50/60 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0" style="font-family: inherit;" />
                                            </div>
                                            <div class="flex flex-col items-center">
                                                <input type="text" x-model="jabatanTtd3" class="w-full text-center bg-amber-50/60 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0" style="font-family: inherit;" />
                                                <div @click="if (metodeTtd === 'digital') bukaModalTtd(3)" class="w-[3.9cm] h-[2.4cm] my-1 flex items-center justify-center rounded-lg border border-dashed border-purple-400 cursor-pointer">
                                                    <template x-if="sig3"><img :src="sig3" class="max-w-[3.9cm] max-h-[2.4cm] object-contain" /></template>
                                                    <template x-if="!sig3"><span class="text-[10px] font-sans text-purple-700 font-semibold">✍️ Klik TTD 3</span></template>
                                                </div>
                                                <input type="text" x-model="namaTtd3" class="w-full text-center font-bold underline bg-amber-50/60 border-b border-dashed border-emerald-600 focus:outline-none px-1 py-0" style="font-family: inherit;" />
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- MODAL KANVAS TANDA TANGAN DIGITAL -->
        <div x-show="signModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-5 space-y-4 shadow-2xl" @click.away="signModalOpen = false">
                <div class="flex items-center justify-between border-b pb-2.5">
                    <div>
                        <h3 class="font-extrabold text-sm text-gray-900">Goreskan Tanda Tangan Digital</h3>
                        <p class="text-xs text-emerald-700 font-semibold">Penandatangan <span x-text="signTargetUrutan"></span></p>
                    </div>
                    <button type="button" @click="signModalOpen = false" class="text-gray-400 hover:text-gray-700 font-bold">✕</button>
                </div>
                <div class="flex flex-col items-center space-y-2">
                    <div class="w-[330px] h-[220px] border-2 border-dashed border-emerald-500 rounded-xl bg-white overflow-hidden shadow-inner">
                        <canvas id="modalSignatureCanvas" class="touch-none w-full h-full cursor-crosshair bg-white"></canvas>
                    </div>
                    <div class="flex items-center justify-between w-[330px] text-xs">
                        <span class="text-[11px] text-gray-400">Gunakan jari atau mouse</span>
                        <button type="button" onclick="window.clearModalSignaturePad()" class="text-rose-600 hover:underline font-bold">Bersihkan</button>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" @click="signModalOpen = false" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 rounded-xl text-xs font-bold transition shadow-2xs">Batal</button>
                    <button type="button" @click="simpanGoresanModal()" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-xs">✓ Tempel ke Kertas Surat</button>
                </div>
            </div>
        </div>

        <!-- MODAL SIMPAN SEBAGAI TEMPLATE -->
        <div x-show="showTemplateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl" @click.away="showTemplateModal = false">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="font-extrabold text-base text-gray-900">Simpan sebagai Template Surat</h3>
                    <button type="button" @click="showTemplateModal = false" class="text-gray-400 hover:text-gray-700 font-bold">✕</button>
                </div>
                <form method="POST" action="{{ route('admin.surat-keluar.template.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nama Template *</label>
                        <input type="text" name="nama" x-model="tplNama" required class="w-full text-xs rounded-xl border-gray-300 py-2 px-3" />
                    </div>
                    <input type="hidden" name="kode_jenis_surat" :value="tplKode">
                    <input type="hidden" name="tujuan_default" :value="tplTujuan">
                    <input type="hidden" name="perihal_default" :value="tplPerihal">
                    <input type="hidden" name="isi_template" :value="tplIsi">
                    <div class="flex justify-end gap-2 pt-2 border-t">
                        <button type="button" @click="showTemplateModal = false" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 rounded-xl text-xs font-bold transition shadow-2xs">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-xs">Simpan Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/signature_pad.umd.min.js') }}"></script>
    <script>
        window.modalSigPad = null;
        window.initModalSignaturePad = function() {
            setTimeout(() => {
                const canvas = document.getElementById('modalSignatureCanvas');
                if (!canvas) return;
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = (canvas.offsetWidth || 330) * ratio;
                canvas.height = (canvas.offsetHeight || 220) * ratio;
                canvas.getContext('2d').scale(ratio, ratio);

                window.modalSigPad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(15, 23, 42)',
                    minWidth: 1.5,
                    maxWidth: 3.5
                });
            }, 60);
        };

        window.clearModalSignaturePad = function() {
            if (window.modalSigPad) window.modalSigPad.clear();
        };

        window.getModalSignatureData = function() {
            if (!window.modalSigPad || window.modalSigPad.isEmpty()) return null;
            return window.modalSigPad.toDataURL('image/png');
        };
    </script>
    @endpush
</x-app-layout>
