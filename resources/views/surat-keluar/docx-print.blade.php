<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Dokumen Resmi {{ $suratKeluar->no_surat }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
    <style>
        body {
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            font-family: 'Figtree', sans-serif;
        }
        #docx-container {
            display: flex;
            justify-content: center;
            padding: 2rem 1rem;
        }
        #docx-render-target {
            width: 100%;
            max-width: 900px;
        }
        #docx-render-target .docx-wrapper {
            background: transparent !important;
            padding: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
        }
        #docx-render-target .docx {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            margin: 0 auto 2rem auto !important;
            background: white !important;
            color: #0f172a !important;
            border-radius: 6px;
        }
        #docx-render-target .docx section {
            padding: 30px 40px !important;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
            }
            #docx-container {
                padding: 0 !important;
                margin: 0 !important;
            }
            #docx-render-target {
                max-width: 100% !important;
                width: 100% !important;
            }
            #docx-render-target .docx {
                box-shadow: none !important;
                margin: 0 !important;
                border-radius: 0 !important;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Toolbar (Hanya di layar, otomatis disembunyikan saat dicetak) -->
    <div class="no-print sticky top-0 z-50 bg-slate-900/90 backdrop-blur-md text-white px-4 py-3 shadow-md flex items-center justify-between border-b border-slate-800">
        <div class="flex items-center gap-3">
            <button type="button" onclick="window.history.back()"
                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali</span>
            </button>
            <div>
                <p class="text-xs font-bold text-slate-100 font-mono">{{ $suratKeluar->no_surat }}</p>
                <p class="text-[11px] text-slate-400">Naskah Asli Dokumen Word Resmi Ber-QR Code</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition flex items-center gap-2 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Container Lembar Word Asli -->
    <div id="docx-container">
        <div id="docx-loading" class="flex flex-col items-center justify-center p-16 space-y-3 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="w-9 h-9 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-sm font-bold text-slate-800">Menyiapkan Dokumen Word Asli...</p>
            <p class="text-xs text-slate-500">Memuat berkas naskah asli beserta QR Code & Kode Verifikasi</p>
        </div>
        <div id="docx-render-target"></div>
    </div>

    <script src="{{ asset('vendor/docx-preview/jszip.min.js') }}"></script>
    <script src="{{ asset('vendor/docx-preview/docx-preview.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const docxUrl = "{{ asset('storage/' . $suratKeluar->file_surat_asli) }}";
            const loadingEl = document.getElementById('docx-loading');
            const targetEl = document.getElementById('docx-render-target');

            if (docxUrl && targetEl && window.docx) {
                fetch(docxUrl)
                    .then(function(res) {
                        if (!res.ok) throw new Error('Gagal mengunduh file .docx');
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
                    })
                    .catch(function(err) {
                        console.error('Gagal render dokumen word:', err);
                        if (loadingEl) {
                            loadingEl.innerHTML = '<p class="text-rose-600 text-sm font-bold">Gagal memuat dokumen Word asli.</p>';
                        }
                    });
            }
        });
    </script>
</body>
</html>
