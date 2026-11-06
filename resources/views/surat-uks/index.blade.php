<x-app-layout>
    @section('title', 'Surat Izin UKS - SAP Al-Falah Putra')

    <div class="space-y-6">
        <!-- Header & Navigasi Aksi -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <span>Surat Izin UKS (Pulang & Sakit)</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Upload berkas surat fisik asli, penerbitan Kode 4-Digit, penjualan Rp 5.000/lembar, dan integrasi kas otomatis ke Bendahara.
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 lg:flex lg:items-center gap-2 w-full lg:w-auto">
                <button type="button"
                        onclick="openModalGenerate()"
                        class="h-10 px-3 sm:px-4 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition active:scale-95 inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                    <span>Upload Surat</span>
                </button>
                <button type="button"
                        onclick="openModalCatatKode()"
                        class="h-10 px-3 sm:px-4 bg-amber-600 hover:bg-amber-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition active:scale-95 inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                    <span>Catat Terjual</span>
                </button>
                <!-- Tombol Unduh Excel dengan Filter Jenis (Hanya Stok Tersedia) -->
                <div class="relative w-full inline-block" x-data="{ open: false }">
                    <button type="button"
                            @click="open = !open"
                            @click.outside="open = false"
                            class="w-full h-10 px-3 sm:px-4 bg-emerald-800 hover:bg-emerald-900 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition active:scale-95 inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Unduh Excel / CSV</span>
                        <svg class="w-3.5 h-3.5 shrink-0 opacity-70 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 sm:left-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-40 text-xs divide-y divide-slate-100"
                         style="display: none;">
                        <div>
                            <div class="px-3 py-1.5 bg-emerald-50 text-[10px] font-bold text-emerald-800 uppercase tracking-wider flex items-center justify-between">
                                <span>Format Excel (.xls) - Kolom Rapi</span>
                                <span class="text-[9px] font-normal text-emerald-600">Disarankan</span>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('admin.surat-uks.export-excel', ['format' => 'xls']) }}"
                                   @click="open = false"
                                   class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 font-semibold transition">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    <span>Semua Stok Tersedia</span>
                                </a>
                                <a href="{{ route('admin.surat-uks.export-excel', ['format' => 'xls', 'jenis' => 'pulang']) }}"
                                   @click="open = false"
                                   class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 font-semibold transition">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Stok Surat Pulang (SIP)</span>
                                </a>
                                <a href="{{ route('admin.surat-uks.export-excel', ['format' => 'xls', 'jenis' => 'sakit']) }}"
                                   @click="open = false"
                                   class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 font-semibold transition">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    <span>Stok Surat Sakit (SIS)</span>
                                </a>
                            </div>
                        </div>
                        <div>
                            <div class="px-3 py-1.5 bg-slate-50 text-[10px] font-bold text-slate-600 uppercase tracking-wider">
                                <span>Format CSV (.csv)</span>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('admin.surat-uks.export-excel', ['format' => 'csv']) }}"
                                   @click="open = false"
                                   class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-slate-900 font-semibold transition">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    <span>Semua Stok Tersedia</span>
                                </a>
                                <a href="{{ route('admin.surat-uks.export-excel', ['format' => 'csv', 'jenis' => 'pulang']) }}"
                                   @click="open = false"
                                   class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-slate-900 font-semibold transition">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Stok Surat Pulang (SIP)</span>
                                </a>
                                <a href="{{ route('admin.surat-uks.export-excel', ['format' => 'csv', 'jenis' => 'sakit']) }}"
                                   @click="open = false"
                                   class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-slate-900 font-semibold transition">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    <span>Stok Surat Sakit (SIS)</span>
                                </a>
                            </div>
                        </div>
                        <div class="px-3 py-1.5 text-[10px] text-slate-500 bg-slate-50 leading-relaxed">
                            Format .xls langsung terbagi rapi di kolom A, B, C Excel tanpa tanda kutip. Khusus status Tersedia.
                        </div>
                    </div>
                </div>
                <form action="{{ route('admin.surat-uks.sync-kas') }}" method="POST" class="w-full inline-block"
                      data-konfirmasi="Sinkronkan seluruh akumulasi kas bulanan UKS ke Buku Kas Bendahara sekarang?"
                      data-judul="Konfirmasi Sinkronisasi Kas"
                      data-tombol="Ya, Sinkronkan">
                    @csrf
                    <button type="submit"
                            title="Sinkronkan akumulasi kas bulanan ke Bendahara"
                            class="w-full h-10 px-3 sm:px-4 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-bold rounded-xl border border-slate-300 shadow-2xs transition active:scale-95 inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        <span>Sinkron Kas</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- 3 KARTU LAPORAN UTAMA REAL-TIME -->
        <div>
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Statistik UKS Real-Time</span>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/90 rounded-full px-2.5 py-0.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live Otomatis</span>
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- 1. Laporan Surat Terdaftar -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">1. Surat Terdaftar</span>
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="flex items-baseline gap-2">
                            <h3 id="stat-total-tercetak" class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums transition-transform">{{ number_format($totalTercetak) }}</h3>
                            <span class="text-xs text-slate-500 font-medium">lembar total</span>
                        </div>
                        <div class="mt-2.5 pt-2.5 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                            <span class="text-slate-600 truncate">Pulang: <strong id="stat-cetak-pulang" class="text-emerald-700 tabular-nums">{{ number_format($totalCetakPulang) }}</strong></span>
                            <span class="text-slate-600 truncate text-right">Sakit: <strong id="stat-cetak-sakit" class="text-blue-700 tabular-nums">{{ number_format($totalCetakSakit) }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- 2. Laporan Kas Masuk (Otomatis Masuk Bendahara) -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-emerald-300 shadow-xs flex flex-col justify-between relative overflow-hidden bg-gradient-to-br from-white to-emerald-50/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">2. Kas Masuk (Rp 5.000)</span>
                            <span class="block text-[10px] text-emerald-600 font-medium">Sinkron ke Buku Kas Bendahara</span>
                        </div>
                        <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="flex items-baseline gap-2">
                            <h3 id="stat-total-kas" class="text-2xl sm:text-3xl font-black text-emerald-700 tabular-nums transition-transform">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</h3>
                            <span id="stat-jumlah-terjual" class="text-xs text-emerald-600 font-semibold tabular-nums">({{ number_format($jumlahTerjual) }} lbr)</span>
                        </div>
                        <div class="mt-2.5 pt-2.5 border-t border-emerald-100 grid grid-cols-2 gap-2 text-[11px]">
                            <span class="text-slate-600 truncate">SIP: <strong id="stat-kas-pulang" class="text-emerald-700 tabular-nums">Rp {{ number_format($kasMasukPulang, 0, ',', '.') }}</strong></span>
                            <span class="text-slate-600 truncate text-right">SIS: <strong id="stat-kas-sakit" class="text-blue-700 tabular-nums">Rp {{ number_format($kasMasukSakit, 0, ',', '.') }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- 3. Laporan Sisa Stok Blangko -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">3. Sisa Stok Tersedia</span>
                        <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="flex items-baseline gap-2">
                            <h3 id="stat-total-sisa-stok" class="text-2xl sm:text-3xl font-black text-amber-600 tabular-nums transition-transform">{{ number_format($totalSisaStok) }}</h3>
                            <span class="text-xs text-slate-500 font-medium">lembar tersedia</span>
                        </div>
                        <div class="mt-2.5 pt-2.5 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                            <span class="text-slate-600 truncate">Pulang: <strong id="stat-stok-pulang" class="text-emerald-700 tabular-nums">{{ number_format($stokPulang) }}</strong></span>
                            <span class="text-slate-600 truncate text-right">Sakit: <strong id="stat-stok-sakit" class="text-blue-700 tabular-nums">{{ number_format($stokSakit) }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Tab Pencarian -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <!-- Filter Tab Status -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
                    <a href="{{ route('admin.surat-uks.index') }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ !request('status') && !request('jenis') ? 'bg-emerald-700 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Dokumen
                    </a>
                    <a href="{{ route('admin.surat-uks.index', ['status' => 'tersedia']) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('status') === 'tersedia' ? 'bg-amber-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        <span id="stat-tab-stok-tersedia">Stok Tersedia ({{ $totalSisaStok }})</span>
                    </a>
                    <a href="{{ route('admin.surat-uks.index', ['status' => 'terjual']) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('status') === 'terjual' ? 'bg-emerald-700 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Terjual / Terpakai
                    </a>
                    <a href="{{ route('admin.surat-uks.index', ['status' => 'kembali']) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('status') === 'kembali' ? 'bg-purple-700 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Telah Kembali
                    </a>
                    <a href="{{ route('admin.surat-uks.index', ['jenis' => 'pulang']) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('jenis') === 'pulang' ? 'bg-emerald-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Izin Pulang
                    </a>
                    <a href="{{ route('admin.surat-uks.index', ['jenis' => 'sakit']) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('jenis') === 'sakit' ? 'bg-blue-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Izin Sakit
                    </a>
                </div>

                <!-- Form Search -->
                <form action="{{ route('admin.surat-uks.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
                    @if (request('jenis')) <input type="hidden" name="jenis" value="{{ request('jenis') }}"> @endif
                    @if (request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    <div class="relative flex-1 md:w-64">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari kode/nama santri..."
                               class="w-full text-xs rounded-xl border-slate-200 pl-8 pr-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button type="submit" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition shrink-0">
                        Cari
                    </button>
                    @if (request('search') || request('jenis') || request('status'))
                        <a href="{{ route('admin.surat-uks.index') }}" class="px-2 py-2 text-slate-500 hover:text-slate-800 text-xs font-medium shrink-0">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- 1. TAMPILAN TABEL KHUSUS DESKTOP (hidden md:block) -->
        <div class="hidden md:block bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">Tgl. Terdaftar</th>
                            <th class="py-3 px-4">Tgl. Beli</th>
                            <th class="py-3 px-4">Nama Santri</th>
                            <th class="py-3 px-4">Kode Surat & Jenis</th>
                            <th class="py-3 px-4">Berkas Asli</th>
                            <th class="py-3 px-4">Kelas & Asrama</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($suratUks as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 whitespace-nowrap text-slate-500 font-medium">
                                    {{ $item->tanggal_cetak ? $item->tanggal_cetak->format('d-m-Y') : '-' }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if ($item->tanggal_pembelian)
                                        <span class="font-semibold text-emerald-800">{{ $item->tanggal_pembelian->format('d-m-Y') }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Belum Dibeli</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if ($item->nama_santri)
                                        <span class="font-bold text-slate-900 block text-xs">{{ $item->nama_santri }}</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500 italic">
                                            (Belum Terjual)
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-300 text-emerald-950 font-mono text-xs font-black tracking-widest">
                                                {{ $item->kode_verifikasi }}
                                            </span>
                                            <span class="text-[10px] font-bold {{ $item->jenis === 'sakit' ? 'text-blue-700 bg-blue-50 border-blue-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200' }} px-2 py-0.5 rounded-md border">
                                                {{ $item->jenis_label }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if ($item->file_surat)
                                        <a href="{{ $item->file_url }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-700 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2 py-1 rounded-lg transition">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Lihat ({{ strtoupper($item->file_ext ?: 'FILE') }})</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Tanpa File</span>
                                    @endif

                                    @if ($item->file_bukti_terjual)
                                        <div class="mt-1">
                                            <a href="{{ $item->file_bukti_url }}" target="_blank"
                                               class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">
                                                <span>Bukti Terjual</span>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-600 font-medium">
                                    @if ($item->kelas || $item->asrama)
                                        <span>{{ $item->kelas ?: '-' }} &bull; {{ $item->asrama ?: '-' }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if ($item->status === 'tersedia')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Tersedia (Stok)
                                        </span>
                                    @elseif ($item->status === 'terjual')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Terjual (Rp 5.000)
                                        </span>
                                    @elseif ($item->status === 'kembali')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            Telah Kembali
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if ($item->status === 'tersedia')
                                            <!-- Tombol Catat Terjual: Membuka modal dengan Kode otomatis terisi -->
                                            <button type="button"
                                                    onclick="openModalCatatKode('{{ $item->kode_verifikasi }}')"
                                                    class="h-8 px-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-lg font-bold text-[11px] shadow-2xs transition active:scale-95 inline-flex items-center gap-1 whitespace-nowrap">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                                                <span>Catat Terjual</span>
                                            </button>
                                        @elseif ($item->status === 'terjual')
                                            <!-- Tombol Tandai Kembali -->
                                            <form action="{{ route('admin.surat-uks.kembali', $item) }}" method="POST" class="inline"
                                                  data-konfirmasi="Tandai santri ini telah kembali ke pondok sekarang?"
                                                  data-judul="Konfirmasi Santri Kembali"
                                                  data-tombol="Ya, Sudah Kembali">
                                                @csrf
                                                <button type="submit"
                                                        class="h-8 px-2.5 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white rounded-lg font-bold text-[11px] shadow-2xs transition active:scale-95 inline-flex items-center gap-1 whitespace-nowrap">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                                    <span>Tandai Kembali</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Hapus -->
                                        <form action="{{ route('admin.surat-uks.destroy', $item) }}"
                                              method="POST"
                                              class="inline"
                                              data-konfirmasi="Apakah Anda yakin ingin menghapus surat Kode {{ $item->kode_verifikasi }}? Transaksi kas terkait juga akan disesuaikan."
                                              data-judul="Konfirmasi Hapus Surat"
                                              data-tombol="Ya, Hapus Surat">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Hapus Surat"
                                                    class="w-8 h-8 text-slate-400 hover:text-rose-600 bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-200 rounded-lg transition shadow-2xs inline-flex items-center justify-center active:scale-95">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                    Belum ada data surat UKS. Silakan klik tombol <strong>"+ Upload / Buat Surat"</strong> untuk mendaftarkan surat baru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($suratUks->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $suratUks->links() }}
                </div>
            @endif
        </div>

        <!-- 2. TAMPILAN KARTU RAPI KHUSUS MOBILE / SMARTPHONE (block md:hidden) -->
        <div class="block md:hidden space-y-3">
            @forelse ($suratUks as $item)
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs space-y-3">
                    <!-- Baris Atas: Kode 4-Digit, Jenis & Status -->
                    <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-2.5">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-300 text-emerald-950 font-mono text-sm font-black tracking-widest">
                                    KODE: {{ $item->kode_verifikasi }}
                                </span>
                                <span class="text-[10px] font-bold {{ $item->jenis === 'sakit' ? 'text-blue-700 bg-blue-50 border-blue-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200' }} px-2 py-0.5 rounded-md border">
                                    {{ $item->jenis_label }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if ($item->status === 'tersedia')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    Tersedia
                                </span>
                            @elseif ($item->status === 'terjual')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Terjual
                                </span>
                            @elseif ($item->status === 'kembali')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    Kembali
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Informasi Santri & Detail Surat -->
                    <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-100 space-y-1.5 text-xs">
                        @if ($item->nama_santri)
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 font-medium w-16 shrink-0">Santri</span>
                                <span class="text-slate-900 font-bold">: {{ $item->nama_santri }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 font-medium w-16 shrink-0">Kelas/Kamar</span>
                                <span class="text-slate-700 font-semibold">: {{ $item->kelas ?: '-' }} &bull; {{ $item->asrama ?: '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 font-medium w-16 shrink-0">Tgl. Beli</span>
                                <span class="text-emerald-800 font-bold">: {{ $item->tanggal_pembelian ? $item->tanggal_pembelian->format('d-m-Y') : '-' }}</span>
                            </div>
                        @else
                            <div class="py-1 text-center">
                                <span class="text-slate-400 italic font-medium">Stok Kosong (Belum Terjual)</span>
                            </div>
                        @endif

                        @if ($item->file_surat)
                            <div class="pt-1 border-t border-slate-200/50 flex items-center justify-between">
                                <span class="text-slate-500 text-[11px]">Berkas Asli:</span>
                                <a href="{{ $item->file_url }}" target="_blank" class="text-blue-600 font-bold text-[11px] hover:underline flex items-center gap-1">
                                    <span>Lihat File ({{ strtoupper($item->file_ext) }})</span>
                                </a>
                            </div>
                        @endif

                        @if ($item->file_bukti_terjual)
                            <div class="pt-1 border-t border-slate-200/50 flex items-center justify-between">
                                <span class="text-emerald-700 text-[11px] font-semibold">Bukti Terjual:</span>
                                <a href="{{ $item->file_bukti_url }}" target="_blank" class="text-emerald-700 font-bold text-[11px] hover:underline">
                                    <span>Lihat Bukti</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Aksi Mobile -->
                    <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100">
                        <div class="flex-1">
                            @if ($item->status === 'tersedia')
                                <button type="button"
                                        onclick="openModalCatatKode('{{ $item->kode_verifikasi }}')"
                                        class="w-full h-10 px-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl font-bold text-xs shadow-xs transition active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    <span>Catat Terjual</span>
                                </button>
                            @elseif ($item->status === 'terjual')
                                <form action="{{ route('admin.surat-uks.kembali', $item) }}" method="POST" class="w-full"
                                      data-konfirmasi="Tandai santri ini telah kembali ke pondok sekarang?"
                                      data-judul="Konfirmasi Santri Kembali"
                                      data-tombol="Ya, Sudah Kembali">
                                    @csrf
                                    <button type="submit"
                                            class="w-full h-10 px-3 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white rounded-xl font-bold text-xs shadow-xs transition active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                        <span>Tandai Kembali</span>
                                    </button>
                                </form>
                            @else
                                <div class="w-full h-10 px-3 bg-slate-100 rounded-xl flex items-center justify-center text-xs font-semibold text-slate-400 italic">
                                    Sudah Kembali
                                </div>
                            @endif
                        </div>

                        <!-- Ikon Hapus -->
                        <div class="flex items-center shrink-0">
                            <form action="{{ route('admin.surat-uks.destroy', $item) }}"
                                  method="POST"
                                  class="inline"
                                  data-konfirmasi="Apakah Anda yakin ingin menghapus surat Kode {{ $item->kode_verifikasi }}? Transaksi kas terkait juga akan disesuaikan."
                                  data-judul="Konfirmasi Hapus Surat"
                                  data-tombol="Ya, Hapus Surat">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        title="Hapus Surat"
                                        class="w-10 h-10 text-slate-400 hover:text-rose-600 bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-200 rounded-xl transition shadow-2xs flex items-center justify-center active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 text-xs">
                    Belum ada data surat UKS. Silakan klik tombol <strong>"+ Upload / Buat Surat"</strong> untuk mendaftarkan surat baru.
                </div>
            @endforelse

            @if ($suratUks->hasPages())
                <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-xs">
                    {{ $suratUks->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL 1: UPLOAD BERKAS / DAFTARKAN SURAT UKS BARU -->
    <div id="modal-generate" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-4 sm:p-6 shadow-2xl border border-slate-200 space-y-4 max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </span>
                    <span>Upload & Daftarkan Surat UKS</span>
                </h3>
                <button type="button" onclick="closeModalGenerate()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.surat-uks.generate-blangko') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Surat Izin *</label>
                    <select name="jenis" required class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="pulang">Surat Izin Pulang (SIP)</option>
                        <option value="sakit">Surat Izin Sakit (SIS)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Upload Berkas Surat Asli (PDF, Word, atau Gambar)
                    </label>
                    <div class="border-2 border-dashed border-emerald-300 hover:border-emerald-500 bg-emerald-50/30 rounded-2xl p-4 text-center cursor-pointer transition">
                        <input type="file" id="berkas-surat-input" name="berkas_surat[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                               class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-700 file:text-white hover:file:bg-emerald-800 cursor-pointer">
                        <p class="text-[11px] text-slate-600 mt-2">
                            Pilih banyak berkas (PDF, Word, Gambar). Tiap berkas otomatis dapat <strong>1 Kode 4-Digit</strong>.
                        </p>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Atau Buat Kode Stok Kosong Tanpa File
                    </label>
                    <input type="number" name="jumlah" value="0" min="0" max="100"
                           class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Isi angka lembar jika hanya ingin menerbitkan kode tanpa upload berkas">
                    <p class="text-xs font-bold text-rose-600 mt-1.5 flex items-center gap-1.5 bg-rose-50 border border-rose-200 rounded-lg px-2.5 py-1.5">
                        <svg class="w-4 h-4 shrink-0 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span><strong>PERHATIAN:</strong> Biarkan angka <strong>0</strong> jika Anda memilih/mengunggah berkas di atas.</span>
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal</label>
                    <input type="date" name="tanggal_cetak" value="{{ date('Y-m-d') }}" required
                           class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModalGenerate()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                        Upload & Terbitkan Kode
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: CATAT TERJUAL DENGAN VERIFIKASI KODE 4-DIGIT INSTAN -->
    <div id="modal-catat-kode" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-4 sm:p-6 shadow-2xl border border-slate-200 space-y-3.5 max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </span>
                    <span>Pencatatan Surat Terjual</span>
                </h3>
                <button type="button" onclick="closeModalCatatKode()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.surat-uks.catat-terjual-kode') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf
                <div class="bg-emerald-50/80 border border-emerald-200 rounded-xl p-3 text-xs text-emerald-900 space-y-1">
                    <span class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Kas Masuk Otomatis: Rp 5.000,-</span>
                    </span>
                    <p class="text-[11px] leading-relaxed">
                        Saat disimpan, kas masuk otomatis terakumulasi di Buku Kas Bendahara.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Verifikasi (4 Digit) *</label>
                    <input type="text" id="catat-input-kode" name="kode_verifikasi" maxlength="4" required placeholder="Contoh: 7F2B"
                           class="w-full text-base font-mono font-bold uppercase rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                    <div id="catat-status-kode" class="mt-1.5 min-h-[22px] flex items-center">
                        <span class="text-[11px] text-slate-500">Ketik 4 digit kode yang tertera di surat fisik santri.</span>
                    </div>
                </div>

                <div class="relative">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Santri *</label>
                        <span class="text-[10.5px] text-emerald-700 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Ketik untuk saran otomatis</span>
                        </span>
                    </div>
                    <input type="text" id="input-nama-santri-kode" name="nama_santri" required autocomplete="off" placeholder="Ketik nama santri..."
                           class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                    <div id="dropdown-santri-kode" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-44 overflow-y-auto z-50 divide-y divide-slate-100 text-xs"></div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kelas *</label>
                        <input type="text" id="input-kelas-kode" name="kelas" required placeholder="Contoh: 3 Aliyah"
                               class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Asrama / Kamar *</label>
                        <input type="text" id="input-asrama-kode" name="asrama" required placeholder="Contoh: Kamar B-04"
                               class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Upload Bukti Surat Fisik (Opsional)</label>
                    <input type="file" name="file_bukti" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                           class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                    <span class="text-[10.5px] text-slate-400 mt-0.5 block">Unggah foto atau scan surat fisik yang telah diisi tulisan tangan.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Pembelian</label>
                    <input type="date" name="tanggal_pembelian" value="{{ date('Y-m-d') }}"
                           class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModalCatatKode()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" id="btn-submit-catat" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                        Catat & Masukkan Kas
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const santriList = {!! json_encode($santriJson ?? []) !!};

        function setupSantriAutocomplete(inputId, dropdownId, kelasId, asramaId) {
            const inputNama = document.getElementById(inputId);
            const dropdown = document.getElementById(dropdownId);
            const inputKelas = document.getElementById(kelasId);
            const inputAsrama = document.getElementById(asramaId);

            if (!inputNama || !dropdown) return;

            function renderSuggestions(query) {
                const q = query.trim().toLowerCase();
                if (q.length < 1) {
                    dropdown.innerHTML = '';
                    dropdown.classList.add('hidden');
                    return;
                }

                const matches = santriList.filter(s => s.nama.toLowerCase().includes(q)).slice(0, 8);
                if (matches.length === 0) {
                    dropdown.innerHTML = `
                        <div class="px-3 py-2 text-slate-400 italic">
                            Nama santri tidak ditemukan di master data (bisa tetap diketik manual).
                        </div>
                    `;
                    dropdown.classList.remove('hidden');
                    return;
                }

                dropdown.innerHTML = matches.map(s => `
                    <button type="button" class="w-full text-left px-3 py-2 hover:bg-emerald-50 hover:text-emerald-950 transition flex items-center justify-between group cursor-pointer"
                            onclick="pilihSantri('${escapeJs(s.nama)}', '${escapeJs(s.kelas)}', '${escapeJs(s.asrama)}', '${inputId}', '${dropdownId}', '${kelasId}', '${asramaId}')">
                        <div>
                            <div class="font-bold text-slate-800 group-hover:text-emerald-800">${s.nama}</div>
                            <div class="text-[11px] text-slate-500">${s.kelas ? 'Kelas ' + s.kelas : '-'} &bull; ${s.asrama ? s.asrama : '-'}</div>
                        </div>
                        <span class="text-[10px] text-emerald-700 bg-emerald-100 group-hover:bg-emerald-200 px-1.5 py-0.5 rounded font-semibold">Pilih</span>
                    </button>
                `).join('');
                dropdown.classList.remove('hidden');
            }

            inputNama.addEventListener('input', function() {
                renderSuggestions(this.value);
                const exact = santriList.find(s => s.nama.toLowerCase() === this.value.trim().toLowerCase());
                if (exact) {
                    if (inputKelas) inputKelas.value = exact.kelas;
                    if (inputAsrama) inputAsrama.value = exact.asrama;
                }
            });

            inputNama.addEventListener('focus', function() {
                if (this.value.trim().length >= 1) {
                    renderSuggestions(this.value);
                }
            });

            document.addEventListener('click', function(e) {
                if (!inputNama.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }

        function escapeJs(str) {
            return (str || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
        }

        function pilihSantri(nama, kelas, asrama, inputId, dropdownId, kelasId, asramaId) {
            const inputNama = document.getElementById(inputId);
            const dropdown = document.getElementById(dropdownId);
            const inputKelas = document.getElementById(kelasId);
            const inputAsrama = document.getElementById(asramaId);

            if (inputNama) inputNama.value = nama;
            if (inputKelas) inputKelas.value = kelas;
            if (inputAsrama) inputAsrama.value = asrama;
            if (dropdown) dropdown.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupSantriAutocomplete('input-nama-santri-kode', 'dropdown-santri-kode', 'input-kelas-kode', 'input-asrama-kode');

            // Setup Real-time Check Kode Verifikasi
            const inputKode = document.getElementById('catat-input-kode');
            const statusKode = document.getElementById('catat-status-kode');
            const btnSubmitCatat = document.getElementById('btn-submit-catat');

            let timeoutKode = null;
            if (inputKode) {
                inputKode.addEventListener('input', function() {
                    const val = this.value.trim().toUpperCase();
                    this.value = val;
                    clearTimeout(timeoutKode);

                    if (val.length < 4) {
                        statusKode.innerHTML = '<span class="text-[11px] text-slate-500">Ketik 4 digit kode yang tertera di surat fisik santri.</span>';
                        return;
                    }

                    statusKode.innerHTML = '<span class="text-[11px] text-slate-400 font-semibold animate-pulse">Memeriksa status kode ' + val + '...</span>';

                    timeoutKode = setTimeout(() => {
                        fetch('{{ route('admin.surat-uks.cek-kode') }}?kode=' + encodeURIComponent(val))
                            .then(res => res.json())
                            .then(data => {
                                if (!data.found) {
                                    statusKode.innerHTML = '<span class="px-2.5 py-1 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 font-bold text-xs flex items-center gap-1.5"><svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg><span>' + data.pesan + '</span></span>';
                                } else if (data.status === 'tersedia') {
                                    statusKode.innerHTML = '<span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-300 text-emerald-800 font-bold text-xs flex items-center gap-1.5"><svg class="w-3.5 h-3.5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>' + data.pesan + '</span></span>';
                                } else if (data.status === 'terjual') {
                                    statusKode.innerHTML = '<span class="px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-300 text-amber-800 font-bold text-xs flex items-center gap-1.5"><svg class="w-3.5 h-3.5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg><span>' + data.pesan + '</span></span>';
                                } else {
                                    statusKode.innerHTML = '<span class="px-2.5 py-1 rounded-lg bg-purple-50 border border-purple-200 text-purple-700 font-bold text-xs flex items-center gap-1.5"><svg class="w-3.5 h-3.5 shrink-0 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>' + data.pesan + '</span></span>';
                                }
                            })
                            .catch(() => {
                                statusKode.innerHTML = '<span class="text-[11px] text-rose-500">Gagal memeriksa kode. Silakan coba lagi.</span>';
                            });
                    }, 300);
                });
            }

            // Real-Time Auto-Update Stats Polling (Setiap 4 Detik & Saat Tab Aktif)
            const STATS_URL = '{{ route('admin.surat-uks.stats') }}';
            let isPollingActive = true;

            function formatRupiah(num) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
            }
            function formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }

            function updateElementIfChanged(id, newText) {
                const el = document.getElementById(id);
                if (!el) return;
                const currentText = el.textContent.trim();
                const targetText = String(newText).trim();
                if (currentText !== targetText) {
                    el.textContent = targetText;
                    el.classList.add('transition-all', 'duration-300', 'text-emerald-500', 'scale-105');
                    setTimeout(() => {
                        el.classList.remove('text-emerald-500', 'scale-105');
                    }, 800);
                }
            }

            async function fetchRealtimeStats() {
                if (!isPollingActive || document.hidden) return;
                try {
                    const res = await fetch(STATS_URL, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!res.ok) return;
                    const data = await res.json();

                    // 1. Surat Terdaftar
                    updateElementIfChanged('stat-total-tercetak', formatNumber(data.total_tercetak));
                    updateElementIfChanged('stat-cetak-pulang', formatNumber(data.total_cetak_pulang));
                    updateElementIfChanged('stat-cetak-sakit', formatNumber(data.total_cetak_sakit));

                    // 2. Kas Masuk
                    updateElementIfChanged('stat-total-kas', formatRupiah(data.total_kas_masuk));
                    updateElementIfChanged('stat-jumlah-terjual', '(' + formatNumber(data.jumlah_terjual) + ' lbr)');
                    updateElementIfChanged('stat-kas-pulang', formatRupiah(data.kas_masuk_pulang));
                    updateElementIfChanged('stat-kas-sakit', formatRupiah(data.kas_masuk_sakit));

                    // 3. Sisa Stok
                    updateElementIfChanged('stat-total-sisa-stok', formatNumber(data.total_sisa_stok));
                    updateElementIfChanged('stat-stok-pulang', formatNumber(data.stok_pulang));
                    updateElementIfChanged('stat-stok-sakit', formatNumber(data.stok_sakit));
                    updateElementIfChanged('stat-tab-stok-tersedia', 'Stok Tersedia (' + formatNumber(data.total_sisa_stok) + ')');
                } catch (err) {
                    // Ignore silent network failure
                }
            }

            // Jalankan polling berkala setiap 4 detik
            setInterval(fetchRealtimeStats, 4000);

            // Fetch instan saat tab browser dibuka kembali
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) fetchRealtimeStats();
            });
        });

        function openModalGenerate() {
            document.getElementById('modal-generate').classList.remove('hidden');
        }
        function closeModalGenerate() {
            document.getElementById('modal-generate').classList.add('hidden');
        }

        function openModalCatatKode(prefillKode = '') {
            const inputKode = document.getElementById('catat-input-kode');
            const inputNama = document.getElementById('input-nama-santri-kode');
            const inputKelas = document.getElementById('input-kelas-kode');
            const inputAsrama = document.getElementById('input-asrama-kode');
            const dropdown = document.getElementById('dropdown-santri-kode');

            if (inputNama) inputNama.value = '';
            if (inputKelas) inputKelas.value = '';
            if (inputAsrama) inputAsrama.value = '';
            if (dropdown) dropdown.classList.add('hidden');

            document.getElementById('modal-catat-kode').classList.remove('hidden');

            if (inputKode) {
                inputKode.value = prefillKode;
                if (prefillKode) {
                    inputKode.dispatchEvent(new Event('input'));
                    if (inputNama) setTimeout(() => inputNama.focus(), 150);
                } else {
                    setTimeout(() => inputKode.focus(), 100);
                }
            }
        }
        function closeModalCatatKode() {
            document.getElementById('modal-catat-kode').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
