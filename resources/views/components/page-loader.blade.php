{{-- Komponen Animasi Pindah Halaman yang Halus, Rapi & Elegan (Logo + Bar Progres) --}}
<style>
    /* Bar Progres Tipis di Paling Atas Layar */
    #sap-top-bar {
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        width: 0%;
        background: linear-gradient(90deg, #059669 0%, #10b981 50%, #34d399 100%);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.55);
        z-index: 100000;
        pointer-events: none;
        opacity: 0;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
    }

    /* Latar Transisi Lembut (Airy & Bersih, Tanpa Layar Gelap) */
    #sap-page-transition {
        position: fixed;
        inset: 0;
        z-index: 99990;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(248, 250, 252, 0.55);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.35s ease;
    }

    #sap-page-transition.sap-active {
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }

    /* Floating Glass Badge yang Elegan & Kompak */
    #sap-transition-badge {
        display: flex;
        align-items: center;
        gap: 14px;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(16, 185, 129, 0.22);
        border-radius: 20px;
        padding: 14px 22px;
        box-shadow: 0 20px 40px -15px rgba(6, 78, 59, 0.12), 0 0 0 1px rgba(255, 255, 255, 0.9) inset;
        transform: translateY(8px) scale(0.97);
        transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    #sap-page-transition.sap-active #sap-transition-badge {
        transform: translateY(0) scale(1);
    }

    /* Animasi Bernapas Logo yang Tenang & Lembut */
    @keyframes sapGentleBreathe {
        0%, 100% {
            transform: scale(1);
            filter: drop-shadow(0 2px 4px rgba(16, 185, 129, 0.2));
        }
        50% {
            transform: scale(1.06);
            filter: drop-shadow(0 4px 8px rgba(16, 185, 129, 0.35));
        }
    }

    .sap-breathe-logo {
        animation: sapGentleBreathe 2.2s ease-in-out infinite;
    }

    /* Bar Progres Kecil di Dalam Badge */
    .sap-mini-track {
        width: 130px;
        height: 4px;
        background: #f1f5f9;
        border-radius: 999px;
        overflow: hidden;
        margin-top: 6px;
    }

    .sap-mini-fill {
        height: 100%;
        width: 30%;
        background: linear-gradient(90deg, #059669, #10b981);
        border-radius: 999px;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @media print {
        #sap-top-bar,
        #sap-page-transition {
            display: none !important;
        }
    }
</style>

<!-- 1. Garis Progres Tipis di Paling Atas Layar -->
<div id="sap-top-bar" class="no-print" aria-hidden="true"></div>

<!-- 2. Badge Pemuatan Mengambang (Floating Glass Badge) yang Tenang & Rapi -->
<div id="sap-page-transition" class="no-print" role="status" aria-live="polite">
    <div id="sap-transition-badge">
        <!-- Logo Al-Falah Putak -->
        <picture class="shrink-0">
            <source srcset="{{ asset('images/logo-nav.webp') }}" type="image/webp">
            <img src="{{ asset('images/logo-nav.png') }}"
                 alt="Logo Al-Falah Putak"
                 loading="lazy"
                 decoding="async"
                 width="36"
                 height="36"
                 class="w-9 h-9 object-contain shrink-0 sap-breathe-logo drop-shadow-sm">
        </picture>

        <!-- Teks Informasi & Mini Progress Bar -->
        <div class="flex flex-col">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span id="sap-transition-text" class="text-xs font-bold text-slate-800">
                    Memuat Halaman...
                </span>
                <span id="sap-transition-percent" class="text-[11px] font-extrabold text-emerald-700 ml-auto tabular-nums">
                    30%
                </span>
            </div>
            <div class="sap-mini-track">
                <div id="sap-mini-fill" class="sap-mini-fill"></div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        if (window.__sapPageTransitionReady) return;
        window.__sapPageTransitionReady = true;

        let topBar = document.getElementById('sap-top-bar');
        let overlay = document.getElementById('sap-page-transition');
        let miniFill = document.getElementById('sap-mini-fill');
        let percentText = document.getElementById('sap-transition-percent');
        let statusText = document.getElementById('sap-transition-text');

        let progressValue = 0;
        let progressInterval = null;
        let safetyTimeout = null;
        let isTransitioning = false;

        function updateProgress(val) {
            progressValue = Math.min(100, Math.max(0, Math.round(val)));
            if (topBar) topBar.style.width = progressValue + '%';
            if (miniFill) miniFill.style.width = progressValue + '%';
            if (percentText) percentText.textContent = progressValue + '%';
        }

        function showTransition(text, onComplete) {
            if (isTransitioning) return;
            isTransitioning = true;

            if (text && statusText) statusText.textContent = text;

            updateProgress(25);
            if (topBar) topBar.style.opacity = '1';
            if (overlay) overlay.classList.add('sap-active');

            clearInterval(progressInterval);
            progressInterval = setInterval(() => {
                if (progressValue < 65) {
                    updateProgress(progressValue + 12);
                } else if (progressValue < 88) {
                    updateProgress(progressValue + 4);
                }
            }, 80);

            if (typeof onComplete === 'function') {
                setTimeout(() => {
                    onComplete();
                }, 1000);
            }

            clearTimeout(safetyTimeout);
            safetyTimeout = setTimeout(() => {
                hideTransition();
            }, 6000);
        }

        function hideTransition() {
            clearInterval(progressInterval);
            clearTimeout(safetyTimeout);
            updateProgress(100);

            setTimeout(() => {
                if (overlay) overlay.classList.remove('sap-active');
                if (topBar) topBar.style.opacity = '0';
                setTimeout(() => {
                    updateProgress(0);
                    isTransitioning = false;
                }, 300);
            }, 180);
        }

        window.sapStartLoader = showTransition;
        window.sapStopLoader = hideTransition;

        // Tangkap klik tautan internal agar transisi terasa sangat halus dan tidak menghentak
        document.addEventListener('click', function (e) {
            if (e.defaultPrevented) return;
            if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
                return;
            }

            if (link.hasAttribute('download') || link.getAttribute('target') === '_blank' || link.hasAttribute('data-no-loader')) {
                return;
            }

            const lowerHref = href.toLowerCase();
            if (
                lowerHref.includes('/pdf') ||
                lowerHref.includes('/cetak') ||
                lowerHref.includes('/unduh') ||
                lowerHref.includes('/download') ||
                lowerHref.includes('/template') ||
                lowerHref.includes('/export') ||
                lowerHref.includes('/backup')
            ) {
                return;
            }

            try {
                const targetUrl = new URL(link.href, window.location.origin);
                if (targetUrl.origin !== window.location.origin) return;
                if (targetUrl.pathname === window.location.pathname && targetUrl.search === window.location.search) {
                    return;
                }

                e.preventDefault();
                showTransition('Memuat Halaman...', function () {
                    window.location.href = link.href;
                });
            } catch (err) {
                // Abaikan jika URL tidak valid
            }
        });

        // Tangkap submit formulir
        document.addEventListener('submit', function (e) {
            if (e.defaultPrevented) return;
            const form = e.target;
            if (!form || form.tagName !== 'FORM') return;
            if (form.getAttribute('target') === '_blank' || form.hasAttribute('data-no-loader') || form.hasAttribute('data-konfirmasi')) return;

            const action = (form.getAttribute('action') || '').toLowerCase();
            if (action.includes('/pdf') || action.includes('/cetak') || action.includes('/unduh') || action.includes('/backup')) {
                return;
            }

            showTransition('Menyimpan Data...');
        });

        // Otomatis tutup saat kembali dari cache tombol Back/Forward browser
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                hideTransition();
            }
        });
    })();
</script>
