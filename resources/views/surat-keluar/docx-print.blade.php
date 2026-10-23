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
        #docx-render-target section.docx,
        #docx-render-target .docx {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            margin: 0 auto 2rem auto !important;
            background: white !important;
            color: #0f172a !important;
            border-radius: 6px;
            box-sizing: border-box !important;
            transition: transform 0.15s ease-out;
        }
        #docx-render-target table {
            max-width: 100% !important;
        }
        #docx-render-target img {
            max-width: 100% !important;
            height: auto !important;
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
            .docx-page-scaler {
                width: 100% !important;
                height: auto !important;
                position: static !important;
                margin: 0 !important;
                box-shadow: none !important;
            }
            #docx-render-target {
                max-width: 100% !important;
                width: 100% !important;
            }
            #docx-render-target section.docx,
            #docx-render-target .docx {
                position: static !important;
                transform: none !important;
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
                    class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition flex items-center gap-2 active:scale-95">
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

            function adjustDocxScale() {
                if (!targetEl) return;
                const sections = targetEl.querySelectorAll('section.docx, .docx');
                if (!sections.length) return;

                const parentContainer = targetEl.parentElement || targetEl;
                const isMobile = window.innerWidth < 768;
                const availableWidth = parentContainer.clientWidth - (isMobile ? 12 : 32);
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
                        wrap.style.margin = '0 auto 2rem auto';
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
                        sec.style.margin = '0 auto 2rem auto';
                        sec.style.boxShadow = '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)';
                    }
                });
            }

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
                        setTimeout(adjustDocxScale, 50);
                        setTimeout(adjustDocxScale, 300);
                    })
                    .catch(function(err) {
                        console.error('Gagal render dokumen word:', err);
                        if (loadingEl) {
                            loadingEl.innerHTML = '<p class="text-rose-600 text-sm font-bold">Gagal memuat dokumen Word asli.</p>';
                        }
                    });

                window.addEventListener('resize', adjustDocxScale);
                window.addEventListener('orientationchange', function () {
                    setTimeout(adjustDocxScale, 150);
                });
            }
        });
    </script>
</body>
</html>
