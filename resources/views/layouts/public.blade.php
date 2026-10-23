<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $namaSitus = 'PONDOK PESANTREN AL-FALAH PUTAK';
        $namaSitusPendek = 'PON-PES AL FALAH PUTAK'; // Nama situs yang tampil di atas URL pada hasil Google
        $judulHalaman = trim(html_entity_decode($__env->yieldContent('title'), ENT_QUOTES, 'UTF-8'));
        $isBeranda = request()->is('/') || $judulHalaman === '' || $judulHalaman === 'Beranda';
        $judulDepan = $isBeranda ? 'HOME' : mb_strtoupper($judulHalaman, 'UTF-8');
        $judulLengkap = $judulDepan . ' | ' . $namaSitus;
        $judulOg = $judulLengkap;
        $deskripsiDefault = 'Website resmi Pondok Pesantren Salafiyah Al-Falah Putak, Desa Putak, Kec. Gelumbang, Kab. Muara Enim, Sumatera Selatan. Pendidikan kitab kuning, tahfidzul Qur\'an, dan pembinaan akhlak santri berhaluan Ahlussunnah wal Jamaah.';
        $deskripsi = trim(html_entity_decode($__env->yieldContent('meta_description'), ENT_QUOTES, 'UTF-8')) ?: $deskripsiDefault;
        $gambarOg = asset('images/og-alfalah-putak.jpg');
        $urlBeranda = url('/') . '/';
        $urlKanonik = $isBeranda ? $urlBeranda : url()->current();
    @endphp

    <title>{{ $judulLengkap }}</title>

    <!-- Google Site Verification Google Search Console -->
    <meta name="google-site-verification" content="VkmH3sunnODzVDbLS8PYiE80wRiCRNe72N1aQdDGgtw" />

    <!-- Meta Dasar & SEO Google Indexing -->
    <meta name="description" content="{{ $deskripsi }}">
    <meta name="keywords" content="Pondok Pesantren Al-Falah Putak, PP Al Falah Putak, Pesantren Al Falah Putak Gelumbang, Pondok Pesantren Muara Enim, Ponpes Al Falah Putak, Pesantren Salafiyah Sumatera Selatan, K.H. Mursyidi, Ny. Hj. Aniswatuzzuhriyah, Media Resmi Al-Falah Putak, Santri Al Falah Putak, Kajian Kitab Kuning, Tahfidzul Quran Putak, Madrasah Diniyah Al Falah, Penerimaan Santri Baru Al Falah Putak, Ponpes Gelumbang">
    <meta name="author" content="{{ $namaSitus }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ $urlKanonik }}">

    <!-- Identitas Nama Situs untuk Hasil Pencarian Google -->
    <meta name="application-name" content="{{ $namaSitusPendek }}">
    <meta name="apple-mobile-web-app-title" content="{{ $namaSitusPendek }}">
    <meta name="theme-color" content="#064e3b">

    <!-- Favicon Google Search Resmi (Memenuhi Logo, Tanpa Sisa Putih) -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192x192.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">

    <!-- Open Graph (WhatsApp, Facebook) — gambar lebar 1200x630, < 300 KB -->
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $namaSitusPendek }}">
    <meta property="og:title" content="{{ $judulOg }}">
    <meta property="og:description" content="{{ $deskripsi }}">
    <meta property="og:url" content="{{ $urlKanonik }}">
    <meta property="og:image" content="{{ $gambarOg }}">
    <meta property="og:image:secure_url" content="{{ $gambarOg }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $namaSitus }}">
    <link rel="image_src" href="{{ $gambarOg }}">

    <!-- Twitter Card (Large Image di Atas) -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $judulOg }}">
    <meta name="twitter:description" content="{{ $deskripsi }}">
    <meta name="twitter:image" content="{{ $gambarOg }}">

    <!-- Structured Data JSON-LD untuk Google Search & Rich Snippet -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "EducationalOrganization",
        "name": "PONDOK PESANTREN AL-FALAH PUTAK",
        "alternateName": [
            "PP Al-Falah Putak",
            "Pondok Pesantren Salafiyah Al-Falah Putak",
            "MEDIA RESMI AL-FALAH PUTAK"
        ],
        "url": "{{ $urlBeranda }}",
        "logo": "{{ asset('favicon-192x192.png') }}",
        "image": "{{ $gambarOg }}",
        "description": "Media Resmi Pondok Pesantren Salafiyah Al-Falah Putak, Gelumbang, Muara Enim, Sumatera Selatan. Menempa generasi santri yang berakhlak mulia, mendalami khazanah kitab salaf, dan berpegang teguh pada Al-Qur'an dan Sunnah.",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Desa Putak",
            "addressLocality": "Gelumbang",
            "addressRegion": "Sumatera Selatan",
            "postalCode": "31171",
            "addressCountry": "ID"
        },
        "founder": [
            {
                "@type": "Person",
                "name": "K.H. Mursyidi"
            },
            {
                "@type": "Person",
                "name": "Ny. Hj. Aniswatuzzuhriyah"
            }
        ],
        "foundingDate": "2005-03-05",
        "telephone": "0821-1870-2718"
    }
    </script>
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "WebSite",
        "name": "{{ $namaSitusPendek }}",
        "alternateName": ["{{ $namaSitus }}", "Ponpes Al Falah Putak"],
        "url": "{{ $urlBeranda }}"
    }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-50 min-h-screen flex flex-col justify-between selection:bg-emerald-600 selection:text-white">
    <x-page-loader />

    <!-- Navbar Publik Responsif (Desktop & Mobile) -->
    <header x-data="{ mobileOpen: false }" class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-emerald-950/10 shadow-xs transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Logo & Identitas Lembaga (MEDIA RESMI AL-FALAH PUTAK) -->
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 sm:gap-3.5 group py-1 min-w-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Pondok Pesantren Al-Falah Putak" class="w-10 h-10 sm:w-11 sm:h-11 md:w-12 md:h-12 object-contain shrink-0 drop-shadow-xs group-hover:scale-105 transition-transform duration-200">
                    <div class="leading-tight min-w-0">
                        <span class="block text-xs xs:text-sm sm:text-base md:text-lg font-black text-emerald-950 tracking-tight group-hover:text-emerald-700 transition truncate">
                            MEDIA RESMI AL-FALAH PUTAK
                        </span>
                        <span class="block text-[11px] sm:text-xs font-semibold text-emerald-700 truncate">
                            Pondok Pesantren Salafiyah Al-Falah Putak
                        </span>
                    </div>
                </a>

                <!-- Navigasi Desktop (Hanya Menu Publik, Tanpa Tombol Login Atas) -->
                <nav class="hidden md:flex items-center gap-1.5 shrink-0">
                    <a href="{{ url('/') }}"
                       class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition {{ request()->is('/') ? 'text-emerald-900 bg-emerald-50 font-bold shadow-xs' : 'text-slate-600 hover:text-emerald-800 hover:bg-slate-100/70' }}">
                        Beranda
                    </a>
                    <a href="{{ url('/pengumuman') }}"
                       class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition {{ request()->is('pengumuman*') ? 'text-emerald-900 bg-emerald-50 font-bold shadow-xs' : 'text-slate-600 hover:text-emerald-800 hover:bg-slate-100/70' }}">
                        Pengumuman
                    </a>
                    <a href="{{ url('/verifikasi') }}"
                       class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition {{ request()->is('verifikasi*') ? 'text-emerald-900 bg-emerald-50 font-bold shadow-xs' : 'text-slate-600 hover:text-emerald-800 hover:bg-slate-100/70' }}">
                        Verifikasi Dokumen
                    </a>
                </nav>

                <!-- Tombol Hamburger Mobile -->
                <div class="flex md:hidden items-center shrink-0">
                    <button type="button"
                            @click="mobileOpen = !mobileOpen"
                            class="p-2 rounded-xl text-slate-700 hover:text-emerald-800 hover:bg-emerald-50 transition border border-slate-200/80 focus:outline-none"
                            aria-label="Buka Menu Navigasi"
                            :aria-expanded="mobileOpen">
                        <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Panel Menu Mobile Dropdown (Sentuhan Halus & Rapi, Tanpa Tombol Login Atas) -->
            <div x-show="mobileOpen"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-3"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-3"
                 class="md:hidden py-3 border-t border-slate-100 space-y-1">
                <a href="{{ url('/') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition {{ request()->is('/') ? 'text-emerald-950 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100/70' }}">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Beranda Utama</span>
                </a>
                <a href="{{ url('/pengumuman') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition {{ request()->is('pengumuman*') ? 'text-emerald-950 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100/70' }}">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    <span>Pengumuman & Warta</span>
                </a>
                <a href="{{ url('/verifikasi') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition {{ request()->is('verifikasi*') ? 'text-emerald-950 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100/70' }}">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Verifikasi Dokumen</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Modal Toast Terpusat di Tengah dengan Animasi Ceklis & Timer 2 Detik -->
    <x-modal-toast />

    <!-- Konten Utama Halaman -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Publik yang Berwibawa & Rapi -->
    <footer class="bg-emerald-950 text-slate-300 pt-12 sm:pt-16 pb-10 sm:pb-12 border-t border-emerald-900/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-10 pb-10 sm:pb-12 border-b border-emerald-900/80">
                <!-- Kolom 1: Profil & Identitas Lembaga (MEDIA RESMI AL-FALAH PUTAK) -->
                <div class="md:col-span-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Pondok Pesantren Al-Falah Putak" class="w-12 h-12 sm:w-14 sm:h-14 object-contain shrink-0 drop-shadow-md">
                        <div class="leading-tight">
                            <span class="text-base sm:text-lg md:text-xl font-bold text-white tracking-wide block">
                                MEDIA RESMI AL-FALAH PUTAK
                            </span>
                            <span class="text-xs sm:text-sm text-emerald-400 font-semibold block mt-0.5">
                                Pondok Pesantren Salafiyah Al-Falah Putak, Muara Enim
                            </span>
                        </div>
                    </div>
                    <p class="text-sm sm:text-base text-slate-300/90 leading-relaxed max-w-lg">
                        Media informasi dan publikasi resmi Pondok Pesantren Salafiyah Al-Falah Putak. Menyajikan warta kegiatan santri, maklumat kelembagaan, serta layanan verifikasi dokumen persuratan resmi.
                    </p>
                    <div class="pt-1 text-xs sm:text-sm text-slate-300 space-y-2">
                        <p class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            <span>{{ $pengaturan['alamat'] ?? 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171' }}</span>
                        </p>
                        <p class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>{{ $pengaturan['kontak'] ?? '0851-8320-1627' }}</span>
                        </p>
                    </div>
                </div>

                <!-- Kolom 2: Tautan Navigasi Cepat & Akses Login Petugas di Bawah -->
                <div class="md:col-span-3">
                    <h4 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-emerald-300 mb-4">Navigasi Utama</h4>
                    <ul class="space-y-2.5 text-sm sm:text-base">
                        <li><a href="{{ url('/') }}" class="hover:text-emerald-400 transition">Beranda Utama</a></li>
                        <li><a href="{{ url('/pengumuman') }}" class="hover:text-emerald-400 transition">Pengumuman &amp; Warta</a></li>
                        <li><a href="{{ url('/verifikasi') }}" class="hover:text-emerald-400 transition">Verifikasi Dokumen</a></li>
                        <!-- Akses Login Petugas di Letakkan di Bawah Saja -->
                        <li class="pt-3 border-t border-emerald-900/80">
                            @auth
                                <a href="{{ route('dashboard') }}" class="text-emerald-400 hover:text-white transition inline-flex items-center gap-1.5 font-bold text-xs sm:text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                    <span>Buka Panel Admin &rarr;</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-slate-400 hover:text-emerald-300 transition inline-flex items-center gap-1.5 font-semibold text-xs sm:text-sm">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                    <span>Portal Sekretariat (Login) &rarr;</span>
                                </a>
                            @endauth
                        </li>
                    </ul>
                </div>

                <!-- Kolom 3: Layanan Sekretariat -->
                <div class="md:col-span-3">
                    <h4 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-emerald-300 mb-4">Layanan Sekretariat</h4>
                    <ul class="space-y-2.5 text-xs sm:text-sm text-slate-300/90 leading-relaxed">
                        <li>Administrasi &amp; Induk Santri</li>
                        <li>Legalisir &amp; Pengesahan Surat</li>
                        <li>Arsip Dokumen &amp; Piagam Ijazah</li>
                        <li class="pt-1.5 text-emerald-400 font-bold">Pelayanan: Sabtu - Kamis (08:00 - 17:00 WIB)</li>
                    </ul>
                </div>
            </div>

            <!-- Hak Cipta Resmi -->
            <div class="pt-6 sm:pt-8 text-center text-xs sm:text-sm text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p>&copy; {{ date('Y') }} MEDIA RESMI AL-FALAH PUTAK &bull; Pondok Pesantren Salafiyah Al-Falah Putak</p>
                <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-400">
                    <span class="text-emerald-400/90">Desa Putak, Gelumbang, Muara Enim</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
