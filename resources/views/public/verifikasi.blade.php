@extends('layouts.public')

@section('title', 'Verifikasi Keaslian Surat')

@section('content')
<!-- Header Verifikasi -->
<section class="bg-emerald-950 text-white py-12 sm:py-16 border-b border-emerald-900/60">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="inline-block px-3.5 py-1 rounded-full bg-emerald-900/80 border border-emerald-700/60 text-emerald-300 text-xs font-semibold uppercase tracking-wider">
            Layanan Resmi Sekretariat
        </span>
        <h1 class="text-xl sm:text-3xl md:text-4xl font-extrabold tracking-tight">
            Verifikasi Keaslian Dokumen Surat
        </h1>
        <p class="max-w-xl mx-auto text-emerald-100/90 text-xs sm:text-sm md:text-base font-normal leading-relaxed">
            Periksa keabsahan dan keaslian dokumen surat resmi yang diterbitkan oleh Sekretariat Pondok Pesantren Al-Falah Putak.
        </p>
    </div>
</section>

<section class="py-10 sm:py-14 bg-slate-50 min-h-[500px]">
    <div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">

        <!-- Form Pencarian Kode Verifikasi -->
        <div class="bg-white p-5 sm:p-7 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="space-y-1">
                <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Pengecekan Kode Verifikasi Surat</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Pindai QR Code pada dokumen surat fisik atau ketik 8 karakter kode verifikasi (atau nomor surat) di bawah ini:
                </p>
            </div>

            <form action="{{ route('publik.verifikasi.index') }}" method="GET" id="form-verifikasi" class="space-y-3 pt-1">
                <div class="flex flex-col sm:flex-row gap-2.5">
                    <div class="flex-grow">
                        <input type="text"
                               name="kode"
                               id="input-kode"
                               value="{{ $kode ?? '' }}"
                               placeholder="Contoh: AF-7B9A atau 001/SPm.01/AF-PA/..."
                               required
                               class="w-full rounded-xl border border-slate-200 text-sm sm:text-base focus:border-emerald-500 focus:ring-emerald-500 py-3 px-4 min-h-[48px] font-mono uppercase tracking-wider">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button"
                                id="btn-toggle-scan"
                                class="flex-1 sm:flex-none min-h-[48px] px-4 py-3 rounded-xl font-bold text-xs sm:text-sm text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition shrink-0 flex items-center justify-center gap-2 shadow-xs active:scale-95">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            <span>Scan QR</span>
                        </button>
                        <button type="submit"
                                class="flex-1 sm:flex-none min-h-[48px] px-5 py-3 rounded-xl font-bold text-xs sm:text-sm text-white bg-emerald-700 hover:bg-emerald-800 shadow-xs transition shrink-0 flex items-center justify-center gap-2 active:scale-95">
                            <span>Periksa</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Container Pemindai QR Kamera (Tersembunyi Awal) -->
            <div id="scanner-wrapper" class="hidden mt-4 p-5 bg-gray-900 rounded-2xl text-white space-y-4 border border-gray-800">
                <div class="flex items-center justify-between border-b border-gray-800 pb-3">
                    <div class="flex items-center gap-2 text-sm font-bold text-emerald-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Pemindai QR Code Dokumen
                    </div>
                    <button type="button" id="btn-close-scan" class="text-gray-400 hover:text-white p-1 rounded-lg hover:bg-gray-800 transition text-sm">
                        ✕ Tutup Scanner
                    </button>
                </div>
                <p class="text-xs text-gray-400">
                    Arahkan kamera ke QR Code yang tertera di surat resmi, atau unggah foto/berkas QR Code di bawah ini:
                </p>
                <div id="qr-reader" class="w-full max-w-sm mx-auto overflow-hidden rounded-xl border border-gray-700 bg-black"></div>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-400 pt-2">
                    <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 bg-gray-800 hover:bg-gray-700 rounded-xl transition text-gray-200">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Unggah Foto / Gambar QR</span>
                        <input type="file" id="qr-input-file" accept="image/*" class="hidden">
                    </label>
                    <span id="scan-status" class="text-emerald-400 italic font-mono text-[11px]"></span>
                </div>
            </div>
        </div>

        @if ($surat)
            <!-- Dokumen Ditemukan & Terverifikasi Sah -->
            <div class="bg-white rounded-2xl border border-emerald-300 shadow-md overflow-hidden">
                <!-- Status Banner Sah -->
                <div class="bg-emerald-950 p-6 sm:p-7 text-white text-center space-y-2 border-b border-emerald-900">
                    <div class="w-12 h-12 mx-auto bg-emerald-900 border border-emerald-700 rounded-full flex items-center justify-center text-emerald-300 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span class="inline-block px-3 py-0.5 rounded-full bg-emerald-900 text-amber-300 text-[10px] font-bold uppercase tracking-wider border border-emerald-800">
                        Status Validasi Sah
                    </span>
                    <h3 class="text-base sm:text-lg font-extrabold tracking-wide text-white uppercase">DOKUMEN RESMI TERVERIFIKASI SAH</h3>
                    <p class="text-xs text-emerald-200/90 max-w-md mx-auto leading-relaxed">
                        Dokumen ini terdaftar secara sah dalam pangkalan data resmi Sekretariat Pondok Pesantren Al-Falah Putra.
                    </p>
                </div>

                <!-- Rincian Data Surat -->
                <div class="p-5 sm:p-8 space-y-4 text-xs sm:text-sm text-gray-700">
                    <div class="border-b border-gray-100 pb-4 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <span class="block text-slate-500 font-semibold uppercase text-xs tracking-wider">Nomor Surat</span>
                            <span class="block text-base sm:text-lg font-mono font-bold text-emerald-950 mt-0.5">{{ $surat->no_surat }}</span>
                        </div>
                        <div class="flex items-center gap-2.5 bg-emerald-50/70 border border-emerald-200 px-3 py-2 rounded-xl">
                            @if (!empty($qrCodeDataUri))
                                <img src="{{ $qrCodeDataUri }}" alt="QR Verifikasi" class="w-12 h-12 sm:w-14 sm:h-14 rounded bg-white p-0.5 border border-emerald-100" />
                            @endif
                            <div>
                                <span class="block text-xs font-bold uppercase text-emerald-800">Kode Verifikasi QR:</span>
                                <span class="font-mono text-sm sm:text-base font-extrabold text-emerald-950">{{ $surat->kode_verifikasi }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-b border-gray-100 pb-3">
                        <div>
                            <span class="block text-slate-500 font-semibold uppercase text-xs tracking-wider">Tanggal Surat</span>
                            <span class="block text-xs sm:text-sm font-semibold text-gray-800 mt-0.5">{{ $surat->tgl_surat ? $surat->tgl_surat->format('d-m-Y') : '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-500 font-semibold uppercase text-xs tracking-wider">Jenis Surat</span>
                            <span class="block text-xs sm:text-sm font-semibold text-gray-800 mt-0.5">{{ $surat->kategori->nama ?? 'Surat Keluar' }}</span>
                        </div>
                    </div>

                    <div class="border-b border-gray-100 pb-3">
                        <span class="block text-slate-500 font-semibold uppercase text-xs tracking-wider">Tujuan Surat</span>
                        <span class="block text-xs sm:text-sm font-semibold text-gray-800 mt-0.5">{{ $surat->tujuan }}</span>
                    </div>

                    <div class="border-b border-gray-100 pb-3">
                        <span class="block text-slate-500 font-semibold uppercase text-xs tracking-wider">Perihal</span>
                        <p class="text-xs sm:text-sm font-semibold text-gray-900 mt-0.5 leading-relaxed">{{ $surat->perihal }}</p>
                    </div>

                    <!-- Daftar Penandatangan Resmi (Jika Dicatat Manual) -->
                    @if (!empty($surat->nama_penandatangan) && $surat->nama_penandatangan !== '-')
                        <div class="border-b border-gray-100 pb-3 space-y-2">
                            <span class="block text-slate-500 font-semibold uppercase text-xs tracking-wider">Penandatangan Resmi Dokumen</span>
                            <div class="grid grid-cols-1 sm:grid-cols-{{ min(3, max(1, $surat->jumlah_penandatangan ?? 1)) }} gap-3">
                                @php
                                    $ttdList = $surat->tandaTangans->isNotEmpty() ? $surat->tandaTangans : collect([
                                        (object)[
                                             'nama_penandatangan' => $surat->nama_penandatangan,
                                             'jabatan_penandatangan' => $surat->jabatan_penandatangan,
                                             'waktu_ttd' => $surat->tandaTangan?->waktu_ttd,
                                             'ip_address' => $surat->tandaTangan?->ip_address,
                                        ]
                                    ]);
                                @endphp
                                @foreach ($ttdList as $ttdItem)
                                    @if ($ttdItem && $ttdItem->nama_penandatangan !== '-')
                                        <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 space-y-1">
                                            <span class="block text-xs sm:text-sm font-bold text-gray-900">{{ $ttdItem->nama_penandatangan }}</span>
                                            <span class="block text-xs text-gray-500">{{ $ttdItem->jabatan_penandatangan }}</span>
                                            @if ($ttdItem->waktu_ttd)
                                                <div class="text-xs text-emerald-800 font-mono mt-1 pt-1 border-t border-emerald-100">
                                                    ✓ Ditandatangani: <strong>{{ $ttdItem->waktu_ttd->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') }} WIB</strong>
                                                    <span class="block text-[11px] text-gray-400 font-sans">IP: {{ $ttdItem->ip_address }}</span>
                                                </div>
                                            @else
                                                <span class="text-xs text-amber-700 font-medium">Tanda Tangan Basah / Fisik</span>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="border-b border-gray-100 pb-3">
                        <span class="block text-gray-400 font-semibold uppercase text-[10px] tracking-wider">Keterangan Lampiran</span>
                        <span class="block text-sm font-semibold text-gray-800 mt-0.5">{{ $surat->lampiran_teks ?: '-' }}</span>
                    </div>

                    @if ($surat->file)
                        <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                <div>
                                    <span class="block font-bold text-blue-950 text-xs">Berkas Lampiran Resmi ({{ $surat->lampiran_teks ?: 'Lampiran' }}):</span>
                                    <span class="text-[11px] text-blue-800">{{ basename($surat->file) }}</span>
                                </div>
                            </div>
                            
                        </div>
                    @endif

                    @php
                        $tampilanPath = $surat->file_final;
                        $extTampilan = $tampilanPath ? strtolower(pathinfo($tampilanPath, PATHINFO_EXTENSION)) : '';
                        if (empty($tampilanPath) || $extTampilan === 'docx') {
                            $matchingPngs = glob(storage_path('app/public/surat-keluar/dokumen-' . $surat->id . '-*.png')) ?: [];
                            if (! empty($matchingPngs)) {
                                $tampilanPath = 'surat-keluar/' . basename($matchingPngs[0]);
                                $extTampilan = 'png';
                            } else {
                                $matchingPdfs = glob(storage_path('app/public/surat-keluar/asli-pdf-' . $surat->id . '-*.pdf')) ?: [];
                                if (! empty($matchingPdfs)) {
                                    $tampilanPath = 'surat-keluar/' . basename($matchingPdfs[0]);
                                    $extTampilan = 'pdf';
                                }
                            }
                        }
                        $isGambarAsli = in_array($extTampilan, ['png', 'jpg', 'jpeg', 'webp'], true);
                    @endphp
                    @if (! empty($surat->file_surat_asli))
                        <div class="pt-3 space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-900 block">📄 Dokumen Surat Asli Resmi (Ber-QR Code)</span>
                                    <span class="text-[11px] text-gray-500 font-medium">Pratinjau naskah dokumen asli langsung dari file Word resmi</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('publik.verifikasi.unduh', [$surat->kode_verifikasi, 'format' => 'docx']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-900 hover:bg-blue-950 text-white rounded-xl text-xs font-bold shadow-xs transition-colors active:scale-95" title="Unduh Dokumen Word (.docx) Asli">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Unduh Word (.docx)</span>
                                    </a>
                                    <a href="{{ route('publik.verifikasi.unduh', [$surat->kode_verifikasi, 'format' => 'pdf']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors active:scale-95" title="Unduh Dokumen PDF Resmi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Unduh PDF</span>
                                    </a>
                                </div>
                            </div>

                            <div class="w-full bg-slate-100/90 p-2 sm:p-5 rounded-2xl border border-slate-200 overflow-x-auto flex flex-col items-center">
                                <div id="docx-loading-verif" class="p-8 text-center space-y-2">
                                    <div class="w-7 h-7 border-3 border-emerald-600 border-t-transparent rounded-full animate-spin mx-auto"></div>
                                    <p class="text-xs text-gray-600 font-medium">Memuat lembar dokumen Word asli...</p>
                                </div>
                                <div id="docx-target-verif" class="w-full flex justify-center items-center"></div>
                            </div>
                        </div>
                    @elseif ($tampilanPath && ($isGambarAsli || $extTampilan === 'pdf'))
                        <div class="pt-3 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-900">📄 Dokumen Surat Asli Resmi (Ber-QR Code)</span>
                                <span class="text-[11px] text-gray-500 font-medium">Hanya untuk pratinjau verifikasi</span>
                            </div>

                            @if ($isGambarAsli)
                                <!-- Tampilan asli sesuai Word seperti foto beserta QR & Kode Verifikasi -->
                                <div class="w-full rounded-2xl overflow-hidden border-2 border-emerald-200 bg-gray-100 p-2 sm:p-4 shadow-inner">
                                    <img src="{{ asset('storage/' . $tampilanPath) }}" alt="Dokumen Surat Resmi {{ $surat->no_surat }}" class="w-full h-auto rounded-lg shadow-md mx-auto bg-white" />
                                </div>
                            @elseif ($extTampilan === 'pdf')
                                <div class="w-full h-[640px] rounded-2xl overflow-hidden border-2 border-emerald-200 bg-gray-100 shadow-inner">
                                    <iframe src="{{ asset('storage/' . $tampilanPath) }}" class="w-full h-full" title="Dokumen Surat Resmi"></iframe>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

        @elseif(!empty($kode))
            <!-- Dokumen Tidak Ditemukan -->
            <div class="bg-white rounded-2xl border border-rose-200 shadow-sm p-8 text-center space-y-4">
                <div class="w-14 h-14 mx-auto bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-2xl font-bold">
                    ✕
                </div>
                <h3 class="text-base font-bold text-gray-900 uppercase">Dokumen Tidak Ditemukan</h3>
                <p class="text-xs text-gray-500 max-w-md mx-auto leading-relaxed">
                    Kode verifikasi atau nomor surat <code class="bg-gray-100 px-2 py-0.5 rounded text-rose-600 font-mono">{{ $kode }}</code> tidak terdaftar dalam pangkalan data kami. Pastikan kode yang Anda masukkan berasal dari dokumen resmi.
                </p>
            </div>
        @endif

    </div>
</section>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnToggle = document.getElementById('btn-toggle-scan');
    const btnClose = document.getElementById('btn-close-scan');
    const wrapper = document.getElementById('scanner-wrapper');
    const inputKode = document.getElementById('input-kode');
    const form = document.getElementById('form-verifikasi');
    const statusEl = document.getElementById('scan-status');
    const fileInput = document.getElementById('qr-input-file');

    let html5QrCode = null;
    let isScanning = false;

    function handleDecodedText(decodedText) {
        if (!decodedText) return;

        statusEl.textContent = 'QR Berhasil Dibaca!';
        statusEl.className = 'text-emerald-400 font-bold text-xs';

        // Jika isi decodedText adalah URL lengkap (.../verifikasi/KODE)
        let extractedCode = decodedText.trim();
        if (extractedCode.includes('/verifikasi/')) {
            const parts = extractedCode.split('/verifikasi/');
            if (parts.length > 1) {
                extractedCode = parts[1].split('?')[0].split('#')[0].trim();
            }
        }

        inputKode.value = extractedCode;
        stopScanner();

        // Otomatis submit form
        setTimeout(() => {
            form.submit();
        }, 500);
    }

    async function startScanner() {
        wrapper.classList.remove('hidden');
        statusEl.textContent = 'Menyiapkan kamera...';

        try {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qr-reader");
            }

            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            await html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    handleDecodedText(decodedText);
                },
                (errorMessage) => {
                    // scanning progress
                }
            );

            isScanning = true;
            statusEl.textContent = 'Kamera aktif. Silakan arahkan ke QR Code.';
        } catch (err) {
            console.error('Error starting scanner:', err);
            statusEl.textContent = 'Gagal mengakses kamera: ' + (err.message || 'Izin kamera ditolak.');
            statusEl.className = 'text-rose-400 text-[11px]';
        }
    }

    async function stopScanner() {
        if (html5QrCode && isScanning) {
            try {
                await html5QrCode.stop();
            } catch (err) {
                console.error('Error stopping scanner:', err);
            }
            isScanning = false;
        }
        wrapper.classList.add('hidden');
        statusEl.textContent = '';
    }

    if (btnToggle) {
        btnToggle.addEventListener('click', function () {
            if (isScanning) {
                stopScanner();
            } else {
                startScanner();
            }
        });
    }

    if (btnClose) {
        btnClose.addEventListener('click', stopScanner);
    }

    if (fileInput) {
        fileInput.addEventListener('change', async function (e) {
            if (e.target.files.length === 0) return;
            const file = e.target.files[0];

            try {
                statusEl.textContent = 'Memproses berkas gambar...';
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("qr-reader");
                }
                const result = await html5QrCode.scanFile(file, true);
                handleDecodedText(result);
            } catch (err) {
                console.error('Scan file error:', err);
                statusEl.textContent = 'Tidak ditemukan QR Code yang valid pada gambar tersebut.';
                statusEl.className = 'text-rose-400 text-[11px]';
            }
        });
    }
});
</script>

