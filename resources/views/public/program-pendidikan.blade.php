@extends('layouts.public')

@section('title', "Program Pendidikan: Kitab Kuning, Tahfidz & Madrasah")
@section('breadcrumb', 'Program Pendidikan')
@section('meta_description', "Program pendidikan Pondok Pesantren Al-Falah Putak Gelumbang, Muara Enim: kajian kitab kuning salaf, tahfidzul Qur'an, Madrasah Diniyah Ula, Wustho, Ulya, Madrasah Qira'ati, pendidikan formal MI, MTs, MA, dan BLKK Teknik Informatika.")

@section('content')
    @include('public.partials.page-header', [
        'breadcrumb' => 'Program Pendidikan',
        'badge' => 'Program & Kurikulum',
        'judul' => 'Program Pendidikan Pondok Pesantren Al-Falah Putak',
        'subjudul' => "Perpaduan pesantren salafiyah dan pendidikan formal untuk santri putra dan putri: kitab kuning, tahfidzul Qur'an, madrasah diniyah, dan keterampilan.",
    ])

    {{-- Ringkasan program diambil dari blok beranda agar bisa diubah sekretaris lewat editor --}}
    @include('public.blocks.program', ['block' => $blokProgram, 'pengaturan' => $pengaturan])

    @php
        $rincianProgram = [
            [
                'judul' => 'Kajian Kitab Kuning Salafiyah',
                'isi' => "Pengkajian kitab-kitab salaf mu'tabarah dalam ilmu nahwu, shorof, fiqih, tauhid, tafsir, hadits, akhlak, dan tasawuf. Metode yang dipakai: sorogan, bandongan, halaqah, hiwar, dan bahtsul masail di bawah bimbingan masyayikh dan asatidz.",
            ],
            [
                'judul' => "Pondok Tahfidzul Qur'an",
                'isi' => "Bimbingan menghafal Al-Qur'an secara tartil dan mutqin, disertai perbaikan makharijul huruf dan penguasaan ilmu tajwid untuk santri putra dan santri putri.",
            ],
            [
                'judul' => 'Madrasah Diniyah Ula, Wustho, dan Ulya',
                'isi' => 'Pendidikan diniyah berjenjang dengan kurikulum pesantren, mulai dari tingkat dasar (Ula), menengah (Wustho), hingga atas (Ulya).',
            ],
            [
                'judul' => "Madrasah Qira'ati",
                'isi' => "Pembelajaran membaca Al-Qur'an dengan metode Qira'ati yang bertahap, cocok untuk santri yang baru memulai belajar Al-Qur'an.",
            ],
            [
                'judul' => 'Pendidikan Formal MI, MTs, dan MA',
                'isi' => 'Santri dapat menempuh pendidikan formal jenjang Madrasah Ibtidaiyah, Madrasah Tsanawiyah, dan Madrasah Aliyah di lingkungan pesantren, sejalan dengan pendidikan diniyah.',
            ],
            [
                'judul' => 'BLKK Teknik Informatika',
                'isi' => 'Balai Latihan Kerja Komunitas (BLKK) bidang Teknik Informatika untuk membekali santri dengan keterampilan komputer dan teknologi.',
            ],
        ];
    @endphp

    <section class="py-10 sm:py-14 bg-white border-b border-slate-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight text-center mb-6 sm:mb-8">
                Rincian Program Pendidikan Santri
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                @foreach($rincianProgram as $program)
                    <article class="p-5 rounded-2xl border border-slate-200/80 bg-slate-50/60">
                        <h3 class="text-base sm:text-lg font-bold text-emerald-900">{{ $program['judul'] }}</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mt-2">{{ $program['isi'] }}</p>
                    </article>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ url('/pendaftaran-santri') }}" class="inline-flex items-center justify-center gap-2 min-h-[48px] px-7 py-3 rounded-xl font-bold text-sm sm:text-base text-emerald-950 bg-amber-400 hover:bg-amber-300 transition">
                    Lihat Informasi Pendaftaran Santri &rarr;
                </a>
            </div>
        </div>
    </section>

    @include('public.partials.tautan-lain')
@endsection

