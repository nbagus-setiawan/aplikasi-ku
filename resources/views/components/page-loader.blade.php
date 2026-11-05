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
        box-shadow: 0 0 12px rgba(16, 185, 129, 0.6);
        z-index: 100000;
        pointer-events: none;
        opacity: 0;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
    }

    /* Animasi Shimmer Wave Alir Halus (Fluid 60FPS Hardware-Accelerated) */
    @keyframes sapSmoothGlow {
        0% {
            transform: translate3d(-150%, 0, 0);
        }
        100% {
            transform: translate3d(150%, 0, 0);
        }
    }

    @keyframes sapBasePulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.72;
        }
    }

    /* Skeleton Elemen Terang (Halaman Utama) */
    .sap-skeleton-pulse {
        position: relative;
        overflow: hidden;
        background-color: #f1f5f9; /* Slate-100 lembut */
        border-radius: inherit;
        animation: sapBasePulse 2.4s ease-in-out infinite;
        will-change: opacity;
    }

    .sap-skeleton-pulse::after {
        content: "";
        position: absolute;
        inset: 0;
        transform: translate3d(-150%, 0, 0);
        background: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0.45) 30%,
            rgba(255, 255, 255, 0.85) 50%,
            rgba(255, 255, 255, 0.45) 70%,
            rgba(255, 255, 255, 0) 100%
        );
        animation: sapSmoothGlow 1.8s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        will-change: transform;
    }

    /* Skeleton Elemen Gelap (Sidebar Hijau Desktop) */
    .sap-skeleton-dark {
        position: relative;
        overflow: hidden;
        background-color: rgba(255, 255, 255, 0.12);
        border-radius: inherit;
        animation: sapBasePulse 2.4s ease-in-out infinite;
        will-change: opacity;
    }

    .sap-skeleton-dark::after {
        content: "";
        position: absolute;
        inset: 0;
        transform: translate3d(-150%, 0, 0);
        background: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0.18) 50%,
            rgba(255, 255, 255, 0) 100%
        );
        animation: sapSmoothGlow 1.8s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        will-change: transform;
    }

    /* Layar Penuh Skeleton Universal Saat Refresh / Reload */
    #sap-skeleton-screen {
        position: fixed;
        inset: 0;
        z-index: 99998;
        background: #f8fafc;
        transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1), transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.4s ease;
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: scale(1);
        will-change: opacity, transform;
    }

    #sap-skeleton-screen.sap-loaded {
        opacity: 0 !important;
        transform: scale(0.995) !important;
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
    <!-- Skeleton Sidebar: Hanya tampil di Desktop (lg:flex), di Mobile 100% HIDDEN sehingga tidak pernah berkedip di HP -->
    <div class="hidden lg:flex w-72 bg-[#065f46] flex-col justify-between p-4 shrink-0 border-r border-emerald-800">
        <div class="space-y-4">
            <!-- Header Logo Skeleton -->
            <div class="flex items-center gap-3 py-2 border-b border-white/10">
                <div class="w-10 h-10 rounded-xl sap-skeleton-dark shrink-0"></div>
                <div class="space-y-1.5 flex-1 min-w-0">
                    <div class="h-4 w-28 rounded-md sap-skeleton-dark"></div>
                    <div class="h-2.5 w-20 rounded-md sap-skeleton-dark"></div>
                </div>
            </div>
            <!-- Menu Items Skeleton -->
            <div class="pt-2 space-y-2">
                <div class="h-10 w-full rounded-xl sap-skeleton-dark"></div>
                <div class="h-10 w-full rounded-xl sap-skeleton-dark"></div>
                <div class="h-10 w-full rounded-xl sap-skeleton-dark"></div>
                <div class="h-10 w-full rounded-xl sap-skeleton-dark"></div>
                <div class="h-10 w-full rounded-xl sap-skeleton-dark"></div>
                <div class="h-10 w-full rounded-xl sap-skeleton-dark"></div>
                <div class="h-10 w-full rounded-xl sap-skeleton-dark"></div>
            </div>
        </div>
        <!-- Profile Skeleton -->
        <div class="h-12 w-full rounded-xl sap-skeleton-dark"></div>
    </div>

    <!-- Skeleton Konten Utama (Mobile & Desktop) -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Skeleton Navbar Atas -->
        <div class="h-16 px-4 sm:px-6 bg-white border-b border-slate-200/80 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="lg:hidden w-9 h-9 rounded-xl bg-slate-200 sap-skeleton-pulse shrink-0"></div>
                <div class="h-5 w-36 sm:w-48 rounded-md bg-slate-200 sap-skeleton-pulse"></div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-slate-200 sap-skeleton-pulse"></div>
                <div class="w-24 h-8 rounded-full bg-slate-200 sap-skeleton-pulse hidden sm:block"></div>
            </div>
        </div>

        <!-- Skeleton Halaman Konten (Proporsional & Mirip Asli) -->
        <div class="p-4 sm:p-6 lg:p-8 space-y-5 max-w-7xl w-full mx-auto">
            <!-- 1. Hero Banner Skeleton -->
            <div class="rounded-3xl p-5 sm:p-7 bg-slate-200/90 border border-slate-300/60 shadow-xs flex flex-col justify-between gap-4 sap-skeleton-pulse min-h-[140px] sm:min-h-[160px]">
                <div class="space-y-3 max-w-md">
                    <div class="flex items-center gap-2">
                        <div class="h-5 w-32 rounded-full bg-white/70"></div>
                        <div class="h-5 w-28 rounded-full bg-white/50 hidden sm:block"></div>
                    </div>
                    <div class="h-7 w-64 sm:w-80 rounded-xl bg-white/80"></div>
                    <div class="h-4 w-full sm:w-96 rounded-md bg-white/60"></div>
                </div>
            </div>

            <!-- 2. Quick Actions Skeleton -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
                <div class="h-4 w-40 rounded-md bg-slate-200 sap-skeleton-pulse mb-3.5"></div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div class="h-16 rounded-2xl bg-slate-100 border border-slate-200/60 flex items-center gap-3 p-3 sap-skeleton-pulse">
                        <div class="w-10 h-10 rounded-2xl bg-slate-200 shrink-0"></div>
                        <div class="space-y-1.5 flex-1 min-w-0">
                            <div class="h-3.5 w-20 rounded bg-slate-200"></div>
                            <div class="h-2.5 w-16 rounded bg-slate-200"></div>
                        </div>
                    </div>
                    <div class="h-16 rounded-2xl bg-slate-100 border border-slate-200/60 flex items-center gap-3 p-3 sap-skeleton-pulse">
                        <div class="w-10 h-10 rounded-2xl bg-slate-200 shrink-0"></div>
                        <div class="space-y-1.5 flex-1 min-w-0">
                            <div class="h-3.5 w-20 rounded bg-slate-200"></div>
                            <div class="h-2.5 w-16 rounded bg-slate-200"></div>
                        </div>
                    </div>
                    <div class="h-16 rounded-2xl bg-slate-100 border border-slate-200/60 flex items-center gap-3 p-3 sap-skeleton-pulse">
                        <div class="w-10 h-10 rounded-2xl bg-slate-200 shrink-0"></div>
                        <div class="space-y-1.5 flex-1 min-w-0">
                            <div class="h-3.5 w-20 rounded bg-slate-200"></div>
                            <div class="h-2.5 w-16 rounded bg-slate-200"></div>
                        </div>
                    </div>
                    <div class="h-16 rounded-2xl bg-slate-100 border border-slate-200/60 flex items-center gap-3 p-3 sap-skeleton-pulse">
                        <div class="w-10 h-10 rounded-2xl bg-slate-200 shrink-0"></div>
                        <div class="space-y-1.5 flex-1 min-w-0">
                            <div class="h-3.5 w-20 rounded bg-slate-200"></div>
                            <div class="h-2.5 w-16 rounded bg-slate-200"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. KPI Metric Cards Skeleton -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between gap-3 min-h-[105px]">
                    <div class="flex items-center justify-between">
                        <div class="h-3.5 w-20 rounded bg-slate-200 sap-skeleton-pulse"></div>
                        <div class="w-7 h-7 rounded-lg bg-slate-200 sap-skeleton-pulse"></div>
                    </div>
                    <div class="h-7 w-16 rounded-lg bg-slate-200 sap-skeleton-pulse"></div>
                    <div class="h-2.5 w-24 rounded bg-slate-200 sap-skeleton-pulse"></div>
                </div>
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between gap-3 min-h-[105px]">
                    <div class="flex items-center justify-between">
                        <div class="h-3.5 w-20 rounded bg-slate-200 sap-skeleton-pulse"></div>
                        <div class="w-7 h-7 rounded-lg bg-slate-200 sap-skeleton-pulse"></div>
                    </div>
                    <div class="h-7 w-16 rounded-lg bg-slate-200 sap-skeleton-pulse"></div>
                    <div class="h-2.5 w-24 rounded bg-slate-200 sap-skeleton-pulse"></div>
                </div>
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between gap-3 min-h-[105px]">
                    <div class="flex items-center justify-between">
                        <div class="h-3.5 w-20 rounded bg-slate-200 sap-skeleton-pulse"></div>
                        <div class="w-7 h-7 rounded-lg bg-slate-200 sap-skeleton-pulse"></div>
                    </div>
                    <div class="h-7 w-16 rounded-lg bg-slate-200 sap-skeleton-pulse"></div>
                    <div class="h-2.5 w-24 rounded bg-slate-200 sap-skeleton-pulse"></div>
                </div>
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between gap-3 min-h-[105px]">
                    <div class="flex items-center justify-between">
                        <div class="h-3.5 w-20 rounded bg-slate-200 sap-skeleton-pulse"></div>
                        <div class="w-7 h-7 rounded-lg bg-slate-200 sap-skeleton-pulse"></div>
                    </div>
                    <div class="h-7 w-16 rounded-lg bg-slate-200 sap-skeleton-pulse"></div>
                    <div class="h-2.5 w-24 rounded bg-slate-200 sap-skeleton-pulse"></div>
                </div>
            </div>

            <!-- 4. Area Visual Chart & Analytics Skeleton -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="h-4 w-44 rounded bg-slate-200 sap-skeleton-pulse"></div>
                        <div class="h-4 w-28 rounded bg-slate-200 sap-skeleton-pulse"></div>
                    </div>
                    <div class="h-52 sm:h-60 rounded-xl bg-slate-100/80 border border-slate-200/50 p-4 flex items-end justify-between gap-3 sap-skeleton-pulse">
                        <div class="w-full h-1/3 rounded-t-lg bg-slate-200"></div>
                        <div class="w-full h-2/3 rounded-t-lg bg-slate-200"></div>
                        <div class="w-full h-1/2 rounded-t-lg bg-slate-200"></div>
                        <div class="w-full h-4/5 rounded-t-lg bg-slate-200"></div>
                        <div class="w-full h-3/5 rounded-t-lg bg-slate-200"></div>
                        <div class="w-full h-full rounded-t-lg bg-slate-200"></div>
                    </div>
                </div>
                <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs space-y-4 flex flex-col justify-between">
                    <div class="h-4 w-36 rounded bg-slate-200 sap-skeleton-pulse pb-3 border-b border-slate-100"></div>
                    <div class="w-36 h-36 rounded-full border-8 border-slate-200 sap-skeleton-pulse mx-auto my-auto"></div>
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                        <div class="h-7 rounded-lg bg-slate-200 sap-skeleton-pulse"></div>
                        <div class="h-7 rounded-lg bg-slate-200 sap-skeleton-pulse"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. Badge Pemuatan Mengambang (Floating Glass Badge) Saat Submit / Pindah Halaman -->
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
        // Hilangkan Skeleton Screen Universal secara sangat halus begitu halaman siap
        function dismissSkeleton() {
            const skeleton = document.getElementById('sap-skeleton-screen');
            if (skeleton && !skeleton.classList.contains('sap-loaded')) {
                skeleton.classList.add('sap-loaded');
                setTimeout(() => {
                    if (skeleton.parentNode) skeleton.parentNode.removeChild(skeleton);
                }, 450);
            }
        }

        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(dismissSkeleton, 150);
        } else {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(dismissSkeleton, 150);
            });
        }
        window.addEventListener('load', () => {
            setTimeout(dismissSkeleton, 80);
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
            if (!overlay) return;

            if (statusText) statusText.textContent = text || 'Memuat Halaman...';
            overlay.classList.add('sap-active');
            isTransitioning = true;

            if (topBar) {
                topBar.style.opacity = '1';
                topBar.style.width = '15%';
            }

            updateProgress(25);

            clearInterval(progressInterval);
            progressInterval = setInterval(() => {
                if (progressValue < 85) {
                    updateProgress(progressValue + (88 - progressValue) * 0.18);
                }
            }, 120);

            if (typeof onComplete === 'function') {
                setTimeout(() => {
                    onComplete();
                }, 350);
            }

            clearTimeout(safetyTimeout);
            safetyTimeout = setTimeout(() => {
                hideTransition();
            }, 6000);
        }

        function hideTransition() {
            if (!isTransitioning && !overlay?.classList.contains('sap-active')) return;

            clearInterval(progressInterval);
            updateProgress(100);

            setTimeout(() => {
                if (overlay) overlay.classList.remove('sap-active');
                if (topBar) {
                    topBar.style.opacity = '0';
                    setTimeout(() => {
                        topBar.style.width = '0%';
                    }, 300);
                }
                isTransitioning = false;
                clearTimeout(safetyTimeout);
            }, 180);
        }

        window.sapStartLoader = showTransition;
        window.sapStopLoader = hideTransition;

        // Tangkap klik tautan internal agar transisi antar halaman halus
        document.addEventListener('click', function (e) {
            if (e.defaultPrevented) return;
            if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

            let anchor = e.target.closest('a');
            if (!anchor) return;

            let href = anchor.getAttribute('href');
            if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript:')) return;
            if (anchor.getAttribute('target') === '_blank') return;
            if (anchor.hasAttribute('download')) return;
            if (anchor.classList.contains('no-loader')) return;

            try {
                let url = new URL(anchor.href, window.location.origin);
                if (url.origin !== window.location.origin) return;
                if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return;

                let ext = url.pathname.split('.').pop().toLowerCase();
                if (['pdf', 'xlsx', 'xls', 'csv', 'zip', 'doc', 'docx', 'png', 'jpg'].includes(ext)) {
                    return;
                }

                showTransition('Membuka Halaman...');
            } catch (err) {}
        });

        // Tangkap submit formulir
        document.addEventListener('submit', function (e) {
            let form = e.target;
            if (form.classList.contains('no-loader')) return;
            if (form.getAttribute('target') === '_blank') return;

            let submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn && submitBtn.hasAttribute('data-confirm')) return;

            showTransition('Menyimpan Data...');
        });

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                hideTransition();
                dismissSkeleton();
            }
        });
    })();
</script>
