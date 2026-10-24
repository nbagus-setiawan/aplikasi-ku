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

        <!-- 3. Metrik Statistik Santri & Jenjang (Responsif: 2 kolom di HP dengan Santri Aktif full width, 5 kolom di Desktop) -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 lg:gap-5">
            <!-- 1. Santri Aktif (Semua) - Full width di HP (col-span-2), 1 kolom di desktop -->
            <a href="{{ route('admin.santri.index') }}" class="col-span-2 lg:col-span-1 bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-2xl p-4 sm:p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between gap-3 group relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] xs:text-xs font-bold text-emerald-100 uppercase tracking-wider truncate">Santri Aktif</span>
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white/15 text-white flex items-center justify-center shrink-0 border border-white/20">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-2xl sm:text-3xl font-black text-white tabular-nums tracking-tight">{{ number_format($totalSantriAktif, 0, ',', '.') }}</h3>
                    <div class="flex items-center justify-between mt-1 text-[11px] text-emerald-100 font-medium">
                        <span>Semua Jenjang</span>
                        <span class="font-bold group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </div>
                </div>
            </a>

            <!-- 2. Santri MTs -->
            <a href="{{ route('admin.santri.index') }}" class="col-span-1 bg-white rounded-2xl p-3.5 sm:p-5 border border-slate-200/80 shadow-xs hover:border-emerald-400 hover:shadow-sm transition flex flex-col justify-between gap-2.5 group">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] xs:text-xs font-bold text-slate-500 uppercase tracking-wider truncate">Santri MTs</span>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-100">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tabular-nums">{{ number_format($countMts, 0, ',', '.') }}</h3>
                    <div class="mt-1 flex flex-wrap gap-1 text-[10px] text-slate-500 font-medium">
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 font-semibold text-slate-600">VII: {{ $countMtsVII }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 font-semibold text-slate-600">VIII: {{ $countMtsVIII }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 font-semibold text-slate-600">IX: {{ $countMtsIX }}</span>
                    </div>
                </div>
            </a>

            <!-- 3. Santri MA -->
            <a href="{{ route('admin.santri.index') }}" class="col-span-1 bg-white rounded-2xl p-3.5 sm:p-5 border border-slate-200/80 shadow-xs hover:border-blue-400 hover:shadow-sm transition flex flex-col justify-between gap-2.5 group">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] xs:text-xs font-bold text-slate-500 uppercase tracking-wider truncate">Santri MA</span>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0 border border-blue-100">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tabular-nums">{{ number_format($countMa, 0, ',', '.') }}</h3>
                    <div class="mt-1 flex flex-wrap gap-1 text-[10px] text-slate-500 font-medium">
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 font-semibold text-slate-600">X: {{ $countMaX }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 font-semibold text-slate-600">XI: {{ $countMaXI }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 font-semibold text-slate-600">XII: {{ $countMaXII }}</span>
                    </div>
                </div>
            </a>

            <!-- 4. Mutakhorijin -->
            <a href="{{ route('admin.santri.index', ['search' => 'Mutakhorijin']) }}" class="col-span-1 bg-white rounded-2xl p-3.5 sm:p-5 border border-slate-200/80 shadow-xs hover:border-amber-400 hover:shadow-sm transition flex flex-col justify-between gap-2.5 group">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] xs:text-xs font-bold text-slate-500 uppercase tracking-wider truncate">Mutakhorijin</span>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-100">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tabular-nums">{{ number_format($countMutakhorijin, 0, ',', '.') }}</h3>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-medium mt-1 truncate">Tamat Kls 12 · Khidmah 1 Th</p>
                </div>
            </a>

            <!-- 5. Mahasantri -->
            <a href="{{ route('admin.santri.index', ['search' => 'Mahasantri']) }}" class="col-span-1 bg-white rounded-2xl p-3.5 sm:p-5 border border-slate-200/80 shadow-xs hover:border-purple-400 hover:shadow-sm transition flex flex-col justify-between gap-2.5 group">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] xs:text-xs font-bold text-slate-500 uppercase tracking-wider truncate">Mahasantri</span>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center shrink-0 border border-purple-100">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tabular-nums">{{ number_format($countMahasantri, 0, ',', '.') }}</h3>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-medium mt-1 truncate">Kuliah Luar · Mukim Pondok</p>
                </div>
            </a>
        </div>

        @if ($countLainnya > 0)
            <div class="flex items-center justify-between text-xs text-slate-500 bg-slate-50 border border-slate-200/60 rounded-xl px-3.5 py-2">
                <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Total {{ $totalSantriAktif }} santri aktif: <strong>{{ $countMts }}</strong> MTs, <strong>{{ $countMa }}</strong> MA, <strong>{{ $countMutakhorijin }}</strong> Mutakhorijin, <strong>{{ $countMahasantri }}</strong> Mahasantri, dan <strong>{{ $countLainnya }}</strong> santri kelas lainnya.
                </span>
                <a href="{{ route('admin.santri.index') }}" class="text-emerald-700 hover:text-emerald-800 font-bold hover:underline shrink-0 ml-2">
                    Lihat Data &rarr;
                </a>
            </div>
        @endif

        <!-- 4. Area Grafik Statistik: Sebaran Jenjang Pendidikan & Hunian Asrama (Grafik Batangan / Bar Chart) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-5 items-stretch">
            <!-- Grafik Batangan 1: Sebaran Santri per Jenjang & Tingkat -->
            <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 lg:p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                Sebaran Kelas & Jenjang
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Sesuai nilai kelas riil yang diinputkan di data santri</p>
                        </div>
                        <!-- Toggle Tampilan: Per Kelas (Riil) vs Per Jenjang (Ringkasan) -->
                        <div class="inline-flex p-0.5 bg-slate-100 border border-slate-200/80 rounded-lg text-xs font-semibold self-start sm:self-auto">
                            <button type="button" id="btnModeKelas" class="px-2.5 py-1 rounded-md bg-white text-emerald-800 shadow-xs font-bold transition">
                                Per Kelas
                            </button>
                            <button type="button" id="btnModeJenjang" class="px-2.5 py-1 rounded-md text-slate-500 hover:text-slate-800 transition">
                                Per Jenjang
                            </button>
                        </div>
                    </div>

                    <!-- Canvas Grafik Batangan Jenjang / Kelas -->
                    <div class="mt-4 relative h-64 sm:h-72">
                        <canvas id="chartJenjangSantri"></canvas>
                    </div>
                </div>

                <!-- Ringkasan Rincian Jenjang di Bawah Grafik -->
                <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
                    <div class="p-2 rounded-xl bg-emerald-50/60 border border-emerald-100/80">
                        <span class="block text-[10px] font-bold text-emerald-800 uppercase tracking-wide">MTs</span>
                        <span class="text-base font-black text-emerald-950 tabular-nums">{{ $countMts }}</span>
                        <span class="block text-[9px] text-emerald-700 mt-0.5">Kls VII–IX</span>
                    </div>
                    <div class="p-2 rounded-xl bg-blue-50/60 border border-blue-100/80">
                        <span class="block text-[10px] font-bold text-blue-800 uppercase tracking-wide">MA</span>
                        <span class="text-base font-black text-blue-950 tabular-nums">{{ $countMa }}</span>
                        <span class="block text-[9px] text-blue-700 mt-0.5">Kls X–XII</span>
                    </div>
                    <div class="p-2 rounded-xl bg-amber-50/60 border border-amber-100/80">
                        <span class="block text-[10px] font-bold text-amber-800 uppercase tracking-wide">Mutakhorijin</span>
                        <span class="text-base font-black text-amber-950 tabular-nums">{{ $countMutakhorijin }}</span>
                        <span class="block text-[9px] text-amber-700 mt-0.5">Khidmah</span>
                    </div>
                    <div class="p-2 rounded-xl bg-purple-50/60 border border-purple-100/80">
                        <span class="block text-[10px] font-bold text-purple-800 uppercase tracking-wide">Mahasantri</span>
                        <span class="text-base font-black text-purple-950 tabular-nums">{{ $countMahasantri }}</span>
                        <span class="block text-[9px] text-purple-700 mt-0.5">Mukim</span>
                    </div>
                </div>
            </div>

            <!-- Grafik Batangan 2: Statistik Hunian Asrama (Hanya yang berpenghuni) -->
            <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 lg:p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                Statistik Hunian Asrama
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Menampilkan kamar yang berpenghuni sesuai inputan data santri
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                {{ $totalKamarTerisi }} Kamar Terisi
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                {{ $totalSantriBerasrama }} Santri
                            </span>
                        </div>
                    </div>

                    <!-- Canvas Grafik Batangan Hunian Asrama -->
                    <div class="mt-4 relative h-64 sm:h-72">
                        @if ($totalKamarTerisi > 0)
                            <canvas id="chartHunianAsrama"></canvas>
                        @else
                            <div class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-400">
                                <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <p class="text-sm font-semibold text-slate-600">Belum ada data kamar terisi</p>
                                <p class="text-xs text-slate-400 mt-1">Data asrama akan otomatis tampil saat santri memiliki alokasi kamar aktif.</p>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($totalSantriTanpaKamar > 0)
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1.5 text-amber-700 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Terdapat <strong>{{ $totalSantriTanpaKamar }}</strong> santri aktif belum memiliki alokasi kamar.
                        </span>
                        <a href="{{ route('admin.santri.index') }}" class="text-emerald-700 hover:text-emerald-800 font-semibold hover:underline">
                            Lengkapi &rarr;
                        </a>
                    </div>
                @else
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span class="text-emerald-700 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Seluruh santri aktif telah teralokasi ke asrama.
                        </span>
                        <a href="{{ route('admin.santri.index') }}" class="text-emerald-700 hover:text-emerald-800 font-semibold hover:underline">
                            Daftar Santri &rarr;
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- 5. Area Bagan / Grafik Statistik Persuratan (Chart.js Responsif) -->
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

            // 3. Inisialisasi Grafik Batang Sebaran Kelas & Jenjang Santri
            const ctxJenjang = document.getElementById('chartJenjangSantri');
            if (ctxJenjang) {
                const dataKelas = {
                    labels: {!! json_encode($chartKelasLabels) !!},
                    data: {!! json_encode($chartKelasData) !!},
                    colors: {!! json_encode($chartKelasColors) !!}
                };

                const dataJenjang = {
                    labels: {!! json_encode($chartJenjangLabels) !!},
                    data: {!! json_encode($chartJenjangData) !!},
                    colors: [
                        '#059669', // MTs (Emerald)
                        '#2563eb', // MA (Blue)
                        '#d97706', // Mutakhorijin (Amber)
                        '#7c3aed', // Mahasantri (Purple)
                        '#64748b'  // Lainnya (Slate)
                    ]
                };

                const chartJenjangInst = new window.Chart(ctxJenjang, {
                    type: 'bar',
                    data: {
                        labels: dataKelas.labels,
                        datasets: [{
                            label: 'Jumlah Santri',
                            data: dataKelas.data,
                            backgroundColor: dataKelas.colors,
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 36
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + context.parsed.y + ' Santri';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: { size: 11 }
                                },
                                grid: {
                                    color: 'rgba(226, 232, 240, 0.6)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: { size: 10, weight: '600' },
                                    autoSkip: false,
                                    maxRotation: 45,
                                    minRotation: 0
                                },
                                grid: { display: false }
                            }
                        }
                    }
                });

                // Handler tombol switch antara 'Per Kelas' (data riil) dan 'Per Jenjang'
                const btnKelas = document.getElementById('btnModeKelas');
                const btnJenjang = document.getElementById('btnModeJenjang');

                if (btnKelas && btnJenjang) {
                    btnKelas.addEventListener('click', function() {
                        btnKelas.className = 'px-2.5 py-1 rounded-md bg-white text-emerald-800 shadow-xs font-bold transition';
                        btnJenjang.className = 'px-2.5 py-1 rounded-md text-slate-500 hover:text-slate-800 transition';
                        chartJenjangInst.data.labels = dataKelas.labels;
                        chartJenjangInst.data.datasets[0].data = dataKelas.data;
                        chartJenjangInst.data.datasets[0].backgroundColor = dataKelas.colors;
                        chartJenjangInst.data.datasets[0].maxBarThickness = 36;
                        chartJenjangInst.update();
                    });

                    btnJenjang.addEventListener('click', function() {
                        btnJenjang.className = 'px-2.5 py-1 rounded-md bg-white text-emerald-800 shadow-xs font-bold transition';
                        btnKelas.className = 'px-2.5 py-1 rounded-md text-slate-500 hover:text-slate-800 transition';
                        chartJenjangInst.data.labels = dataJenjang.labels;
                        chartJenjangInst.data.datasets[0].data = dataJenjang.data;
                        chartJenjangInst.data.datasets[0].backgroundColor = dataJenjang.colors;
                        chartJenjangInst.data.datasets[0].maxBarThickness = 52;
                        chartJenjangInst.update();
                    });
                }
            }

            // 4. Inisialisasi Grafik Batang Statistik Hunian Asrama
            const ctxAsrama = document.getElementById('chartHunianAsrama');
            if (ctxAsrama) {
                new window.Chart(ctxAsrama, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartAsramaLabels) !!},
                        datasets: [{
                            label: 'Jumlah Santri',
                            data: {!! json_encode($chartAsramaData) !!},
                            backgroundColor: '#d97706',
                            hoverBackgroundColor: '#b45309',
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 36
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + context.parsed.y + ' Santri Menempati';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: { size: 11 }
                                },
                                grid: {
                                    color: 'rgba(226, 232, 240, 0.6)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: { size: 10, weight: '600' },
                                    autoSkip: false,
                                    maxRotation: 45,
                                    minRotation: 0
                                },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
