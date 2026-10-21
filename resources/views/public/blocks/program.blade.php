@php
    $data = $block['data'] ?? [];
    $judul = $data['judul'] ?? 'Program Pendidikan Santri';
    $subjudul = $data['subjudul'] ?? 'Jalur pendidikan dan pendalaman ilmu agama di Pondok Pesantren Al-Falah Putra';

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

<section class="py-14 sm:py-20 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16 space-y-3">
            <span class="inline-block text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-3.5 py-1.5 rounded-full border border-emerald-100">
                Pendidikan & Kurikulum
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                {{ $judul }}
            </h2>
            <p class="text-slate-600 text-sm sm:text-base">
                {{ $subjudul }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
            @foreach($programs as $prog)
                <div class="bg-slate-50/80 rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs hover:border-emerald-300 hover:bg-white hover:shadow-md transition duration-200 flex flex-col justify-between">
                    <div class="space-y-4">
                        <span class="inline-block px-3 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/70">
                            {{ $prog['tag'] }}
                        </span>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900">
                            {{ $prog['judul'] }}
                        </h3>
                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                            {{ $prog['desk'] }}
                        </p>
                    </div>

                    <div class="pt-6 mt-6 border-t border-slate-200/60 flex items-center text-xs font-semibold text-emerald-800">
                        <span>Pondok Pesantren Al-Falah Putra</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
