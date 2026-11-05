<x-app-layout>
    @section('title', 'Dashboard Sekretariat')

    <div class="space-y-5 sm:space-y-6">
        <!-- 1. Executive Greeting Header (Modern Slate Gradient Banner) -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-5 sm:p-6 lg:p-7 text-white shadow-xs border border-slate-800 relative overflow-hidden">
            <!-- Ornamen latar belakang halus -->
            <div class="absolute -right-10 -bottom-10 w-52 h-52 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -top-10 w-44 h-44 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
                <!-- Teks Sambutan Rata Kiri -->
                <div class="space-y-2 text-left flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-800/90 border border-slate-700/80 text-emerald-400 text-xs font-semibold rounded-full shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            SAP Al-Falah Putra
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-300 font-medium hidden sm:inline-flex">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </span>
                        @if ($totalPerluTindakan > 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                {{ $totalPerluTindakan }} Perlu Tindakan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Tertib Administrasi
                            </span>
                        @endif
                    </div>

                    <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold tracking-tight text-white leading-tight">
                        {{ $greeting }}, {{ Auth::user()->name ?? 'Sekretaris' }}
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-2xl font-normal">
                        Pusat kendali terpadu administrasi kesekretariatan, persuratan resmi, data santri, keuangan kas pondok, dan jadwal agenda Pondok Pesantren Al-Falah Putra.
                    </p>

                    <div class="flex items-center gap-2.5 pt-1 text-xs text-slate-400 font-medium sm:hidden">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}
                        </span>
                        <span>&bull;</span>
                        <span>Gelumbang, Muara Enim</span>
                    </div>
                </div>

                <!-- Gambar Logo Banner: Tampil di Layar Tablet & Desktop -->
                <div class="hidden md:flex shrink-0 items-center justify-center pl-2">
                    <img src="{{ asset('images/baner.webp') }}"
                         alt="Banner Al-Falah Putra"
                         class="h-24 lg:h-28 w-auto object-contain select-none drop-shadow-md" />
                </div>
            </div>
        </div>

        <!-- 2. Pusat Tindakan & Perhatian Cepat (Secretary Action Center) -->
        @if ($totalPerluTindakan > 0)
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-900">Pusat Tindakan Sekretariat</h2>
                            <p class="text-[11px] text-slate-500">Terdapat {{ $totalPerluTindakan }} agenda dan berkas yang memerlukan tindak lanjut Anda</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <!-- Alert 1: Surat Masuk Baru Butuh Disposisi -->
                    @if ($suratMasukBelumDisposisi > 0)
                        <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200/90 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                    <h3 class="font-bold text-amber-950 truncate">{{ $suratMasukBelumDisposisi }} Surat Masuk Baru</h3>
                                </div>
                                <p class="text-slate-600 text-[11px] mt-0.5">Menunggu disposisi pimpinan pondok</p>
                            </div>
                            <a href="{{ route('admin.surat-masuk.index', ['status_disposisi' => 'baru']) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg text-xs transition shrink-0 shadow-2xs">
                                <span>Disposisi</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    @endif

                    <!-- Alert 2: Draft Surat Keluar -->
                    @if ($suratKeluarDraft > 0)
                        <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200/90 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    <h3 class="font-bold text-blue-950 truncate">{{ $suratKeluarDraft }} Draft Surat Keluar</h3>
                                </div>
                                <p class="text-slate-600 text-[11px] mt-0.5">Menunggu finalisasi atau tanda tangan</p>
                            </div>
                            <a href="{{ route('admin.surat-keluar.index', ['status' => 'draft']) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs transition shrink-0 shadow-2xs">
                                <span>Tinjau</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    @endif

                    <!-- Alert 3: Agenda Kegiatan Hari Ini -->
                    @if ($agendasHariIni->count() > 0)
                        <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200/90 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <h3 class="font-bold text-emerald-950 truncate">{{ $agendasHariIni->count() }} Kegiatan Hari Ini</h3>
                                </div>
                                <p class="text-slate-600 text-[11px] mt-0.5">{{ Str::limit($agendasHariIni->first()->judul, 28) }}</p>
                            </div>
                            <a href="{{ route('admin.agenda.index') }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs transition shrink-0 shadow-2xs">
                                <span>Lihat</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    @endif

                    <!-- Alert 4: Peringatan Agenda H-1 (Besok) -->
                    @if ($agendasBesok->count() > 0)
                        <div class="p-3.5 rounded-xl bg-purple-50/70 border border-purple-200/90 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                    <h3 class="font-bold text-purple-950 truncate">{{ $agendasBesok->count() }} Kegiatan Besok (H-1)</h3>
                                </div>
                                <p class="text-slate-600 text-[11px] mt-0.5">{{ Str::limit($agendasBesok->first()->judul, 28) }}</p>
                            </div>
                            <a href="{{ route('admin.agenda.index', ['view' => 'kalender']) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-lg text-xs transition shrink-0 shadow-2xs">
                                <span>Kalender</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- 3. Top 4 KPI Metrics (Empat Pilar Utama Sekretariat & Keuangan Pesantren) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- Pilar 1: Santri Aktif -->
            <a href="{{ route('admin.santri.index') }}"
               class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs hover:border-emerald-500 hover:shadow-sm transition-all duration-200 flex flex-col justify-between gap-3 group">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider truncate">Santri Aktif</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-100 group-hover:scale-105 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                </div>
                <div>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">
                        {{ number_format($totalSantriAktif, 0, ',', '.') }}
                    </h3>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 font-medium mt-1 truncate">
                        <span>Santri MTs: <strong class="text-slate-700">{{ $countMts }}</strong></span>
                        <span>&bull;</span>
                        <span>Santri MA: <strong class="text-slate-700">{{ $countMa }}</strong></span>
                    </div>
                </div>
            </a>

            <!-- Pilar 2: Surat Masuk Bulan Ini -->
            <a href="{{ route('admin.surat-masuk.index') }}"
               class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs hover:border-blue-500 hover:shadow-sm transition-all duration-200 flex flex-col justify-between gap-3 group">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider truncate">Surat Masuk (Bln Ini)</span>
                    <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shrink-0 border border-blue-100 group-hover:scale-105 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </span>
                </div>
                <div>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">
                        {{ number_format($suratMasukBulanIni, 0, ',', '.') }}
                    </h3>
                    <div class="text-[11px] mt-1 font-medium truncate">
                        @if ($suratMasukBelumDisposisi > 0)
                            <span class="text-amber-600 font-bold">{{ $suratMasukBelumDisposisi }} perlu disposisi</span>
                        @else
                            <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Seluruhnya didisposisi
                            </span>
                        @endif
                    </div>
                </div>
            </a>

            <!-- Pilar 3: Surat Keluar Bulan Ini -->
            <a href="{{ route('admin.surat-keluar.index') }}"
               class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs hover:border-purple-500 hover:shadow-sm transition-all duration-200 flex flex-col justify-between gap-3 group">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider truncate">Surat Keluar (Bln Ini)</span>
                    <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center shrink-0 border border-purple-100 group-hover:scale-105 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </span>
                </div>
                <div>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">
                        {{ number_format($suratKeluarBulanIni, 0, ',', '.') }}
                    </h3>
                    <div class="text-[11px] mt-1 font-medium truncate">
                        @if ($suratKeluarDraft > 0)
                            <span class="text-amber-600 font-bold">{{ $suratKeluarDraft }} draft surat</span>
                        @else
                            <span class="text-slate-500">Semua telah diterbitkan</span>
                        @endif
                    </div>
                </div>
            </a>

            <!-- Pilar 4: Saldo Kas Pesantren Riil -->
            <a href="{{ route('admin.bendahara.index') }}"
               class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs hover:border-emerald-500 hover:shadow-sm transition-all duration-200 flex flex-col justify-between gap-3 group">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider truncate">Saldo Kas Pesantren</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-100 group-hover:scale-105 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 tabular-nums tracking-tight">
                        Rp {{ number_format($saldoKasRiil, 0, ',', '.') }}
                    </h3>
                    <div class="flex items-center gap-1.5 text-[11px] font-medium mt-1 truncate">
                        <span class="text-emerald-700 font-semibold">+{{ number_format($debitBulanIni, 0, ',', '.') }}</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-rose-600 font-semibold">-{{ number_format($kreditBulanIni, 0, ',', '.') }}</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- 4. Area Visual Statistik: Komposisi Jenjang Santri & Hunian Asrama -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            <!-- Bagian Kiri: Komposisi Jenjang Santri (Segmented Visual & Detail Cards) -->
            <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 lg:p-6 shadow-xs flex flex-col">
                <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            Sebaran Jenjang Santri
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Proporsi santri berdasarkan tingkat pendidikan</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-100">
                        {{ $totalSantriAktif }} Santri
                    </span>
                </div>

                <!-- Visual Segmented Bar Proporsional -->
                <div class="mt-4 space-y-1.5">
                    <div class="h-3 w-full bg-slate-100 rounded-full flex overflow-hidden p-0.5 gap-0.5">
                        @if ($pctMts > 0)
                            <div style="width: {{ $pctMts }}%; background-color: #059669;" class="h-full rounded-l-full transition-all" title="MTs: {{ $countMts }} ({{ $pctMts }}%)"></div>
                        @endif
                        @if ($pctMa > 0)
                            <div style="width: {{ $pctMa }}%; background-color: #2563eb;" class="h-full transition-all" title="MA: {{ $countMa }} ({{ $pctMa }}%)"></div>
                        @endif
                        @if ($pctMutakhorijin > 0)
                            <div style="width: {{ $pctMutakhorijin }}%; background-color: #d97706;" class="h-full transition-all" title="Mutakhorijin: {{ $countMutakhorijin }} ({{ $pctMutakhorijin }}%)"></div>
                        @endif
                        @if ($pctMahasantri > 0)
                            <div style="width: {{ $pctMahasantri }}%; background-color: #7c3aed;" class="h-full rounded-r-full transition-all" title="Mahasantri: {{ $countMahasantri }} ({{ $pctMahasantri }}%)"></div>
                        @endif
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400 font-semibold px-0.5">
                        <span>MTs {{ $pctMts }}%</span>
                        <span>MA {{ $pctMa }}%</span>
                        <span>Mutakhorijin {{ $pctMutakhorijin }}%</span>
                        <span>Mahasantri {{ $pctMahasantri }}%</span>
                    </div>
                </div>

                <!-- Kartu Rincian Tiap Jenjang (2x2 Grid Bersih & Rapi) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-3.5">
                    <!-- MTs -->
                    <div class="p-3 rounded-xl bg-emerald-50/50 border border-emerald-100/80">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-900">Santri MTs (Tsanawiyah)</span>
                            <span class="text-xs font-black text-emerald-700">{{ $pctMts }}%</span>
                        </div>
                        <div class="mt-1 flex items-baseline gap-1">
                            <span class="text-lg font-black text-emerald-950 tabular-nums">{{ $countMts }}</span>
                            <span class="text-[11px] text-emerald-700">Santri</span>
                        </div>
                        <div class="mt-1.5 pt-1.5 border-t border-emerald-100/60 flex items-center gap-1.5 text-[10px] text-emerald-800 font-semibold">
                            <span>VII: {{ $countMtsVII }}</span>
                            <span>&bull;</span>
                            <span>VIII: {{ $countMtsVIII }}</span>
                            <span>&bull;</span>
                            <span>IX: {{ $countMtsIX }}</span>
                        </div>
                    </div>

                    <!-- MA -->
                    <div class="p-3 rounded-xl bg-blue-50/50 border border-blue-100/80">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-900">Santri MA (Aliyah)</span>
                            <span class="text-xs font-black text-blue-700">{{ $pctMa }}%</span>
                        </div>
                        <div class="mt-1 flex items-baseline gap-1">
                            <span class="text-lg font-black text-blue-950 tabular-nums">{{ $countMa }}</span>
                            <span class="text-[11px] text-blue-700">Santri</span>
                        </div>
                        <div class="mt-1.5 pt-1.5 border-t border-blue-100/60 flex items-center gap-1.5 text-[10px] text-blue-800 font-semibold">
                            <span>X: {{ $countMaX }}</span>
                            <span>&bull;</span>
                            <span>XI: {{ $countMaXI }}</span>
                            <span>&bull;</span>
                            <span>XII: {{ $countMaXII }}</span>
                        </div>
                    </div>

                    <!-- Mutakhorijin -->
                    <div class="p-3 rounded-xl bg-amber-50/50 border border-amber-100/80">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-900">Mutakhorijin</span>
                            <span class="text-xs font-black text-amber-700">{{ $pctMutakhorijin }}%</span>
                        </div>
                        <div class="mt-1 flex items-baseline gap-1">
                            <span class="text-lg font-black text-amber-950 tabular-nums">{{ $countMutakhorijin }}</span>
                            <span class="text-[11px] text-amber-700">Santri</span>
                        </div>
                        <p class="mt-1.5 pt-1.5 border-t border-amber-100/60 text-[10px] text-amber-800 font-medium">Tamat Kls 12 &bull; Khidmah 1 Tahun</p>
                    </div>

                    <!-- Mahasantri -->
                    <div class="p-3 rounded-xl bg-purple-50/50 border border-purple-100/80">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-purple-900">Mahasantri</span>
                            <span class="text-xs font-black text-purple-700">{{ $pctMahasantri }}%</span>
                        </div>
                        <div class="mt-1 flex items-baseline gap-1">
                            <span class="text-lg font-black text-purple-950 tabular-nums">{{ $countMahasantri }}</span>
                            <span class="text-[11px] text-purple-700">Santri</span>
                        </div>
                        <p class="mt-1.5 pt-1.5 border-t border-purple-100/60 text-[10px] text-purple-800 font-medium">Kuliah &bull; Mukim Pondok</p>
                    </div>
                </div>

                <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-medium text-slate-500">Data riil database santri aktif</span>
                    <a href="{{ route('admin.santri.index') }}" class="text-emerald-700 hover:text-emerald-800 font-semibold hover:underline">
                        Buka Data Santri &rarr;
                    </a>
                </div>
            </div>

            <!-- Bagian Kanan: Statistik Hunian Asrama -->
            <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 lg:p-6 shadow-xs flex flex-col">
                <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Statistik Hunian Asrama
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Keterisian kamar berpenghuni santri aktif</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                            {{ $totalKamarTerisi }} Kamar
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                            {{ $totalSantriBerasrama }} Santri
                        </span>
                    </div>
                </div>

                <!-- Daftar Kamar Berpenghuni -->
                <div class="mt-4 space-y-2 pr-1" style="max-height: 270px; overflow-y: auto;">
                    @forelse ($hunianKamarList as $hk)
                        @php
                            $pctKamar = $maxHunian > 0 ? round(($hk['total'] / $maxHunian) * 100) : 0;
                        @endphp
                        <div class="p-2 sm:p-2.5 rounded-xl hover:bg-slate-50 transition border border-slate-100/90">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-bold text-slate-800 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    {{ $hk['nama'] }}
                                </span>
                                <span class="inline-flex items-center gap-1 font-black text-amber-900 bg-amber-50 border border-amber-200/60 px-2 py-0.5 rounded-md text-[11px] tabular-nums">
                                    {{ $hk['total'] }} Santri
                                </span>
                            </div>
                            <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all" style="width: {{ max($pctKamar, 5) }}%; background-color: #d97706; min-width: 6px;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="h-40 flex flex-col items-center justify-center text-center p-6 text-slate-400">
                            <p class="text-xs">Belum ada data kamar terisi.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    @if ($totalSantriTanpaKamar > 0)
                        <span class="text-amber-700 font-medium truncate">
                            {{ $totalSantriTanpaKamar }} santri belum ada alokasi kamar
                        </span>
                    @else
                        <span class="text-emerald-700 font-medium">Seluruh santri aktif telah memiliki kamar</span>
                    @endif
                    <a href="{{ route('admin.santri.index') }}" class="text-emerald-700 hover:text-emerald-800 font-semibold hover:underline shrink-0">
                        Kelola Kamar &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- 5. Area Bagan / Grafik Statistik Persuratan (Chart.js Responsif) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            <!-- Grafik 1: Tren Persuratan Bulanan (Line/Area Chart) -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 lg:p-6 shadow-xs flex flex-col justify-between">
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
            <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 lg:p-6 shadow-xs flex flex-col justify-between">
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

        <!-- 6. Tabbed Activity Center: Pusat Aktivitas & Berkas Terkini (Modern Alpine Tabs) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden"
             x-data="{ tab: 'surat-masuk' }">
            <!-- Header Tab Switcher -->
            <div class="px-4 sm:px-6 py-3.5 bg-slate-50/80 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-900"></span>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Aktivitas Terkini Pesantren</h2>
                </div>

                <!-- Tab Navigation Buttons -->
                <div class="flex items-center gap-1.5 bg-slate-200/70 p-1 rounded-xl text-xs font-semibold overflow-x-auto">
                    <button @click="tab = 'surat-masuk'"
                            :class="tab === 'surat-masuk' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        Surat Masuk
                    </button>
                    <button @click="tab = 'surat-keluar'"
                            :class="tab === 'surat-keluar' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Surat Keluar
                    </button>
                    <button @click="tab = 'kas'"
                            :class="tab === 'kas' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                        <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Kas Bendahara
                    </button>
                    <button @click="tab = 'agenda'"
                            :class="tab === 'agenda' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                        <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Agenda Pondok
                    </button>
                </div>
            </div>

            <!-- Tab Content 1: Surat Masuk -->
            <div x-show="tab === 'surat-masuk'" class="divide-y divide-slate-100">
                @forelse ($suratMasukTerbaru as $sm)
                    <a href="{{ route('admin.surat-masuk.show', $sm) }}" class="p-4 hover:bg-slate-50 transition flex items-center justify-between text-xs gap-3 block group">
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-slate-900 group-hover:text-blue-700 transition truncate text-xs sm:text-sm">{{ $sm->perihal }}</p>
                            <p class="text-[11px] sm:text-xs text-slate-500 truncate mt-0.5">Dari: {{ $sm->pengirim }} &bull; {{ $sm->tgl_diterima ? $sm->tgl_diterima->format('d-m-Y') : '-' }}</p>
                            <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-900 border border-emerald-200 font-mono font-bold text-[11px]">
                                    #{{ $sm->no_agenda }}
                                </span>
                                <span class="text-[11px] font-mono text-slate-500 truncate">{{ $sm->no_surat }}</span>
                            </div>
                        </div>
                        <div class="shrink-0 flex items-center gap-2">
                            @if ($sm->status_disposisi === 'baru')
                                <span class="px-2.5 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 font-bold rounded-full text-xs">
                                    Baru
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold rounded-full text-xs">
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
                <div class="p-3 bg-slate-50/50 text-right">
                    <a href="{{ route('admin.surat-masuk.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-bold hover:underline">
                        Lihat Seluruh Surat Masuk &rarr;
                    </a>
                </div>
            </div>

            <!-- Tab Content 2: Surat Keluar -->
            <div x-show="tab === 'surat-keluar'" x-cloak class="divide-y divide-slate-100">
                @forelse ($suratKeluarTerbaru as $sk)
                    <a href="{{ route('admin.surat-keluar.show', $sk) }}" class="p-4 hover:bg-slate-50 transition flex items-center justify-between text-xs gap-3 block group">
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-slate-900 group-hover:text-blue-700 transition truncate text-xs sm:text-sm">{{ $sk->perihal }}</p>
                            <p class="text-[11px] sm:text-xs text-slate-500 truncate mt-0.5">Kepada: {{ $sk->tujuan }} &bull; {{ $sk->tgl_surat ? $sk->tgl_surat->format('d-m-Y') : '-' }}</p>
                            <span class="inline-block mt-1.5 px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-mono font-semibold text-[11px]">
                                {{ $sk->no_surat }}
                            </span>
                        </div>
                        <div class="shrink-0">
                            @if ($sk->status === 'draft')
                                <span class="px-2.5 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 font-bold rounded-full text-xs">Draft</span>
                            @else
                                <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold rounded-full text-xs">{{ ucfirst($sk->status) }}</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Belum ada catatan surat keluar.
                    </div>
                @endforelse
                <div class="p-3 bg-slate-50/50 text-right">
                    <a href="{{ route('admin.surat-keluar.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-bold hover:underline">
                        Lihat Seluruh Surat Keluar &rarr;
                    </a>
                </div>
            </div>

            <!-- Tab Content 3: Transaksi Kas Bendahara -->
            <div x-show="tab === 'kas'" x-cloak class="divide-y divide-slate-100">
                @forelse ($transaksiKasTerbaru as $tk)
                    <div class="p-4 hover:bg-slate-50 transition flex items-center justify-between text-xs gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded font-mono font-bold text-[10px] {{ $tk->jenis === 'debit' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ strtoupper($tk->jenis) }}
                                </span>
                                <p class="font-bold text-slate-900 truncate text-xs sm:text-sm">{{ $tk->keterangan }}</p>
                            </div>
                            <p class="text-[11px] sm:text-xs text-slate-500 truncate mt-1">
                                Kategori: {{ $tk->kategori ?? 'Umum' }} &bull; Tanggal: {{ $tk->tanggal ? $tk->tanggal->format('d-m-Y') : '-' }} &bull; Kode: {{ $tk->kode_transaksi }}
                            </p>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="block text-sm sm:text-base font-black tabular-nums {{ $tk->jenis === 'debit' ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $tk->jenis === 'debit' ? '+' : '-' }}Rp {{ number_format($tk->nominal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Belum ada catatan transaksi buku kas.
                    </div>
                @endforelse
                <div class="p-3 bg-slate-50/50 text-right">
                    <a href="{{ route('admin.bendahara.index') }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-bold hover:underline">
                        Buka Buku Kas Bendahara &rarr;
                    </a>
                </div>
            </div>

            <!-- Tab Content 4: Agenda Pondok Terdekat -->
            <div x-show="tab === 'agenda'" x-cloak class="divide-y divide-slate-100">
                @forelse ($agendasTerdekat as $ag)
                    <a href="{{ route('admin.agenda.show', $ag) }}" class="p-4 hover:bg-slate-50 transition flex items-center justify-between text-xs gap-3 block group">
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-slate-900 group-hover:text-blue-700 transition truncate text-xs sm:text-sm">{{ $ag->judul }}</p>
                            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">{{ $ag->mulai->format('d-m-Y H:i') }} WIB &bull; Lokasi: {{ $ag->lokasi }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">PJ: {{ $ag->pj ? $ag->pj->nama : '-' }}</p>
                        </div>
                        <div class="shrink-0">
                            <span class="px-2.5 py-0.5 bg-purple-50 text-purple-800 border border-purple-200 font-bold rounded-full text-xs">
                                {{ ucfirst($ag->status) }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Belum ada agenda mendatang.
                    </div>
                @endforelse
                <div class="p-3 bg-slate-50/50 text-right">
                    <a href="{{ route('admin.agenda.index') }}" class="text-xs text-purple-700 hover:text-purple-800 font-bold hover:underline">
                        Buka Kalender Agenda Lengkap &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- 7. Baris Pintasan Cepat Pelayanan Sekretariat (Touch-Friendly & Responsif) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-600">Pintasan Cepat Pelayanan</h2>
                <span class="text-xs text-slate-400 hidden sm:inline">Total Arsip Dokumen: <strong class="text-slate-700 font-bold">{{ $totalArsip }} Berkas</strong></span>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                <a href="{{ route('admin.surat-keluar.create') }}"
                   class="flex items-center gap-2.5 sm:gap-3 p-3 sm:p-3.5 min-h-[48px] rounded-xl bg-purple-50/80 hover:bg-purple-100 border border-purple-200/80 text-purple-950 font-bold text-xs sm:text-sm transition active:scale-95 shadow-xs">
                    <span class="w-8 h-8 rounded-lg bg-purple-700 text-white flex items-center justify-center font-extrabold text-sm shrink-0">+</span>
                    <div class="min-w-0">
                        <span class="block truncate">Surat Keluar</span>
                        <span class="text-[11px] sm:text-xs font-normal text-purple-700 truncate hidden sm:block">Nomor Otomatis + QR</span>
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

                <a href="{{ route('admin.bendahara.create') }}"
                   class="flex items-center gap-2.5 sm:gap-3 p-3 sm:p-3.5 min-h-[48px] rounded-xl bg-emerald-50/80 hover:bg-emerald-100 border border-emerald-200/80 text-emerald-950 font-bold text-xs sm:text-sm transition active:scale-95 shadow-xs">
                    <span class="w-8 h-8 rounded-lg bg-emerald-700 text-white flex items-center justify-center font-extrabold text-sm shrink-0">+</span>
                    <div class="min-w-0">
                        <span class="block truncate">Transaksi Kas</span>
                        <span class="text-[11px] sm:text-xs font-normal text-emerald-700 truncate hidden sm:block">Buku Kas Bendahara</span>
                    </div>
                </a>

                <a href="{{ route('admin.agenda.create') }}"
                   class="flex items-center gap-2.5 sm:gap-3 p-3 sm:p-3.5 min-h-[48px] rounded-xl bg-amber-50/80 hover:bg-amber-100 border border-amber-200/80 text-amber-950 font-bold text-xs sm:text-sm transition active:scale-95 shadow-xs">
                    <span class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center font-extrabold text-sm shrink-0">+</span>
                    <div class="min-w-0">
                        <span class="block truncate">Jadwal Agenda</span>
                        <span class="text-[11px] sm:text-xs font-normal text-amber-800 truncate hidden sm:block">Kalender Kegiatan</span>
                    </div>
                </a>
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
                                backgroundColor: 'rgba(5, 150, 105, 0.10)',
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
                                backgroundColor: 'rgba(37, 99, 235, 0.10)',
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
                                backgroundColor: '#0f172a',
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
