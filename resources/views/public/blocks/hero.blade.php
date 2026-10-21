@php
    $data = $block['data'] ?? [];
    $badge = $data['badge'] ?? 'Pondok Pesantren Salafiyah Al-Falah Putak';
    $judul = $data['judul'] ?? 'Pondok Pesantren Al-Falah Putra';
    $subjudul = $data['subjudul'] ?? 'Mencetak generasi santri yang berakhlak mulia, mendalami khazanah kitab salaf, berwawasan luas, dan berpegang teguh pada Al-Qur\'an dan Sunnah.';
    $ctaText = $data['cta_text'] ?? 'Lihat Pengumuman';
    $ctaUrl = $data['cta_url'] ?? '/pengumuman';
    $ctaSecText = $data['cta_secondary_text'] ?? 'Verifikasi Surat';
    $ctaSecUrl = $data['cta_secondary_url'] ?? '/verifikasi';
@endphp

<section class="relative bg-emerald-950 text-white py-14 sm:py-20 lg:py-24 overflow-hidden border-b border-emerald-900/60">
    <!-- Aksen Latar Belakang Hangat & Bersih -->
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[radial-gradient(#ffffff_1.5px,transparent_1.5px)] [background-size:24px_24px]"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">

            <!-- Kolom Teks Utama -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                @if(!empty($badge))
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-900/80 border border-emerald-700/60 text-emerald-300 text-xs font-semibold tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>{{ $badge }}</span>
                    </div>
                @endif

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                    {{ $judul }}
                </h1>

                <p class="max-w-2xl text-sm sm:text-base lg:text-lg text-emerald-100/90 font-normal leading-relaxed mx-auto lg:mx-0">
                    {{ $subjudul }}
                </p>

                <!-- Tombol Navigasi Aksi (Responsif di HP & Desktop) -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 sm:gap-4 w-full">
                    @if(!empty($ctaText))
                        <a href="{{ url($ctaUrl) }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-bold text-sm text-emerald-950 bg-amber-400 hover:bg-amber-300 shadow-sm transition active:scale-98">
                            <span>{{ $ctaText }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @endif

                    @if(!empty($ctaSecText))
                        <a href="{{ url($ctaSecUrl) }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-bold text-sm text-white bg-emerald-900/90 hover:bg-emerald-800/90 border border-emerald-700/70 transition active:scale-98">
                            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            <span>{{ $ctaSecText }}</span>
                        </a>
                    @endif
                </div>

                <!-- Nilai-Nilai Pondok (Sederhana & Bersahaja) -->
                <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-4 sm:gap-6 text-xs text-emerald-300/80 border-t border-emerald-900">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        Khazanah Kitab Salaf
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        Tahfidzul Qur'an & Adab
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        Layanan Persuratan Sah
                    </span>
                </div>
            </div>

            <!-- Kolom Visual Foto Pesantren -->
            <div class="lg:col-span-5">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    <div class="rounded-3xl overflow-hidden shadow-xl border border-emerald-800/80 bg-emerald-900/40">
                        <img src="{{ asset('images/hero.jpg') }}"
                             alt="Pondok Pesantren Al-Falah Putra"
                             class="w-full h-64 sm:h-80 lg:h-96 object-cover object-center">
                        <div class="p-4 sm:p-5 bg-emerald-950 border-t border-emerald-900 flex items-center justify-between text-xs">
                            <div>
                                <p class="font-bold text-white text-sm">Pondok Pesantren Al-Falah Putra</p>
                                <p class="text-emerald-300 text-xs">Desa Putak, Kec. Gelumbang, Kab. Muara Enim</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-900 text-emerald-200 border border-emerald-700/60 shrink-0">
                                Berdiri 2005
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
