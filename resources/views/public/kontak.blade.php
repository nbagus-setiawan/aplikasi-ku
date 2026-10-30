@extends('layouts.public')

@section('title', 'Kontak & Lokasi di Gelumbang, Muara Enim')
@section('breadcrumb', 'Kontak & Lokasi')
@section('meta_description', 'Alamat, nomor WhatsApp sekretariat, email, jam pelayanan, dan peta lokasi Pondok Pesantren Al-Falah Putak di Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan.')

@section('content')
    @include('public.partials.page-header', [
        'breadcrumb' => 'Kontak & Lokasi',
        'badge' => 'Kontak & Lokasi',
        'judul' => 'Kontak dan Lokasi Pondok Pesantren Al-Falah Putak',
        'subjudul' => 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan. Hubungi sekretariat untuk informasi santri, pendaftaran, dan layanan surat.',
    ])

    {{-- Data kontak diambil dari blok beranda agar bisa diubah sekretaris lewat editor --}}
    @include('public.blocks.kontak', ['block' => $blokKontak, 'pengaturan' => $pengaturan])

    <section class="py-10 sm:py-14 bg-slate-50 border-t border-slate-200/80">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
            <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight text-center">
                Peta Lokasi Pondok Pesantren
            </h2>
            <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm bg-white aspect-[4/3] sm:aspect-[16/9]">
                <iframe
                    src="https://maps.google.com/maps?q=-3.193733,104.485842&z=15&output=embed"
                    title="Peta lokasi Pondok Pesantren Al-Falah Putak, Gelumbang, Muara Enim"
                    class="w-full h-full border-0"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    allowfullscreen></iframe>
            </div>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="https://maps.app.goo.gl/sVdzFiBgD7VdYKteA" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 min-h-[48px] px-6 py-3 rounded-xl font-bold text-sm text-white bg-emerald-800 hover:bg-emerald-700 transition">
                    Buka di Google Maps
                </a>
                @if($nomorWaClean)
                    <a href="https://wa.me/{{ $nomorWaClean }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 min-h-[48px] px-6 py-3 rounded-xl font-bold text-sm text-emerald-950 bg-amber-400 hover:bg-amber-300 transition">
                        Chat WhatsApp Sekretariat
                    </a>
                @endif
            </div>
        </div>
    </section>

    @include('public.partials.tautan-lain')
@endsection

