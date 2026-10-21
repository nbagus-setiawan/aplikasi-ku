@php
    $data = $block['data'] ?? [];
    $judul = $data['judul'] ?? 'Sekilas Tentang Pondok';
    $kutipan = $data['kutipan'] ?? 'Menempa insan santri yang tangguh lahir dan batin berlandaskan aqidah Ahlussunnah wal Jama\'ah.';
    $deskripsi = $data['deskripsi'] ?? 'Pondok Pesantren Al-Falah Putra adalah unit pendidikan santri putra di bawah naungan Pondok Pesantren Salafiyah Al-Falah Putak, didirikan pada 5 Maret 2005 (10 Muharram) oleh K.H. Mursyidi dan Ny. Hj. Aniswatuzzuhriyah di Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim.';
    $visi = $data['visi'] ?? 'Mencetak generasi santri yang berilmu amaliyah, beramal ilmiah, dan berakhlakul karimah yang berpegang teguh pada Al-Qur\'an dan Sunnah.';
    $misi = $data['misi'] ?? '1. Menyelenggarakan pendidikan kitab kuning bersanad muttashil. 2. Membina hafalan Al-Qur\'an dan tahsin bertajwid. 3. Menanamkan adab, kemandirian, dan khidmah kepada umat.';
@endphp

<section class="py-10 sm:py-16 lg:py-20 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Bagian Profil -->
        <div class="max-w-3xl mx-auto text-center space-y-2.5 mb-8 sm:mb-12">
            <span class="inline-block text-xs font-bold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-3.5 py-1.5 rounded-full border border-emerald-100">
                Profil & Identitas
            </span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                {{ $judul }}
            </h2>
            @if(!empty($kutipan))
                <div class="pt-1">
                    <p class="text-xs sm:text-sm md:text-base text-emerald-800 italic font-medium leading-relaxed max-w-2xl mx-auto px-4 py-2 rounded-xl bg-emerald-50/60 border border-emerald-100/80">
                        “{{ $kutipan }}”
                    </p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 lg:gap-10 items-start">
            <!-- Kolom Deskripsi & Sejarah -->
            <div class="lg:col-span-7 space-y-4 sm:space-y-5 text-slate-700 leading-relaxed text-xs sm:text-sm md:text-base">
                <p class="text-left leading-relaxed">
                    {{ $deskripsi }}
                </p>

                <!-- Nilai Pondok & Sanad Keilmuan -->
                <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-slate-700 space-y-1.5">
                    <h3 class="text-xs sm:text-sm font-bold text-emerald-950 flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span>Sanad Keilmuan & Adab Salaf</span>
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pendidikan santri difokuskan pada pemahaman kitab kuning lintas disiplin (Nahwu, Sharaf, Fiqih, Hadits, Tafsir, Tasawuf) dengan penekanan pada adab keseharian dan keikhlasan berkhidmah.
                    </p>
                </div>

                <div class="pt-1 flex flex-wrap gap-2 text-xs font-semibold text-slate-600">
                    <span class="px-3 py-1 bg-slate-100 rounded-lg">Kitab Kuning Salafiyah</span>
                    <span class="px-3 py-1 bg-slate-100 rounded-lg">Tahfidzul Qur'an</span>
                    <span class="px-3 py-1 bg-slate-100 rounded-lg">Madrasah Diniyah Ula/Wustho/Ulya</span>
                    <span class="px-3 py-1 bg-slate-100 rounded-lg">Pendidikan Formal MI, MTs, MA</span>
                </div>
            </div>

            <!-- Kolom Visi & Misi (Rapi & Manusiawi) -->
            <div class="lg:col-span-5 space-y-4">
                @if(!empty($visi))
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-xs space-y-2">
                        <div class="flex items-center gap-2 text-emerald-900 font-bold text-xs sm:text-sm">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span>
                            <h4>Visi Lembaga</h4>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            {{ $visi }}
                        </p>
                    </div>
                @endif

                @if(!empty($misi))
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-xs space-y-2">
                        <div class="flex items-center gap-2 text-emerald-900 font-bold text-xs sm:text-sm">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span>
                            <h4>Misi Lembaga</h4>
                        </div>
                        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            {{ $misi }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
