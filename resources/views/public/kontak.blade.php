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

    <section class="py-10 sm:py-14 bg-slate-50/70 border-t border-slate-200/80">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="text-center space-y-1.5 max-w-2xl mx-auto">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold text-emerald-800 bg-emerald-100/70 uppercase tracking-wider">
                    Titik Presisi
                </span>
                <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Peta Lokasi Pondok Pesantren
                </h2>
                <p class="text-xs sm:text-sm text-slate-600">
                    Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan
                </p>
            </div>

            <div class="rounded-3xl overflow-hidden border border-slate-200/90 shadow-xs bg-white aspect-[4/3] sm:aspect-[16/9] lg:aspect-[21/9]">
                <iframe
                    src="https://maps.google.com/maps?q=-3.193733,104.485842&z=15&output=embed"
                    title="Peta lokasi Pondok Pesantren Al-Falah Putak, Gelumbang, Muara Enim"
                    class="w-full h-full border-0"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    allowfullscreen></iframe>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <a href="https://maps.app.goo.gl/sVdzFiBgD7VdYKteA" target="_blank" rel="noopener"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 min-h-[46px] px-6 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-emerald-800 hover:bg-emerald-700 shadow-2xs transition active:scale-95">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                    <span>Buka Rute di Google Maps</span>
                </a>
                @if($nomorWaClean)
                    <a href="https://wa.me/{{ $nomorWaClean }}" target="_blank" rel="noopener"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 min-h-[46px] px-6 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-emerald-950 bg-amber-400 hover:bg-amber-300 shadow-2xs transition active:scale-95">
                        <svg class="w-4 h-4 text-emerald-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span>Chat WhatsApp Sekretariat</span>
                    </a>
                @endif
            </div>
        </div>
    </section>

    @include('public.partials.tautan-lain')
@endsection

