@extends('layouts.app')

@section('title', 'Laporan Neraca - Bendahara Al-Falah Putra')

@section('content')
<div class="space-y-6" x-data="neracaApp()">

    <!-- ========================================================================= -->
    <!-- HEADER & AKSI UTAMA                                                       -->
    <!-- ========================================================================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Akuntansi & Keuangan
                </span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="text-xs font-semibold text-slate-500">Pondok Pesantren Al-Falah Putra</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Laporan Neraca (Posisi Keuangan)</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Posisi sumber daya ekonomi, kekayaan kas, kewajiban, dan ekuitas saldo dana pesantren 
                <strong class="text-slate-800">{{ $labelPeriode }}</strong>.
            </p>
        </div>

        <!-- Tombol Unduh PDF -->
        <div class="flex items-center gap-2.5 self-start md:self-center">
            <a href="{{ route('admin.bendahara.neraca.cetak', request()->query()) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs sm:text-sm shadow-xs transition">
                <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Laporan PDF</span>
            </a>
        </div>
    </div>

    <!-- TAB NAVIGASI SUB-MODUL BENDAHARA (3 TAB LENGKAP) -->
    <div class="flex items-center gap-2 border-b border-slate-200">
        <a href="{{ route('admin.bendahara.index') }}"
           class="px-4 py-3 text-xs sm:text-sm font-bold text-slate-600 hover:text-emerald-700 border-b-2 border-transparent hover:border-slate-300 transition flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Buku Kas Umum</span>
        </a>

        <a href="{{ route('admin.bendahara.laba-rugi') }}"
           class="px-4 py-3 text-xs sm:text-sm font-bold text-slate-600 hover:text-emerald-700 border-b-2 border-transparent hover:border-slate-300 transition flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Laporan Laba Rugi</span>
        </a>

        <a href="{{ route('admin.bendahara.neraca') }}"
           class="px-4 py-3 text-xs sm:text-sm font-bold text-emerald-800 border-b-2 border-emerald-600 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
            <span>Laporan Neraca</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
        </a>
    </div>

    <!-- ========================================================================= -->
    <!-- PANEL FILTER PERIODE                                                      -->
    <!-- ========================================================================= -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <form method="GET" action="{{ route('admin.bendahara.neraca') }}" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-slate-100 text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </span>
                    <span class="text-xs sm:text-sm font-bold text-slate-800">Pilih Tanggal Posisi Keuangan:</span>
                </div>

                <!-- 4 Pilihan Mode Filter Cepat -->
                <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 self-start sm:self-auto">
                    <button type="submit" name="filter_tipe" value="bulan"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $filterTipe === 'bulan' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Bulanan
                    </button>
                    <button type="submit" name="filter_tipe" value="tahun"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $filterTipe === 'tahun' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Tahunan
                    </button>
                    <button type="submit" name="filter_tipe" value="kustom"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $filterTipe === 'kustom' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Rentang Bebas
                    </button>
                    <button type="submit" name="filter_tipe" value="semua"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $filterTipe === 'semua' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Posisi Terkini
                    </button>
                </div>
            </div>

            <!-- Kontrol Input Berdasarkan Tipe Filter -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
                @if($filterTipe === 'bulan')
                    <div class="col-span-1 sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Posisi Akhir Bulan:</label>
                        <select name="filter_bulan" onchange="this.form.submit()" class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                            @foreach($bulanTersedia as $b)
                                <option value="{{ $b['ym'] }}" {{ $filterBulan === $b['ym'] ? 'selected' : '' }}>
                                    {{ $b['nama_bulan'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @elseif($filterTipe === 'tahun')
                    <div class="col-span-1 sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Posisi Akhir Tahun:</label>
                        <select name="filter_tahun" onchange="this.form.submit()" class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                            @foreach($tahunTersedia as $t)
                                <option value="{{ $t['tahun'] }}" {{ $filterTahun == $t['tahun'] ? 'selected' : '' }}>
                                    {{ $t['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @elseif($filterTipe === 'kustom')
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Dari Tanggal (Saldo Awal):</label>
                        <input type="date" name="dari_tanggal" value="{{ $dari }}" class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Sampai Tanggal (Cut-off):</label>
                        <input type="date" name="sampai_tanggal" value="{{ $sampai }}" class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                    </div>
                    <div>
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition">
                            Terapkan Periode
                        </button>
                    </div>
                @else
                    <div class="col-span-1 sm:col-span-2">
                        <p class="text-xs text-slate-500 py-2">
                            Menampilkan posisi neraca terkini per hari ini berdasarkan saldo kumulatif buku kas.
                        </p>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- BANNER STATUS KESEIMBANGAN (BALANCE)                                      -->
    <!-- ========================================================================= -->
    <div class="p-4 sm:p-5 rounded-3xl bg-emerald-50 text-emerald-950 border border-emerald-200 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="p-2.5 rounded-2xl bg-emerald-100 text-emerald-700 shrink-0 border border-emerald-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black tracking-wider text-emerald-900 uppercase">STATUS NERACA:</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-600 text-white">SEIMBANG (BALANCE)</span>
                </div>
                <p class="text-xs text-emerald-800 mt-1">
                    Total Aset bernilai sama persis dengan Total Kewajiban ditambah Ekuitas Saldo Dana sesuai persamaan dasar akuntansi.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-6 self-stretch md:self-auto justify-between md:justify-end pt-2 md:pt-0 border-t md:border-t-0 border-emerald-200 text-right">
            <div>
                <span class="text-[11px] text-emerald-700 block uppercase font-bold">Total Aset (Aktiva)</span>
                <span class="font-mono font-black text-lg text-emerald-900">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
            </div>
            <span class="text-xl font-bold text-emerald-600">=</span>
            <div>
                <span class="text-[11px] text-emerald-700 block uppercase font-bold">Kewajiban + Ekuitas</span>
                <span class="font-mono font-black text-lg text-emerald-900">Rp {{ number_format($totalPasiva, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TABEL SKONTRO DUA SISI: AKTIVA (KIRI) VS PASIVA (KANAN)                    -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        <!-- ==================== SISI KIRI: ASET / AKTIVA ==================== -->
        <div class="bg-white rounded-3xl border border-emerald-200/80 shadow-xs overflow-hidden flex flex-col">
            <div class="p-4 sm:p-5 bg-emerald-50/60 border-b border-emerald-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </span>
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-emerald-950 uppercase tracking-wider">SISI AKTIVA (ASET)</h2>
                        <p class="text-[11px] text-emerald-700">Seluruh kekayaan & sumber daya kas pesantren</p>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Aktiva
                </span>
            </div>

            <div class="p-4 sm:p-5 space-y-5">
                <!-- I. ASET LANCAR -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-emerald-50/60 border border-emerald-100">
                        <h3 class="text-xs font-black uppercase text-emerald-900 tracking-wider">I. ASET LANCAR</h3>
                        <span class="text-xs font-bold text-emerald-700">Kas & Setara Kas</span>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs sm:text-sm px-1">
                        @foreach($asetLancar as $al)
                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-slate-700">{{ $al['nama'] }}</span>
                                <span class="font-mono font-bold text-slate-900">
                                    {{ $al['nominal'] > 0 ? 'Rp ' . number_format($al['nominal'], 0, ',', '.') : '-' }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center justify-between text-xs font-bold text-emerald-900">
                        <span>Jumlah Aset Lancar:</span>
                        <span class="font-mono font-black text-sm text-emerald-800">
                            Rp {{ number_format($totalAsetLancar, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- II. ASET TETAP -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-emerald-50/50 border border-emerald-100">
                        <h3 class="text-xs font-black uppercase text-emerald-900 tracking-wider">II. ASET TETAP (INVENTARIS)</h3>
                        <span class="text-xs font-bold text-emerald-700">Peralatan & Gedung</span>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs sm:text-sm px-1">
                        @foreach($asetTetap as $at)
                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-slate-600">{{ $at['nama'] }}</span>
                                <span class="font-mono font-medium text-slate-500">
                                    {{ $at['nominal'] > 0 ? 'Rp ' . number_format($at['nominal'], 0, ',', '.') : '-' }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-3 bg-emerald-50/70 rounded-xl border border-emerald-100 flex items-center justify-between text-xs font-bold text-emerald-900">
                        <span>Jumlah Aset Tetap:</span>
                        <span class="font-mono font-black text-sm text-emerald-800">
                            Rp {{ number_format($totalAsetTetap, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- TOTAL AKHIR ASET (AKTIVA) -->
            <div class="p-4 sm:p-5 bg-emerald-50 border-t-2 border-emerald-500 flex items-center justify-between">
                <div>
                    <span class="text-xs font-black uppercase tracking-wider text-emerald-950 block">TOTAL ASET (AKTIVA):</span>
                    <span class="text-[11px] text-emerald-700">Aset Lancar + Aset Tetap</span>
                </div>
                <div class="text-right">
                    <span class="font-mono font-black text-xl text-emerald-800">
                        Rp {{ number_format($totalAset, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ==================== SISI KANAN: KEWAJIBAN & EKUITAS ==================== -->
        <div class="bg-white rounded-3xl border border-blue-200/80 shadow-xs overflow-hidden flex flex-col">
            <div class="p-4 sm:p-5 bg-blue-50/70 border-b border-blue-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-blue-100 text-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </span>
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-blue-950 uppercase tracking-wider">SISI PASIVA (KEWAJIBAN & EKUITAS)</h2>
                        <p class="text-[11px] text-blue-700">Liabilitas & akumulasi saldo dana pesantren</p>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold px-3 py-1 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                    Pasiva
                </span>
            </div>

            <div class="p-4 sm:p-5 space-y-5">
                <!-- I. KEWAJIBAN (LIABILITAS) -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-amber-50/70 border border-amber-100">
                        <h3 class="text-xs font-black uppercase text-amber-900 tracking-wider">I. KEWAJIBAN (LIABILITAS)</h3>
                        <span class="text-xs font-bold text-amber-700">Utang Operasional</span>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs sm:text-sm px-1">
                        @foreach($kewajiban as $kw)
                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-slate-600">{{ $kw['nama'] }}</span>
                                <span class="font-mono font-medium text-slate-500">
                                    {{ $kw['nominal'] > 0 ? 'Rp ' . number_format($kw['nominal'], 0, ',', '.') : '-' }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-3 bg-amber-50/70 rounded-xl border border-amber-100 flex items-center justify-between text-xs font-bold text-amber-900">
                        <span>Jumlah Kewajiban:</span>
                        <span class="font-mono font-black text-sm text-amber-800">
                            Rp {{ number_format($totalKewajiban, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- II. EKUITAS DANA (SALDO DANA PESANTREN) -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-blue-50/70 border border-blue-100">
                        <h3 class="text-xs font-black uppercase text-blue-900 tracking-wider">II. EKUITAS DANA (SALDO DANA)</h3>
                        <span class="text-xs font-bold text-blue-700">Kekayaan Bersih Kas</span>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs sm:text-sm px-1">
                        @foreach($ekuitas as $ek)
                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-slate-700 font-medium">{{ $ek['nama'] }}</span>
                                <span class="font-mono font-bold text-slate-900">
                                    {{ $ek['nominal'] < 0 ? '-' : '' }}Rp {{ number_format(abs($ek['nominal']), 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-3 bg-blue-50 rounded-xl border border-blue-100 flex items-center justify-between text-xs font-bold text-blue-900">
                        <span>Jumlah Ekuitas Saldo Dana:</span>
                        <span class="font-mono font-black text-sm text-blue-800">
                            Rp {{ number_format($totalEkuitas, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- TOTAL AKHIR KEWAJIBAN & EKUITAS (PASIVA) -->
            <div class="p-4 sm:p-5 bg-blue-50 border-t-2 border-blue-500 flex items-center justify-between">
                <div>
                    <span class="text-xs font-black uppercase tracking-wider text-blue-950 block">TOTAL KEWAJIBAN & EKUITAS:</span>
                    <span class="text-[11px] text-blue-700">Liabilitas + Saldo Dana</span>
                </div>
                <div class="text-right">
                    <span class="font-mono font-black text-xl text-blue-800">
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
