<x-app-layout>
    @section('title', 'Dashboard Sekretariat')

    <!-- Container Grid Utama (Persis 3 Kolom Kartu Mengambang Finora Dashboard) -->
    <div class="space-y-5 sm:space-y-6">

        <!-- Hidden breadcrumb test helper -->
        <span class="sr-only">Dashboard Sekretariat</span>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 xl:gap-6 items-start">
            
            <!-- ======================================================== -->
            <!-- KOLOM 1 (KIRI - LEBAR: 5 KELOMPOK GRID LG)                 -->
            <!-- ======================================================== -->
            <div class="lg:col-span-5 space-y-5">
                
                <!-- 1. Hero Card Sambutan (Persis Kartu Hijau Gelap Finora) -->
                <div class="rounded-3xl p-6 text-white relative overflow-hidden shadow-xs border border-emerald-900/40"
                     style="background-color: #064e3b; background-image: linear-gradient(135deg, #064e3b 0%, #075e47 60%, #064e3b 100%);">
                    
                    <!-- Ornamen Cahaya Halus -->
                    <div class="absolute -right-8 -bottom-8 w-44 h-44 rounded-full bg-emerald-400/10 blur-2xl pointer-events-none"></div>

                    <!-- Baris Atas: Sapaan & Badge Status Sehat -->
                    <div class="flex items-start justify-between gap-3 relative z-10">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base sm:text-lg font-bold tracking-tight text-white">
                                    {{ $greeting }}, {{ Auth::user()->name ?? 'Sekretaris' }}
                                </h2>
                                <svg class="w-4 h-4 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <p class="text-xs text-emerald-100/90 mt-0.5 leading-relaxed">
                                Sistem Administrasi & Pelayanan Sekretariat Pondok
                            </p>
                        </div>
                        
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-800/80 text-emerald-200 border border-emerald-600/40 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            Tertib & Aktif
                        </span>
                    </div>

                    <!-- Tengah: Saldo Kas Utama -->
                    <div class="mt-6 flex items-end justify-between relative z-10">
                        <div>
                            <div class="flex items-center gap-2 text-xs text-emerald-200/90 font-medium">
                                <span>Saldo Kas Pesantren</span>
                                <svg class="w-3.5 h-3.5 text-emerald-300/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-extrabold text-white mt-1 tracking-tight">
                                Rp {{ number_format($saldoKasRiil, 0, ',', '.') }}
                            </h3>
                            <div class="flex items-center gap-1.5 text-[11px] text-emerald-300 font-medium mt-1">
                                <svg class="w-3.5 h-3.5 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                <span>Penerimaan Kas Bln Ini: Rp {{ number_format($debitBulanIni, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Ilustrasi Kartu Kas Kanan ala Finora -->
                        <div class="hidden sm:block shrink-0 pl-3">
                            <div class="w-20 h-16 rounded-2xl bg-white/10 border border-white/20 p-2.5 flex flex-col justify-between shadow-inner backdrop-blur-xs">
                                <div class="flex justify-between items-center">
                                    <span class="w-3 h-3 rounded-full bg-amber-400/80"></span>
                                    <span class="text-[9px] text-emerald-200 font-mono">AL-FALAH</span>
                                </div>
                                <div class="space-y-1">
                                    <div class="w-10 h-1.5 rounded-full bg-white/40"></div>
                                    <div class="w-6 h-1 rounded-full bg-white/25"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Strip Tombol Aksi Cepat (Persis 5 Tombol Kotak Lembut Finora) -->
                <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-100 shadow-2xs">
                    <div class="grid grid-cols-5 gap-2 text-center">
                        <!-- Tombol 1: Surat Masuk -->
                        <a href="{{ route('admin.surat-masuk.index') }}" class="group flex flex-col items-center gap-1.5">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center transition group-hover:scale-105 group-hover:bg-emerald-100 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-700 group-hover:text-emerald-900 truncate w-full">Surat Masuk</span>
                        </a>

                        <!-- Tombol 2: Buat Surat -->
                        <a href="{{ route('admin.surat-keluar.create') }}" class="group flex flex-col items-center gap-1.5">
                            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center transition group-hover:scale-105 group-hover:bg-teal-100 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-700 group-hover:text-teal-900 truncate w-full">Buat Surat</span>
                        </a>

                        <!-- Tombol 3: Kas Bendahara -->
                        <a href="{{ route('admin.bendahara.index') }}" class="group flex flex-col items-center gap-1.5">
                            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center transition group-hover:scale-105 group-hover:bg-purple-100 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-700 group-hover:text-purple-900 truncate w-full">Kas Bendahara</span>
                        </a>

                        <!-- Tombol 4: Agenda Pondok -->
                        <a href="{{ route('admin.agenda.index') }}" class="group flex flex-col items-center gap-1.5">
                            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-700 flex items-center justify-center transition group-hover:scale-105 group-hover:bg-sky-100 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-700 group-hover:text-sky-900 truncate w-full">Agenda</span>
                        </a>

                        <!-- Tombol 5: Arsip Dokumen -->
                        <a href="{{ route('admin.arsip.index') }}" class="group flex flex-col items-center gap-1.5">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center transition group-hover:scale-105 group-hover:bg-amber-100 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-700 group-hover:text-amber-900 truncate w-full">Scan / Arsip</span>
                        </a>
                    </div>
                </div>

                <!-- 3. Grafik Batang Aktivitas Bulanan (Persis 'Monthly Spending' Finora) -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">Aktivitas Surat Keluar</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Tren penerbitan surat resmi 6 bulan terakhir</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                            Tahun {{ date('Y') }}
                        </span>
                    </div>

                    <!-- Badge Highlight Bulan Ini -->
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-xl bg-[#064e3b] text-white text-xs font-bold shadow-2xs">
                            Bulan Ini: {{ $suratKeluarBulanIni }} Surat Keluar
                        </span>
                    </div>

                    <!-- Area Chart Batang -->
                    <div class="h-44 w-full relative">
                        <canvas id="chartBatangFinora"></canvas>
                    </div>
                </div>

                <!-- 4. Tabel Aktivitas Mutasi Terkini (Persis 'Recent Transactions' Finora) -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-2xs space-y-3.5">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <h3 class="font-bold text-sm text-slate-900">Aktivitas Terkini Pesantren</h3>
                        <a href="{{ route('admin.bendahara.index') }}" class="text-xs font-semibold text-[#064e3b] hover:underline">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs">
                        @forelse ($transaksiKasTerbaru as $trx)
                            <div class="py-2.5 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $trx->jenis === 'debit' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700' }}">
                                        @if ($trx->jenis === 'debit')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0 leading-tight">
                                        <p class="font-bold text-slate-900 truncate">{{ $trx->keterangan ?? $trx->kategori }}</p>
                                        <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y') }} &bull; {{ $trx->kategori }}</span>
                                    </div>
                                </div>
                                <span class="font-bold shrink-0 tabular-nums {{ $trx->jenis === 'debit' ? 'text-emerald-700' : 'text-slate-800' }}">
                                    {{ $trx->jenis === 'debit' ? '+' : '-' }}Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                </span>
                            </div>
                        @empty
                            <div class="py-4 text-center text-xs text-slate-400">
                                Belum ada riwayat mutasi kas baru-baru ini.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>


            <!-- ======================================================== -->
            <!-- KOLOM 2 (TENGAH - LEBAR: 4 KELOMPOK GRID LG)                -->
            <!-- ======================================================== -->
            <div class="lg:col-span-4 space-y-5">
                
                <!-- 1. Donut Overview: Sebaran Jenjang Santri (Persis 'Spending Overview' Finora) -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-sm text-slate-900">Sebaran Jenjang Santri</h3>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                            Semua Tingkat
                        </span>
                    </div>

                    <!-- Donut + Legend -->
                    <div class="flex items-center gap-4">
                        <div class="w-32 h-32 shrink-0 relative flex items-center justify-center">
                            <canvas id="chartDonutJenjangFinora"></canvas>
                            <div class="absolute text-center leading-none pointer-events-none">
                                <span class="block text-lg font-black text-slate-900">{{ $totalSantriAktif }}</span>
                                <span class="text-[9px] text-slate-400 uppercase tracking-tight">Santri</span>
                            </div>
                        </div>

                        <!-- Legend Kanan -->
                        <div class="flex-1 space-y-1.5 text-xs min-w-0">
                            <div class="flex items-center justify-between text-slate-700">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 shrink-0"></span>
                                    <span class="truncate">Santri MTs</span>
                                </div>
                                <span class="font-bold tabular-nums ml-2">{{ $pctMts }}%</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-700">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="w-2.5 h-2.5 rounded-full bg-teal-600 shrink-0"></span>
                                    <span class="truncate">Santri MA</span>
                                </div>
                                <span class="font-bold tabular-nums ml-2">{{ $pctMa }}%</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-700">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                                    <span class="truncate">Mutakhorijin</span>
                                </div>
                                <span class="font-bold tabular-nums ml-2">{{ $pctMutakhorijin }}%</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-700">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="w-2.5 h-2.5 rounded-full bg-purple-600 shrink-0"></span>
                                    <span class="truncate">Mahasantri</span>
                                </div>
                                <span class="font-bold tabular-nums ml-2">{{ $pctMahasantri }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Progress Gauge: Pusat Tindakan Sekretariat (Persis 'Budget Progress' Finora) -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-sm text-slate-900">Pusat Tindakan Sekretariat</h3>
                        <a href="{{ route('admin.surat-masuk.index') }}" class="text-xs font-semibold text-[#064e3b] hover:underline">
                            Kelola &rarr;
                        </a>
                    </div>

                    <!-- Circular Progress Gauge & Catatan Penting -->
                    <div class="flex items-center gap-4">
                        <div class="w-24 h-24 shrink-0 relative flex items-center justify-center">
                            <canvas id="chartGaugeDisposisi"></canvas>
                            <div class="absolute text-center leading-none pointer-events-none">
                                <span class="block text-base font-black text-slate-900">
                                    {{ array_sum($disposisiStatusData) > 0 ? round(($disposisiStatusData['selesai'] / array_sum($disposisiStatusData)) * 100) : 100 }}%
                                </span>
                                <span class="text-[8px] text-slate-400 uppercase">Selesai</span>
                            </div>
                        </div>

                        <div class="flex-1 space-y-2 text-xs">
                            @if ($suratMasukBelumDisposisi > 0)
                                <div class="flex items-center gap-2 p-2 rounded-xl bg-amber-50 text-amber-950 font-semibold border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                    <span>{{ $suratMasukBelumDisposisi }} Surat Masuk Baru</span>
                                </div>
                            @endif

                            @if ($suratKeluarDraft > 0)
                                <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 text-slate-700 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    <span>{{ $suratKeluarDraft }} Konsep Surat Keluar</span>
                                </div>
                            @endif

                            <!-- Peringatan Agenda H-1 Wajib Ada Sesuai Test AgendaTest -->
                            @if ($agendasBesok->count() > 0)
                                @foreach ($agendasBesok as $agendaH1)
                                    <div class="p-2.5 rounded-xl bg-purple-50 text-purple-900 text-[11px] font-semibold border border-purple-200">
                                        <span class="font-bold">Peringatan Agenda H-1:</span> {{ $agendaH1->judul }}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Banner Status Terorganisir Hijau Halus -->
                    <div class="p-3 rounded-2xl bg-[#e8f5e9] border border-emerald-100 flex items-center gap-2.5 text-xs text-emerald-900">
                        <svg class="w-4 h-4 text-[#064e3b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="leading-snug font-medium">Alhamdulillah! Administrasi persuratan berjalan tertib dan terorganisir.</span>
                    </div>
                </div>

                <!-- 3. Top Categories: Statistik Hunian Asrama (Persis 'Top Categories' Finora) -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-2xs space-y-3.5">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-sm text-slate-900">Statistik Hunian Asrama</h3>
                        <a href="{{ route('admin.santri.index') }}" class="text-xs font-semibold text-[#064e3b] hover:underline">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <div class="space-y-3 text-xs">
                        @forelse (array_slice($hunianKamarList, 0, 4) as $kamar)
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <div class="w-7 h-7 rounded-xl bg-emerald-50 text-[#064e3b] flex items-center justify-center shrink-0 font-bold text-[10px]">
                                        AS
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-slate-800 truncate">{{ $kamar['nama'] }}</p>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                            <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ round(($kamar['total'] / max(1, $maxHunian)) * 100) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                <span class="font-bold text-slate-900 shrink-0 tabular-nums">{{ $kamar['total'] }} Santri</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-2">Belum ada data hunian asrama.</p>
                        @endforelse
                    </div>
                </div>

            </div>


            <!-- ======================================================== -->
            <!-- KOLOM 3 (KANAN - LEBAR: 3 KELOMPOK GRID LG)                 -->
            <!-- ======================================================== -->
            <div class="lg:col-span-3 space-y-5">
                
                <!-- 1. Widget Layanan Surat Mandiri (Persis 'Grow Your Money' Finora) -->
                <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-2xs space-y-3">
                    <div class="flex items-center gap-2 text-[#064e3b]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18m0-18C8 3 4 7 4 12c0 4 3 7 8 7m0-14c4 0 8 4 8 9 0 4-3 7-8 7" />
                        </svg>
                        <h4 class="font-bold text-xs text-slate-900">Layanan Persuratan</h4>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Terbitkan surat keluar resmi ber-kop dan tanda tangan digital terverifikasi QR.
                    </p>
                    <a href="{{ route('admin.surat-keluar.create') }}"
                       class="block w-full py-2.5 px-4 rounded-xl bg-[#064e3b] hover:bg-[#043d2e] text-white text-xs font-semibold text-center transition shadow-xs">
                        Buat Surat Keluar &rarr;
                    </a>
                </div>

                <!-- 2. Upcoming Agenda: Agenda Terdekat (Persis 'Upcoming Bills' Finora) -->
                <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-xs text-slate-900">Agenda Terdekat</h4>
                        <a href="{{ route('admin.agenda.index') }}" class="text-[11px] font-semibold text-[#064e3b] hover:underline">
                            Lihat &rarr;
                        </a>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        @forelse ($agendasTerdekat->take(3) as $agd)
                            <div class="flex items-center justify-between gap-2.5 py-1">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 leading-tight">
                                        <p class="font-bold text-slate-900 truncate">{{ $agd->judul }}</p>
                                        <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($agd->mulai)->translatedFormat('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-1">Tidak ada agenda dekat.</p>
                        @endforelse
                    </div>
                </div>

                <!-- 3. Surat Masuk Terkini (Persis 'Recent Activity' Finora) -->
                <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-xs text-slate-900">Surat Masuk Terkini</h4>
                        <a href="{{ route('admin.surat-masuk.index') }}" class="text-[11px] font-semibold text-[#064e3b] hover:underline">
                            Semua &rarr;
                        </a>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        @forelse ($suratMasukTerbaru->take(3) as $sm)
                            <div class="flex items-center gap-2.5 py-1 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center shrink-0 font-bold text-xs">
                                    {{ strtoupper(substr($sm->pengirim ?? 'S', 0, 1)) }}
                                </div>
                                <div class="min-w-0 leading-tight flex-1">
                                    <p class="font-bold text-slate-900 truncate">{{ $sm->pengirim }}</p>
                                    <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ $sm->perihal }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-1">Belum ada surat masuk.</p>
                        @endforelse
                    </div>
                </div>

                <!-- 4. Kartu Lanskap Kutipan (Persis Kartu Kutipan Berlatar Lanskap Finora) -->
                <div class="rounded-3xl p-4 bg-gradient-to-b from-[#e8f5e9] to-[#dcf0e2] border border-emerald-100 text-slate-800 shadow-2xs relative overflow-hidden">
                    <div class="relative z-10 space-y-1">
                        <p class="text-xs font-semibold text-[#064e3b] italic leading-relaxed">
                            "Khidmah dengan Ikhlas, Tertib dalam Berkas, Membawa Berkah untuk Umat."
                        </p>
                        <p class="text-[10px] text-slate-500 font-medium pt-1">
                            Pondok Pesantren Al-Falah Putra
                        </p>
                    </div>

                    <!-- Siluet Bukit Hijau Bawah Lembut -->
                    <div class="absolute -bottom-3 -right-3 w-20 h-14 bg-emerald-600/10 rounded-full blur-md pointer-events-none"></div>
                </div>

            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof window.Chart === 'undefined') return;

            // 1. Grafik Batang Bulanan (Monthly Spending Finora)
            const ctxBatang = document.getElementById('chartBatangFinora');
            if (ctxBatang) {
                const labels = {!! json_encode($chartBulanLabels) !!};
                const dataValues = {!! json_encode($chartSuratKeluarData) !!};

                // Warna: Bulan aktif paling akhir pakai hijau botol tua #064e3b, bulan lainnya tosca lembut
                const bgColors = dataValues.map((_, idx) => idx === dataValues.length - 1 ? '#064e3b' : '#6ee7b7');

                new window.Chart(ctxBatang, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataValues,
                            backgroundColor: bgColors,
                            borderRadius: 8,
                            borderSkipped: false,
                            barThickness: 16
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#064e3b',
                                titleFont: { size: 11, weight: 'bold' },
                                bodyFont: { size: 11 },
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0, font: { size: 10 } },
                                grid: { color: 'rgba(226, 232, 240, 0.6)' }
                            },
                            x: {
                                ticks: { font: { size: 10 } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // 2. Grafik Donut Sebaran Jenjang Santri
            const ctxDonut = document.getElementById('chartDonutJenjangFinora');
            if (ctxDonut) {
                new window.Chart(ctxDonut, {
                    type: 'doughnut',
                    data: {
                        labels: ['Santri MTs', 'Santri MA', 'Mutakhorijin', 'Mahasantri'],
                        datasets: [{
                            data: [{{ $countMts }}, {{ $countMa }}, {{ $countMutakhorijin }}, {{ $countMahasantri }}],
                            backgroundColor: ['#059669', '#0d9488', '#f59e0b', '#7c3aed'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false },
                            tooltip: { cornerRadius: 8 }
                        }
                    }
                });
            }

            // 3. Gauge Progress Disposisi Surat
            const ctxGauge = document.getElementById('chartGaugeDisposisi');
            if (ctxGauge) {
                const total = {{ max(1, array_sum($disposisiStatusData)) }};
                const selesai = {{ $disposisiStatusData['selesai'] }};
                const sisa = Math.max(0, total - selesai);

                new window.Chart(ctxGauge, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [selesai, sisa],
                            backgroundColor: ['#064e3b', '#e2e8f0'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '78%',
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