@if (! empty($surat->file_surat_asli))
<style>
    #docx-target-verif {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    #docx-target-verif .docx-wrapper {
        background: transparent !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    #docx-target-verif section.docx,
    #docx-target-verif .docx {
        background: white !important;
        color: #0f172a !important;
        border-radius: 4px;
        box-sizing: border-box !important;
    }
    #docx-target-verif table {
        max-width: 100% !important;
    }
    #docx-target-verif img {
        max-width: 100% !important;
        height: auto !important;
    }
</style>
<script src="{{ asset('vendor/docx-preview/jszip.min.js') }}"></script>
<script src="{{ asset('vendor/docx-preview/docx-preview.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const docxUrl = "{{ asset('storage/' . $surat->file_surat_asli) }}";
        const loadingEl = document.getElementById('docx-loading-verif');
        const targetEl = document.getElementById('docx-target-verif');

        function adjustDocxScale() {
            if (!targetEl) return;
            const sections = targetEl.querySelectorAll('section.docx, .docx');
            if (!sections.length) return;

            const parentContainer = targetEl.parentElement || targetEl;
            const isMobile = window.innerWidth < 768;
            const availableWidth = parentContainer.clientWidth - (isMobile ? 12 : 24);
            if (availableWidth <= 20) return;

            sections.forEach(function (sec) {
                if (!sec.dataset.origWidth) {
                    sec.dataset.origWidth = sec.offsetWidth || 794;
                }
                const origW = parseFloat(sec.dataset.origWidth) || 794;

                if (!sec.dataset.origHeight) {
                    sec.dataset.origHeight = sec.scrollHeight || sec.offsetHeight || 1123;
                }
                const origH = parseFloat(sec.dataset.origHeight) || 1123;

                let wrap = sec.parentElement;
                if (!wrap || !wrap.classList.contains('docx-page-scaler')) {
                    wrap = document.createElement('div');
                    wrap.className = 'docx-page-scaler';
                    sec.parentNode.insertBefore(wrap, sec);
                    wrap.appendChild(sec);
                }

                if (availableWidth < origW) {
                    const scale = Math.max(0.30, availableWidth / origW);
                    const scaledW = Math.round(origW * scale);
                    const scaledH = Math.round(origH * scale);

                    wrap.style.width = scaledW + 'px';
                    wrap.style.height = scaledH + 'px';
                    wrap.style.margin = '0 auto 1.5rem auto';
                    wrap.style.position = 'relative';
                    wrap.style.overflow = 'hidden';
                    wrap.style.boxShadow = '0 4px 20px -2px rgba(0, 0, 0, 0.12)';
                    wrap.style.borderRadius = '6px';
                    wrap.style.maxWidth = '100%';

                    sec.style.width = origW + 'px';
                    sec.style.minWidth = origW + 'px';
                    sec.style.maxWidth = origW + 'px';
                    sec.style.height = origH + 'px';
                    sec.style.position = 'absolute';
                    sec.style.top = '0';
                    sec.style.left = '0';
                    sec.style.transformOrigin = 'top left';
                    sec.style.transform = 'scale(' + scale + ')';
                    sec.style.margin = '0';
                    sec.style.boxShadow = 'none';
                } else {
                    wrap.style.width = origW + 'px';
                    wrap.style.height = 'auto';
                    wrap.style.margin = '0 auto 1.5rem auto';
                    wrap.style.position = 'relative';
                    wrap.style.overflow = 'visible';
                    wrap.style.boxShadow = 'none';
                    wrap.style.borderRadius = '0';
                    wrap.style.maxWidth = '100%';

                    sec.style.width = origW + 'px';
                    sec.style.minWidth = origW + 'px';
                    sec.style.maxWidth = origW + 'px';
                    sec.style.height = 'auto';
                    sec.style.position = 'relative';
                    sec.style.top = '';
                    sec.style.left = '';
                    sec.style.transformOrigin = '';
                    sec.style.transform = '';
                    sec.style.margin = '0 auto 1.5rem auto';
                    sec.style.boxShadow = '0 4px 25px -4px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(0, 0, 0, 0.05)';
                }
            });
        }

        if (docxUrl && targetEl && window.docx) {
            fetch(docxUrl)
                .then(function(res) {
                    if (!res.ok) throw new Error('Gagal mengambil file .docx');
                    return res.blob();
                })
                .then(function(blob) {
                    return window.docx.renderAsync(blob, targetEl, null, {
                        inWrapper: true,
                        ignoreWidth: false,
                        ignoreHeight: false,
                        breakPages: true,
                        renderHeaders: true,
                        renderFooters: true
                    });
                })
                .then(function() {
                    if (loadingEl) loadingEl.style.display = 'none';
                    setTimeout(adjustDocxScale, 50);
                    setTimeout(adjustDocxScale, 300);
                })
                .catch(function(err) {
                    console.error('Gagal render docx preview verifikasi:', err);
                    if (loadingEl) {
                        loadingEl.innerHTML = '<p class="text-xs text-gray-500">Pratinjau naskah dokumen asli dapat dibuka melalui berkas resmi.</p>';
                    }
                });

            window.addEventListener('resize', adjustDocxScale);
            window.addEventListener('orientationchange', function () {
                setTimeout(adjustDocxScale, 150);
            });
        }
    });
</script>
@endif
@endpush
@endsection
