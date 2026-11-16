<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Presensi Korlap Sholat Jamaah - Ponpes Al-Falah Putra</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
        .swal2-popup {
            border-radius: 1.25rem !important;
            font-family: inherit !important;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased min-h-screen pb-28">

    <!-- ========================================================================= -->
    <!-- HEADER RESMI PESANTREN (STICKY TOP: TIDAK IKUT TER-SCROLL DI MOBILE)     -->
    <!-- ========================================================================= -->
    <header class="sticky top-0 z-40 bg-emerald-900/95 backdrop-blur-md text-white shadow-md border-b border-emerald-800/80">
        <div class="max-w-2xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <picture class="shrink-0">
                    <source srcset="{{ asset('images/logo-nav.webp') }}" type="image/webp">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 sm:w-9 sm:h-9 object-contain drop-shadow-sm">
                </picture>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-300 block truncate">Pondok Pesantren Al-Falah Putra</span>
                    <h1 class="text-xs sm:text-sm font-black tracking-tight text-white truncate">Presensi Korlap Sholat Jamaah</h1>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="text-[11px] font-mono font-bold text-emerald-200 block">{{ $now->locale('id')->translatedFormat('d M Y') }}</span>
                <span class="text-[10px] text-emerald-300/90 font-mono" id="realtime-clock">{{ $now->format('H:i') }} WIB</span>
            </div>
        </div>
    </header>

    <!-- KONTEN UTAMA -->
    <main class="max-w-2xl mx-auto px-3.5 sm:px-4 py-4 space-y-4" x-data="absensiPublikApp()">

        <!-- NOTIFIKASI FLASH SUKSES / ERROR -->
        @if (session('success'))
            <div class="p-3.5 sm:p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 shadow-xs flex items-start gap-3">
                <span class="p-1 rounded-full bg-emerald-200 text-emerald-800 shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                <div class="min-w-0 flex-1 text-xs sm:text-sm font-semibold">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-3.5 sm:p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 shadow-xs flex items-start gap-3">
                <span class="p-1 rounded-full bg-rose-200 text-rose-800 shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </span>
                <div class="min-w-0 flex-1 text-xs sm:text-sm font-semibold">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3.5 sm:p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 shadow-xs text-xs sm:text-sm">
                <p class="font-bold mb-1">Periksa kembali data Anda:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- ===================================================================== -->
        <!-- KONDISI A: JIKA WAKTU SHOLAT SEDANG DITUTUP                           -->
        <!-- ===================================================================== -->
        @if (!$isBisaAbsen)
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                <!-- Banner Status Tutup (Nuansa Elegan & Ramah) -->
                <div class="p-5 sm:p-6 bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white text-center space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-white/10 flex items-center justify-center text-amber-300 shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/90 text-white mb-1.5 shadow-xs">
                            Presensi Sedang Istirahat
                        </span>
                        <h2 class="text-lg sm:text-xl font-black text-white tracking-tight">
                            Presensi Sholat Sedang Ditutup
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-md mx-auto leading-relaxed">
                            Form presensi terdekat berikutnya adalah <strong class="text-amber-300 font-extrabold uppercase">Sholat {{ $namaSholat }}</strong> yang akan dibuka pukul <strong class="text-white font-mono font-bold">{{ substr($sholatTerpilih->jam_buka ?? '00:00', 0, 5) }} WIB</strong>.
                        </p>
                    </div>

                    <!-- COUNTDOWN TIMER MENUJU BUKA -->
                    <div class="pt-2">
                        <div class="inline-flex items-center justify-center gap-1.5 font-mono select-none bg-white/10 px-4 py-2 rounded-2xl backdrop-blur-xs border border-white/15">
                            <div class="text-center min-w-[42px]">
                                <span class="text-lg sm:text-xl font-black text-white block leading-none" x-text="hours">00</span>
                                <span class="text-[9px] font-bold text-slate-300 uppercase mt-0.5 block">Jam</span>
                            </div>
                            <span class="text-emerald-300 font-black text-lg">:</span>
                            <div class="text-center min-w-[42px]">
                                <span class="text-lg sm:text-xl font-black text-white block leading-none" x-text="minutes">00</span>
                                <span class="text-[9px] font-bold text-slate-300 uppercase mt-0.5 block">Menit</span>
                            </div>
                            <span class="text-emerald-300 font-black text-lg">:</span>
                            <div class="text-center min-w-[42px]">
                                <span class="text-lg sm:text-xl font-black text-amber-300 block leading-none" x-text="seconds">00</span>
                                <span class="text-[9px] font-bold text-amber-300 uppercase mt-0.5 block">Detik</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400">
                        * Halaman akan otomatis memperbarui tampilan saat jam buka tiba.
                    </p>
                </div>

                <!-- JADWAL 5 WAKTU SHOLAT HARI INI -->
                <div class="p-4 sm:p-5 bg-white space-y-3">
                    <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">
                            Jadwal 5 Sholat Jamaah Hari Ini:
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono font-bold">WIB</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        @foreach (App\Models\AbsensiSholatJadwal::SHOLAT_LIST as $key => $label)
                            @php
                                $j = $jadwals->get($key);
                                $isTargetSholat = ($namaSholat === $key);
                                $jamSekarangStr = $now->format('H:i:s');
                                $sudahLewat = ($j && $jamSekarangStr > $j->jam_tutup && $j->is_buka_manual !== true);
                            @endphp
                            <div class="p-2.5 rounded-xl border text-center transition {{ $isTargetSholat ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20' : 'bg-slate-50 border-slate-200' }}">
                                <span class="text-xs font-black uppercase block {{ $isTargetSholat ? 'text-emerald-950' : 'text-slate-800' }}">
                                    {{ $label }}
                                </span>
                                <span class="text-[11px] font-mono font-bold block mt-0.5 {{ $isTargetSholat ? 'text-emerald-700' : 'text-slate-600' }}">
                                    {{ substr($j->jam_buka ?? '00:00', 0, 5) }} - {{ substr($j->jam_tutup ?? '00:00', 0, 5) }}
                                </span>
                                <div class="mt-1.5">
                                    @if ($isTargetSholat)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-600 text-white">
                                            Berikutnya
                                        </span>
                                    @elseif ($sudahLewat)
                                        <span class="inline-block px-1.5 py-0.5 rounded-full text-[9px] font-semibold bg-slate-200 text-slate-500">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-block px-1.5 py-0.5 rounded-full text-[9px] font-semibold bg-slate-200 text-slate-700">
                                            Menunggu
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-3 bg-slate-50 border-t border-slate-200 text-center">
                    <p class="text-[11px] text-slate-500">
                        Pusat Layanan & Sekretariat Pondok Pesantren Al-Falah Putra
                    </p>
                </div>
            </div>

        <!-- ===================================================================== -->
        <!-- KONDISI B: JIKA WAKTU SHOLAT SEDANG DIBUKA (FORM PRESENSI AKTIF)      -->
        <!-- ===================================================================== -->
        @else
            <!-- HERO CARD: WAKTU SHOLAT AKTIF -->
            <div class="rounded-2xl border border-emerald-600/30 overflow-hidden shadow-sm bg-gradient-to-br from-emerald-800 via-emerald-700 to-teal-800 text-white p-4 sm:p-5 space-y-3">
                <div class="flex items-center justify-between gap-3 flex-wrap sm:flex-nowrap">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-200 block">Presensi Berlangsung</span>
                        <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                            <span>Sholat {{ ucfirst($namaSholat) }}</span>
                            @if ($sudahDiabsen)
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/20 text-white border border-white/30 backdrop-blur-xs">
                                    Sudah Pernah Diisi
                                </span>
                            @endif
                        </h2>
                    </div>

                    <!-- Badge Status Tepat / Terlambat -->
                    <div>
                        @if ($fase === 'tepat')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black shadow-xs bg-emerald-400 text-emerald-950">
                                <span class="w-2 h-2 rounded-full bg-emerald-950 animate-pulse"></span>
                                <span>Tepat Waktu</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black shadow-xs bg-amber-300 text-amber-950">
                                <span class="w-2 h-2 rounded-full bg-amber-950 animate-pulse"></span>
                                <span>Tenggat Terlambat</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Timeline 3 Jam Penting -->
                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-white/20 text-center">
                    <div class="rounded-xl p-1.5 sm:p-2 bg-black/20">
                        <span class="text-[9px] text-white/80 block font-semibold">1. Jam Buka</span>
                        <span class="text-xs sm:text-sm font-black font-mono text-white mt-0.5 block">
                            {{ substr($sholatTerpilih->jam_buka ?? '00:00', 0, 5) }}
                        </span>
                    </div>
                    <div class="rounded-xl p-1.5 sm:p-2 bg-black/20">
                        <span class="text-[9px] text-white/80 block font-semibold">2. Batas Tepat</span>
                        <span class="text-xs sm:text-sm font-black font-mono text-white mt-0.5 block">
                            {{ substr($sholatTerpilih->jam_tepat ?? '00:00', 0, 5) }}
                        </span>
                    </div>
                    <div class="rounded-xl p-1.5 sm:p-2 bg-black/20">
                        <span class="text-[9px] text-white/80 block font-semibold">3. Jam Tutup</span>
                        <span class="text-xs sm:text-sm font-black font-mono text-white mt-0.5 block">
                            {{ substr($sholatTerpilih->jam_tutup ?? '00:00', 0, 5) }}
                        </span>
                    </div>
                </div>

                <!-- Countdown Timer Sisa Waktu -->
                <div class="flex items-center justify-between gap-2 pt-1 text-xs">
                    <span class="text-emerald-100 font-semibold">{{ $keteranganFase }}:</span>
                    <div class="inline-flex items-center gap-1 font-mono font-bold bg-white/15 px-2.5 py-1 rounded-lg backdrop-blur-xs text-white">
                        <span x-text="hours">00</span>:<span x-text="minutes">00</span>:<span x-text="seconds">00</span>
                    </div>
                </div>
            </div>

            <!-- FORMULIR PRESENSI KOORDINATOR -->
            @if ($anggotas->isEmpty())
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 text-center space-y-2 shadow-xs">
                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="text-sm font-bold text-slate-800">Tidak ada petugas yang terjadwal pada Sholat {{ ucfirst($namaSholat) }}.</p>
                    <p class="text-xs text-slate-500">Sekretaris belum mengatur jadwal piket sholat untuk petugas pada waktu ini.</p>
                </div>
            @else
                <form id="form-absensi" method="POST" action="{{ route('absensi-sholat.store') }}" @submit="confirmSubmit($event)" class="space-y-4">
                    @csrf
                    <input type="hidden" name="waktu_sholat" value="{{ $namaSholat }}">

                    <!-- 1. PILIH NAMA KOORDINATOR YANG MENGISI -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs space-y-2">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            1. Koordinator Lapangan Pengabsen <span class="text-rose-500">*</span>
                        </label>
                        <select id="koordinator_id" name="koordinator_id" required class="w-full text-sm font-semibold rounded-xl border-slate-300 py-2.5 px-3 bg-slate-50 focus:bg-white focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                            <option value="">-- Pilih Nama Anda (Koordinator) --</option>
                            @foreach ($koordinators as $k)
                                <option value="{{ $k->id }}" {{ (old('koordinator_id', $koordinatorTerpilihId) == $k->id) ? 'selected' : '' }}>
                                    {{ $k->nama }} ({{ ucfirst($k->bagian) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 italic">
                            * Koordinator yang mengabsen otomatis dicatat hadir tepat/terlambat sesuai jam simpan.
                        </p>
                    </div>

                    <!-- 2. TOOLBAR PENCARIAN & FILTER PETUGAS JADWAL INI -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2 px-1">
                            <div>
                                <h2 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide">
                                    2. Daftar Petugas Sholat {{ ucfirst($namaSholat) }} ({{ $anggotas->count() }} Orang)
                                </h2>
                                <p class="text-[11px] text-slate-500">
                                    Hanya menampilkan petugas yang bertugas pada sholat ini. Koordinator di urutan paling atas.
                                </p>
                            </div>
                            <button type="button" @click="resetSemuaHadir()" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline shrink-0 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                Set Semua Hadir
                            </button>
                        </div>

                        <!-- Search Bar Cepat (Live Filter) -->
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text"
                                   x-model="search"
                                   placeholder="Ketik nama petugas untuk mencari cepat..."
                                   class="w-full text-sm pl-9 pr-8 py-2.5 rounded-xl border border-slate-300 bg-white shadow-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                            <button type="button" x-show="search" @click="search = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 text-xs font-bold">✕</button>
                        </div>

                        <!-- Filter Status Tabs -->
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs font-bold select-none">
                            <button type="button"
                                    @click="filterStatus = 'semua'"
                                    class="px-3 py-1.5 rounded-xl transition shrink-0"
                                    :class="filterStatus === 'semua' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'">
                                Semua ({{ $anggotas->count() }})
                            </button>
                            <button type="button"
                                    @click="filterStatus = 'hadir'"
                                    class="px-3 py-1.5 rounded-xl transition shrink-0 flex items-center gap-1"
                                    :class="filterStatus === 'hadir' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'">
                                <span>Hadir</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-800" x-text="countHadir">0</span>
                            </button>
                            <button type="button"
                                    @click="filterStatus = 'absen'"
                                    class="px-3 py-1.5 rounded-xl transition shrink-0 flex items-center gap-1"
                                    :class="filterStatus === 'absen' ? 'bg-rose-700 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'">
                                <span>Sakit / Izin / Alfa</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-100 text-rose-800" x-text="countSakit + countIzin + countAlfa">0</span>
                            </button>
                        </div>
                    </div>

                    <!-- 3. LIST KARTU PETUGAS (DI MOBILE: TOMBOL DI BAWAH NAMA, KOORDINATOR PALING ATAS) -->
                    <div class="space-y-2.5">
                        @foreach ($anggotas as $idx => $p)
                            @php
                                $presensiLama = $riwayatHariIni->get($p->id);
                                $statusAwal = $presensiLama ? $presensiLama->status : 'hadir';
                                $ketAwal = $presensiLama ? $presensiLama->keterangan : '';
                                $isKoordinator = ($p->bagian === 'koordinator');
                            @endphp
                            <div class="p-3.5 sm:p-4 rounded-2xl bg-white border shadow-xs space-y-2.5 transition"
                                 x-data="{
                                     status: '{{ old("presensi.{$p->id}.status", $statusAwal) }}',
                                     nama: '{{ addslashes($p->nama) }}',
                                     bagian: '{{ $p->bagian }}'
                                 }"
                                 x-show="
                                     (!search || nama.toLowerCase().includes(search.toLowerCase())) &&
                                     (filterStatus === 'semua' ||
                                      (filterStatus === 'hadir' && status === 'hadir') ||
                                      (filterStatus === 'absen' && status !== 'hadir'))
                                 "
                                 :class="{
                                     'border-amber-300 ring-1 ring-amber-300/40 bg-amber-50/20': status === 'sakit',
                                     'border-sky-300 ring-1 ring-sky-300/40 bg-sky-50/20': status === 'izin',
                                     'border-rose-300 ring-1 ring-rose-300/40 bg-rose-50/20': status === 'alfa',
                                     'border-slate-200/90': status === 'hadir'
                                 }">

                                <!-- BARIS ATAS: NAMA PETUGAS & BADGE -->
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <!-- Avatar Inisial -->
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0 text-white shadow-xs {{ $isKoordinator ? 'bg-purple-700' : 'bg-emerald-700' }}">
                                            <span>{{ strtoupper(substr($p->nama, 0, 2)) }}</span>
                                        </div>

                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="text-xs font-mono text-slate-400 font-bold">{{ $idx + 1 }}.</span>
                                                <span class="text-sm font-bold text-slate-900 truncate">{{ $p->nama }}</span>
                                                @if ($isKoordinator)
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-purple-100 text-purple-800 border border-purple-200 shadow-2xs">
                                                        Koordinator
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Text Indikator -->
                                    <span class="text-[11px] font-bold shrink-0 uppercase tracking-wider font-mono"
                                          :class="{
                                              'text-emerald-700': status === 'hadir',
                                              'text-amber-600': status === 'sakit',
                                              'text-sky-600': status === 'izin',
                                              'text-rose-600': status === 'alfa'
                                          }"
                                          x-text="status.toUpperCase()">
                                    </span>
                                </div>

                                <!-- BARIS BAWAH: 4 TOMBOL STATUS BESAR & ERGONOMIS DI BAWAH NAMA -->
                                <div class="grid grid-cols-4 gap-1.5 sm:gap-2 pt-1">
                                    <!-- HADIR -->
                                    <label class="cursor-pointer flex items-center justify-center py-2.5 px-2 rounded-xl text-xs font-bold transition select-none text-center min-h-[42px] border"
                                           :class="status === 'hadir' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'">
                                        <input type="radio" name="presensi[{{ $p->id }}][status]" value="hadir" x-model="status" @change="recount()" class="sr-only">
                                        <span>Hadir</span>
                                    </label>

                                    <!-- SAKIT -->
                                    <label class="cursor-pointer flex items-center justify-center py-2.5 px-2 rounded-xl text-xs font-bold transition select-none text-center min-h-[42px] border"
                                           :class="status === 'sakit' ? 'bg-amber-500 text-white border-amber-500 shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'">
                                        <input type="radio" name="presensi[{{ $p->id }}][status]" value="sakit" x-model="status" @change="recount()" class="sr-only">
                                        <span>Sakit</span>
                                    </label>

                                    <!-- IZIN -->
                                    <label class="cursor-pointer flex items-center justify-center py-2.5 px-2 rounded-xl text-xs font-bold transition select-none text-center min-h-[42px] border"
                                           :class="status === 'izin' ? 'bg-sky-600 text-white border-sky-600 shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'">
                                        <input type="radio" name="presensi[{{ $p->id }}][status]" value="izin" x-model="status" @change="recount()" class="sr-only">
                                        <span>Izin</span>
                                    </label>

                                    <!-- ALFA -->
                                    <label class="cursor-pointer flex items-center justify-center py-2.5 px-2 rounded-xl text-xs font-bold transition select-none text-center min-h-[42px] border"
                                           :class="status === 'alfa' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'">
                                        <input type="radio" name="presensi[{{ $p->id }}][status]" value="alfa" x-model="status" @change="recount()" class="sr-only">
                                        <span>Alfa</span>
                                    </label>
                                </div>

                                <!-- KOLOM KETERANGAN JIKA SAKIT ATAU IZIN -->
                                <div x-show="status === 'sakit' || status === 'izin'" x-transition class="pt-1">
                                    <input type="text"
                                           name="presensi[{{ $p->id }}][keterangan]"
                                           value="{{ old("presensi.{$p->id}.keterangan", $ketAwal) }}"
                                           placeholder="Tulis alasan izin / sakit..."
                                           class="w-full text-xs sm:text-sm rounded-xl border-amber-300 py-2 px-3 bg-amber-50/50 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- 4. TOMBOL SIMPAN MELAYANG (FLOATING STICKY BOTTOM BAR) -->
                    <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-200 px-4 py-3 z-40 shadow-2xl">
                        <div class="max-w-2xl mx-auto flex items-center justify-between gap-3">
                            <!-- Ringkasan Live Counter -->
                            <div class="flex items-center gap-1.5 text-xs font-mono font-bold shrink-0">
                                <span class="px-2 py-1 rounded-lg bg-emerald-100 text-emerald-800" title="Hadir">H: <span x-text="countHadir">0</span></span>
                                <span class="px-2 py-1 rounded-lg bg-amber-100 text-amber-800" title="Sakit">S: <span x-text="countSakit">0</span></span>
                                <span class="px-2 py-1 rounded-lg bg-sky-100 text-sky-800" title="Izin">I: <span x-text="countIzin">0</span></span>
                                <span class="px-2 py-1 rounded-lg bg-rose-100 text-rose-800" title="Alfa">A: <span x-text="countAlfa">0</span></span>
                            </div>

                            <button type="submit"
                                    class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-emerald-700 hover:bg-emerald-800 active:scale-95 transition shadow-md flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ $sudahDiabsen ? 'Perbarui Presensi' : 'Simpan Presensi ' . ucfirst($namaSholat) }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        @endif
    </main>

    <script>
        function absensiPublikApp() {
            return {
                search: '',
                filterStatus: 'semua',
                countHadir: 0,
                countSakit: 0,
                countIzin: 0,
                countAlfa: 0,
                targetEpoch: {{ $targetEpochMs }},
                diffSeconds: 0,
                hours: '00',
                minutes: '00',
                seconds: '00',
                isExpired: false,

                init() {
                    this.recount();
                    this.initCountdown();

                    // Realtime clock header
                    setInterval(() => {
                        const d = new Date();
                        const h = String(d.getHours()).padStart(2, '0');
                        const m = String(d.getMinutes()).padStart(2, '0');
                        const el = document.getElementById('realtime-clock');
                        if (el) el.innerText = h + ':' + m + ' WIB';
                    }, 1000);
                },

                initCountdown() {
                    this.updateCountdown();
                    setInterval(() => {
                        this.updateCountdown();
                    }, 1000);
                },

                updateCountdown() {
                    const nowMs = Date.now();
                    const diff = Math.max(0, Math.floor((this.targetEpoch - nowMs) / 1000));
                    this.diffSeconds = diff;

                    if (diff <= 0) {
                        this.hours = '00';
                        this.minutes = '00';
                        this.seconds = '00';
                        if (!this.isExpired) {
                            this.isExpired = true;
                            setTimeout(() => {
                                window.location.reload();
                            }, 2500);
                        }
                        return;
                    }

                    const h = Math.floor(diff / 3600);
                    const m = Math.floor((diff % 3600) / 60);
                    const s = diff % 60;

                    this.hours = String(h).padStart(2, '0');
                    this.minutes = String(m).padStart(2, '0');
                    this.seconds = String(s).padStart(2, '0');
                },

                recount() {
                    let h = 0, s = 0, i = 0, a = 0;
                    document.querySelectorAll('input[type="radio"]:checked').forEach(el => {
                        if (el.value === 'hadir') h++;
                        else if (el.value === 'sakit') s++;
                        else if (el.value === 'izin') i++;
                        else if (el.value === 'alfa') a++;
                    });
                    this.countHadir = h;
                    this.countSakit = s;
                    this.countIzin = i;
                    this.countAlfa = a;
                },

                resetSemuaHadir() {
                    document.querySelectorAll('input[type="radio"][value="hadir"]').forEach(el => {
                        el.checked = true;
                        el.dispatchEvent(new Event('change'));
                    });
                    this.recount();
                },

                confirmSubmit(e) {
                    e.preventDefault();
                    const form = e.target;

                    // 1. Validasi Koordinator Terpilih
                    const koordinatorSelect = document.getElementById('koordinator_id');
                    if (!koordinatorSelect || !koordinatorSelect.value) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Koordinator Belum Dipilih',
                                text: 'Mohon pilih nama Koordinator Lapangan yang bertugas terlebih dahulu.',
                                confirmButtonColor: '#047857',
                                confirmButtonText: 'Baik, Saya Pilih'
                            });
                        } else {
                            alert('Mohon pilih nama Koordinator Lapangan terlebih dahulu.');
                        }
                        return;
                    }

                    const koordinatorNama = koordinatorSelect.options[koordinatorSelect.selectedIndex].text;
                    const namaSholatCap = "{{ ucfirst($namaSholat) }}";

                    // 2. Pop Up SweetAlert2 Konfirmasi
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Konfirmasi Presensi ' + namaSholatCap,
                            html: `
                                <div class="text-left text-xs sm:text-sm space-y-2 p-3 bg-slate-50 rounded-xl border border-slate-200 mt-2">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500 font-semibold">Waktu Sholat:</span>
                                        <strong class="text-slate-900">${namaSholatCap}</strong>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500 font-semibold">Koordinator:</span>
                                        <strong class="text-slate-900 truncate max-w-[200px]">${koordinatorNama}</strong>
                                    </div>
                                    <div class="pt-2 border-t border-slate-200 flex justify-between font-mono font-bold text-xs">
                                        <span class="text-emerald-700">H: ${this.countHadir}</span>
                                        <span class="text-amber-700">S: ${this.countSakit}</span>
                                        <span class="text-blue-700">I: ${this.countIzin}</span>
                                        <span class="text-rose-700">A: ${this.countAlfa}</span>
                                    </div>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-3">Apakah seluruh data presensi di atas sudah sesuai dan siap dikirim?</p>
                            `,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#047857',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Simpan Presensi',
                            cancelButtonText: 'Batal / Cek Lagi',
                            reverseButtons: true,
                            focusConfirm: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        // Fallback browser confirm jika SweetAlert belum siap
                        if (confirm(`Simpan presensi sholat ${namaSholatCap}? Hadir: ${this.countHadir}, Sakit: ${this.countSakit}, Izin: ${this.countIzin}, Alfa: ${this.countAlfa}`)) {
                            form.submit();
                        }
                    }
                }
            }
        }
    </script>
</body>
</html>
