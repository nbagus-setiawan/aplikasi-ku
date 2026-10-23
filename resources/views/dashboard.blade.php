<x-app-layout>
    @section('title', 'Dashboard Sekretariat')

    <div class="space-y-5 sm:space-y-6">
        <!-- 1. Banner Sambutan Ringkas & Elegan (Sangat Rapi & Responsif) -->
        <div class="bg-gradient-to-br from-emerald-950 via-emerald-900 to-teal-950 rounded-2xl p-4 sm:p-6 lg:p-7 text-white shadow-xs border border-emerald-800/80 relative overflow-hidden">
            <!-- Ornamen latar belakang halus -->
            <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Teks Sambutan Rata Kiri -->
                <div class="space-y-2 text-left flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-900/90 border border-emerald-700/60 text-emerald-300 text-xs font-semibold rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            SAP Al-Falah Putra
                        </span>
                        <span class="text-xs text-emerald-300/80 font-mono hidden sm:inline">
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </span>
                    </div>

                    <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold tracking-tight text-white leading-snug">
                        Ahlan wa Sahlan, {{ Auth::user()->name ?? 'Sekretaris' }}
                    </h1>

                    <p class="text-xs sm:text-sm md:text-base text-emerald-100/90 leading-relaxed max-w-xl font-normal">
                        Sistem pelayanan persuratan resmi, agenda kegiatan santri, dan arsip dokumen Pondok Pesantren Al-Falah Putra.
                    </p>

                    <div class="flex items-center gap-2.5 pt-1 text-xs text-emerald-300/80 font-medium sm:hidden">
                        <span>📅 {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</span>
                        <span>&bull;</span>
                        <span>Putak, Gelumbang</span>
                    </div>
                </div>

                <!-- Gambar baner.webp: Hanya Tampil di Layar Tablet & Desktop (Disembunyikan di Ponsel agar Sangat Rapi) -->
                <div class="hidden md:flex shrink-0 items-center justify-center pl-2">
                    <img src="{{ asset('images/baner.webp') }}"
                         alt="Banner Al-Falah Putra"
                         class="h-24 lg:h-28 w-auto object-contain select-none drop-shadow-md" />
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
                    <a href="{{ route('admin.agenda.index', ['view' => 'kalender']) }}" class="text-xs text-blue-600 hover:text-blue-700 font-extrabold hover:underline">
                        Buka Kalender &rarr;
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($agendasBesok as $ab)
                        <div class="p-3.5 bg-white rounded-xl border border-amber-200/90 flex items-center justify-between text-xs gap-3">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-slate-900 truncate text-sm">{{ $ab->judul }}</h4>
                                <p class="text-slate-500 text-xs mt-0.5">Waktu: {{ $ab->mulai->format('H:i') }} WIB &bull; Lokasi: {{ $ab->lokasi }}</p>
                                <p class="text-xs text-amber-800 font-semibold mt-0.5">PJ: {{ $ab->pj ? $ab->pj->nama : '-' }}</p>
                            </div>
                            <!-- Tombol Detail Berwarna Biru Sesuai Aturan Sistem -->
                            <a href="{{ route('admin.agenda.show', $ab) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold rounded-lg text-xs transition shrink-0 shadow-xs">
                                <span>Detail</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 3. Empat Kartu Statistik Utama (Responsive Grid: 2 kolom di HP, 4 kolom di Laptop/Desktop) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
            <!-- Santri Aktif -->
            <a href="{{ route('admin.santri.index') }}" class="bg-white rounded-2xl p-3.5 sm:p-5 border border-slate-200/80 shadow-xs hover:border-emerald-400 hover:shadow-sm transition flex flex-col justify-between gap-3 group">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] xs:text-xs sm:text-sm font-bold text-slate-500 uppercase tracking-wider truncate">Santri Aktif</span>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-100">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 tabular-nums">{{ number_format($totalSantriAktif, 0, ',', '.') }}</h3>
                    <span class="inline-flex items-center gap-1 text-[11px] sm:text-xs font-semibold text-emerald-700 mt-1 group-hover:translate-x-0.5 transition-transform">
                        Kelola Santri &rarr;
                    </span>
                </div>
            </a>

            <!-- Surat Masuk Bulan Ini -->
            <a href="{{ route('admin.surat-masuk.index') }}" class="bg-white rounded-2xl p-3.5 sm:p-5 border border-slate-200/80 shadow-xs hover:border-blue-400 hover:shadow-sm transition flex flex-col justify-between gap-3 group">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] xs:text-xs sm:text-sm font-bold text-slate-500 uppercase tracking-wider truncate">Surat Masuk</span>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shrink-0 border border-blue-100">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 tabular-nums">{{ $suratMasukBulanIni }}</h3>
                    @if ($suratMasukBelumDisposisi > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] sm:text-xs font-bold bg-amber-100 text-amber-800 mt-1">
                            {{ $suratMasukBelumDisposisi }} perlu disposisi
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-[11px] sm:text-xs font-semibold text-blue-700 mt-1 group-hover:translate-x-0.5 transition-transform">
                            Buku Agenda &rarr;
                        </span>
                    @endif
                </div>
            </a>

            <!-- Surat Keluar Bulan Ini -->
            <a href="{{ route('admin.surat-keluar.index') }}" class="bg-white rounded-2xl p-3.5 sm:p-5 border border-slate-200/80 shadow-xs hover:border-amber-400 hover:shadow-sm transition flex flex-col justify-between gap-3 group">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] xs:text-xs sm:text-sm font-bold text-slate-500 uppercase tracking-wider truncate">Surat Keluar</span>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-100">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 tabular-nums">{{ $suratKeluarBulanIni }}</h3>
                    <span class="inline-flex items-center gap-1 text-[11px] sm:text-xs font-semibold text-amber-700 mt-1 group-hover:translate-x-0.5 transition-transform">
                        Buku Ekspedisi &rarr;
                    </span>
                </div>
            </a>

            <!-- Agenda Mendatang -->
            <a href="{{ route('admin.agenda.index') }}" class="bg-white rounded-2xl p-3.5 sm:p-5 border border-slate-200/80 shadow-xs hover:border-purple-400 hover:shadow-sm transition flex flex-col justify-between gap-3 group">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] xs:text-xs sm:text-sm font-bold text-slate-500 uppercase tracking-wider truncate">Agenda Terjadwal</span>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center shrink-0 border border-purple-100">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 tabular-nums">{{ $agendaMendatang }}</h3>
                    <span class="inline-flex items-center gap-1 text-[11px] sm:text-xs font-semibold text-purple-700 mt-1 group-hover:translate-x-0.5 transition-transform">
                        Jadwal Kegiatan &rarr;
                    </span>
                </div>
            </a>
        </div>

        <!-- 4. Area Bagan / Grafik Statistik (Chart.js Responsif) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            <!-- Grafik 1: Tren Persuratan Bulanan (Line/Area Chart) -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 lg:p-6 shadow-xs flex flex-col justify-between">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">Tren Persuratan (6 Bulan Terakhir)</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Perbandingan volume surat masuk dan keluar</p>
                    </div>
                    <!-- Legenda Kustom -->
                    <div class="flex items-center gap-4 text-xs font-semibold">
                        <span class="inline-flex items-center gap-1.5 text-emerald-800">
                            <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                            Surat Masuk
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-blue-800">
                            <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                            Surat Keluar
                        </span>
                    </div>
                </div>

                <!-- Canvas Chart Responsif -->
                <div class="relative w-full h-56 sm:h-64 lg:h-72 pt-3">
                    <canvas id="chartSurat"></canvas>
                </div>
            </div>

            <!-- Grafik 2: Komposisi Status Santri (Doughnut Chart) -->
            <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 lg:p-6 shadow-xs flex flex-col justify-between">
                <div class="pb-3 border-b border-slate-100">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Distribusi Status Santri</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Total terdaftar: <strong class="text-slate-800">{{ number_format($totalSantriSemua, 0, ',', '.') }} Santri</strong></p>
                </div>

                <!-- Canvas Doughnut Responsif -->
                <div class="relative w-full h-44 sm:h-48 lg:h-52 flex items-center justify-center my-auto py-2">
                    <canvas id="chartSantri"></canvas>
                </div>

                <!-- Rincian Badge Keterangan -->
                <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 text-xs">
                    <div class="flex items-center justify-between p-2 rounded-xl bg-emerald-50 text-emerald-950 border border-emerald-100">
                        <span class="flex items-center gap-1.5 font-medium">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            Aktif
                        </span>
                        <strong class="font-bold tabular-nums">{{ number_format($santriStatusData['aktif']) }}</strong>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-xl bg-blue-50 text-blue-950 border border-blue-100">
                        <span class="flex items-center gap-1.5 font-medium">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            Lulus
                        </span>
                        <strong class="font-bold tabular-nums">{{ number_format($santriStatusData['lulus']) }}</strong>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-xl bg-amber-50 text-amber-950 border border-amber-100">
                        <span class="flex items-center gap-1.5 font-medium">
                            <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                            Mutasi
                        </span>
                        <strong class="font-bold tabular-nums">{{ number_format($santriStatusData['mutasi']) }}</strong>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-xl bg-slate-100 text-slate-800 border border-slate-200">
                        <span class="flex items-center gap-1.5 font-medium">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            Keluar
                        </span>
                        <strong class="font-bold tabular-nums">{{ number_format($santriStatusData['keluar']) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Baris Pintasan Cepat Sekretariat (Touch-Friendly & Responsif) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-600">Pintasan Cepat Pelayanan</h2>
                <span class="text-xs text-slate-400 hidden sm:inline">Total Arsip Dokumen: <strong class="text-slate-700 font-bold">{{ $totalArsip }} Berkas</strong></span>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                <a href="{{ route('admin.surat-keluar.create') }}"
                   class="flex items-center gap-2.5 sm:gap-3 p-3 sm:p-3.5 min-h-[48px] rounded-xl bg-emerald-50/80 hover:bg-emerald-100 border border-emerald-200/80 text-emerald-950 font-bold text-xs sm:text-sm transition active:scale-95 shadow-xs">
                    <span class="w-8 h-8 rounded-lg bg-emerald-700 text-white flex items-center justify-center font-extrabold text-sm shrink-0">+</span>
                    <div class="min-w-0">
                        <span class="block truncate">Surat Keluar</span>
                        <span class="text-[11px] sm:text-xs font-normal text-emerald-700 truncate hidden sm:block">Nomor Otomatis + QR</span>
                    </div>
                </a>

                <a href="{{ route('admin.surat-masuk.create') }}"
                   class="flex items-center gap-2.5 sm:gap-3 p-3 sm:p-3.5 min-h-[48px] rounded-xl bg-blue-50/80 hover:bg-blue-100 border border-blue-200/80 text-blue-950 font-bold text-xs sm:text-sm transition active:scale-95 shadow-xs">
                    <span class="w-8 h-8 rounded-lg bg-blue-700 text-white flex items-center justify-center font-extrabold text-sm shrink-0">+</span>
                    <div class="min-w-0">
                        <span class="block truncate">Surat Masuk</span>
                        <span class="text-[11px] sm:text-xs font-normal text-blue-700 truncate hidden sm:block">Buku Agenda Otomatis</span>
                    </div>
                </a>

                <a href="{{ route('admin.agenda.create') }}"
                   class="flex items-center gap-2.5 sm:gap-3 p-3 sm:p-3.5 min-h-[48px] rounded-xl bg-purple-50/80 hover:bg-purple-100 border border-purple-200/80 text-purple-950 font-bold text-xs sm:text-sm transition active:scale-95 shadow-xs">
                    <span class="w-8 h-8 rounded-lg bg-purple-700 text-white flex items-center justify-center font-extrabold text-sm shrink-0">+</span>
                    <div class="min-w-0">
                        <span class="block truncate">Jadwal Agenda</span>
                        <span class="text-[11px] sm:text-xs font-normal text-purple-700 truncate hidden sm:block">Kalender Kegiatan</span>
                    </div>
                </a>

                <a href="{{ route('admin.arsip.create') }}"
                   class="flex items-center gap-2.5 sm:gap-3 p-3 sm:p-3.5 min-h-[48px] rounded-xl bg-amber-50/80 hover:bg-amber-100 border border-amber-200/80 text-amber-950 font-bold text-xs sm:text-sm transition active:scale-95 shadow-xs">
                    <span class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center font-extrabold text-sm shrink-0">+</span>
                    <div class="min-w-0">
                        <span class="block truncate">Unggah Arsip</span>
                        <span class="text-[11px] sm:text-xs font-normal text-amber-800 truncate hidden sm:block">Dokumen Digital</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- 6. Tiga Kolom Aktivitas Terbaru (Tinggi Sejajar, Rapi, & Bersih) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 items-start">
            <!-- Kolom 1: 5 Surat Masuk Terbaru -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 sm:py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs sm:text-sm font-bold text-slate-900">Surat Masuk Terbaru</h2>
                        <p class="text-[11px] sm:text-xs text-slate-500">5 catatan surat masuk terakhir</p>
                    </div>
                    <a href="{{ route('admin.surat-masuk.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-bold hover:underline">
                        Semua &rarr;
                    </a>
                </div>
                <div class="divide-y divide-slate-100 flex-1">
                    @forelse ($suratMasukTerbaru as $sm)
                        <a href="{{ route('admin.surat-masuk.show', $sm) }}" class="p-3.5 sm:p-4 hover:bg-slate-50 transition flex items-start justify-between text-xs gap-3 block group">
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-slate-900 group-hover:text-blue-700 transition truncate text-xs sm:text-sm">{{ $sm->perihal }}</p>
                                <p class="text-[11px] sm:text-xs text-slate-500 truncate mt-0.5">Dari: {{ $sm->pengirim }} &bull; {{ $sm->tgl_diterima ? $sm->tgl_diterima->format('d-m-Y') : '-' }}</p>
                                <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                    <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-900 border border-emerald-200 font-mono font-bold text-[11px] sm:text-xs">
                                        #{{ $sm->no_agenda }}
                                    </span>
                                    <span class="text-[11px] sm:text-xs font-mono text-slate-500 truncate">{{ $sm->no_surat }}</span>
                                </div>
                            </div>
                            <div class="shrink-0">
                                @if ($sm->status_disposisi === 'baru')
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 font-bold rounded-full text-[11px] sm:text-xs">
                                        Baru
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold rounded-full text-[11px] sm:text-xs">
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
                <div class="px-4 sm:px-5 py-3.5 sm:py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs sm:text-sm font-bold text-slate-900">Surat Keluar Terbaru</h2>
                        <p class="text-[11px] sm:text-xs text-slate-500">5 surat keluar terakhir</p>
                    </div>
                    <a href="{{ route('admin.surat-keluar.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-bold hover:underline">
                        Semua &rarr;
                    </a>
                </div>
                <div class="divide-y divide-slate-100 flex-1">
                    @forelse ($suratKeluarTerbaru as $sk)
                        <a href="{{ route('admin.surat-keluar.show', $sk) }}" class="p-3.5 sm:p-4 hover:bg-slate-50 transition flex items-start justify-between text-xs gap-3 block group">
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-slate-900 group-hover:text-blue-700 transition truncate text-xs sm:text-sm">{{ $sk->perihal }}</p>
                                <p class="text-[11px] sm:text-xs text-slate-500 truncate mt-0.5">Kepada: {{ $sk->tujuan }} &bull; {{ $sk->tgl_surat ? $sk->tgl_surat->format('d-m-Y') : '-' }}</p>
                                <span class="inline-block mt-1.5 px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-mono font-semibold text-[11px] sm:text-xs">
                                    {{ $sk->no_surat }}
                                </span>
                            </div>
                            <div class="shrink-0">
                                @if ($sk->status === 'draft')
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 font-bold rounded-full text-[11px] sm:text-xs">Draft</span>
                                @else
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold rounded-full text-[11px] sm:text-xs">{{ ucfirst($sk->status) }}</span>
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
                <div class="px-4 sm:px-5 py-3.5 sm:py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs sm:text-sm font-bold text-slate-900">Agenda Terdekat</h2>
                        <p class="text-[11px] sm:text-xs text-slate-500">5 jadwal kegiatan mendatang</p>
                    </div>
                    <a href="{{ route('admin.agenda.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-bold hover:underline">
                        Semua &rarr;
                    </a>
                </div>
                <div class="divide-y divide-slate-100 flex-1">
                    @forelse ($agendasTerdekat as $ag)
                        <a href="{{ route('admin.agenda.show', $ag) }}" class="p-3.5 sm:p-4 hover:bg-slate-50 transition flex items-start justify-between text-xs gap-3 block group">
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-slate-900 group-hover:text-blue-700 transition truncate text-xs sm:text-sm">{{ $ag->judul }}</p>
                                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">{{ $ag->mulai->format('d-m-Y H:i') }} WIB</p>
                                <p class="text-[11px] sm:text-xs text-slate-400 truncate mt-0.5">Lokasi: {{ $ag->lokasi }}</p>
                            </div>
                            <div class="shrink-0">
                                <span class="px-2 py-0.5 bg-purple-50 text-purple-800 border border-purple-200 font-bold rounded-full text-[11px] sm:text-xs">
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.Chart === 'undefined') return;

            // 1. Inisialisasi Grafik Tren Persuratan (Line/Area Chart)
            const ctxSurat = document.getElementById('chartSurat');
            if (ctxSurat) {
                new window.Chart(ctxSurat, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartBulanLabels) !!},
                        datasets: [
                            {
                                label: 'Surat Masuk',
                                data: {!! json_encode($chartSuratMasukData) !!},
                                borderColor: '#059669', // emerald-600
                                backgroundColor: 'rgba(5, 150, 105, 0.12)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#059669'
                            },
                            {
                                label: 'Surat Keluar',
                                data: {!! json_encode($chartSuratKeluarData) !!},
                                borderColor: '#2563eb', // blue-600
                                backgroundColor: 'rgba(37, 99, 235, 0.12)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#2563eb'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#064e3b',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 10,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: { size: 12 }
                                },
                                grid: {
                                    color: 'rgba(226, 232, 240, 0.6)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: { size: 12 }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // 2. Inisialisasi Grafik Donut Komposisi Santri
            const ctxSantri = document.getElementById('chartSantri');
            if (ctxSantri) {
                new window.Chart(ctxSantri, {
                    type: 'doughnut',
                    data: {
                        labels: ['Aktif', 'Lulus', 'Mutasi', 'Keluar'],
                        datasets: [{
                            data: [
                                {{ $santriStatusData['aktif'] }},
                                {{ $santriStatusData['lulus'] }},
                                {{ $santriStatusData['mutasi'] }},
                                {{ $santriStatusData['keluar'] }}
                            ],
                            backgroundColor: [
                                '#059669', // emerald
                                '#2563eb', // blue
                                '#d97706', // amber
                                '#94a3b8'  // slate
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 10,
                                cornerRadius: 8
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
