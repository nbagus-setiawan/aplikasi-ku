@extends('layouts.public')

@section('title', 'Profil, Sejarah, Visi & Misi')
@section('breadcrumb', 'Profil')
@section('meta_description', 'Profil Pondok Pesantren Salafiyah Al-Falah Putak di Desa Putak, Gelumbang, Muara Enim, Sumatera Selatan: sejarah berdiri sejak 2005, visi dan misi, pengasuh K.H. Mursyidi dan Ny. Hj. Aniswatuzzuhriyah, serta identitas lembaga.')

@section('content')
    @include('public.partials.page-header', [
        'breadcrumb' => 'Profil',
        'badge' => 'Profil & Sejarah',
        'judul' => 'Profil Pondok Pesantren Al-Falah Putak',
        'subjudul' => 'Pondok pesantren salafiyah putra dan putri di Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan, berdiri sejak 5 Maret 2005.',
    ])

    {{-- Isi profil diambil dari blok beranda agar bisa diubah sekretaris lewat editor --}}
    @include('public.blocks.profil', ['block' => $blokProfil, 'pengaturan' => $pengaturan])

    <section class="py-10 sm:py-14 bg-white border-b border-slate-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-3">
                <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight">Sejarah Singkat</h2>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    Pondok Pesantren Salafiyah Al-Falah Putak didirikan pada 5 Maret 2005 (10 Muharram 1426 H) oleh
                    K.H. Mursyidi bersama Ny. Hj. Aniswatuzzuhriyah di Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim.
                    Sejak berdiri, pondok ini menjadi tempat santri putra dan putri di Sumatera Selatan untuk mendalami ilmu agama.
                </p>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    Pondok memadukan tradisi pesantren salaf (pengajian kitab kuning, tahfidzul Qur'an, dan madrasah diniyah)
                    dengan pendidikan formal, berlandaskan akidah Ahlussunnah wal Jama'ah dan fiqih madzhab Syafi'i.
                </p>
            </div>
            <div class="space-y-3">
                <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight">Identitas Lembaga</h2>
                <dl class="text-sm sm:text-base divide-y divide-slate-100 border border-slate-200/80 rounded-2xl overflow-hidden">
                    @foreach([
                        'Nama Lembaga' => 'Pondok Pesantren Salafiyah Al-Falah Putak',
                        'Tanggal Berdiri' => '5 Maret 2005 (10 Muharram 1426 H)',
                        'Pengasuh' => 'K.H. Mursyidi dan Ny. Hj. Aniswatuzzuhriyah',
                        'Jenis Santri' => 'Putra dan Putri (berasrama)',
                        'Akidah & Madzhab' => "Ahlussunnah wal Jama'ah, madzhab Syafi'i",
                        'Alamat' => $pengaturan['alamat'] ?? 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171',
                    ] as $label => $nilai)
                        <div class="grid grid-cols-3 gap-3 px-4 py-3 bg-white">
                            <dt class="font-semibold text-slate-700">{{ $label }}</dt>
                            <dd class="col-span-2 text-slate-600">{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

    @include('public.partials.tautan-lain')
@endsection
