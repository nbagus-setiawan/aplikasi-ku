@extends('layouts.app')

@section('title', 'Absensi Korlap Sholat Jamaah - SAP Al-Falah Putra')

@section('content')
@php
    $tglCarbon = Carbon\Carbon::parse($tanggalTerpilih)->locale('id');
    $tglFormatWa = $tglCarbon->translatedFormat('l, d F Y');
    $tidakHadirList = $presensiHariIni->filter(fn($p) => in_array($p->status, ['sakit', 'izin', 'alfa']));

    // Garis pembatas solid penuh & pas selebar judul di WhatsApp (32 karakter)
    $garisWa = str_repeat('━', 32);

    $pesanWaText = "🕌 *LAPORAN PRESENSI SHOLAT JAMAAH*\n"
        . "*PONDOK PESANTREN AL-FALAH PUTRA*\n"
        . $garisWa . "\n"
        . "📅 *Hari, Tanggal :* " . $tglFormatWa . "\n"
        . "👥 *Total Petugas :* " . $totalPetugas . " Orang\n\n"
        . "📊 *REKAP KEHADIRAN 5 WAKTU:*\n";

    foreach ($ringkasanPerSholat as $key => $r) {
        $namaSholat = $r['nama'];
        $totalHadir = (int)($r['hadir_tepat'] + $r['hadir_telat']);
        $totalRecord = (int)($r['total'] ?? 0);

        if ($totalRecord === 0) {
            $pesanWaText .= "• *" . $namaSholat . "* : ⏳ _Belum dimulai_\n";
        } else {
            $detailList = [];
            $detailList[] = $totalHadir . " Hadir" . ($r['hadir_telat'] > 0 ? " (Tepat: " . $r['hadir_tepat'] . ", Telat: " . $r['hadir_telat'] . ")" : "");

            $absenList = [];
            if ($r['sakit'] > 0) $absenList[] = $r['sakit'] . " Sakit";
            if ($r['izin'] > 0) $absenList[] = $r['izin'] . " Izin";
            if ($r['alfa'] > 0) $absenList[] = $r['alfa'] . " Alfa";

            if (!empty($absenList)) {
                $detailList[] = implode(', ', $absenList);
            }

            $pesanWaText .= "• *" . $namaSholat . "* : " . implode(' | ', $detailList) . "\n";
        }
    }

    $pesanWaText .= "\n" . $garisWa . "\n"
        . "⚠️ *CATATAN PETUGAS TIDAK HADIR:*\n";

    if ($tidakHadirList->isEmpty()) {
        $pesanWaText .= "✅ _Alhamdulillah seluruh petugas terjadwal hadir._\n";
    } else {
        foreach ($tidakHadirList as $th) {
            $namaPt = $th->petugas->nama ?? 'Petugas';
            $sholatPt = ucfirst($th->waktu_sholat);
            $statusPt = ucfirst($th->status);

            // Rapikan keterangan panjang otomatis alfa
            $ket = trim((string)($th->keterangan ?? ''));
            if (stripos($ket, 'Otomatis Alfa') !== false || stripos($ket, 'Tidak mengisi presensi') !== false) {
                $ketTampil = " (Tidak absen)";
            } elseif ($ket !== '') {
                $ketTampil = " (" . $ket . ")";
            } else {
                $ketTampil = "";
            }

            $pesanWaText .= "• *" . $namaPt . "* [" . $sholatPt . "] ➔ " . $statusPt . $ketTampil . "\n";
        }
    }

    $pesanWaText .= "\n🔗 *Tautan Presensi Online:*\n"
        . route('absensi-sholat.index') . "\n"
        . $garisWa . "\n"
        . "_Sistem Administrasi Pondok (SAP) Al-Falah Putra_";
