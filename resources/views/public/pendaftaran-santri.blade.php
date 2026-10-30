@extends('layouts.public')

@section('title', 'Pendaftaran Santri Baru Putra & Putri')
@section('breadcrumb', 'Pendaftaran Santri Baru')
@section('meta_description', "Informasi pendaftaran santri baru putra dan putri Pondok Pesantren Al-Falah Putak, Gelumbang, Muara Enim, Sumatera Selatan: alur pendaftaran, pilihan program kitab kuning, tahfidz, madrasah diniyah, dan kontak WhatsApp sekretariat.")

@section('content')
    @include('public.partials.page-header', [
        'breadcrumb' => 'Pendaftaran Santri Baru',
        'badge' => 'Penerimaan Santri Baru',
        'judul' => 'Pendaftaran Santri Baru Pondok Pesantren Al-Falah Putak',
        'subjudul' => 'Informasi penerimaan santri putra dan putri di Gelumbang, Muara Enim. Hubungi sekretariat untuk jadwal, persyaratan, dan rincian pendaftaran terbaru.',
    ])

    @php
        $pesanWa = "Assalamu'alaikum. Saya ingin bertanya tentang pendaftaran santri baru di Pondok Pesantren Al-Falah Putak.";
        $tautanWa = $nomorWaClean ? 'https://wa.me/' . $nomorWaClean . '?text=' . rawurlencode($pesanWa) : url('/konsultasi');
        $dataProgram = $blokProgram['data'] ?? [];
        $pilihanProgram = array_values(array_filter([
            $dataProgram['item_1_judul'] ?? null,
            $dataProgram['item_2_judul'] ?? null,
            $dataProgram['item_3_judul'] ?? null,
        ]));
        $langkah = [
            ['judul' => 'Hubungi Sekretariat', 'isi' => 'Hubungi sekretariat lewat WhatsApp atau datang langsung ke pondok untuk menanyakan jadwal dan kuota pendaftaran.'],
            ['judul' => 'Konsultasi Program', 'isi' => 'Diskusikan program yang sesuai untuk calon santri: kitab kuning, tahfidzul Qur\'an, madrasah diniyah, atau pendidikan formal.'],
            ['judul' => 'Lengkapi Berkas', 'isi' => 'Siapkan dan serahkan berkas pendaftaran sesuai arahan sekretariat.'],
            ['judul' => 'Pantau Pengumuman', 'isi' => 'Ikuti pengumuman resmi di website ini untuk informasi lanjutan, termasuk jadwal masuk pondok.'],
        ];
    @endphp

    <section class="py-10 sm:py-14 bg-white border-b border-slate-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div>
                <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight text-center mb-6 sm:mb-8">
                    Alur Pendaftaran Santri
                </h2>
                <ol class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($langkah as $i => $l)
                        <li class="p-5 rounded-2xl border border-slate-200/80 bg-slate-50/60">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-800 text-white text-sm font-bold">{{ $i + 1 }}</span>
                            <h3 class="text-base font-bold text-emerald-900 mt-3">{{ $l['judul'] }}</h3>
                            <p class="text-sm text-slate-600 leading-relaxed mt-1">{{ $l['isi'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>

            @if(count($pilihanProgram))
                <div>
                    <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight text-center mb-4">
                        Pilihan Program untuk Calon Santri
                    </h2>
                    <ul class="max-w-2xl mx-auto space-y-2 text-sm sm:text-base text-slate-700">
                        @foreach($pilihanProgram as $p)
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-700 font-bold">&check;</span>
                                <span>{{ $p }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <p class="text-center mt-4 text-sm">
                        <a href="{{ url('/program-pendidikan') }}" class="font-semibold text-emerald-800 hover:underline">Baca rincian program pendidikan &rarr;</a>
                    </p>
                </div>
            @endif

            <div>
                <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight text-center mb-6">
                    Pengumuman Pendaftaran Terbaru
                </h2>
                @if($pengumumanPendaftaran->count())
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($pengumumanPendaftaran as $item)
                            <a href="{{ route('publik.pengumuman.show', $item->slug) }}" class="block p-5 rounded-2xl border border-slate-200/80 hover:border-emerald-300 hover:shadow-sm transition bg-white">
                                <span class="block text-xs text-slate-500">{{ optional($item->tgl_tayang ?? $item->created_at)->format('d-m-Y') }}</span>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 mt-1">{{ $item->judul }}</h3>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-sm text-slate-600">
                        Belum ada pengumuman pendaftaran. Silakan pantau halaman
                        <a href="{{ url('/pengumuman') }}" class="font-semibold text-emerald-800 hover:underline">Pengumuman</a>
                        atau hubungi sekretariat.
                    </p>
                @endif
            </div>

            <div class="text-center rounded-2xl bg-emerald-950 text-white p-6 sm:p-8 space-y-3">
                <h2 class="text-lg sm:text-2xl font-extrabold">Tanya Pendaftaran ke Sekretariat</h2>
                <p class="text-sm text-emerald-100/90 max-w-xl mx-auto">
                    Jadwal, persyaratan, dan biaya pendaftaran dijelaskan langsung oleh sekretariat pondok.
                </p>
                <a href="{{ $tautanWa }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 min-h-[48px] px-7 py-3 rounded-xl font-bold text-sm sm:text-base text-emerald-950 bg-amber-400 hover:bg-amber-300 transition">
                    Hubungi via WhatsApp
                </a>
            </div>
        </div>
    </section>

    @include('public.partials.tautan-lain')
@endsection

