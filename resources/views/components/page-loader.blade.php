{{-- Komponen Skeleton Loader & Animasi Pindah Halaman Universal Al-Falah Putra --}}
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

    /* Animasi Efek Berkilau (Shimmer Wave) untuk Skeleton */
    @keyframes sapShimmerWave {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .sap-skeleton-pulse {
        position: relative;
        overflow: hidden;
        background-color: #e2e8f0;
    }

    .sap-skeleton-pulse::after {
        content: "";
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        transform: translateX(-100%);
        background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.65) 50%, rgba(255,255,255,0) 100%);
        animation: sapShimmerWave 1.4s infinite ease-in-out;
    }

    /* Layar Penuh Skeleton Universal Saat Refresh / Reload */
    #sap-skeleton-screen {
        position: fixed;
        inset: 0;
        z-index: 99998;
        background: #f8fafc;
        transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.3s ease;
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    #sap-skeleton-screen.sap-loaded {
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    /* Latar Transisi Pindah Halaman */
    #sap-page-transition {
        position: fixed;
        inset: 0;
        z-index: 99990;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(248, 250, 252, 0.65);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.3s ease;
    }

    #sap-page-transition.sap-active {
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }

    /* Floating Glass Badge yang Elegan */
    #sap-transition-badge {
        display: flex;
        align-items: center;
        gap: 14px;
        background: rgba(255, 255, 255, 0.96);
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
        #sap-skeleton-screen,
        #sap-page-transition {
            display: none !important;
        }
    }
</style>

<!-- 1. Garis Progres Tipis di Paling Atas Layar -->
<div id="sap-top-bar" class="no-print" aria-hidden="true"></div>

<!-- 2. SKELETON SCREEN UNIVERSAL SAAT REFRESH / RELOAD SEMUA HALAMAN -->
<div id="sap-skeleton-screen" class="no-print flex min-h-screen">
    <!-- Skeleton Sidebar: Hanya muncul di Desktop (lg:flex), di Mobile HIDDEN sehingga 100% TIDAK PERNAH MUNCUL SIDEBAR di HP -->
    <div class="hidden lg:flex w-72 bg-[#065f46] flex-col justify-between p-4 shrink-0 border-r border-emerald-800">
        <div class="space-y-4">
            <!-- Header Logo Skeleton -->
            <div class="flex items-center gap-3 py-2 border-b border-white/10">
                <div class="w-10 h-10 rounded-xl bg-white/20 sap-skeleton-pulse shrink-0"></div>
                <div class="space-y-1.5 flex-1 min-w-0">
                    <div class="h-4 w-28 bg-white/25 rounded-md sap-skeleton-pulse"></div>
                    <div class="h-2.5 w-20 bg-white/15 rounded-md sap-skeleton-pulse"></div>
                </div>
            </div>
            <!-- Menu Items Skeleton -->
            <div class="pt-2 space-y-2">
                <div class="h-9 w-full bg-white/20 rounded-xl sap-skeleton-pulse"></div>
                <div class="h-9 w-full bg-white/10 rounded-xl sap-skeleton-pulse"></div>
                <div class="h-9 w-full bg-white/10 rounded-xl sap-skeleton-pulse"></div>
                <div class="h-9 w-full bg-white/10 rounded-xl sap-skeleton-pulse"></div>
                <div class="h-9 w-full bg-white/10 rounded-xl sap-skeleton-pulse"></div>
                <div class="h-9 w-full bg-white/10 rounded-xl sap-skeleton-pulse"></div>
                <div class="h-9 w-full bg-white/10 rounded-xl sap-skeleton-pulse"></div>
            </div>
        </div>
        <!-- Profile Skeleton -->
        <div class="h-12 w-full bg-white/15 rounded-xl sap-skeleton-pulse"></div>
    </div>

    <!-- Skeleton Konten Utama (Mobile & Desktop) -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Skeleton Navbar Atas -->
        <div class="h-16 px-4 sm:px-6 bg-white border-b border-slate-200/80 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="lg:hidden w-9 h-9 rounded-xl bg-slate-200 sap-skeleton-pulse shrink-0"></div>
                <div class="h-5 w-32 sm:w-48 bg-slate-200 rounded-md sap-skeleton-pulse"></div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-slate-200 sap-skeleton-pulse"></div>
                <div class="w-24 h-8 rounded-full bg-slate-200 sap-skeleton-pulse hidden sm:block"></div>
            </div>
        </div>

        <!-- Skeleton Halaman Konten (Hero + Cards + Tabel) -->
        <div class="p-4 sm:p-6 lg:p-8 space-y-5 max-w-7xl w-full mx-auto">
            <!-- Hero Banner Shimmer -->
            <div class="h-32 sm:h-40 w-full rounded-3xl bg-slate-200/90 sap-skeleton-pulse"></div>

            <!-- Grid 4 Kartu Shimmer -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                <div class="h-24 rounded-2xl bg-slate-200 sap-skeleton-pulse"></div>
                <div class="h-24 rounded-2xl bg-slate-200 sap-skeleton-pulse"></div>
                <div class="h-24 rounded-2xl bg-slate-200 sap-skeleton-pulse"></div>
                <div class="h-24 rounded-2xl bg-slate-200 sap-skeleton-pulse"></div>
            </div>

            <!-- Kartu Besar Shimmer (Tabel / Grafik) -->
            <div class="h-56 sm:h-72 rounded-3xl bg-slate-200 sap-skeleton-pulse"></div>
        </div>
    </div>
</div>

<!-- 3. Badge Pemuatan Mengambang (Floating Glass Badge) Saat Submit / Pindah -->
<div id="sap-page-transition" class="no-print" role="status" aria-live="polite">
    <div id="sap-transition-badge">
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
        // Hilangkan Skeleton Screen Universal secara halus setelah halaman siap
        function dismissSkeleton() {
            const skeleton = document.getElementById('sap-skeleton-screen');
            if (skeleton) {
                skeleton.classList.add('sap-loaded');
                setTimeout(() => {
                    if (skeleton.parentNode) skeleton.parentNode.removeChild(skeleton);
                }, 350);
            }
        }

        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(dismissSkeleton, 180);
        } else {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(dismissSkeleton, 180);
            });
        }
        window.addEventListener('load', () => {
            setTimeout(dismissSkeleton, 100);
        });

        // Fail-safe jika load event lambat
        setTimeout(dismissSkeleton, 2000);

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
                }, 400);
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

        // Tangkap klik tautan internal agar transisi antar halaman halus
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

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                hideTransition();
            }
        });
    })();
</script>