@endphp
<div class="space-y-6" x-data="absensiAdminApp()">

    <!-- ========================================================================= -->
    <!-- HEADER MODUL & TOMBOL SALIN TAUTAN PUBLIK                                 -->
    <!-- ========================================================================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
        <div class="min-w-0">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Kedisiplinan & Ubudiyah
                </span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="text-xs font-semibold text-slate-500">Pondok Pesantren Al-Falah Putra</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Absensi Korlap (Sholat Jamaah)</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5 leading-relaxed">
                Pemantauan kedisiplinan petugas korlap 5 waktu sholat, grafik kehadiran terpadu bulanan, rekapitulasi, dan manajemen jam.
            </p>
        </div>

        <!-- TOMBOL AKSI: SALIN LINK PRESENSI (UNTUK KOORDINATOR DI WA) -->
        <div class="flex items-center gap-2.5 w-full sm:w-auto flex-wrap sm:flex-nowrap">
            <button type="button"
                    @click="copyLink('{{ route('absensi-sholat.index') }}')"
                    class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow-xs transition">
                <svg class="w-4 h-4 text-emerald-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                <span x-text="copied ? 'Tautan Disalin!' : 'Salin Link Presensi'">Salin Link Presensi</span>
            </button>

            <a href="{{ route('absensi-sholat.index') }}"
               target="_blank"
               class="inline-flex items-center justify-center gap-1.5 w-full sm:w-auto px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm border border-slate-300 transition"
               title="Buka Halaman Presensi Petugas di Tab Baru">
                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Buka Link</span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB NAVIGASI SUB-MODUL ABSENSI KORLAP (SMOOTH TOUCH SCROLL)               -->
    <!-- ========================================================================= -->
    <div class="overflow-x-auto whitespace-nowrap border-b border-slate-200 -mx-4 px-4 sm:mx-0 sm:px-0" style="scrollbar-width: none; -ms-overflow-style: none;">
        <div class="flex items-center gap-1 sm:gap-2 min-w-max">
            <!-- 1. MONITORING HARI INI -->
            <a href="{{ route('admin.absensi-sholat.index', ['tab' => 'monitoring', 'tanggal' => $tanggalTerpilih]) }}"
               class="px-3.5 sm:px-4 py-3 text-xs sm:text-sm font-bold border-b-2 flex items-center gap-2 whitespace-nowrap shrink-0 {{ $activeTab === 'monitoring' ? 'text-emerald-800 border-emerald-600' : 'text-slate-600 hover:text-emerald-700 border-transparent hover:border-slate-300' }}">
                <svg class="w-4 h-4 {{ $activeTab === 'monitoring' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Monitoring Hari Ini</span>
            </a>

            <!-- 2. GRAFIK TERPADU BULANAN -->
            <a href="{{ route('admin.absensi-sholat.index', ['tab' => 'grafik', 'bulan' => $bulanTerpilih]) }}"
               class="px-3.5 sm:px-4 py-3 text-xs sm:text-sm font-bold border-b-2 flex items-center gap-2 whitespace-nowrap shrink-0 {{ $activeTab === 'grafik' ? 'text-emerald-800 border-emerald-600' : 'text-slate-600 hover:text-emerald-700 border-transparent hover:border-slate-300' }}">
                <svg class="w-4 h-4 {{ $activeTab === 'grafik' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                <span>Grafik Per Sholat (Bulanan)</span>
            </a>

            <!-- 3. LOG RIWAYAT DETAIL -->
            <a href="{{ route('admin.absensi-sholat.index', ['tab' => 'log', 'bulan' => $bulanTerpilih]) }}"
               class="px-3.5 sm:px-4 py-3 text-xs sm:text-sm font-bold border-b-2 flex items-center gap-2 whitespace-nowrap shrink-0 {{ $activeTab === 'log' ? 'text-emerald-800 border-emerald-600' : 'text-slate-600 hover:text-emerald-700 border-transparent hover:border-slate-300' }}">
                <svg class="w-4 h-4 {{ $activeTab === 'log' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Log Riwayat</span>
            </a>

            <!-- 4. REKAPITULASI KEHADIRAN -->
            <a href="{{ route('admin.absensi-sholat.index', ['tab' => 'rekap', 'bulan' => $bulanTerpilih]) }}"
               class="px-3.5 sm:px-4 py-3 text-xs sm:text-sm font-bold border-b-2 flex items-center gap-2 whitespace-nowrap shrink-0 {{ $activeTab === 'rekap' ? 'text-emerald-800 border-emerald-600' : 'text-slate-600 hover:text-emerald-700 border-transparent hover:border-slate-300' }}">
                <svg class="w-4 h-4 {{ $activeTab === 'rekap' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Rekapitulasi Kehadiran</span>
            </a>

            <!-- 5. SETELAN JAM 5 SHOLAT -->
            <a href="{{ route('admin.absensi-sholat.index', ['tab' => 'jadwal']) }}"
               class="px-3.5 sm:px-4 py-3 text-xs sm:text-sm font-bold border-b-2 flex items-center gap-2 whitespace-nowrap shrink-0 {{ $activeTab === 'jadwal' ? 'text-emerald-800 border-emerald-600' : 'text-slate-600 hover:text-emerald-700 border-transparent hover:border-slate-300' }}">
                <svg class="w-4 h-4 {{ $activeTab === 'jadwal' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                <span>Setelan Jam Sholat</span>
            </a>

            <!-- 6. MASTER PETUGAS KORLAP -->
            <a href="{{ route('admin.absensi-sholat.index', ['tab' => 'petugas']) }}"
               class="px-3.5 sm:px-4 py-3 text-xs sm:text-sm font-bold border-b-2 flex items-center gap-2 whitespace-nowrap shrink-0 {{ $activeTab === 'petugas' ? 'text-emerald-800 border-emerald-600' : 'text-slate-600 hover:text-emerald-700 border-transparent hover:border-slate-300' }}">
                <svg class="w-4 h-4 {{ $activeTab === 'petugas' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Kelola Petugas Korlap ({{ $totalPetugas }})</span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: MONITORING HARI INI                                                -->
    <!-- ========================================================================= -->
    @if ($activeTab === 'monitoring')
        <div class="space-y-5">
            <!-- Filter Tanggal Monitoring -->
            <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-emerald-100 text-emerald-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    <div>
                        <h2 class="text-xs sm:text-sm font-bold text-slate-800">Monitoring 5 Sholat Jamaah</h2>
                        <p class="text-[11px] text-slate-500">Tanggal: {{ Carbon\Carbon::parse($tanggalTerpilih)->locale('id')->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <form method="GET" action="{{ route('admin.absensi-sholat.index') }}" class="flex items-center gap-2">
                        <input type="hidden" name="tab" value="monitoring">
                        <input type="date" name="tanggal" value="{{ $tanggalTerpilih }}" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-300 py-1.5 px-3 bg-white font-semibold">
                        @if ($tanggalTerpilih !== $now->toDateString())
                            <a href="{{ route('admin.absensi-sholat.index', ['tab' => 'monitoring']) }}" class="text-xs text-emerald-700 font-bold hover:underline">Hari Ini</a>
                        @endif
                    </form>

                    <!-- TOMBOL BAGIKAN KE WHATSAPP -->
                    <button type="button"
                            @click="openModalBagikanWa()"
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition shrink-0"
                            title="Bagikan Ringkasan Presensi Hari Ini ke Grup WhatsApp">
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.456 5.711 1.456h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.411z"/>
                        </svg>
                        <span>Bagikan ke WA</span>
                    </button>

                    <a href="{{ route('admin.absensi-sholat.cetak-harian', ['tanggal' => $tanggalTerpilih]) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition shrink-0"
                       title="Buka daftar presensi harian resmi untuk dicetak atau diunduh ke PDF">
                        <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Cetak & Unduh Harian</span>
                    </a>
                </div>
            </div>

            <!-- 5 KARTU STATUS REALTIME PER SHOLAT -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                @foreach ($ringkasanPerSholat as $key => $r)
                    @php
                        $jdw = $jadwals->get($key);
                        $totalHadir = $r['hadir_tepat'] + $r['hadir_telat'];
                        $persenHadir = ($r['total'] > 0) ? round(($totalHadir / $r['total']) * 100) : 0;
                    @endphp
                    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col justify-between space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <span class="text-xs font-black uppercase tracking-wider text-slate-900">{{ $r['nama'] }}</span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full {{ $r['total'] > 0 ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-slate-100 text-slate-500' }}">
                                {{ $r['total'] > 0 ? 'Terisi' : 'Kosong' }}
                            </span>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-baseline justify-between">
                                <span class="text-xs text-slate-500">Hadir Tepat:</span>
                                <span class="text-xs font-bold font-mono text-emerald-700">{{ $r['hadir_tepat'] }}</span>
                            </div>
                            <div class="flex items-baseline justify-between">
                                <span class="text-xs text-slate-500">Hadir Telat:</span>
                                <span class="text-xs font-bold font-mono text-amber-600">{{ $r['hadir_telat'] }}</span>
                            </div>
                            <div class="flex items-baseline justify-between">
                                <span class="text-xs text-slate-500">Sakit / Izin:</span>
                                <span class="text-xs font-bold font-mono text-blue-600">{{ $r['sakit'] + $r['izin'] }}</span>
                            </div>
                            <div class="flex items-baseline justify-between">
                                <span class="text-xs text-slate-500">Alfa:</span>
                                <span class="text-xs font-bold font-mono text-rose-600">{{ $r['alfa'] }}</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="text-slate-500">Tingkat Hadir:</span>
                                <span class="font-bold font-mono text-slate-800">
                                    {{ $persenHadir }}%
                                    @if ($r['total'] > 0)
                                        <span class="text-[10px] font-normal text-slate-400">({{ $totalHadir }}/{{ $r['total'] }})</span>
                                    @endif
                                </span>
                            </div>
                            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-600 rounded-full transition-all" style="width: {{ $persenHadir }}%"></div>
                            </div>
                            <span class="text-[10px] text-slate-400 block truncate mt-1">Korlap: {{ $r['koordinator'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- TABEL LOG PRESENSI HARI TERPILIH -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide">
                        Rincian Log Presensi Tanggal Terpilih ({{ $presensiHariIni->count() }} Data)
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs divide-y divide-slate-100">
                        <thead class="bg-slate-50 text-[11px] font-bold uppercase text-slate-600">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Nama Petugas</th>
                                <th class="px-4 py-3 text-center">Waktu Sholat</th>
                                <th class="px-4 py-3 text-center">Jam Absen</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Kategori Jam</th>
                                <th class="px-4 py-3">Koordinator Pengabsen</th>
                                <th class="px-4 py-3">Keterangan</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($presensiHariIni as $idx => $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-4 py-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3 font-bold text-slate-900">{{ $item->petugas->nama ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase bg-slate-100 text-slate-800">
                                            {{ $item->waktu_sholat }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono text-slate-600">{{ substr($item->jam_absen, 0, 5) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                            {{ $item->status === 'hadir' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $item->status === 'sakit' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $item->status === 'izin' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $item->status === 'alfa' ? 'bg-rose-100 text-rose-800' : '' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($item->status === 'hadir')
                                            <span class="text-[10px] font-bold {{ $item->kategori_waktu === 'tepat_waktu' ? 'text-emerald-700' : 'text-amber-600' }}">
                                                {{ $item->kategori_waktu === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $item->koordinator->nama ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-500 italic">{{ $item->keterangan ?: '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click="openEditStatusModal({{ $item->id }}, {{ \Illuminate\Support\Js::from($item->petugas->nama ?? 'Petugas') }}, {{ \Illuminate\Support\Js::from(ucfirst($item->waktu_sholat)) }}, {{ \Illuminate\Support\Js::from($item->status) }}, {{ \Illuminate\Support\Js::from($item->kategori_waktu) }}, {{ \Illuminate\Support\Js::from($item->keterangan ?? '') }})" class="p-1 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-slate-100 transition" title="Koreksi Status & Keterangan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-slate-400 italic">
                                        Belum ada data presensi sholat yang masuk pada tanggal ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 2: GRAFIK TERPADU BULANAN (SINGLE CANVAS 5 SHOLAT)                    -->
    <!-- ========================================================================= -->
    @if ($activeTab === 'grafik')
        <div class="space-y-4">
            <!-- Header Filter Bulan Grafik -->
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide">
                        Grafik Tren Kehadiran Sholat Jamaah (Per Hari dalam 1 Bulan)
                    </h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Menampilkan tren jumlah petugas hadir untuk 5 waktu sholat berdampingan dalam satu grafik terpadu periode {{ $dtBulan->locale('id')->translatedFormat('F Y') }}.
                    </p>
                </div>

                <form method="GET" action="{{ route('admin.absensi-sholat.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="tab" value="grafik">
                    <select name="bulan" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border-slate-300 py-1.5 px-3 bg-white">
                        @foreach ($bulanOptions as $opt)
                            <option value="{{ $opt['val'] }}" {{ $bulanTerpilih === $opt['val'] ? 'selected' : '' }}>
                                {{ $opt['label'] }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- CANVAS CHART.JS TERPADU -->
            <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-500 pb-2 border-b border-slate-100">
                    <span class="inline-flex items-center gap-1.5 font-medium text-slate-700">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <span>Jumlah kehadiran dihitung <strong>per waktu sholat</strong> sesuai jadwal piket masing-masing</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="inline-block w-4 border-b border-dashed border-slate-400"></span>
                        <span>Garis grid putus-putus menandai interval jumlah kehadiran</span>
                    </span>
                </div>
                <div class="h-72 sm:h-96 w-full">
                    <canvas id="unifiedSholatChart"></canvas>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 3: LOG RIWAYAT DETAIL                                                 -->
    <!-- ========================================================================= -->
    @if ($activeTab === 'log')
        <div class="space-y-4">
            <!-- Panel Filter Pencarian Log -->
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('admin.absensi-sholat.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-2.5 items-end">
                    <input type="hidden" name="tab" value="log">
                    
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Pilih Bulan</label>
                        <select name="bulan" class="w-full text-xs font-semibold rounded-xl border-slate-300 py-1.5 px-3 bg-white">
                            @foreach ($bulanOptions as $opt)
                                <option value="{{ $opt['val'] }}" {{ $bulanTerpilih === $opt['val'] ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Waktu Sholat</label>
                        <select name="filter_sholat" class="w-full text-xs font-semibold rounded-xl border-slate-300 py-1.5 px-3 bg-white">
                            <option value="">Semua Sholat</option>
                            @foreach (App\Models\AbsensiSholatJadwal::SHOLAT_LIST as $k => $lbl)
                                <option value="{{ $k }}" {{ request('filter_sholat') === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Status Kehadiran</label>
                        <select name="filter_status" class="w-full text-xs font-semibold rounded-xl border-slate-300 py-1.5 px-3 bg-white">
                            <option value="">Semua Status</option>
                            <option value="hadir" {{ request('filter_status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="sakit" {{ request('filter_status') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="izin" {{ request('filter_status') === 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="alfa" {{ request('filter_status') === 'alfa' ? 'selected' : '' }}>Alfa</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="w-full py-1.5 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs transition">
                            Terapkan Filter
                        </button>
                        <a href="{{ route('admin.absensi-sholat.index', ['tab' => 'log']) }}" class="py-1.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tabel Log Paginated -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs divide-y divide-slate-100">
                        <thead class="bg-slate-50 text-[11px] font-bold uppercase text-slate-600">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3 text-center">Sholat</th>
                                <th class="px-4 py-3">Nama Petugas</th>
                                <th class="px-4 py-3 text-center">Jam</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Ketepatan</th>
                                <th class="px-4 py-3">Pengabsen</th>
                                <th class="px-4 py-3">Keterangan</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($logs as $idx => $log)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-4 py-3 text-center text-slate-400 font-mono">{{ $logs->firstItem() + $idx }}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-800 whitespace-nowrap">{{ $log->tanggal->format('d-m-Y') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase bg-slate-100 text-slate-800">{{ $log->waktu_sholat }}</span>
                                    </td>
                                    <td class="px-4 py-3 font-bold text-slate-900">{{ $log->petugas->nama ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center font-mono text-slate-600">{{ substr($log->jam_absen, 0, 5) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                            {{ $log->status === 'hadir' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $log->status === 'sakit' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $log->status === 'izin' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $log->status === 'alfa' ? 'bg-rose-100 text-rose-800' : '' }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($log->status === 'hadir')
                                            <span class="text-[10px] font-bold {{ $log->kategori_waktu === 'tepat_waktu' ? 'text-emerald-700' : 'text-amber-600' }}">
                                                {{ $log->kategori_waktu === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $log->koordinator->nama ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-500 italic">{{ $log->keterangan ?: '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click="openEditStatusModal({{ $log->id }}, {{ \Illuminate\Support\Js::from($log->petugas->nama ?? 'Petugas') }}, {{ \Illuminate\Support\Js::from(ucfirst($log->waktu_sholat)) }}, {{ \Illuminate\Support\Js::from($log->status) }}, {{ \Illuminate\Support\Js::from($log->kategori_waktu) }}, {{ \Illuminate\Support\Js::from($log->keterangan ?? '') }})" class="p-1 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-slate-100 transition" title="Koreksi Status & Keterangan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-8 text-center text-slate-400 italic">
                                        Tidak ada riwayat presensi yang cocok dengan filter pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($logs->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 4: REKAPITULASI KEHADIRAN BULANAN & CETAK PDF                         -->
    <!-- ========================================================================= -->
    @if ($activeTab === 'rekap')
        <div class="space-y-4">
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide">
                        Rekapitulasi Kehadiran Korlap Bulan {{ $dtBulan->locale('id')->translatedFormat('F Y') }}
                    </h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Statistik kumulatif kehadiran seluruh petugas korlap sholat jamaah.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <form method="GET" action="{{ route('admin.absensi-sholat.index') }}" class="flex items-center gap-2">
                        <input type="hidden" name="tab" value="rekap">
                        <select name="bulan" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border-slate-300 py-1.5 px-3 bg-white">
                            @foreach ($bulanOptions as $opt)
                                <option value="{{ $opt['val'] }}" {{ $bulanTerpilih === $opt['val'] ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                            @endforeach
                        </select>
                    </form>

                    <!-- Tombol Utama: Cetak & Unduh Rekapitulasi PDF Resmi -->
                    <a href="{{ route('admin.absensi-sholat.cetak-rekap', ['bulan' => $bulanTerpilih]) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition shrink-0"
                       title="Buka dokumen rekapitulasi resmi untuk dicetak atau diunduh ke PDF">
                        <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Cetak & Unduh Rekap PDF</span>
                    </a>
                </div>
            </div>

            <!-- Tabel Rekapitulasi -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs divide-y divide-slate-100">
                        <thead class="bg-slate-50 text-[11px] font-bold uppercase text-slate-600">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Nama Petugas</th>
                                <th class="px-4 py-3 text-center">Bagian</th>
                                <th class="px-4 py-3 text-center text-emerald-800">Tepat Waktu</th>
                                <th class="px-4 py-3 text-center text-amber-700">Terlambat</th>
                                <th class="px-4 py-3 text-center text-emerald-700 font-black">Total Hadir</th>
                                <th class="px-4 py-3 text-center text-amber-600">Sakit</th>
                                <th class="px-4 py-3 text-center text-blue-600">Izin</th>
                                <th class="px-4 py-3 text-center text-rose-600">Alfa</th>
                                <th class="px-4 py-3 text-center">Total Sesi</th>
                                <th class="px-4 py-3 text-right">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($rekapBulanan as $idx => $r)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-4 py-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3 font-bold text-slate-900">{{ $r->petugas->nama }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $r->petugas->bagian === 'koordinator' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $r->petugas->bagian }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono font-semibold text-emerald-700">{{ $r->hadir_tepat }}</td>
                                    <td class="px-4 py-3 text-center font-mono font-semibold text-amber-600">{{ $r->hadir_telat }}</td>
                                    <td class="px-4 py-3 text-center font-mono font-black text-emerald-800 bg-emerald-50/50">{{ $r->hadir_total }}</td>
                                    <td class="px-4 py-3 text-center font-mono text-amber-600">{{ $r->sakit }}</td>
                                    <td class="px-4 py-3 text-center font-mono text-blue-600">{{ $r->izin }}</td>
                                    <td class="px-4 py-3 text-center font-mono text-rose-600 {{ $r->alfa > 0 ? 'font-bold' : '' }}">{{ $r->alfa }}</td>
                                    <td class="px-4 py-3 text-center font-mono text-slate-500">{{ $r->total_sesi }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="px-2 py-1 rounded-lg text-xs font-mono font-black {{ $r->persentase_hadir >= 80 ? 'bg-emerald-100 text-emerald-800' : ($r->persentase_hadir >= 60 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                            {{ $r->persentase_hadir }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-8 text-center text-slate-400 italic">
                                        Belum ada data petugas korlap sholat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 5: SETELAN JAM 5 WAKTU SHOLAT                                         -->
    <!-- ========================================================================= -->
    @if ($activeTab === 'jadwal')
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4 sm:p-6 space-y-5">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-slate-900 uppercase tracking-wide">
                    Pengaturan Jam Buka, Tepat Waktu, dan Jam Tutup 5 Sholat
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Tentukan batas jam pengisian presensi oleh Koordinator. Petugas yang mengisi setelah jam tepat akan otomatis berkategori "Terlambat".
                </p>
            </div>

            <form method="POST" action="{{ route('admin.absensi-sholat.update-jadwal') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="divide-y divide-slate-100">
                    @foreach (App\Models\AbsensiSholatJadwal::SHOLAT_LIST as $key => $lbl)
                        @php $jdw = $jadwals->get($key); @endphp
                        <div class="py-3.5 first:pt-0 last:pb-0 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-3">
                                <span class="text-sm font-black text-slate-800 block uppercase">{{ $lbl }}</span>
                                <span class="text-[11px] text-slate-400">Sholat {{ $lbl }}</span>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Jam Buka</label>
                                <input type="time" name="jadwals[{{ $key }}][jam_buka]" value="{{ substr($jdw->jam_buka ?? '00:00', 0, 5) }}" required class="w-full text-xs font-semibold rounded-xl border-slate-300 py-1.5 px-2.5">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Batas Tepat Waktu</label>
                                <input type="time" name="jadwals[{{ $key }}][jam_tepat]" value="{{ substr($jdw->jam_tepat ?? '00:00', 0, 5) }}" required class="w-full text-xs font-semibold rounded-xl border-slate-300 py-1.5 px-2.5">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Jam Tutup</label>
                                <input type="time" name="jadwals[{{ $key }}][jam_tutup]" value="{{ substr($jdw->jam_tutup ?? '00:00', 0, 5) }}" required class="w-full text-xs font-semibold rounded-xl border-slate-300 py-1.5 px-2.5">
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Kontrol Manual</label>
                                <select name="jadwals[{{ $key }}][is_buka_manual]" class="w-full text-xs font-semibold rounded-xl border-slate-300 py-1.5 px-2.5">
                                    <option value="otomatis" {{ $jdw?->is_buka_manual === null ? 'selected' : '' }}>Otomatis (Ikuti Jam)</option>
                                    <option value="buka" {{ $jdw?->is_buka_manual === true ? 'selected' : '' }}>Buka Paksa (Manual)</option>
                                    <option value="tutup" {{ $jdw?->is_buka_manual === false ? 'selected' : '' }}>Tutup Paksa (Manual)</option>
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow-xs transition">
                        Simpan Pengaturan Jam Sholat
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 6: KELOLA MASTER PETUGAS KORLAP                                       -->
    <!-- ========================================================================= -->
    @if ($activeTab === 'petugas')
        <div class="space-y-4">
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide">
                        Master Data Petugas Korlap Sholat Jamaah
                    </h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Daftar petugas yang akan muncul pada formulir presensi koordinator di link publik.
                    </p>
                </div>

                <button type="button" @click="openCreatePetugasModal()" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Petugas Korlap</span>
                </button>
            </div>

            <!-- Tabel Petugas -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs divide-y divide-slate-100">
                        <thead class="bg-slate-50 text-[11px] font-bold uppercase text-slate-600">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Nama Petugas</th>
                                <th class="px-4 py-3 text-center">Bagian</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3">Jadwal Sholat</th>
                                <th class="px-4 py-3">Sumber Data</th>
                                <th class="px-4 py-3">Keterangan</th>
                                <th class="px-4 py-3 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($semuaPetugas as $idx => $p)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-4 py-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3 font-bold text-slate-900">{{ $p->nama }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $p->bagian === 'koordinator' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700' }}">
                                            {{ ucfirst($p->bagian) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $p->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ ucfirst($p->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if (empty($p->jadwal_sholat) || count($p->jadwal_sholat) === 5)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                Semua Sholat
                                            </span>
                                        @else
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($p->jadwal_sholat as $js)
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-slate-100 text-slate-700">
                                                        {{ $js }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">
                                        {{ $p->pengurus_id ? 'Tersinkron Pengurus' : 'Input Manual' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-500 italic">{{ $p->keterangan ?: '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click="openEditPetugasModal({{ $p->id }}, {{ \Illuminate\Support\Js::from($p->nama) }}, {{ \Illuminate\Support\Js::from($p->bagian) }}, {{ \Illuminate\Support\Js::from($p->status) }}, {{ \Illuminate\Support\Js::from($p->keterangan ?? '') }}, {{ \Illuminate\Support\Js::from($p->jadwal_sholat ?? ['subuh','dzuhur','ashar','maghrib','isya']) }})" class="p-1 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-slate-100 transition" title="Edit Petugas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>

                                            <form method="POST" action="{{ route('admin.absensi-sholat.petugas.destroy', $p->id) }}" onsubmit="return confirm('Hapus petugas {{ $p->nama }}?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Petugas">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-slate-400 italic">
                                        Belum ada petugas korlap. Klik "Tambah Petugas Korlap" di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 1: TAMBAH PETUGAS KORLAP (2 CARA: PENGURUS ATAU MANUAL)            -->
    <!-- ========================================================================= -->
    <div x-show="modalPetugasCreate" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="modalPetugasCreate = false"></div>

            <div class="inline-block w-full max-w-md p-5 sm:p-6 my-8 text-left bg-white rounded-2xl shadow-xl transform transition-all relative z-10 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Tambah Petugas Korlap</h3>
                    <button type="button" @click="modalPetugasCreate = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.absensi-sholat.petugas.store') }}" class="space-y-3.5">
                    @csrf

                    <!-- 2 Opsi Sumber Nama: Dari Pengurus atau Input Manual -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Metode Penginputan Nama</label>
                        <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl">
                            <button type="button" @click="sumberNama = 'pengurus'" class="py-1.5 text-xs font-bold rounded-lg transition text-center" :class="sumberNama === 'pengurus' ? 'bg-white text-emerald-800 shadow-xs' : 'text-slate-600'">
                                Dari Data Pengurus
                            </button>
                            <button type="button" @click="sumberNama = 'manual'" class="py-1.5 text-xs font-bold rounded-lg transition text-center" :class="sumberNama === 'manual' ? 'bg-white text-emerald-800 shadow-xs' : 'text-slate-600'">
                                Input Manual
                            </button>
                        </div>
                        <input type="hidden" name="sumber_nama" :value="sumberNama">
                    </div>

                    <!-- Input Nama dari Pengurus: Searchable Combobox (Bisa Ketik Langsung Muncul Namanya) -->
                    <div x-show="sumberNama === 'pengurus'" class="relative" @click.away="dropdownPengurusOpen = false">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Pilih Pengurus <span class="text-rose-500">*</span>
                        </label>
                        
                        <div class="relative">
                            <input type="text"
                                   x-model="searchPengurus"
                                   @focus="dropdownPengurusOpen = true"
                                   @input="dropdownPengurusOpen = true; selectedPengurusId = ''"
                                   placeholder="Ketik untuk mencari nama pengurus..."
                                   class="w-full text-xs font-semibold rounded-xl border-slate-300 py-2.5 pl-3 pr-8 bg-white focus:ring-emerald-500 focus:border-emerald-500">
                            
                            <!-- Tombol Reset / Ikon Search -->
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2.5">
                                <template x-if="selectedPengurusId">
                                    <button type="button" @click="clearPengurus()" class="p-1 rounded text-slate-400 hover:text-rose-600 transition" title="Ganti Pengurus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </template>
                                <template x-if="!selectedPengurusId">
                                    <svg class="w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </template>
                            </div>
                        </div>

                        <!-- Hidden Input untuk dikirim ke Controller -->
                        <input type="hidden" name="pengurus_id" :value="selectedPengurusId">

                        <!-- Floating List Hasil Pencarian Pengurus -->
                        <div x-show="dropdownPengurusOpen"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-30 max-h-48 overflow-y-auto divide-y divide-slate-100">
                            <template x-for="p in filteredPengurus" :key="p.id">
                                <button type="button"
                                        @click="selectPengurus(p)"
                                        class="w-full text-left px-3 py-2 text-xs hover:bg-emerald-50 hover:text-emerald-900 transition flex items-center justify-between group">
                                    <span class="font-bold text-slate-800 group-hover:text-emerald-800" x-text="p.nama"></span>
                                    <span class="text-[10px] text-slate-400 group-hover:text-emerald-700 italic ml-2 shrink-0" x-text="p.jabatan"></span>
                                </button>
                            </template>
                            <div x-show="filteredPengurus.length === 0" class="px-3 py-3 text-xs text-slate-400 italic text-center">
                                Tidak ada pengurus dengan nama "<span class="font-bold text-slate-600" x-text="searchPengurus"></span>"
                            </div>
                        </div>

                        <!-- Info Terpilih -->
                        <div x-show="selectedPengurusId" class="mt-1 flex items-center gap-1.5 text-[11px] text-emerald-700 font-bold">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Terpilih: <span x-text="selectedPengurusNama" class="underline"></span></span>
                        </div>
                    </div>

                    <!-- Input Nama Manual -->
                    <div x-show="sumberNama === 'manual'">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap Petugas <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" placeholder="Contoh: M. Zaini / Santri Kamar 3" class="w-full text-xs rounded-xl border-slate-300 py-2 px-3">
                    </div>

                    <!-- Pilihan Bagian: Koordinator / Anggota -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bagian / Peran</label>
                        <select name="bagian" required class="w-full text-xs font-semibold rounded-xl border-slate-300 py-2 px-3 bg-white">
                            <option value="anggota">Anggota Korlap</option>
                            <option value="koordinator">Koordinator (Pengabsen)</option>
                        </select>
                    </div>

                    <!-- Status: Aktif / Nonaktif -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Keaktifan</label>
                        <select name="status" required class="w-full text-xs font-semibold rounded-xl border-slate-300 py-2 px-3 bg-white">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <!-- Pilihan Jadwal Piket Sholat (Format Dropdown / Drop Saja) -->
                    <div class="relative" @click.away="dropdownJadwalCreateOpen = false">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Jadwal Piket Sholat <span class="text-rose-500">*</span>
                        </label>

                        <button type="button"
                                @click="dropdownJadwalCreateOpen = !dropdownJadwalCreateOpen"
                                class="w-full flex items-center justify-between text-xs font-semibold rounded-xl border border-slate-300 py-2 px-3 bg-white hover:border-slate-400 transition text-left">
                            <span class="truncate text-slate-800" x-text="getJadwalLabel(createJadwals)"></span>
                            <div class="flex items-center gap-1.5 shrink-0 ml-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800"
                                      x-text="createJadwals.length + ' Sholat'"></span>
                                <svg class="w-4 h-4 text-slate-400 transition transform" :class="dropdownJadwalCreateOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>

                        <!-- Menu Dropdown Checklist Sholat -->
                        <div x-show="dropdownJadwalCreateOpen"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-30 p-2 space-y-1">
                            <div class="flex items-center justify-between pb-1.5 mb-1 border-b border-slate-100 px-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Pilih Waktu Sholat</span>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="pilihSemuaSholatCreate()" class="text-[10px] font-bold text-emerald-700 hover:underline">Pilih Semua</button>
                                    <span class="text-slate-300">|</span>
                                    <button type="button" @click="createJadwals = []" class="text-[10px] font-bold text-slate-500 hover:text-rose-600">Kosongkan</button>
                                </div>
                            </div>

                            @foreach (App\Models\AbsensiSholatJadwal::SHOLAT_LIST as $sholatKey => $sholatLabel)
                                <label class="flex items-center justify-between px-2.5 py-1.5 rounded-lg hover:bg-emerald-50/60 cursor-pointer text-xs font-semibold text-slate-700 transition">
                                    <span class="flex items-center gap-2">
                                        <input type="checkbox" name="jadwal_sholat[]" value="{{ $sholatKey }}" x-model="createJadwals" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        <span>Sholat {{ $sholatLabel }}</span>
                                    </span>
                                    <span class="text-[10px] font-mono font-bold uppercase text-slate-400">{{ $sholatKey }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1 italic">
                            * Klik dropdown di atas untuk memilih waktu sholat piket petugas.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keterangan / Catatan Tambahan</label>
                        <input type="text" name="keterangan" placeholder="Opsional (misal: Shift Pagi / Kamar B-04)" class="w-full text-xs rounded-xl border-slate-300 py-2 px-3">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="modalPetugasCreate = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition">Simpan Petugas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: EDIT PETUGAS KORLAP                                              -->
    <!-- ========================================================================= -->
    <div x-show="modalPetugasEdit" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="modalPetugasEdit = false"></div>

            <div class="inline-block w-full max-w-md p-5 sm:p-6 my-8 text-left bg-white rounded-2xl shadow-xl transform transition-all relative z-10 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Edit Data Petugas</h3>
                    <button type="button" @click="modalPetugasEdit = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form :action="'{{ url('/admin/absensi-sholat/petugas') }}/' + editPetugasData.id" method="POST" class="space-y-3.5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Petugas</label>
                        <input type="text" name="nama" x-model="editPetugasData.nama" required class="w-full text-xs rounded-xl border-slate-300 py-2 px-3">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bagian / Peran</label>
                        <select name="bagian" x-model="editPetugasData.bagian" required class="w-full text-xs font-semibold rounded-xl border-slate-300 py-2 px-3 bg-white">
                            <option value="anggota">Anggota Korlap</option>
                            <option value="koordinator">Koordinator (Pengabsen)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status</label>
                        <select name="status" x-model="editPetugasData.status" required class="w-full text-xs font-semibold rounded-xl border-slate-300 py-2 px-3 bg-white">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <!-- Pilihan Jadwal Piket Sholat (Format Dropdown / Drop Saja) -->
                    <div class="relative" @click.away="dropdownJadwalEditOpen = false">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Jadwal Piket Sholat <span class="text-rose-500">*</span>
                        </label>

                        <button type="button"
                                @click="dropdownJadwalEditOpen = !dropdownJadwalEditOpen"
                                class="w-full flex items-center justify-between text-xs font-semibold rounded-xl border border-slate-300 py-2 px-3 bg-white hover:border-slate-400 transition text-left">
                            <span class="truncate text-slate-800" x-text="getJadwalLabel(editPetugasData.jadwal_sholat)"></span>
                            <div class="flex items-center gap-1.5 shrink-0 ml-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800"
                                      x-text="(editPetugasData.jadwal_sholat ? editPetugasData.jadwal_sholat.length : 0) + ' Sholat'"></span>
                                <svg class="w-4 h-4 text-slate-400 transition transform" :class="dropdownJadwalEditOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>

                        <!-- Menu Dropdown Checklist Sholat -->
                        <div x-show="dropdownJadwalEditOpen"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-30 p-2 space-y-1">
                            <div class="flex items-center justify-between pb-1.5 mb-1 border-b border-slate-100 px-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Pilih Waktu Sholat</span>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="pilihSemuaSholatEdit()" class="text-[10px] font-bold text-emerald-700 hover:underline">Pilih Semua</button>
                                    <span class="text-slate-300">|</span>
                                    <button type="button" @click="editPetugasData.jadwal_sholat = []" class="text-[10px] font-bold text-slate-500 hover:text-rose-600">Kosongkan</button>
                                </div>
                            </div>

                            @foreach (App\Models\AbsensiSholatJadwal::SHOLAT_LIST as $sholatKey => $sholatLabel)
                                <label class="flex items-center justify-between px-2.5 py-1.5 rounded-lg hover:bg-emerald-50/60 cursor-pointer text-xs font-semibold text-slate-700 transition">
                                    <span class="flex items-center gap-2">
                                        <input type="checkbox" name="jadwal_sholat[]" value="{{ $sholatKey }}" x-model="editPetugasData.jadwal_sholat" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        <span>Sholat {{ $sholatLabel }}</span>
                                    </span>
                                    <span class="text-[10px] font-mono font-bold uppercase text-slate-400">{{ $sholatKey }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1 italic">
                            * Klik dropdown di atas untuk mengubah waktu sholat piket petugas.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keterangan</label>
                        <input type="text" name="keterangan" x-model="editPetugasData.keterangan" class="w-full text-xs rounded-xl border-slate-300 py-2 px-3">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="modalPetugasEdit = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition">Perbarui Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: KOREKSI STATUS LOG PRESENSI                                      -->
    <!-- ========================================================================= -->
    <div x-show="modalEditStatus" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="modalEditStatus = false"></div>

            <div class="inline-block w-full max-w-sm p-5 my-8 text-left bg-white rounded-2xl shadow-xl transform transition-all relative z-10 space-y-3.5">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Koreksi Presensi & Keterangan</h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5" x-show="editLogData.nama">
                            <span class="font-bold text-slate-700" x-text="editLogData.nama"></span>
                            <span class="text-slate-400">&bull;</span>
                            <span class="text-emerald-700 font-semibold" x-text="'Sholat ' + editLogData.sholat"></span>
                        </p>
                    </div>
                    <button type="button" @click="modalEditStatus = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form :action="'{{ url('/admin/absensi-sholat/log') }}/' + editLogData.id" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Kehadiran</label>
                        <select name="status" x-model="editLogData.status" required class="w-full text-xs font-semibold rounded-xl border-slate-300 py-2 px-3 bg-white">
                            <option value="hadir">Hadir</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="alfa">Alfa</option>
                        </select>
                    </div>

                    <div x-show="editLogData.status === 'hadir'">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori Waktu</label>
                        <select name="kategori_waktu" x-model="editLogData.kategori_waktu" class="w-full text-xs font-semibold rounded-xl border-slate-300 py-2 px-3 bg-white">
                            <option value="tepat_waktu">Tepat Waktu</option>
                            <option value="terlambat">Terlambat</option>
                        </select>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase">Keterangan / Alasan</label>
                            <button type="button"
                                    x-show="editLogData.keterangan && editLogData.keterangan.trim() !== ''"
                                    @click="editLogData.keterangan = ''"
                                    class="text-[10px] font-bold text-rose-600 hover:underline">
                                Hapus Keterangan
                            </button>
                        </div>
                        <input type="text"
                               name="keterangan"
                               x-model="editLogData.keterangan"
                               placeholder="Tulis keterangan atau kosongkan jika tidak ada"
                               class="w-full text-xs rounded-xl border-slate-300 py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500">
                        <p class="text-[10px] text-slate-400 mt-1 italic">
                            * Kosongkan kolom ini jika ingin menghapus keterangan lama.
                        </p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="modalEditStatus = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 4: BAGIKAN RINGKASAN PRESENSI KE WHATSAPP                           -->
    <!-- ========================================================================= -->
    <div x-show="modalBagikanWa" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="modalBagikanWa = false"></div>

            <div class="inline-block w-full max-w-lg p-5 my-8 text-left bg-white rounded-2xl shadow-2xl transform transition-all relative z-10 space-y-4 border border-slate-100">
                <!-- Header Modal -->
                <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.456 5.711 1.456h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.411z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Bagikan Ringkasan ke WhatsApp</h3>
                            <p class="text-[11px] text-slate-500">Pratinjau pesan untuk grup pengurus / korlap pondok</p>
                        </div>
                    </div>
                    <button type="button" @click="modalBagikanWa = false" class="text-slate-400 hover:text-slate-600 transition p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Bubble Pratinjau Chat WhatsApp -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs px-1">
                        <span class="font-bold text-slate-700">Teks Pesan WhatsApp (Dapat Diedit):</span>
                        <span class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                            Siap Dikirim
                        </span>
                    </div>

                    <div class="relative rounded-2xl p-2.5 bg-emerald-50/40 border border-emerald-200">
                        <textarea x-model="pesanWa"
                                  rows="13"
                                  class="w-full text-xs font-mono p-3 rounded-xl border border-emerald-300 bg-white text-slate-800 leading-relaxed focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-inner"
                                  placeholder="Ketik atau edit isi pesan..."></textarea>
                    </div>

                    <p class="text-[11px] text-slate-500 italic px-1">
                        * Anda dapat menambahkan salam atau info tambahan langsung pada kotak teks di atas sebelum dikirim atau disalin.
                    </p>
                </div>

                <!-- Footer Aksi -->
                <div class="pt-3 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-between gap-2.5">
                    <button type="button"
                            @click="modalBagikanWa = false"
                            class="w-full sm:w-auto px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
                        Tutup
                    </button>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button"
                                @click="salinPesanWa()"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                            <span x-text="waCopied ? 'Tersalin! ✅' : 'Salin Teks'">Salin Teks</span>
                        </button>

                        <button type="button"
                                @click="kirimKeWa()"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.456 5.711 1.456h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.411z"/>
                            </svg>
                            <span>Kirim ke WhatsApp</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($activeTab === 'grafik')
    <!-- SCRIPT CHART.JS TERPADU 5 SHOLAT (1 CANVAS BULANAN) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('unifiedSholatChart');
            if (!ctx) return;

            const labels = @json($chartLabels);
            const datasetsData = @json($chartDatasets);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Subuh',
                            data: datasetsData.subuh,
                            borderColor: '#0284c7', // Sky-600
                            backgroundColor: 'rgba(2, 132, 199, 0.1)',
                            borderWidth: 2.5,
                            tension: 0.3,
                            pointRadius: 3,
                        },
                        {
                            label: 'Dzuhur',
                            data: datasetsData.dzuhur,
                            borderColor: '#eab308', // Amber-500
                            backgroundColor: 'rgba(234, 179, 8, 0.1)',
                            borderWidth: 2.5,
                            tension: 0.3,
                            pointRadius: 3,
                        },
                        {
                            label: 'Ashar',
                            data: datasetsData.ashar,
                            borderColor: '#f97316', // Orange-500
                            backgroundColor: 'rgba(249, 115, 22, 0.1)',
                            borderWidth: 2.5,
                            tension: 0.3,
                            pointRadius: 3,
                        },
                        {
                            label: 'Maghrib',
                            data: datasetsData.maghrib,
                            borderColor: '#10b981', // Emerald-500
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2.5,
                            tension: 0.3,
                            pointRadius: 3,
                        },
                        {
                            label: 'Isya',
                            data: datasetsData.isya,
                            borderColor: '#8b5cf6', // Violet-500
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            borderWidth: 2.5,
                            tension: 0.3,
                            pointRadius: 3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 20,
                                boxHeight: 3,
                                font: {
                                    family: 'Figtree, sans-serif',
                                    size: 11,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return 'Tanggal ' + context[0].label + ' {{ $dtBulan->locale('id')->translatedFormat('F Y') }}';
                                },
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + ' Petugas Hadir';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Tanggal (Hari ke-)',
                                font: { size: 10, weight: 'bold' }
                            },
                            grid: {
                                display: true,
                                color: 'rgba(226, 232, 240, 0.7)',
                                borderDash: [3, 4],
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Jumlah Petugas Hadir (Orang)',
                                font: { size: 10, weight: 'bold' }
                            },
                            beginAtZero: true,
                            suggestedMax: {{ $suggestedMaxY ?? 10 }},
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                font: { weight: 'bold' }
                            },
                            grid: {
                                display: true,
                                color: 'rgba(100, 116, 139, 0.25)', // Slate pembatas lembut
                                borderDash: [6, 6], // Garis putus-putus pembatas jumlah hadir!
                            }
                        }
                    }
                }
            });
        });
    </script>
@endif

<script>
    function absensiAdminApp() {
        return {
            copied: false,
            modalPetugasCreate: false,
            modalPetugasEdit: false,
            modalEditStatus: false,
            modalBagikanWa: false,
            waCopied: false,
            pesanWa: @json($pesanWaText ?? ''),
            sumberNama: 'pengurus',
            createJadwals: ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'],
            editPetugasData: { id: null, nama: '', bagian: 'anggota', status: 'aktif', keterangan: '', jadwal_sholat: [] },
            editLogData: { id: null, nama: '', sholat: '', status: 'hadir', kategori_waktu: 'tepat_waktu', keterangan: '' },

            // Searchable Pengurus Combobox State
            searchPengurus: '',
            selectedPengurusId: '',
            selectedPengurusNama: '',
            dropdownPengurusOpen: false,
            daftarPengurus: @json($pengurusList->map(fn($p) => ['id' => $p->id, 'nama' => $p->nama, 'jabatan' => $p->jabatan ?? 'Pengurus'])),

            // Dropdown Jadwal Sholat Menu State
            dropdownJadwalCreateOpen: false,
            dropdownJadwalEditOpen: false,

            get filteredPengurus() {
                if (!this.searchPengurus || !this.searchPengurus.trim()) {
                    return this.daftarPengurus;
                }
                const q = this.searchPengurus.toLowerCase();
                return this.daftarPengurus.filter(p =>
                    p.nama.toLowerCase().includes(q) ||
                    (p.jabatan && p.jabatan.toLowerCase().includes(q))
                );
            },
            selectPengurus(p) {
                this.selectedPengurusId = p.id;
                this.selectedPengurusNama = p.nama + (p.jabatan ? ' (' + p.jabatan + ')' : '');
                this.searchPengurus = p.nama;
                this.dropdownPengurusOpen = false;
            },
            clearPengurus() {
                this.selectedPengurusId = '';
                this.selectedPengurusNama = '';
                this.searchPengurus = '';
                this.dropdownPengurusOpen = true;
            },
            getJadwalLabel(list) {
                if (!list || list.length === 0) return '-- Pilih Waktu Sholat --';
                if (list.length === 5) return 'Semua Sholat (Subuh - Isya)';
                const namaMap = { subuh: 'Subuh', dzuhur: 'Dzuhur', ashar: 'Ashar', maghrib: 'Maghrib', isya: 'Isya' };
                return list.map(k => namaMap[k] || k).join(', ');
            },

            copyLink(url) {
                navigator.clipboard.writeText(url).then(() => {
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2500);
                }).catch(() => {
                    prompt('Salin link berikut:', url);
                });
            },
            openModalBagikanWa() {
                this.modalBagikanWa = true;
                this.waCopied = false;
            },
            salinPesanWa() {
                navigator.clipboard.writeText(this.pesanWa).then(() => {
                    this.waCopied = true;
                    setTimeout(() => { this.waCopied = false; }, 2500);
                }).catch(() => {
                    prompt('Salin pesan berikut:', this.pesanWa);
                });
            },
            kirimKeWa() {
                const url = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(this.pesanWa);
                window.open(url, '_blank');
            },
            openCreatePetugasModal() {
                this.sumberNama = 'pengurus';
                this.searchPengurus = '';
                this.selectedPengurusId = '';
                this.selectedPengurusNama = '';
                this.dropdownPengurusOpen = false;
                this.dropdownJadwalCreateOpen = false;
                this.createJadwals = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
                this.modalPetugasCreate = true;
            },
            pilihSemuaSholatCreate() {
                this.createJadwals = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
            },
            openEditPetugasModal(id, nama, bagian, status, keterangan, jadwalSholat) {
                this.dropdownJadwalEditOpen = false;
                this.editPetugasData = {
                    id: id,
                    nama: nama,
                    bagian: bagian,
                    status: status,
                    keterangan: keterangan,
                    jadwal_sholat: (Array.isArray(jadwalSholat) && jadwalSholat.length > 0) ? jadwalSholat : ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya']
                };
                this.modalPetugasEdit = true;
            },
            pilihSemuaSholatEdit() {
                this.editPetugasData.jadwal_sholat = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
            },
            openEditStatusModal(id, nama, sholat, status, kat, ket) {
                this.editLogData = {
                    id: id,
                    nama: nama || '',
                    sholat: sholat || '',
                    status: status || 'hadir',
                    kategori_waktu: kat || 'tepat_waktu',
                    keterangan: ket || ''
                };
                this.modalEditStatus = true;
            }
        }
    }
</script>
@endsection
