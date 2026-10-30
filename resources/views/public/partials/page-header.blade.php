{{-- Header halaman informasi publik: satu H1 per halaman + breadcrumb terlihat (PRD 6.12) --}}
<section class="bg-emerald-950 text-white py-10 sm:py-16 border-b border-emerald-900/60">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <nav aria-label="Breadcrumb" class="text-xs sm:text-sm text-emerald-200/80">
            <ol class="inline-flex items-center gap-1.5">
                <li><a href="{{ url('/') }}" class="hover:text-white transition">Beranda</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page" class="text-emerald-100 font-semibold">{{ $breadcrumb }}</li>
            </ol>
        </nav>
        <span class="inline-block px-3.5 py-1 rounded-full bg-emerald-900/80 border border-emerald-700/60 text-emerald-300 text-xs font-semibold uppercase tracking-wider">
            {{ $badge }}
        </span>
        <h1 class="text-xl sm:text-3xl md:text-4xl font-extrabold tracking-tight">
            {{ $judul }}
        </h1>
        <p class="max-w-2xl mx-auto text-emerald-100/90 text-xs sm:text-sm md:text-base font-normal leading-relaxed">
            {{ $subjudul }}
        </p>
    </div>
</section>

