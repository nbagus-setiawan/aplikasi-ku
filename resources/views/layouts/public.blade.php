<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $namaSitus = 'PONDOK PESANTREN AL-FALAH PUTAK';
        $namaSitusPendek = 'PON-PES AL FALAH PUTAK'; // Nama situs resmi yang tampil di atas URL pada hasil Google
        $judulHalaman = trim(html_entity_decode($__env->yieldContent('title'), ENT_QUOTES, 'UTF-8'));
        $isBeranda = request()->is('/') || $judulHalaman === '' || $judulHalaman === 'Beranda';
        $judulDepan = $isBeranda ? 'HOME' : mb_strtoupper($judulHalaman, 'UTF-8');
        $judulLengkap = $judulDepan . ' | ' . $namaSitus;
        $judulOg = $judulLengkap;

        // Deskripsi Lengkap Umum Resmi (Putra & Putri, kurikulum salaf, titik lokasi, pengasuh)
        $deskripsiDefault = 'Website resmi Pondok Pesantren Salafiyah Al-Falah Putak, lembaga pendidikan Islam salafiyah di Desa Putak, Kec. Gelumbang, Kab. Muara Enim, Sumatera Selatan. Menempa insan santri putra dan putri yang berakhlak mulia, berwawasan luas, mandiri, dan berpegang teguh pada Al-Qur\'an dan Sunnah Nabi SAW dengan landasan akidah Ahlussunnah wal Jama\'ah serta madzhab Syafi\'i. Menyelenggarakan pengajian khazanah kitab kuning salaf, tahfidzul Qur\'an, madrasah diniyah takmiliyah, dan pembinaan karakter di bawah bimbingan Pengasuh K.H. Mursyidi dan Ny. Hj. Aniswatuzzuhriyah.';
        
        $deskripsiCustom = trim(html_entity_decode($__env->yieldContent('meta_description'), ENT_QUOTES, 'UTF-8'));
        $deskripsi = $deskripsiCustom ?: $deskripsiDefault;

        $gambarOgCustom = trim($__env->yieldContent('og_image'));
        $gambarOg = $gambarOgCustom ?: asset('images/og-alfalah-putak.jpg');
        $urlBeranda = url('/') . '/';
        $urlKanonik = $isBeranda ? $urlBeranda : url()->current();

        // Keywords Lengkap untuk Menguasai Pencarian Lokal & Regional Melawan Pesaing
        $kataKunciSEO = 'Pondok Pesantren Al-Falah Putak, PP Al Falah Putak, Ponpes Al Falah Putak, Pesantren Salafiyah Sumatera Selatan, Pondok Pesantren Putra Putri Gelumbang, Pondok Pesantren Terbaik di Sumatera Selatan, Pondok Pesantren Muara Enim, Pesantren Terdekat Prabumulih, Pesantren Palembang Ogan Ilir, K.H. Mursyidi, Ny. Hj. Aniswatuzzuhriyah, Media Resmi Al-Falah Putak, Santri Al Falah Putak, Kajian Kitab Kuning Salaf, Nahwu Shorof, Tahfidzul Quran Muara Enim, Madrasah Diniyah Al Falah, Penerimaan Santri Baru Al Falah Putak, Ponpes Gelumbang, Pesantren Ahlussunnah wal Jamaah Sumsel, Sekolah Islam Berasrama Gelumbang';
    @endphp

    <title>{{ $judulLengkap }}</title>

    <!-- Google Site Verification (Wajib Tetap Ada untuk Google Search Console) -->
     <meta name="google-site-verification" content="72Nq8pD4SrApP7ntXjAQOdaCnfwLRmT3lMQ5bRs7cHY" />


    <!-- Open Graph (Wajib di Atas untuk WhatsApp Crawler & Media Sosial) -->
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="{{ $isBeranda ? 'website' : 'article' }}">
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
    <meta itemprop="name" content="{{ $judulOg }}">
    <meta itemprop="description" content="{{ $deskripsi }}">
    <meta itemprop="image" content="{{ $gambarOg }}">
    <link rel="image_src" href="{{ $gambarOg }}">

    <!-- Twitter Card (Pratinjau Gambar Besar) -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $judulOg }}">
    <meta name="twitter:description" content="{{ $deskripsi }}">
    <meta name="twitter:image" content="{{ $gambarOg }}">

    <!-- Meta Dasar & SEO Google Indexing -->
    <meta name="description" content="{{ $deskripsi }}">
    <meta name="keywords" content="{{ $kataKunciSEO }}">
    <meta name="author" content="{{ $namaSitus }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="google" content="notranslate">
    <link rel="canonical" href="{{ $urlKanonik }}">

    <!-- Local SEO & Geo Targeting (Google Maps Presisi: -3.193732918976179, 104.48584233774767) -->
    <meta name="geo.region" content="ID-SS">
    <meta name="geo.placename" content="Desa Putak, Gelumbang, Muara Enim, Sumatera Selatan">
    <meta name="geo.position" content="-3.193733;104.485842">
    <meta name="ICBM" content="-3.193733, 104.485842">

    <!-- Identitas Nama Situs untuk Hasil Pencarian Google -->
    <meta name="application-name" content="{{ $namaSitusPendek }}">
    <meta name="apple-mobile-web-app-title" content="{{ $namaSitusPendek }}">
    <meta name="theme-color" content="#064e3b">

    <!-- Favicon Google Search Resmi (Memenuhi Ruang Logo Secara Penuh Tanpa Margin Sisa) -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=3">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-32x32.png') }}?v=3">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=3">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}?v=3">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}?v=3">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192x192.png') }}?v=3">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=3">

    <!-- Senjata Rahasia SEO: Schema Markup JSON-LD Terstruktur Standar Global Schema.org -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@graph": [
            {
                "@@type": ["EducationalOrganization", "ReligiousOrganization"],
                "@@id": "{{ $urlBeranda }}#organization",
                "name": "Pondok Pesantren Salafiyah Al-Falah Putak",
                "alternateName": [
                    "Pondok Pesantren Al-Falah Putak",
                    "PP Al-Falah Putak",
                    "Ponpes Al Falah Putak",
                    "Pondok Pesantren Putra Putri Al-Falah Putak",
                    "MEDIA RESMI AL-FALAH PUTAK"
                ],
                "url": "{{ $urlBeranda }}",
                "logo": {
                    "@@type": "ImageObject",
                    "@@id": "{{ $urlBeranda }}#logo",
                    "url": "{{ asset('favicon-192x192.png') }}",
                    "caption": "Logo Pondok Pesantren Al-Falah Putak",
                    "width": 192,
                    "height": 192
                },
                "image": {
                    "@@type": "ImageObject",
                    "@@id": "{{ $urlBeranda }}#primaryimage",
                    "url": "{{ $gambarOg }}",
                    "width": 1200,
                    "height": 630
                },
                "description": "{{ $deskripsiDefault }}",
                "foundingDate": "2005-03-05",
                "founder": [
                    {
                        "@@type": "Person",
                        "name": "K.H. Mursyidi",
                        "jobTitle": "Pengasuh Pondok Pesantren"
                    },
                    {
                        "@@type": "Person",
                        "name": "Ny. Hj. Aniswatuzzuhriyah",
                        "jobTitle": "Pengasuh Pondok Pesantren"
                    }
                ],
                "address": {
                    "@@type": "PostalAddress",
                    "streetAddress": "Jl. Pesantren Al-Falah, Desa Putak",
                    "addressLocality": "Kecamatan Gelumbang",
                    "addressRegion": "Sumatera Selatan",
                    "postalCode": "31171",
                    "addressCountry": "ID"
                },
                "geo": {
                    "@@type": "GeoCoordinates",
                    "latitude": -3.193732918976179,
                    "longitude": 104.48584233774767
                },
                "hasMap": "https://maps.app.goo.gl/sVdzFiBgD7VdYKteA",
                "telephone": "+6282118702718",
                "email": "sekretariat@ponpesalfalahputak.my.id",
                "contactPoint": [
                    {
                        "@@type": "ContactPoint",
                        "telephone": "+6282118702718",
                        "contactType": "Informasi & Pendaftaran Santri Baru",
                        "areaServed": "ID",
                        "availableLanguage": ["Indonesian", "Arabic"]
                    }
                ],
                "sameAs": [
                    "https://maps.app.goo.gl/sVdzFiBgD7VdYKteA"
                ],
                "knowsAbout": [
                    "Pendidikan Pesantren Salafiyah",
                    "Pendidikan Santri Putra dan Putri",
                    "Kajian Khazanah Kitab Kuning",
                    "Tahfidzul Qur'an",
                    "Nahwu dan Shorof",
                    "Fiqih Madzhab Syafi'i",
                    "Akidah Ahlussunnah wal Jama'ah",
                    "Madrasah Diniyah Takmiliyah",
                    "Pembinaan Karakter dan Kemandirian Santri"
                ],
                "hasOfferCatalog": {
                    "@@type": "OfferCatalog",
                    "name": "Program Pendidikan Pesantren",
                    "itemListElement": [
                        {
                            "@@type": "Offer",
                            "itemOffered": {
                                "@@type": "Course",
                                "name": "Pengajian Kitab Kuning (Salafiyah)",
                                "description": "Pendalaman kitab-kitab salaf mu'tabarah meliputi nahwu, shorof, fiqih, tafsir, hadits, tasawuf, dan ushul fiqih.",
                                "provider": {
                                    "@@type": "EducationalOrganization",
                                    "name": "Pondok Pesantren Salafiyah Al-Falah Putak",
                                    "sameAs": "{{ $urlBeranda }}"
                                }
                            }
                        },
                        {
                            "@@type": "Offer",
                            "itemOffered": {
                                "@@type": "Course",
                                "name": "Program Tahfidz & Tahsin Al-Qur'an",
                                "description": "Bimbingan menghafal Al-Qur'an bersanad dan tajwid tartil untuk santri putra dan santri putri.",
                                "provider": {
                                    "@@type": "EducationalOrganization",
                                    "name": "Pondok Pesantren Salafiyah Al-Falah Putak",
                                    "sameAs": "{{ $urlBeranda }}"
                                }
                            }
                        },
                        {
                            "@@type": "Offer",
                            "itemOffered": {
                                "@@type": "Course",
                                "name": "Madrasah Diniyah Takmiliyah",
                                "description": "Pendidikan klasikal terstruktur berjenjang untuk penguatan ilmu keislaman.",
                                "provider": {
                                    "@@type": "EducationalOrganization",
                                    "name": "Pondok Pesantren Salafiyah Al-Falah Putak",
                                    "sameAs": "{{ $urlBeranda }}"
                                }
                            }
                        }
                    ]
                }
            },
            {
                "@@type": "WebSite",
                "@@id": "{{ $urlBeranda }}#website",
                "url": "{{ $urlBeranda }}",
                "name": "{{ $namaSitusPendek }}",
                "alternateName": [
                    "{{ $namaSitus }}",
                    "Ponpes Al Falah Putak",
                    "Website Resmi Ponpes Al Falah Putak"
                ],
                "publisher": {
                    "@@id": "{{ $urlBeranda }}#organization"
                },
                "inLanguage": "id-ID",
                "potentialAction": {
                    "@@type": "SearchAction",
                    "target": {
                        "@@type": "EntryPoint",
                        "urlTemplate": "{{ url('/pengumuman') }}?q={search_term_string}"
                    },
                    "query-input": "required name=search_term_string"
                }
            },
            {
                "@@type": "WebPage",
                "@@id": "{{ $urlKanonik }}#webpage",
                "url": "{{ $urlKanonik }}",
                "name": "{{ $judulOg }}",
                "isPartOf": {
                    "@@id": "{{ $urlBeranda }}#website"
                },
                "about": {
                    "@@id": "{{ $urlBeranda }}#organization"
                },
                "description": "{{ $deskripsi }}",
                "inLanguage": "id-ID"
            }
        ]
    }
    </script>

    @yield('extra_schema')

    <!-- Fonts & Resource Preconnect -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @if($isBeranda)
    <!-- Preload LCP Hero Image untuk Skor Performa Maksimal 100% di Mobile & Desktop -->
    <link rel="preload" as="image" href="{{ asset('images/hero-mobile.webp') }}" type="image/webp" media="(max-width: 640px)" fetchpriority="high">
    <link rel="preload" as="image" href="{{ asset('images/hero.webp') }}" type="image/webp" media="(min-width: 641px)" fetchpriority="high">
    @endif

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
                    <picture class="shrink-0">
                        <source srcset="{{ asset('images/logo-nav.webp') }}" type="image/webp">
                        <img src="{{ asset('images/logo-nav.png') }}"
                             alt="Logo Pondok Pesantren Al-Falah Putak"
                             width="48"
                             height="48"
                             class="w-10 h-10 sm:w-11 sm:h-11 md:w-12 md:h-12 object-contain drop-shadow-xs group-hover:scale-105 transition-transform duration-200">
                    </picture>
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
                    <a href="{{ url('/konsultasi') }}"
                       class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition {{ request()->is('konsultasi*') ? 'text-emerald-900 bg-emerald-50 font-bold shadow-xs' : 'text-slate-600 hover:text-emerald-800 hover:bg-slate-100/70' }}">
                        Layanan Konsultasi
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
                            class="w-11 h-11 flex items-center justify-center rounded-xl text-slate-700 hover:text-emerald-800 hover:bg-emerald-50 active:scale-95 transition border border-slate-200/90 focus:outline-none"
                            aria-label="Buka Menu Navigasi"
                            :aria-expanded="mobileOpen">
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                 class="md:hidden py-3 border-t border-slate-100 space-y-1.5">
                <a href="{{ url('/') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm sm:text-base font-bold min-h-[46px] transition active:scale-[0.99] {{ request()->is('/') ? 'text-emerald-950 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100/70' }}">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Beranda Utama</span>
                </a>
                <a href="{{ url('/pengumuman') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm sm:text-base font-bold min-h-[46px] transition active:scale-[0.99] {{ request()->is('pengumuman*') ? 'text-emerald-950 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100/70' }}">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    <span>Pengumuman & Warta</span>
                </a>
                <a href="{{ url('/konsultasi') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm sm:text-base font-bold min-h-[46px] transition active:scale-[0.99] {{ request()->is('konsultasi*') ? 'text-emerald-950 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100/70' }}">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span>Layanan Konsultasi</span>
                </a>
                <a href="{{ url('/verifikasi') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm sm:text-base font-bold min-h-[46px] transition active:scale-[0.99] {{ request()->is('verifikasi*') ? 'text-emerald-950 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100/70' }}">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
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

    <!-- Footer Publik: Lengkap di Beranda, Minimalis di Halaman Lainnya -->
    @if (request()->is('/'))
    <footer class="bg-emerald-950 text-slate-300 pt-12 sm:pt-16 pb-10 sm:pb-12 border-t border-emerald-900/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-10 pb-10 sm:pb-12 border-b border-emerald-900/80">
                <!-- Kolom 1: Profil & Identitas Lembaga (MEDIA RESMI AL-FALAH PUTAK) -->
                <div class="md:col-span-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <picture class="shrink-0">
                            <source srcset="{{ asset('images/logo-nav.webp') }}" type="image/webp">
                            <img src="{{ asset('images/logo-nav.png') }}"
                                 alt="Logo Pondok Pesantren Al-Falah Putak"
                                 loading="lazy"
                                 decoding="async"
                                 width="56"
                                 height="56"
                                 class="w-12 h-12 sm:w-14 sm:h-14 object-contain shrink-0 drop-shadow-md">
                        </picture>
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
                            <span>{{ $pengaturan['kontak'] ?? '0821-1870-2717' }}</span>
                        </p>
                    </div>
                </div>

                <!-- Kolom 2: Tautan Navigasi Cepat & Akses Login Petugas di Bawah -->
                <div class="md:col-span-3">
                    <h4 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-emerald-300 mb-4">Navigasi Utama</h4>
                    <ul class="space-y-2.5 text-sm sm:text-base">
                        <li><a href="{{ url('/') }}" class="hover:text-emerald-400 transition py-1 inline-block">Beranda Utama</a></li>
                        <li><a href="{{ url('/pengumuman') }}" class="hover:text-emerald-400 transition py-1 inline-block">Pengumuman &amp; Warta</a></li>
                        <li><a href="{{ url('/konsultasi') }}" class="hover:text-emerald-400 transition py-1 inline-block">Layanan Konsultasi</a></li>
                        <li><a href="{{ url('/verifikasi') }}" class="hover:text-emerald-400 transition py-1 inline-block">Verifikasi Dokumen</a></li>
                        <!-- Akses Login Petugas di Letakkan di Bawah Saja -->
                        <li class="pt-3 border-t border-emerald-900/80">
                            @auth
                                <a href="{{ route('dashboard') }}" class="text-emerald-400 hover:text-white transition inline-flex items-center gap-1.5 font-bold text-xs sm:text-sm min-h-[40px]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                    <span>Buka Panel Admin &rarr;</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-emerald-200 hover:text-white transition inline-flex items-center gap-1.5 font-semibold text-xs sm:text-sm min-h-[40px]">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                    <span>Portal Sekretariat (Login) &rarr;</span>
                                </a>
                            @endauth
                        </li>
                    </ul>
                </div>

                <!-- Kolom 3: Layanan Sekretariat & Jam Operasional -->
                <div class="md:col-span-3">
                    <h4 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-emerald-300 mb-4">Layanan &amp; Waktu</h4>
                    <ul class="space-y-2.5 text-xs sm:text-sm text-slate-300/90 leading-relaxed">
                        <li>Administrasi &amp; Surat-Menyurat</li>
                        <li>Legalisir &amp; Pengesahan Dokumen</li>
                        <li class="pt-2 border-t border-emerald-900/70 text-emerald-300 font-semibold">
                            Informasi Layanan:
                        </li>
                        <li class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-300">Senin - Kamis</span>
                            <span class="font-bold text-emerald-400">08.00 - 16.00 WIB</span>
                        </li>
                        <li class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-300">Sabtu - Minggu</span>
                            <span class="font-bold text-emerald-400">07.30 - 15.00 WIB</span>
                        </li>
                        <li class="text-[11px] sm:text-xs text-amber-300/80 pt-0.5">
                            * Jumat: Libur / Pelayanan Terjadwal
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Hak Cipta Resmi Sesuai Arahan Pengguna -->
            <div class="pt-6 sm:pt-8 text-center text-xs sm:text-sm text-slate-300 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p>@2026 JAKASTRA OFFICIAL. All Reght Reserved</p>
                <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-300">
                    <span class="text-emerald-300 font-medium">Desa Putak, Gelumbang, Muara Enim</span>
                </div>
            </div>
        </div>
    </footer>
    @else
    <!-- Footer Minimalis untuk Halaman Selain Beranda -->
    <footer class="bg-emerald-950 text-slate-400 py-6 border-t border-emerald-900/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs sm:text-sm">
            <p>@2026 JAKASTRA OFFICIAL. All Reght Reserved</p>
        </div>
    </footer>
    @endif

    @stack('scripts')
</body>
</html>
