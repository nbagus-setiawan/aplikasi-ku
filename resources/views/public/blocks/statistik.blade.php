@php
    $data = $block['data'] ?? [];
    $judul = $data['judul'] ?? 'Pondok Pesantren dalam Angka';
    $subjudul = $data['subjudul'] ?? 'Data riil perkembangan pendidikan dan kependudukan santri';

    // Prioritaskan data riil dari database jika tersedia
    $santri = $statistikOtomatis['santri_aktif'] ?? ($data['santri_aktif'] ?? '0');
    $pengurus = $statistikOtomatis['pengurus_asatidz'] ?? ($data['pengurus_asatidz'] ?? '0');
    $kamar = $statistikOtomatis['asrama_kamar'] ?? ($data['asrama_kamar'] ?? '0');
    $alumni = $statistikOtomatis['angkatan_alumni'] ?? ($data['angkatan_alumni'] ?? '0');
@endphp

<section class="py-12 sm:py-16 bg-slate-100/70 border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                Statistik Pesantren
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                {{ $judul }}
            </h2>
            <p class="text-slate-600 text-xs sm:text-sm">
                {{ $subjudul }}
            </p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Kartu 1: Santri Aktif -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center transition-all hover:border-emerald-300 hover:shadow-sm">
                <span class="block text-2xl sm:text-4xl font-extrabold text-emerald-800 tabular-nums">
                    {{ $santri }}
                </span>
                <span class="block text-[11px] sm:text-xs font-bold text-slate-600 uppercase tracking-wider mt-1.5">
                    Santri Putra Aktif
                </span>
            </div>

            <!-- Kartu 2: Asatidz & Pengurus -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center transition-all hover:border-emerald-300 hover:shadow-sm">
                <span class="block text-2xl sm:text-4xl font-extrabold text-emerald-800 tabular-nums">
                    {{ $pengurus }}
                </span>
                <span class="block text-[11px] sm:text-xs font-bold text-slate-600 uppercase tracking-wider mt-1.5">
                    Asatidz & Pengurus
                </span>
            </div>

            <!-- Kartu 3: Kamar Asrama -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center transition-all hover:border-emerald-300 hover:shadow-sm">
                <span class="block text-2xl sm:text-4xl font-extrabold text-emerald-800 tabular-nums">
                    {{ $kamar }}
                </span>
                <span class="block text-[11px] sm:text-xs font-bold text-slate-600 uppercase tracking-wider mt-1.5">
                    Kamar Asrama Putra
                </span>
            </div>

            <!-- Kartu 4: Angkatan Alumni -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center transition-all hover:border-emerald-300 hover:shadow-sm">
                <span class="block text-2xl sm:text-4xl font-extrabold text-emerald-800 tabular-nums">
                    {{ $alumni }}
                </span>
                <span class="block text-[11px] sm:text-xs font-bold text-slate-600 uppercase tracking-wider mt-1.5">
                    Angkatan Alumni
                </span>
            </div>
        </div>
    </div>
</section>
