@php
    $data = $block['data'] ?? [];
    $judul = $data['judul'] ?? 'Pondok Pesantren dalam Angka';
    $subjudul = $data['subjudul'] ?? 'Data riil perkembangan pendidikan dan kependudukan santri putra Pondok Pesantren Al-Falah Putak';
    $subjudul = str_replace('Al-Falah Putra', 'Al-Falah Putak', $subjudul);

    // Prioritaskan data riil dari database jika tersedia
    $santri = $statistikOtomatis['santri_aktif'] ?? ($data['santri_aktif'] ?? '0');
    $pengurus = $statistikOtomatis['pengurus_asatidz'] ?? ($data['pengurus_asatidz'] ?? '0');
    $kamar = $statistikOtomatis['asrama_kamar'] ?? ($data['asrama_kamar'] ?? '0');
    $alumni = $statistikOtomatis['angkatan_alumni'] ?? ($data['angkatan_alumni'] ?? '0');
@endphp

<section class="py-10 sm:py-14 bg-slate-100/70 border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-8 space-y-2">
            <span class="inline-block text-xs font-semibold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-3.5 py-1 rounded-full border border-emerald-100">
                Statistik Pesantren
            </span>
            <h2 class="text-xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">
                {{ $judul }}
            </h2>
            <p class="text-slate-600 text-xs sm:text-sm md:text-base leading-relaxed">
                {{ $subjudul }}
            </p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
            <!-- Kartu 1: Santri Aktif -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center transition-all hover:border-emerald-300 hover:shadow-xs">
                <span class="block text-2xl sm:text-3xl md:text-4xl font-extrabold text-emerald-800 tabular-nums">
                    {{ $santri }}
                </span>
                <span class="block text-xs sm:text-sm font-bold text-slate-700 mt-1">
                    Santri Aktif
                </span>
            </div>

            <!-- Kartu 2: Asatidz & Pengurus -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center transition-all hover:border-emerald-300 hover:shadow-xs">
                <span class="block text-2xl sm:text-3xl md:text-4xl font-extrabold text-emerald-800 tabular-nums">
                    {{ $pengurus }}
                </span>
                <span class="block text-xs sm:text-sm font-bold text-slate-700 mt-1">
                    Asatidz & Pengurus
                </span>
            </div>

            <!-- Kartu 3: Kamar Asrama -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center transition-all hover:border-emerald-300 hover:shadow-xs">
                <span class="block text-2xl sm:text-3xl md:text-4xl font-extrabold text-emerald-800 tabular-nums">
                    {{ $kamar }}
                </span>
                <span class="block text-xs sm:text-sm font-bold text-slate-700 mt-1">
                    Kamar Asrama
                </span>
            </div>

            <!-- Kartu 4: Angkatan Alumni -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center transition-all hover:border-emerald-300 hover:shadow-xs">
                <span class="block text-2xl sm:text-3xl md:text-4xl font-extrabold text-emerald-800 tabular-nums">
                    {{ $alumni }}
                </span>
                <span class="block text-xs sm:text-sm font-bold text-slate-700 mt-1">
                    Angkatan Alumni
                </span>
            </div>
        </div>
    </div>
</section>
