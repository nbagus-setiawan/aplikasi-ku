@php
    $data = $block['data'] ?? [];
    $kutipan = $data['kutipan'] ?? 'Menempa insan santri yang tangguh lahir dan batin berlandaskan aqidah Ahlussunnah wal Jama\'ah.';
    $deskripsi = $data['deskripsi'] ?? 'Pondok Pesantren Salafiyah Al-Falah Putak didirikan pada 5 Maret 2005 (10 Muharram 1426 H) oleh K.H. Mursyidi bersama Ny. Hj. Aniswatuzzuhriyah di Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan. Al-Falah Putak memadukan sistem pendidikan Pesantren Salafiyah tradisional (pengkajian kitab kuning, Pondok Tahfidzul Qur\'an, Madrasah Diniyah Ula/Wustho/Ulya, dan Madrasah Qira\'ati) dengan pendidikan formal (MI, MTs, dan MA) serta pembinaan kemandirian dan adab santri.';
    $deskripsi = str_replace('Al-Falah Putra', 'Al-Falah Putak', $deskripsi);
    $visi = $data['visi'] ?? 'Mencetak generasi santri yang berilmu amaliyah, beramal ilmiah, dan berakhlakul karimah yang berpegang teguh pada Al-Qur\'an dan Sunnah.';
    $misi = $data['misi'] ?? '1. Menyelenggarakan pendidikan kitab kuning bersanad muttashil. 2. Membina hafalan Al-Qur\'an dan tahsin bertajwid. 3. Menanamkan adab, kemandirian, dan khidmah kepada umat.';
@endphp

