<x-app-layout>
    @section('title', 'Detail Surat Keluar ' . $suratKeluar->no_surat)

    <div class="max-w-5xl mx-auto space-y-6 pb-12" x-data="{ modalDigital: false, modalBasah: false, selectedUrutan: 1 }">
        <!-- Top Navigation & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                    <a href="{{ route('admin.surat-keluar.index') }}" class="hover:text-emerald-700 transition">Surat Keluar</a>
                    <span>/</span>
                    <span class="text-emerald-800 font-semibold font-mono">{{ $suratKeluar->no_surat }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight font-mono">{{ $suratKeluar->no_surat }}</h1>
                    @if ($suratKeluar->status === 'draft')
                        <span class="px-2.5 py-0.5 bg-amber-50 text-amber-800 border border-amber-300 font-bold rounded-full text-xs">Draft</span>
                    @elseif ($suratKeluar->status === 'final')
                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-300 font-bold rounded-full text-xs">Final (Terkunci)</span>
                    @else
                        <span class="px-2.5 py-0.5 bg-blue-50 text-blue-800 border border-blue-300 font-bold rounded-full text-xs">Terkirim</span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Tombol Cetak PDF -->
                <a href="{{ route('admin.surat-keluar.cetak', $suratKeluar) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white text-gray-700 hover:bg-gray-50 text-xs sm:text-sm font-semibold rounded-xl border border-gray-300 shadow-2xs transition active:scale-95">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak PDF Resmi</span>
                </a>

                @if (! $suratKeluar->isFinal())
                    <!-- Tombol Ubah Data -->
                    <a href="{{ route('admin.surat-keluar.edit', $suratKeluar) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white text-gray-700 hover:bg-gray-50 text-xs sm:text-sm font-semibold rounded-xl border border-gray-300 shadow-2xs transition active:scale-95">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Ubah</span>
                    </a>
                @endif

                @if ($suratKeluar->status === 'final')
                    <form method="POST" action="{{ route('admin.surat-keluar.status', $suratKeluar) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="terkirim">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span>Tandai Terkirim</span>
                        </button>
                    </form>
                @endif

                <a href="{{ route('admin.surat-keluar.index') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl transition active:scale-95">
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <!-- Alert Notifikasi Tautan Sekali Pakai -->
        @if (session('tautan_ttd'))
            <div class="bg-emerald-50 border border-emerald-300 rounded-2xl p-5 space-y-3 shadow-xs">
                <div class="flex items-center gap-2 text-emerald-900 font-bold text-sm">
                    <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    <span>Tautan Tanda Tangan Sekali Pakai Berhasil Dibuat (Penandatangan ke-{{ session('tautan_urutan', 1) }})</span>
                </div>
                <p class="text-xs text-emerald-800 leading-relaxed">
                    Kirim tautan ini kepada penandatangan terkait. Tautan ini hanya berlaku selama <strong>30 menit</strong> dan akan hangus otomatis setelah ditandatangani.
                </p>
                <div class="flex items-center gap-2" x-data="{ copied: false }">
                    <input type="text" readonly value="{{ session('tautan_ttd') }}" id="tautanInput"
                           class="w-full text-xs bg-white rounded-xl border-emerald-300 text-gray-800 font-mono py-2.5 px-3" />
                    <button type="button"
                            @click="navigator.clipboard.writeText(document.getElementById('tautanInput').value); copied = true; setTimeout(() => copied = false, 3000)"
                            class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-xs whitespace-nowrap flex items-center gap-1.5 shrink-0">
                        <span x-show="!copied">Salin Tautan</span>
                        <span x-show="copied" class="text-emerald-200">Tersalin!</span>
                    </button>
                </div>
            </div>
        @endif

        <!-- Alert Buka Cetak Otomatis -->
        @if (session('auto_open_cetak'))
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-2xs">
                <div class="text-xs text-blue-900 font-semibold text-center sm:text-left">
                    🖨️ Dokumen PDF siap dicetak. Jika tab cetak belum terbuka otomatis, klik tombol di samping:
                </div>
                <a href="{{ session('auto_open_cetak') }}" target="_blank" id="autoOpenCetakLink"
                   class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold whitespace-nowrap shadow-xs">
                    Buka & Cetak PDF Sekarang
                </a>
            </div>
            <script>
                window.addEventListener('DOMContentLoaded', function() {
                    window.open(@js(session('auto_open_cetak')), '_blank');
                });
            </script>
        @endif

        <!-- Card Detail Surat -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                <span class="font-extrabold text-xs text-gray-800 uppercase tracking-wider">Rincian Dokumen Surat Keluar</span>
                <div class="flex items-center gap-2.5 bg-white px-3 py-1.5 rounded-xl border border-emerald-200 shadow-2xs">
                    @if (!empty($qrCodeDataUri))
                        <img src="{{ $qrCodeDataUri }}" alt="QR Verifikasi" class="w-10 h-10 rounded border border-gray-100" />
                    @endif
                    <div class="normal-case leading-tight">
                        <span class="block text-[10px] text-gray-400 font-bold uppercase">Kode Verifikasi QR:</span>
                        <span class="text-emerald-800 font-mono text-xs font-extrabold">{{ $suratKeluar->kode_verifikasi }}</span>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-xs sm:text-sm">
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Nomor Surat Baku</span>
                    <span class="block text-base font-mono font-extrabold text-emerald-900 mt-0.5">{{ $suratKeluar->no_surat }}</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Jenis & Kategori Surat</span>
                    <span class="block text-sm font-semibold text-gray-800 mt-0.5">
                        {{ $suratKeluar->kategori ? $suratKeluar->kategori->nama . ' (' . $suratKeluar->kategori->kode . ')' : '-' }}
                    </span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Tempat & Tanggal Surat</span>
                    <span class="block text-sm font-semibold text-gray-800 mt-0.5">{{ $suratKeluar->tempat_surat ?: 'Putak' }}, {{ $suratKeluar->formatted_tgl_surat }}</span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Lampiran & Kertas</span>
                    <span class="block text-sm font-semibold text-gray-800 mt-0.5">
                        Lampiran: {{ $suratKeluar->lampiran_teks ?: '- (Tidak Ada)' }} &bull; {{ strtoupper($suratKeluar->ukuran_kertas ?? 'A4') }}
                    </span>
                </div>
                <div class="md:col-span-2 pt-1 border-t border-gray-100">
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Tujuan Surat (Kepada Yth)</span>
                    <span class="block text-sm font-bold text-gray-900 mt-0.5">{{ $suratKeluar->tujuan }}</span>
                </div>
                <div class="md:col-span-2">
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Perihal Dokumen</span>
                    <span class="block text-sm sm:text-base font-extrabold text-gray-900 mt-0.5">{{ $suratKeluar->perihal }}</span>
                </div>

                <!-- DAFTAR PENANDATANGAN DOKUMEN -->
                <div class="md:col-span-2 border-t border-gray-100 pt-4">
                    <span class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-3">
                        Daftar Penandatangan Dokumen Resmi ({{ $suratKeluar->jumlah_penandatangan ?? 1 }} Orang):
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @php
                            $ttdMap = $suratKeluar->tandaTangans->keyBy('urutan');
                            $penList = $suratKeluar->penandatangans ?? [
                                ['urutan' => 1, 'nama' => $suratKeluar->nama_penandatangan, 'jabatan' => $suratKeluar->jabatan_penandatangan]
                            ];
                        @endphp
                        @foreach ($penList as $p)
                            @php
                                $u = $p['urutan'] ?? 1;
                                $ttdData = $ttdMap[$u] ?? null;
                                $sudahTtd = !empty($ttdData?->data_ttd);
                            @endphp
                            <div class="p-3.5 rounded-2xl border {{ $sudahTtd ? 'bg-emerald-50/70 border-emerald-300' : 'bg-gray-50 border-gray-200' }} space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-gray-700">Penandatangan {{ $u }}</span>
                                    @if ($sudahTtd)
                                        <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full">✓ Sudah TTD</span>
                                    @else
                                        <span class="text-[10px] bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded-full">Menunggu</span>
                                    @endif
                                </div>
                                <div class="font-bold text-xs sm:text-sm text-gray-900">{{ $p['nama'] ?? '-' }}</div>
                                <div class="text-[11px] text-gray-500">{{ $p['jabatan'] ?? '-' }}</div>
                                @if ($sudahTtd && $ttdData->waktu_ttd)
                                    <div class="text-[10px] text-emerald-800 pt-1.5 border-t border-emerald-200/60 font-mono">
                                        Waktu: {{ $ttdData->waktu_ttd->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') }} WIB
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- BERKAS LAMPIRAN TERSEDIA -->
                @if ($suratKeluar->file)
                    <div class="md:col-span-2 pt-2">
                        <span class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">📎 Berkas Lampiran Pendukung</span>
                        <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-blue-50/60 rounded-2xl border border-blue-200">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-bold text-blue-950 truncate">{{ basename($suratKeluar->file) }}</p>
                                    <p class="text-[11px] text-blue-700">Berkas lampiran resmi terlampir pada surat dinas</p>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $suratKeluar->file) }}" target="_blank"
                               class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                Buka Berkas Lampiran
                            </a>
                        </div>
                    </div>
                @endif

                <!-- DOKUMEN SURAT ASLI RESMI (BER-QR CODE) - TETAP TEPAT SEPERTI ASLINYA -->
                @if ($suratKeluar->file_final || $suratKeluar->file_surat_asli)
                    @php
                        $tampilanPath = $suratKeluar->file_final ?: $suratKeluar->file_surat_asli;
                        $extTampilan = strtolower(pathinfo($tampilanPath, PATHINFO_EXTENSION));
                        $isGambarAsli = in_array($extTampilan, ['png', 'jpg', 'jpeg', 'webp'], true);
                    @endphp
                    <div class="md:col-span-2 space-y-3 pt-3 border-t border-gray-100">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="block text-xs font-bold uppercase tracking-wider text-emerald-900">📄 Dokumen Surat Asli Resmi (Ber-QR Code)</span>
                            <div class="flex flex-wrap items-center gap-2">
                                @if ($suratKeluar->file_surat_asli)
                                    <a href="{{ asset('storage/' . $suratKeluar->file_surat_asli) }}" download
                                       class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                                        Unduh File Word Asli (.docx)
                                    </a>
                                @endif
                                @if ($suratKeluar->file_final)
                                    <a href="{{ asset('storage/' . $suratKeluar->file_final) }}" target="_blank"
                                       class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                                        Buka Dokumen
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if ($isGambarAsli)
                            <div class="p-3 bg-gray-100 rounded-2xl border border-emerald-200">
                                <img src="{{ asset('storage/' . $tampilanPath) }}" alt="Dokumen Surat {{ $suratKeluar->no_surat }}" class="w-full max-w-2xl mx-auto h-auto rounded-xl shadow-xs bg-white" />
                            </div>
                        @else
                            <div class="w-full h-[650px] rounded-2xl overflow-hidden border border-emerald-200 bg-gray-100 shadow-inner">
                                <iframe src="{{ asset('storage/' . $tampilanPath) }}" class="w-full h-full" title="Dokumen Surat {{ $suratKeluar->no_surat }}"></iframe>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="px-6 sm:px-8 py-3 bg-gray-50/60 border-t border-gray-100 text-xs text-gray-500">
                Dibuat oleh: <span class="font-semibold text-gray-700">{{ $suratKeluar->user ? $suratKeluar->user->name : 'Sekretaris' }}</span> &bull; {{ $suratKeluar->created_at->format('d-m-Y H:i') }} WIB
            </div>
        </div>

        <!-- PANEL FINALISASI TANDA TANGAN (JIKA BELUM FINAL) -->
        @if (! $suratKeluar->isFinal())
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6 sm:p-8 space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-extrabold text-gray-900">Finalisasi Tanda Tangan Dokumen</h2>
                        <p class="text-xs text-gray-500">Pilih opsi tanda tangan untuk melegalkan dokumen surat keluar resmi.</p>
                    </div>
                </div>

                <!-- PILIH TARGET PENANDATANGAN -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Pilih Penandatangan yang Ingin Dibubuhi Tanda Tangan:
                    </label>
                    <select x-model="selectedUrutan"
                            class="w-full sm:w-80 text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs font-medium">
                        @foreach ($penList as $p)
                            @php
                                $uOpt = $p['urutan'] ?? 1;
                                $ttdDataOpt = $ttdMap[$uOpt] ?? null;
                                $isSigned = !empty($ttdDataOpt?->data_ttd);
                            @endphp
                            <option value="{{ $uOpt }}">
                                Penandatangan {{ $uOpt }}: {{ $p['nama'] ?? '-' }} ({{ $isSigned ? '✓ Sudah TTD' : '⏳ Belum TTD' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <!-- Opsi 1: TTD Digital Langsung di Layar -->
                    <div class="p-5 rounded-2xl border border-gray-200 hover:border-emerald-500 transition flex flex-col justify-between space-y-4 bg-gray-50/50">
                        <div>
                            <span class="block font-bold text-xs sm:text-sm text-gray-900">1. Tanda Tangan Langsung di Layar</span>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                Penandatangan hadir di tempat dan menggambar goresan tanda tangan di layar perangkat ini (smartphone / laptop).
                            </p>
                        </div>
                        <button type="button" @click="modalDigital = true; $nextTick(() => initSignaturePad())"
                                class="w-full py-2.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-xs">
                            Buka Kanvas TTD
                        </button>
                    </div>

                    <!-- Opsi 2: Kirim Tautan Jarak Jauh -->
                    <div class="p-5 rounded-2xl border border-gray-200 hover:border-emerald-500 transition flex flex-col justify-between space-y-4 bg-gray-50/50">
                        <div>
                            <span class="block font-bold text-xs sm:text-sm text-gray-900">2. Kirim Tautan Jarak Jauh</span>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                Buat tautan sekali pakai yang berlaku 30 menit untuk dikirimkan melalui WhatsApp kepada penandatangan.
                            </p>
                        </div>
                        <form method="POST" action="{{ route('admin.surat-keluar.tautan-ttd', $suratKeluar) }}">
                            @csrf
                            <input type="hidden" name="urutan" :value="selectedUrutan">
                            <button type="submit"
                                    class="w-full py-2.5 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded-xl transition shadow-xs">
                                Buat Tautan (30 Menit)
                            </button>
                        </form>
                    </div>

                    <!-- Opsi 3: Tanda Tangan Basah -->
                    <div class="p-5 rounded-2xl border border-gray-200 hover:border-emerald-500 transition flex flex-col justify-between space-y-4 bg-gray-50/50">
                        <div>
                            <span class="block font-bold text-xs sm:text-sm text-gray-900">3. Tanda Tangan Basah (Manual)</span>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                Cetak PDF kosong, bubuhkan tanda tangan fisik dan stempel, lalu unggah berkas pindaian final.
                            </p>
                        </div>
                        <button type="button" @click="modalBasah = true"
                                class="w-full py-2.5 px-4 bg-gray-800 hover:bg-gray-900 text-white font-bold text-xs rounded-xl transition shadow-xs">
                            Finalisasi TTD Basah
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal Tanda Tangan Digital Langsung -->
        <div x-show="modalDigital" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-4 shadow-2xl relative"
                 @click.away="modalDigital = false">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="font-extrabold text-base text-gray-900">Kanvas Tanda Tangan Digital</h3>
                        <p class="text-xs text-gray-500">Penandatangan Target: Ke-<span x-text="selectedUrutan" class="font-bold text-emerald-800"></span></p>
                    </div>
                    <button type="button" @click="modalDigital = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                        ✕
                    </button>
                </div>

                <div class="space-y-3">
                    <p class="text-xs text-gray-600 text-center">
                        Bubuhkan tanda tangan pada kotak proporsional (rasio 3:2) di bawah menggunakan jari atau mouse:
                    </p>
                    <div class="flex justify-center">
                        <div class="w-[300px] sm:w-[380px] h-[200px] sm:h-[253px] border-2 border-dashed border-emerald-500 rounded-2xl bg-white overflow-hidden shadow-inner flex items-center justify-center relative ring-4 ring-emerald-50">
                            <canvas id="signatureCanvas" width="380" height="253" class="touch-none bg-white w-full h-full cursor-crosshair"></canvas>
                        </div>
                    </div>
                    <div class="flex justify-between items-center px-1">
                        <span class="text-[11px] text-gray-400">Rasio kanvas 3:2 (presisi sesuai PDF)</span>
                        <button type="button" id="clearCanvasBtn"
                                class="text-xs text-rose-600 hover:text-rose-800 font-bold underline">
                            Bersihkan Kanvas
                        </button>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.surat-keluar.ttd-digital', $suratKeluar) }}" id="formTtdDigital">
                    @csrf
                    <input type="hidden" name="data_ttd" id="inputDataTtd">
                    <input type="hidden" name="urutan" :value="selectedUrutan">

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100">
                        <button type="button" @click="modalDigital = false"
                                class="px-4 py-2.5 border border-gray-300 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="button" id="submitTtdDigitalBtn"
                                class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-emerald-700/20 transition active:scale-95">
                            Simpan & Bubuhkan ke Surat
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tanda Tangan Basah -->
        <div x-show="modalBasah" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-4 shadow-2xl relative"
                 @click.away="modalBasah = false">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="font-extrabold text-base text-gray-900">Finalisasi Tanda Tangan Basah</h3>
                    <button type="button" @click="modalBasah = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                        ✕
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.surat-keluar.ttd-basah', $suratKeluar) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="file_final" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                            Unggah Berkas Pindaian / Foto Surat Bertanda Tangan Basah (PDF/JPG, Maks 5 MB)
                        </label>
                        <input type="file" name="file_final" id="file_final" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full text-xs file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-300 rounded-xl cursor-pointer" />
                        <p class="text-[11px] text-gray-400 mt-1.5">
                            Opsional. Jika tidak diunggah sekarang, surat tetap berstatus final dan dapat diunggah nanti.
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100">
                        <button type="button" @click="modalBasah = false"
                                class="px-4 py-2.5 border border-gray-300 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 bg-gray-900 hover:bg-black text-white text-xs sm:text-sm font-bold rounded-xl shadow-md transition active:scale-95">
                            Finalisasi Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/signature_pad.umd.min.js') }}"></script>
    <script>
        let signaturePad = null;

        function initSignaturePad() {
            setTimeout(() => {
                const canvas = document.getElementById('signatureCanvas');
                if (!canvas) return;

                const rect = canvas.getBoundingClientRect();
                const displayWidth = Math.round(rect.width) || 380;
                const displayHeight = Math.round(rect.height) || 253;
                const ratio = Math.max(window.devicePixelRatio || 1, 1);

                canvas.width = displayWidth * ratio;
                canvas.height = displayHeight * ratio;
                const ctx = canvas.getContext('2d');
                ctx.scale(ratio, ratio);

                if (!signaturePad) {
                    signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(15, 23, 42)',
                        minWidth: 1.5,
                        maxWidth: 3.5
                    });
                } else {
                    signaturePad.clear();
                }
            }, 100);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const clearBtn = document.getElementById('clearCanvasBtn');
            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    if (signaturePad) signaturePad.clear();
                });
            }

            const submitBtn = document.getElementById('submitTtdDigitalBtn');
            if (submitBtn) {
                submitBtn.addEventListener('click', () => {
                    if (!signaturePad || signaturePad.isEmpty()) {
                        alert('Mohon bubuhkan goresan tanda tangan terlebih dahulu pada kanvas.');
                        return;
                    }
                    const dataUrl = signaturePad.toDataURL('image/png');
                    document.getElementById('inputDataTtd').value = dataUrl;
                    document.getElementById('formTtdDigital').submit();
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
