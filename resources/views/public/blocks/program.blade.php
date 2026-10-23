@php
    $data = $block['data'] ?? [];
    $judul = $data['judul'] ?? 'Program Pendidikan Santri';
    $subjudul = $data['subjudul'] ?? 'Jalur pendidikan dan pendalaman ilmu agama di Pondok Pesantren Al-Falah Putak';
    $subjudul = str_replace('Al-Falah Putra', 'Al-Falah Putak', $subjudul);

    $programs = [
        [
            'judul' => $data['item_1_judul'] ?? 'Kajian Kitab Salafiyah',
            'desk' => $data['item_1_desk'] ?? 'Pengajian sorogan dan bandongan kitab-kitab salaf lintas disiplin ilmu fikih, nahwu, sharaf, hadits, dan tasawuf berakhlakul karimah.',
            'tag' => 'Khazanah Kitab Kuning',
        ],
        [
            'judul' => $data['item_2_judul'] ?? 'Tahfidzul Qur\'an',
            'desk' => $data['item_2_desk'] ?? 'Bimbingan intensif hafalan Al-Qur\'an dengan metode mutqin, pembenahan makharijul huruf, dan penguasaan kaidah tajwid.',
            'tag' => 'Al-Qur\'an Bersanad',
        ],
        [
            'judul' => $data['item_3_judul'] ?? 'Madrasah Diniyah',
            'desk' => $data['item_3_desk'] ?? 'Kurikulum diniyah berjenjang (Ula, Wustha, dan Ulya) serta sinergi dengan pendidikan formal untuk membekali masa depan santri.',
            'tag' => 'Kurikulum Berjenjang',
        ],
    ];
@endphp

<section class="py-10 sm:py-14 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10 space-y-2">
            <span class="inline-block text-xs font-semibold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-3.5 py-1 rounded-full border border-emerald-100">
                Pendidikan & Kurikulum
            </span>
            <h2 class="text-xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                {{ $judul }}
            </h2>
            <p class="text-slate-600 text-xs sm:text-sm md:text-base leading-relaxed">
                {{ $subjudul }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
            @foreach($programs as $prog)
                <div class="bg-slate-50/80 rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs hover:border-emerald-300 hover:bg-white hover:shadow-sm transition duration-200 flex flex-col justify-between h-full">
                    <div class="space-y-3">
                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold uppercase tracking-wider text-emerald-800 bg-emerald-100/70">
                            {{ $prog['tag'] }}
                        </span>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">
                            {{ $prog['judul'] }}
                        </h3>
                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                            {{ $prog['desk'] }}
                        </p>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-200/60 flex items-center text-xs font-semibold text-emerald-800">
                        <span>Pondok Pesantren Al-Falah Putak</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
