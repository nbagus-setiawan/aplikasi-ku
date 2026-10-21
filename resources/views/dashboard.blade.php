<x-app-layout>
    @section('title', 'Dashboard Sekretariat')

    <div class="space-y-6">
        <!-- 1. Banner Sambutan Utama dengan Identitas Pesantren & baner.webp Responsif -->
        <div class="bg-emerald-950 rounded-2xl p-6 sm:p-8 text-white shadow-sm border border-emerald-900 relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <!-- Teks Sambutan Kiri -->
                <div class="max-w-2xl space-y-3 text-center md:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-900/90 border border-emerald-700/60 text-emerald-300 text-xs font-semibold rounded-full">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Sistem Administrasi Pesantren (SAP)</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                        Ahlan wa Sahlan, {{ Auth::user()->name ?? 'Sekretaris' }}
                    </h1>
                    <p class="text-xs sm:text-sm text-emerald-100/90 leading-relaxed font-normal">
                        Pondok Pesantren Al-Falah Putra di bawah naungan Pondok Pesantren Al-Falah Putak, Desa Putak, Kec. Gelumbang, Kab. Muara Enim. Kelola administrasi persuratan resmi, agenda kegiatan santri, dan arsip dokumen secara tertib dan akurat.
                    </p>
                </div>

                <!-- Gambar baner.webp di Sisi Kanan (Responsif HP & Desktop) -->
                <div class="shrink-0 flex items-center justify-center w-full md:w-auto">
                    <img src="{{ asset('images/baner.webp') }}"
                         alt="Banner Al-Falah Putra"
                         class="h-32 sm:h-40 md:h-44 w-auto object-contain select-none md:mr-10 lg:mr-14" />
                </div>
            </div>
        </div>

        <!-- 2. Banner Peringatan Agenda H-1 (Jika Ada Kegiatan Besok) -->
        @if ($agendasBesok->count() > 0)
            <div class="bg-amber-50/90 border border-amber-300/80 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2 text-amber-950 font-bold text-xs sm:text-sm">
                        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Peringatan Agenda H-1: Terdapat {{ $agendasBesok->count() }} Kegiatan Besok Hari</span>
                    </div>
                    <a href="{{ route('admin.agenda.index', ['view' => 'kalender']) }}" class="text-xs text-amber-900 font-extrabold hover:underline">
                        Buka Kalender &rarr;
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($agendasBesok as $ab)
                        <div class="p-3.5 bg-white rounded-xl border border-amber-200/90 flex items-center justify-between text-xs gap-3">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-slate-900 truncate">{{ $ab->judul }}</h4>
                                <p class="text-slate-500 text-[11px] mt-0.5">Waktu: {{ $ab->mulai->format('H:i') }} WIB &bull; Lokasi: {{ $ab->lokasi }}</p>
                                <p class="text-[11px] text-amber-800 font-semibold mt-0.5">PJ: {{ $ab->pj ? $ab->pj->nama : '-' }}</p>
                            </div>
                            <a href="{{ route('admin.agenda.show', $ab) }}" class="px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-950 font-bold rounded-lg text-xs transition shrink-0">
                                Detail
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 3. Empat Kartu Statistik Utama (Seimbang & Proporsional) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- Santri Aktif -->
            <a href="{{ route('admin.santri.index') }}" class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-emerald-400 hover:shadow-sm transition flex items-center justify-between gap-4 group">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Santri Aktif</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1 tabular-nums">{{ number_format($totalSantriAktif, 0, ',', '.') }}</h3>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-800 mt-1.5 group-hover:translate-x-0.5 transition-transform">
                        Kelola Santri &rarr;
                    </span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </a>

            <!-- Surat Masuk Bulan Ini -->
            <a href="{{ route('admin.surat-masuk.index') }}" class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-blue-400 hover:shadow-sm transition flex items-center justify-between gap-4 group">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Surat Masuk (Bulan Ini)</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1 tabular-nums">{{ $suratMasukBulanIni }}</h3>
                    @if ($suratMasukBelumDisposisi > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-100 text-amber-800 mt-1.5">
                            {{ $suratMasukBelumDisposisi }} perlu disposisi
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-800 mt-1.5 group-hover:translate-x-0.5 transition-transform">
                            Buku Agenda &rarr;
                        </span>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shrink-0 border border-blue-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
            </a>

            <!-- Surat Keluar Bulan Ini -->
            <a href="{{ route('admin.surat-keluar.index') }}" class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-amber-400 hover:shadow-sm transition flex items-center justify-between gap-4 group">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Surat Keluar (Bulan Ini)</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1 tabular-nums">{{ $suratKeluarBulanIni }}</h3>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-800 mt-1.5 group-hover:translate-x-0.5 transition-transform">
                        Arsip Surat &rarr;
                    </span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </div>
            </a>

            <!-- Agenda Mendatang -->
            <a href="{{ route('admin.agenda.index') }}" class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-purple-400 hover:shadow-sm transition flex items-center justify-between gap-4 group">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Agenda Mendatang</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1 tabular-nums">{{ $agendaMendatang }}</h3>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-purple-800 mt-1.5 group-hover:translate-x-0.5 transition-transform">
                        Jadwal Kegiatan &rarr;
                    </span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center shrink-0 border border-purple-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </a>
        </div>

        <!-- 4. Baris Pintasan Cepat Sekretariat (Horizontal Rapi & Touch-Friendly) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-600">Pintasan Cepat Pelayanan</h2>
                <span class="text-xs text-slate-400">Total Arsip Dokumen: <strong class="text-slate-700 font-bold">{{ $totalArsip }} Berkas</strong></span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="{{ route('admin.surat-keluar.create') }}"
                   class="flex items-center gap-3 p-3.5 rounded-xl bg-emerald-50/80 hover:bg-emerald-100 border border-emerald-200/80 text-emerald-950 font-bold text-xs transition active:scale-98">
                    <span class="w-8 h-8 rounded-lg bg-emerald-700 text-white flex items-center justify-center font-extrabold text-sm shrink-0">+</span>
                    <div>
                        <span class="block">Upload Surat Keluar</span>
                        <span class="text-[10px] font-normal text-emerald-700">Nomor Otomatis + QR Verifikasi</span>
                    </div>
                </a>

                <a href="{{ route('admin.surat-masuk.create') }}"
                   class="flex items-center gap-3 p-3.5 rounded-xl bg-blue-50/80 hover:bg-blue-100 border border-blue-200/80 text-blue-950 font-bold text-xs transition active:scale-98">
                    <span class="w-8 h-8 rounded-lg bg-blue-700 text-white flex items-center justify-center font-extrabold text-sm shrink-0">+</span>
                    <div>
                        <span class="block">Upload Surat Masuk</span>
                        <span class="text-[10px] font-normal text-blue-700">Deteksi & Agenda Otomatis</span>
                    </div>
                </a>

                <a href="{{ route('admin.agenda.create') }}"
                   class="flex items-center gap-3 p-3.5 rounded-xl bg-purple-50/80 hover:bg-purple-100 border border-purple-200/80 text-purple-950 font-bold text-xs transition active:scale-98">
                    <span class="w-8 h-8 rounded-lg bg-purple-700 text-white flex items-center justify-center font-extrabold text-sm shrink-0">+</span>
                    <div>
                        <span class="block">Tambah Jadwal Agenda</span>
                        <span class="text-[10px] font-normal text-purple-700">Kalender Kegiatan Santri</span>
                    </div>
                </a>

                <a href="{{ route('admin.arsip.create') }}"
                   class="flex items-center gap-3 p-3.5 rounded-xl bg-amber-50/80 hover:bg-amber-100 border border-amber-200/80 text-amber-950 font-bold text-xs transition active:scale-98">
                    <span class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center font-extrabold text-sm shrink-0">+</span>
                    <div>
                        <span class="block">Unggah Arsip Dokumen</span>
                        <span class="text-[10px] font-normal text-amber-800">Penyimpanan Berkas Digital</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- 5. Tiga Kolom Aktivitas Terbaru (Tinggi Sejajar, Rapi, & Bersih) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
            <!-- Kolom 1: 5 Surat Masuk Terbaru -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col overflow-hidden">
                <div class="px-5 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Surat Masuk Terbaru</h2>
                        <p class="text-[11px] text-slate-500">5 catatan surat masuk terakhir</p>
                    </div>
                    <a href="{{ route('admin.surat-masuk.index') }}" class="text-xs text-emerald-800 font-bold hover:underline">
                        Semua &rarr;
                    </a>
                </div>
                <div class="divide-y divide-slate-100 flex-1">
                    @forelse ($suratMasukTerbaru as $sm)
                        <a href="{{ route('admin.surat-masuk.show', $sm) }}" class="p-4 hover:bg-slate-50 transition flex items-start justify-between text-xs gap-3 block">
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-slate-900 truncate">{{ $sm->perihal }}</p>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">Dari: {{ $sm->pengirim }}</p>
                                <div class="flex items-center gap-1.5 mt-1.5">
                                    <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-900 border border-emerald-200 font-mono font-bold text-[10px]">
                                        #{{ $sm->no_agenda }}
                                    </span>
                                    <span class="text-[10px] font-mono text-slate-400 truncate">{{ $sm->no_surat }}</span>
                                </div>
                            </div>
                            <div class="shrink-0">
                                @if ($sm->status_disposisi === 'baru')
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 font-bold rounded-full text-[10px]">
                                        Baru
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold rounded-full text-[10px]">
                                        {{ ucfirst($sm->status_disposisi) }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            Belum ada catatan surat masuk.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Kolom 2: 5 Surat Keluar Terbaru -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col overflow-hidden">
                <div class="px-5 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Surat Keluar Terbaru</h2>
                        <p class="text-[11px] text-slate-500">5 surat keluar terakhir</p>
                    </div>
                    <a href="{{ route('admin.surat-keluar.index') }}" class="text-xs text-emerald-800 font-bold hover:underline">
                        Semua &rarr;
                    </a>
                </div>
                <div class="divide-y divide-slate-100 flex-1">
                    @forelse ($suratKeluarTerbaru as $sk)
                        <a href="{{ route('admin.surat-keluar.show', $sk) }}" class="p-4 hover:bg-slate-50 transition flex items-start justify-between text-xs gap-3 block">
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-slate-900 truncate">{{ $sk->perihal }}</p>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">Kepada: {{ $sk->tujuan }}</p>
                                <span class="inline-block mt-1.5 px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-mono font-semibold text-[10px]">
                                    {{ $sk->no_surat }}
                                </span>
                            </div>
                            <div class="shrink-0">
                                @if ($sk->status === 'draft')
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 font-bold rounded-full text-[10px]">Draft</span>
                                @else
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold rounded-full text-[10px]">{{ ucfirst($sk->status) }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            Belum ada catatan surat keluar.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Kolom 3: 5 Agenda Terdekat -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col overflow-hidden">
                <div class="px-5 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Agenda Terdekat</h2>
                        <p class="text-[11px] text-slate-500">5 jadwal kegiatan mendatang</p>
                    </div>
                    <a href="{{ route('admin.agenda.index') }}" class="text-xs text-emerald-800 font-bold hover:underline">
                        Semua &rarr;
                    </a>
                </div>
                <div class="divide-y divide-slate-100 flex-1">
                    @forelse ($agendasTerdekat as $ag)
                        <a href="{{ route('admin.agenda.show', $ag) }}" class="p-4 hover:bg-slate-50 transition flex items-start justify-between text-xs gap-3 block">
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-slate-900 truncate">{{ $ag->judul }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $ag->mulai->format('d-m-Y H:i') }} WIB</p>
                                <p class="text-[10px] text-slate-400 truncate mt-0.5">Lokasi: {{ $ag->lokasi }}</p>
                            </div>
                            <div class="shrink-0">
                                <span class="px-2 py-0.5 bg-purple-50 text-purple-800 border border-purple-200 font-bold rounded-full text-[10px]">
                                    {{ ucfirst($ag->status) }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            Belum ada agenda mendatang.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