<section class="py-12 sm:py-16 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Bagian Profil -->
        <div class="max-w-3xl mx-auto text-center space-y-3 mb-10 sm:mb-14">
            <span class="inline-block text-xs sm:text-sm font-semibold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-3.5 py-1 rounded-full border border-emerald-100">
                Profil &amp; Identitas
            </span>

            <!-- Judul Profil: Di Ponsel Berbaris Dua Sesuai Arahan, Di Desktop Menyatu Rapi (Font Sama dengan Pengumuman & Warta) -->
            <h2 class="text-xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                <span class="block sm:inline">Sekilas Tentang</span>
                <span class="block sm:inline sm:ml-1.5">Pondok Pesantren Al-Falah Putak</span>
            </h2>

            @if(!empty($kutipan))
                <div class="pt-1">
                    <p class="text-xs sm:text-sm md:text-base text-emerald-800 italic font-medium leading-relaxed max-w-2xl mx-auto px-4 py-2.5 rounded-xl bg-emerald-50/70 border border-emerald-100/90 shadow-2xs">
                        “{{ $kutipan }}”
                    </p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 items-start">
            <!-- Kolom Visual Foto Pengasuh (Abah & Ibuk — Setengah Badan Pas & Rapi) -->
            <div class="lg:col-span-5 space-y-3 lg:-mt-2">
                <div class="rounded-2xl overflow-hidden border border-emerald-200/90 shadow-md bg-white">
                    <!-- Foto Setengah Badan Abah & Ibuk Sesuai Crop Sempurna -->
                    <div class="w-full bg-slate-50 relative overflow-hidden flex items-center justify-center">
                        <img src="{{ asset('images/pengasuh.png') }}"
                             alt="K.H. Mursyidi & Ny. Hj. Aniswatuzzuhriyah — Pengasuh Pondok Pesantren Al-Falah Putak"
                             class="w-full h-auto object-contain block hover:scale-102 transition duration-500">
                    </div>

                    <!-- Plakat Keterangan Pengasuh -->
                    <div class="p-4 sm:p-4.5 bg-emerald-950 text-white text-center border-t border-emerald-800/80">
                        <h4 class="font-bold text-white text-base sm:text-lg tracking-wide">
                            K.H. Mursyidi &amp; Ny. Hj. Aniswatuzzuhriyah
                        </h4>
                        <p class="text-amber-300 text-xs sm:text-sm font-semibold mt-1">
                            Pendiri &amp; Pengasuh Pondok Pesantren Al-Falah Putak
                        </p>
                        <div class="mt-2.5 inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-medium bg-emerald-900/90 text-emerald-200 border border-emerald-700/60">
                            <span>Didirikan 5 Maret 2005 (10 Muharram 1426 H)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Teks Deskripsi, Sanad & Visi Misi -->
            <div class="lg:col-span-7 space-y-5 text-slate-700 leading-relaxed text-sm sm:text-base">
                <p class="text-left leading-relaxed">
                    {{ $deskripsi }}
                </p>

                <!-- Nilai Pondok & Sanad Keilmuan -->
                <div class="p-4 sm:p-5 rounded-xl bg-emerald-50/80 border border-emerald-100/90 text-slate-700 space-y-2">
                    <h3 class="text-sm sm:text-base font-bold text-emerald-950 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span>Sanad Keilmuan &amp; Adab Salaf</span>
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pendidikan santri difokuskan pada pemahaman kitab kuning lintas disiplin (Nahwu, Sharaf, Fiqih, Hadits, Tafsir, Tasawuf) dengan penekanan pada adab keseharian dan keikhlasan berkhidmah.
                    </p>
                </div>

                <!-- Badges Program Unggulan -->
                <div class="pt-1 flex flex-wrap gap-2 text-xs sm:text-sm font-semibold text-slate-700">
                    <span class="px-3.5 py-1.5 bg-slate-100 rounded-xl">Kitab Kuning Salafiyah</span>
                    <span class="px-3.5 py-1.5 bg-slate-100 rounded-xl">Tahfidzul Qur'an</span>
                    <span class="px-3.5 py-1.5 bg-slate-100 rounded-xl">Madrasah Diniyah Ula/Wustho/Ulya</span>
                    <span class="px-3.5 py-1.5 bg-slate-100 rounded-xl">Pendidikan Formal MI, MTs, MA</span>
                </div>
            </div>
        </div>

        <!-- Bagian Visi & Misi Lembaga yang Luas, Lega, & Proporsional -->
        @if(!empty($visi) || !empty($misi))
            <div class="mt-10 sm:mt-14 pt-8 sm:pt-10 border-t border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 items-stretch">
                    @if(!empty($visi))
                        <div class="bg-gradient-to-br from-emerald-50/70 via-white to-slate-50 p-6 sm:p-7 rounded-2xl border border-emerald-100/90 shadow-xs flex flex-col justify-between">
                            <div class="space-y-3.5">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-emerald-100/80 text-emerald-900 font-bold text-xs sm:text-sm tracking-wide">
                                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                    <span>Visi Pondok Pesantren</span>
                                </div>
                                <h3 class="text-base sm:text-lg md:text-xl font-extrabold text-slate-900 leading-snug">
                                    Arah &amp; Cita-Cita Utama Santri
                                </h3>
                                <p class="text-sm sm:text-base text-slate-700 leading-relaxed pt-1">
                                    {{ $visi }}
                                </p>
                            </div>
                            <div class="pt-4 mt-6 border-t border-emerald-200/50 flex items-center gap-2 text-xs font-semibold text-emerald-800">
                                <span>Pondok Pesantren Salafiyah Al-Falah Putak</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($misi))
                        <div class="bg-gradient-to-br from-slate-50 via-white to-emerald-50/50 p-6 sm:p-7 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                            <div class="space-y-3.5">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-emerald-100/80 text-emerald-900 font-bold text-xs sm:text-sm tracking-wide">
                                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                    <span>Misi Pondok Pesantren</span>
                                </div>
                                <h3 class="text-base sm:text-lg md:text-xl font-extrabold text-slate-900 leading-snug">
                                    Langkah Nyata Pendidikan &amp; Khidmah
                                </h3>
                                @php
                                    $misiItems = [];
                                    if (preg_match('/1\.\s/', $misi)) {
                                        $parts = preg_split('/(?=\d+\.\s)/', $misi, -1, PREG_SPLIT_NO_EMPTY);
                                        foreach ($parts as $p) {
                                            $cleaned = trim(preg_replace('/^\d+\.\s*/', '', $p));
                                            if (!empty($cleaned)) {
                                                $misiItems[] = $cleaned;
                                            }
                                        }
                                    } elseif (str_contains($misi, "\n")) {
                                        $lines = explode("\n", $misi);
                                        foreach ($lines as $l) {
                                            $cleaned = trim(preg_replace('/^[-•*\d.]\s*/', '', $l));
                                            if (!empty($cleaned)) {
                                                $misiItems[] = $cleaned;
                                            }
                                        }
                                    }
                                @endphp

                                @if(!empty($misiItems))
                                    <ul class="space-y-3 pt-1">
                                        @foreach($misiItems as $idx => $item)
                                            <li class="flex items-start gap-3 text-sm sm:text-base text-slate-700 leading-relaxed">
                                                <span class="w-6 h-6 rounded-full bg-emerald-700 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                                                    {{ $idx + 1 }}
                                                </span>
                                                <span class="flex-1">{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm sm:text-base text-slate-700 leading-relaxed pt-1">
                                        {{ $misi }}
                                    </p>
                                @endif
                            </div>
                            <div class="pt-4 mt-6 border-t border-slate-200/60 flex items-center gap-2 text-xs font-semibold text-emerald-800">
                                <span>Kurikulum Kitab Salaf &amp; Tahfidzul Qur'an</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>
