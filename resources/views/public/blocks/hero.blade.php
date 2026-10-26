@php
    $data = $block['data'] ?? [];
    $badge = $data['badge'] ?? 'MEDIA RESMI AL-FALAH PUTAK';
    $judul = $data['judul'] ?? 'Pondok Pesantren Al-Falah Putak';
    if ($judul === 'Pondok Pesantren Al-Falah Putra') {
        $judul = 'Pondok Pesantren Al-Falah Putak';
    }
    $subjudul = $data['subjudul'] ?? 'Mencetak generasi santri yang berakhlak mulia, mendalami khazanah kitab salaf, berwawasan luas, dan berpegang teguh pada Al-Qur\'an dan Sunnah.';
    $subjudul = str_replace('Al-Falah Putra', 'Al-Falah Putak', $subjudul);
    $ctaText = $data['cta_text'] ?? 'Lihat Pengumuman';
    $ctaUrl = $data['cta_url'] ?? '/pengumuman';
    $ctaSecText = $data['cta_secondary_text'] ?? 'Verifikasi Dokumen';
    $ctaSecUrl = $data['cta_secondary_url'] ?? '/verifikasi';
@endphp

<!-- Hero Section: Tinggi Lega, Luas ke Bawah di Mobile & Berbagai Layar -->
<section class="relative text-white py-16 sm:py-24 lg:py-32 min-h-[75vh] sm:min-h-[82vh] lg:min-h-[88vh] flex items-center justify-center overflow-hidden border-b border-emerald-900/80">
    <!-- Background Foto Gedung Pesantren (LCP Candidate Element dengan Prioritas Tinggi) -->
    <picture class="absolute inset-0 w-full h-full pointer-events-none select-none">
        <source media="(max-width: 640px)" srcset="{{ asset('images/hero-mobile.webp') }}" type="image/webp">
        <source media="(max-width: 640px)" srcset="{{ asset('images/hero-mobile.jpg') }}" type="image/jpeg">
        <source srcset="{{ asset('images/hero.webp') }}" type="image/webp">
        <img src="{{ asset('images/hero.jpg') }}"
             alt="Gedung Utama Pondok Pesantren Al-Falah Putak"
             fetchpriority="high"
             decoding="async"
             width="1280"
             height="816"
             class="w-full h-full object-cover object-center transform scale-105">
    </picture>

    <!-- Lapisan Overlay Gradasi Transparan & Elegan (Warna Hijau Lembut & Natural) -->
    <div class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-emerald-950/65 to-slate-950/80 backdrop-blur-[0.5px]"></div>
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[radial-gradient(#ffffff_1.5px,transparent_1.5px)] [background-size:24px_24px]"></div>

    <!-- Pendaran Cahaya Halus -->
    <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Konten Hero: Luas, Nyaman, dan Font Proporsional di Ponsel Maupun Layar Lebar -->
    <div class="relative w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6 sm:space-y-8 my-auto">
        @if(!empty($badge))
            <div class="inline-flex items-center gap-2 px-4 py-1.5 sm:px-5 sm:py-2 rounded-full bg-emerald-900/90 border border-emerald-500/60 text-emerald-300 text-xs sm:text-sm font-bold tracking-wide shadow-md">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>{{ $badge }}</span>
            </div>
        @endif

        <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-black tracking-tight text-white leading-tight drop-shadow-md">
            {{ $judul }}
        </h1>

        <p class="max-w-3xl mx-auto text-sm sm:text-base md:text-lg lg:text-xl text-emerald-100/95 font-normal leading-relaxed drop-shadow-xs">
            {{ $subjudul }}
        </p>

        <!-- Tombol Aksi (CTA) Sejajar & Nyaman Ditekan di Ponsel -->
        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3.5 sm:gap-4 w-full sm:w-auto">
            @if(!empty($ctaText))
                <a href="{{ url($ctaUrl) }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 min-h-[48px] px-7 py-3.5 sm:px-8 sm:py-4 rounded-xl font-bold text-sm sm:text-base text-emerald-950 bg-amber-400 hover:bg-amber-300 shadow-md shadow-amber-400/20 transition active:scale-95 text-center">
                    <span>{{ $ctaText }}</span>
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            @endif

            @if(!empty($ctaSecText))
                <a href="{{ url($ctaSecUrl) }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 min-h-[48px] px-7 py-3.5 sm:px-8 sm:py-4 rounded-xl font-bold text-sm sm:text-base text-white bg-emerald-900/90 hover:bg-emerald-800/90 border border-emerald-600/70 shadow-md transition active:scale-95 text-center">
                    <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>{{ $ctaSecText }}</span>
                </a>
            @endif
        </div>

        <!-- Nilai-Nilai Pondok (Sejajar di Bawah Tombol) -->
        <div class="pt-6 sm:pt-8 flex flex-wrap items-center justify-center gap-4 sm:gap-8 text-xs sm:text-sm md:text-base text-emerald-200/90 border-t border-emerald-900/80">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span>Khazanah Kitab Salaf</span>
            </span>
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span>Tahfidzul Qur'an &amp; Adab</span>
            </span>
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span>Layanan Persuratan Sah</span>
            </span>
        </div>
    </div>
</section>
