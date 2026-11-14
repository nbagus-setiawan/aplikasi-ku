@extends('layouts.app')

@section('title', 'Laporan Neraca - Bendahara Al-Falah Putra')

@section('content')
<div class="space-y-6" x-data="neracaApp()">

    <!-- ========================================================================= -->
    <!-- HEADER & AKSI UTAMA                                                       -->
    <!-- ========================================================================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
        <div class="min-w-0">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Akuntansi & Keuangan
                </span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="text-xs font-semibold text-slate-500">Pondok Pesantren Al-Falah Putra</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Laporan Neraca (Posisi Keuangan)</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5 leading-relaxed">
                Posisi sumber daya ekonomi, kekayaan kas, kewajiban, dan ekuitas saldo dana pesantren 
                <strong class="text-slate-800 font-semibold">{{ $labelPeriode }}</strong>.
            </p>
        </div>

        <!-- Tombol Unduh PDF -->
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('admin.bendahara.neraca.cetak', request()->query()) }}"
               target="_blank"
               class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs sm:text-sm shadow-xs transition">
                <svg class="w-4 h-4 text-blue-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Laporan PDF</span>
            </a>
        </div>
    </div>

    <!-- TAB NAVIGASI SUB-MODUL BENDAHARA (RESPONSIF TOUCH & DESKTOP) -->
    <div class="overflow-x-auto whitespace-nowrap border-b border-slate-200 -mx-4 px-4 sm:mx-0 sm:px-0" style="scrollbar-width: none; -ms-overflow-style: none;">
        <div class="flex items-center gap-1 sm:gap-2 min-w-max">
            <a href="{{ route('admin.bendahara.index') }}"
               class="px-3.5 sm:px-4 py-3 text-xs sm:text-sm font-bold text-slate-600 hover:text-emerald-700 border-b-2 border-transparent hover:border-slate-300 transition flex items-center gap-2 whitespace-nowrap shrink-0">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Buku Kas Umum</span>
            </a>

            <a href="{{ route('admin.bendahara.laba-rugi') }}"
               class="px-3.5 sm:px-4 py-3 text-xs sm:text-sm font-bold text-slate-600 hover:text-emerald-700 border-b-2 border-transparent hover:border-slate-300 transition flex items-center gap-2 whitespace-nowrap shrink-0">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Laporan Laba Rugi</span>
            </a>

            <a href="{{ route('admin.bendahara.neraca') }}"
               class="px-3.5 sm:px-4 py-3 text-xs sm:text-sm font-bold text-emerald-800 border-b-2 border-emerald-600 flex items-center gap-2 whitespace-nowrap shrink-0">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                <span>Laporan Neraca</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- PANEL FILTER PERIODE NERACA (RESPONSIF MOBILE & DESKTOP BEBAS BENTROK)    -->
    <!-- ========================================================================= -->
    <div class="bg-white p-2.5 sm:p-3 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.bendahara.neraca') }}">
            <!-- KHUSUS LAYAR MOBILE (sm:hidden): RINGKAS & TANGGAL RAPI -->
            <div class="sm:hidden">
                @if($filterTipe === 'kustom')
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <select name="filter_tipe" onchange="this.form.submit()" class="w-28 text-xs font-bold rounded-xl border-slate-300 py-1.5 px-2 bg-slate-100 text-slate-800 shrink-0">
                                <option value="bulan" {{ $filterTipe === 'bulan' ? 'selected' : '' }}>Bulanan</option>
                                <option value="tahun" {{ $filterTipe === 'tahun' ? 'selected' : '' }}>Tahunan</option>
                                <option value="kustom" {{ $filterTipe === 'kustom' ? 'selected' : '' }}>Bebas</option>
                                <option value="semua" {{ $filterTipe === 'semua' ? 'selected' : '' }}>Semua</option>
                            </select>
                            <span class="text-xs text-slate-500 font-medium truncate">Tentukan rentang tanggal:</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <input type="date" name="dari_tanggal" value="{{ $dari }}" class="flex-1 min-w-0 text-xs rounded-xl border-slate-300 py-1.5 px-2">
                            <span class="text-xs text-slate-400 shrink-0">s/d</span>
                            <input type="date" name="sampai_tanggal" value="{{ $sampai }}" class="flex-1 min-w-0 text-xs rounded-xl border-slate-300 py-1.5 px-2">
                            <button type="submit" class="py-1.5 px-3 rounded-xl bg-emerald-700 text-white font-bold text-xs shrink-0 hover:bg-emerald-800 transition">OK</button>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <!-- Dropdown Pilihan Tipe Filter -->
                        <select name="filter_tipe" onchange="this.form.submit()" class="w-28 text-xs font-bold rounded-xl border-slate-300 py-1.5 px-2 bg-slate-100 text-slate-800 shrink-0">
                            <option value="bulan" {{ $filterTipe === 'bulan' ? 'selected' : '' }}>Bulanan</option>
                            <option value="tahun" {{ $filterTipe === 'tahun' ? 'selected' : '' }}>Tahunan</option>
                            <option value="kustom" {{ $filterTipe === 'kustom' ? 'selected' : '' }}>Bebas</option>
                            <option value="semua" {{ $filterTipe === 'semua' ? 'selected' : '' }}>Semua</option>
                        </select>

                        <!-- Dropdown Pilihan Nilai Sejajar (Bulanan / Tahunan / Semua) -->
                        <div class="flex-1 min-w-0">
                            @if($filterTipe === 'bulan')
                                <select name="filter_bulan" onchange="this.form.submit()" class="w-full text-xs font-semibold rounded-xl border-slate-300 py-1.5 px-2.5 bg-white text-slate-800 truncate">
                                    @foreach($bulanTersedia as $b)
                                        <option value="{{ $b['ym'] }}" {{ $filterBulan === $b['ym'] ? 'selected' : '' }}>
                                            {{ $b['nama_bulan'] }}
                                        </option>
                                    @endforeach
                                </select>
                            @elseif($filterTipe === 'tahun')
                                <select name="filter_tahun" onchange="this.form.submit()" class="w-full text-xs font-semibold rounded-xl border-slate-300 py-1.5 px-2.5 bg-white text-slate-800">
                                    @foreach($tahunTersedia as $t)
                                        <option value="{{ $t['tahun'] }}" {{ $filterTahun == $t['tahun'] ? 'selected' : '' }}>
                                            {{ $t['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <span class="text-xs text-slate-500 italic block truncate py-1.5 px-2 bg-slate-50 rounded-xl border border-slate-200">Posisi Akumulatif Kas</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- KHUSUS LAYAR DESKTOP / TABLET (hidden sm:flex) -->
            <div class="hidden sm:flex sm:items-center justify-between gap-3">
                <!-- Mode Filter Cepat: Bulanan, Tahunan, Rentang Bebas, Posisi Terkini -->
                <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 shrink-0">
                    <button type="submit" name="filter_tipe" value="bulan"
                            class="px-3 py-1 rounded-lg text-xs font-bold transition text-center {{ $filterTipe === 'bulan' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Bulanan
                    </button>
                    <button type="submit" name="filter_tipe" value="tahun"
                            class="px-3 py-1 rounded-lg text-xs font-bold transition text-center {{ $filterTipe === 'tahun' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Tahunan
                    </button>
                    <button type="submit" name="filter_tipe" value="kustom"
                            class="px-3 py-1 rounded-lg text-xs font-bold transition text-center {{ $filterTipe === 'kustom' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Rentang Bebas
                    </button>
                    <button type="submit" name="filter_tipe" value="semua"
                            class="px-3 py-1 rounded-lg text-xs font-bold transition text-center {{ $filterTipe === 'semua' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Posisi Terkini
                    </button>
                </div>

                <!-- Kontrol Dropdown / Tanggal Sejajar Desktop -->
                <div class="flex items-center gap-2 justify-end">
                    @if($filterTipe === 'bulan')
                        <select name="filter_bulan" onchange="this.form.submit()" class="w-60 text-xs sm:text-sm font-semibold rounded-xl border-slate-300 py-1.5 px-3 bg-white">
                            @foreach($bulanTersedia as $b)
                                <option value="{{ $b['ym'] }}" {{ $filterBulan === $b['ym'] ? 'selected' : '' }}>
                                    {{ $b['nama_bulan'] }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($filterTipe === 'tahun')
                        <select name="filter_tahun" onchange="this.form.submit()" class="w-48 text-xs sm:text-sm font-semibold rounded-xl border-slate-300 py-1.5 px-3 bg-white">
                            @foreach($tahunTersedia as $t)
                                <option value="{{ $t['tahun'] }}" {{ $filterTahun == $t['tahun'] ? 'selected' : '' }}>
                                    {{ $t['label'] }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($filterTipe === 'kustom')
                        <div class="flex items-center gap-1.5">
                            <input type="date" name="dari_tanggal" value="{{ $dari }}" class="text-xs rounded-xl border-slate-300 py-1.5 px-2.5">
                            <span class="text-xs text-slate-400">s/d</span>
                            <input type="date" name="sampai_tanggal" value="{{ $sampai }}" class="text-xs rounded-xl border-slate-300 py-1.5 px-2.5">
                            <button type="submit" class="py-1.5 px-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition shrink-0">
                                OK
                            </button>
                        </div>
                    @else
                        <span class="text-xs text-slate-500 py-1 px-2 italic">Posisi akumulatif kas terkini</span>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- BANNER STATUS KESEIMBANGAN: MOBILE (md:hidden) & DESKTOP (hidden md:flex) -->
    <!-- ========================================================================= -->

    <!-- 1. TAMPILAN BANNER MOBILE: RINGKAS, LEGA & PROPORSIONAL -->
    <div class="md:hidden p-4 rounded-2xl bg-emerald-50 border border-emerald-200 shadow-xs space-y-3">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 min-w-0">
                <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700 shrink-0 border border-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <span class="text-xs font-black uppercase tracking-wider text-emerald-950">STATUS NERACA</span>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-black bg-emerald-600 text-white shrink-0">SEIMBANG (BALANCE)</span>
        </div>
        <div class="grid grid-cols-2 gap-3 pt-2.5 border-t border-emerald-200/70 text-center">
            <div class="p-2.5 rounded-xl bg-white/80 border border-emerald-100">
                <span class="text-xs font-semibold text-emerald-800 block truncate">Total Aset (Aktiva)</span>
                <span class="font-mono font-black text-sm text-emerald-950 block truncate mt-1">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
            </div>
            <div class="p-2.5 rounded-xl bg-white/80 border border-emerald-100">
                <span class="text-xs font-semibold text-emerald-800 block truncate">Kewajiban + Ekuitas</span>
                <span class="font-mono font-black text-sm text-emerald-950 block truncate mt-1">Rp {{ number_format($totalPasiva, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- 2. TAMPILAN BANNER DESKTOP: LEBAR & INFORMATIF -->
    <div class="hidden md:flex p-5 sm:p-6 rounded-2xl bg-emerald-50 text-emerald-950 border border-emerald-200 shadow-xs items-center justify-between gap-6">
        <div class="flex items-center gap-3.5 min-w-0">
            <span class="p-2.5 rounded-xl bg-emerald-100 text-emerald-700 shrink-0 border border-emerald-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs font-black tracking-wider text-emerald-900 uppercase">STATUS NERACA:</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-600 text-white">SEIMBANG (BALANCE)</span>
                </div>
                <p class="text-xs sm:text-sm text-emerald-800 mt-1 leading-relaxed">
                    Total Aset bernilai sama persis dengan Total Kewajiban ditambah Ekuitas Saldo Dana sesuai persamaan dasar akuntansi.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-6 shrink-0">
            <div class="text-right">
                <span class="text-xs text-emerald-700 block uppercase font-bold">Total Aset (Aktiva)</span>
                <span class="font-mono font-bold text-base sm:text-lg text-emerald-900">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
            </div>
            <span class="text-lg font-bold text-emerald-600">=</span>
            <div class="text-right">
                <span class="text-xs text-emerald-700 block uppercase font-bold">Kewajiban + Ekuitas</span>
                <span class="font-mono font-bold text-base sm:text-lg text-emerald-900">Rp {{ number_format($totalPasiva, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TABEL SKONTRO DUA SISI: AKTIVA (KIRI) VS PASIVA (KANAN)                    -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 items-stretch">

        <!-- ==================== SISI KIRI: ASET / AKTIVA ==================== -->
        <div class="bg-white rounded-2xl border border-emerald-200/80 shadow-xs overflow-hidden flex flex-col justify-between h-full">
            <div>
                <div class="p-4 sm:p-4.5 bg-emerald-50/70 border-b border-emerald-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-xs sm:text-sm font-bold text-emerald-950 uppercase tracking-wider truncate">SISI AKTIVA (ASET)</h2>
                            <p class="text-xs text-emerald-700 truncate mt-0.5">Seluruh kekayaan & sumber daya kas pesantren</p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
                        Aktiva
                    </span>
                </div>

                <div class="p-4 sm:p-5 space-y-4">
                    <!-- I. ASET LANCAR (KAS PESANTREN) -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-emerald-50/70 border border-emerald-100">
                            <h3 class="text-xs font-black uppercase text-emerald-900 tracking-wider">I. ASET LANCAR (KAS PESANTREN)</h3>
                            <span class="text-xs font-bold text-emerald-700">Data Riil Kas</span>
                        </div>

                        <div class="divide-y divide-slate-100 px-1">
                            @foreach($asetLancar as $al)
                                <div class="py-3 flex items-center justify-between gap-3">
                                    <span class="text-xs sm:text-sm text-slate-800 font-medium leading-normal">{{ $al['nama'] }}</span>
                                    <span class="font-mono font-bold text-xs sm:text-sm text-slate-900 whitespace-nowrap">
                                        {{ $al['nominal'] < 0 ? '-' : '' }}Rp {{ number_format(abs($al['nominal']), 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="p-3 sm:p-3.5 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center justify-between text-xs font-bold text-emerald-900">
                            <span>Jumlah Aset Kas Lancar:</span>
                            <span class="font-mono font-black text-sm text-emerald-800 whitespace-nowrap">
                                Rp {{ number_format($totalAsetLancar, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    @if(!empty($asetTetap))
                    <!-- II. ASET TETAP -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-emerald-50/50 border border-emerald-100">
                            <h3 class="text-xs font-black uppercase text-emerald-900 tracking-wider">II. ASET TETAP (INVENTARIS)</h3>
                            <span class="text-xs font-bold text-emerald-700">Peralatan & Gedung</span>
                        </div>

                        <div class="divide-y divide-slate-100 px-1">
                            @foreach($asetTetap as $at)
                                <div class="py-3 flex items-center justify-between gap-3">
                                    <span class="text-xs sm:text-sm text-slate-700 font-medium leading-normal">{{ $at['nama'] }}</span>
                                    <span class="font-mono font-medium text-xs sm:text-sm text-slate-500 whitespace-nowrap">
                                        {{ $at['nominal'] > 0 ? 'Rp ' . number_format($at['nominal'], 0, ',', '.') : '-' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="p-3 sm:p-3.5 bg-emerald-50/70 rounded-xl border border-emerald-100 flex items-center justify-between text-xs font-bold text-emerald-900">
                            <span>Jumlah Aset Tetap:</span>
                            <span class="font-mono font-black text-sm text-emerald-800 whitespace-nowrap">
                                Rp {{ number_format($totalAsetTetap, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- TOTAL AKHIR ASET (AKTIVA) -->
            <div class="p-3.5 sm:p-4 bg-emerald-50 border-t-2 border-emerald-500 flex items-center justify-between gap-2">
                <div>
                    <span class="text-xs font-black uppercase tracking-wider text-emerald-950 block">TOTAL ASET (AKTIVA):</span>
                    <span class="text-xs text-emerald-700 mt-0.5">Saldo Akhir Kas (Sinkron Buku Kas)</span>
                </div>
                <div class="text-right">
                    <span class="font-mono font-black text-base sm:text-lg text-emerald-800 whitespace-nowrap">
                        Rp {{ number_format($totalAset, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ==================== SISI KANAN: KEWAJIBAN & EKUITAS ==================== -->
        <div class="bg-white rounded-2xl border border-blue-200/80 shadow-xs overflow-hidden flex flex-col justify-between h-full">
            <div>
                <div class="p-4 sm:p-4.5 bg-blue-50/70 border-b border-blue-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="p-1.5 rounded-lg bg-blue-100 text-blue-700 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-xs sm:text-sm font-bold text-blue-950 uppercase tracking-wider truncate">SISI PASIVA (EKUITAS SALDO DANA)</h2>
                            <p class="text-xs text-blue-700 truncate mt-0.5">Akumulasi saldo awal & hasil laba rugi</p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold px-3 py-1 rounded-full bg-blue-100 text-blue-800 border border-blue-200 shrink-0">
                        Pasiva
                    </span>
                </div>

                <div class="p-4 sm:p-5 space-y-4">
                    @if(!empty($kewajiban))
                    <!-- I. KEWAJIBAN (LIABILITAS) -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-amber-50/70 border border-amber-100">
                            <h3 class="text-xs font-black uppercase text-amber-900 tracking-wider">I. KEWAJIBAN (LIABILITAS)</h3>
                            <span class="text-xs font-bold text-amber-700">Utang Operasional</span>
                        </div>

                        <div class="divide-y divide-slate-100 px-1">
                            @foreach($kewajiban as $kw)
                                <div class="py-3 flex items-center justify-between gap-3">
                                    <span class="text-xs sm:text-sm text-slate-700 font-medium leading-normal">{{ $kw['nama'] }}</span>
                                    <span class="font-mono font-medium text-xs sm:text-sm text-slate-500 whitespace-nowrap">
                                        {{ $kw['nominal'] > 0 ? 'Rp ' . number_format($kw['nominal'], 0, ',', '.') : '-' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="p-3 sm:p-3.5 bg-amber-50/70 rounded-xl border border-amber-100 flex items-center justify-between text-xs font-bold text-amber-900">
                            <span>Jumlah Kewajiban:</span>
                            <span class="font-mono font-black text-sm text-amber-800 whitespace-nowrap">
                                Rp {{ number_format($totalKewajiban, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @endif

                    <!-- EKUITAS DANA (SALDO DANA PESANTREN) -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-blue-50/70 border border-blue-100">
                            <h3 class="text-xs font-black uppercase text-blue-900 tracking-wider">{{ !empty($kewajiban) ? 'II.' : 'I.' }} EKUITAS DANA (SALDO DANA KAS)</h3>
                            <span class="text-xs font-bold text-blue-700">Sinkron Laba Rugi</span>
                        </div>

                        <div class="divide-y divide-slate-100 px-1">
                            @foreach($ekuitas as $ek)
                                <div class="py-3 flex items-center justify-between gap-3">
                                    <span class="text-xs sm:text-sm text-slate-800 font-medium leading-normal">{{ $ek['nama'] }}</span>
                                    <span class="font-mono font-bold text-xs sm:text-sm whitespace-nowrap {{ $ek['nominal'] < 0 ? 'text-rose-700' : 'text-slate-900' }}">
                                        {{ $ek['nominal'] < 0 ? '-' : '' }}Rp {{ number_format(abs($ek['nominal']), 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="p-3 sm:p-3.5 bg-blue-50 rounded-xl border border-blue-100 flex items-center justify-between text-xs font-bold text-blue-900">
                            <span>{{ $totalEkuitas >= 0 ? 'Surplus Kas Operasional (Saldo Dana):' : 'Defisit Kas Operasional (Saldo Dana):' }}</span>
                            <span class="font-mono font-black text-sm text-blue-800 whitespace-nowrap">
                                Rp {{ number_format($totalEkuitas, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOTAL AKHIR KEWAJIBAN & EKUITAS (PASIVA) -->
            <div class="p-3.5 sm:p-4 bg-blue-50 border-t-2 border-blue-500 flex items-center justify-between gap-2">
                <div>
                    <span class="text-xs font-black uppercase tracking-wider text-blue-950 block">TOTAL PASIVA (EKUITAS DANA):</span>
                    <span class="text-xs text-blue-700 mt-0.5">Pendapatan + Saldo Periode Lalu - Beban</span>
                </div>
                <div class="text-right">
                    <span class="font-mono font-black text-base sm:text-lg text-blue-800 whitespace-nowrap">
                        Rp {{ number_format($totalPasiva, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function neracaApp() {
    return {
        // Alpine logic
    };
}
</script>
@endsection
