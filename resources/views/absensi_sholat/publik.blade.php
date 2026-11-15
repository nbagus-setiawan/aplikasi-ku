<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
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
        /* Custom solid helper styles to prevent Tailwind purge issues on production */
        .sap-bg-header { background: linear-gradient(135deg, #022c22 0%, #064e3b 50%, #134e4a 100%); }
        .sap-bg-card-open { background: linear-gradient(135deg, #064e3b 0%, #047857 100%); }
        .sap-bg-card-late { background: linear-gradient(135deg, #78350f 0%, #b45309 100%); }
        .sap-bg-card-closed { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased min-h-screen pb-24">

    <!-- HEADER RESMI PESANTREN (MOBILE-FIRST) -->
    <header class="sap-bg-header text-white shadow-md border-b border-emerald-800" style="background-color: #064e3b;">
        <div class="max-w-2xl mx-auto px-4 py-3.5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <picture class="shrink-0">
                    <source srcset="{{ asset('images/logo-nav.webp') }}" type="image/webp">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-9 h-9 object-contain drop-shadow-md">
                </picture>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-300 block">Pondok Pesantren Al-Falah Putra</span>
                    <h1 class="text-sm sm:text-base font-black tracking-tight text-white truncate">Presensi Korlap Sholat Jamaah</h1>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="text-xs font-mono font-bold text-emerald-200 block">{{ $now->locale('id')->translatedFormat('d M Y') }}</span>
                <span class="text-[11px] text-emerald-300/90 font-mono" id="realtime-clock">{{ $now->format('H:i') }} WIB</span>
            </div>
        </div>
    </header>

    <!-- KONTEN UTAMA -->
    <main class="max-w-2xl mx-auto px-3 sm:px-4 py-4 space-y-4" x-data="absensiPublikApp()">

        <!-- NOTIFIKASI FLASH SUKSES / ERROR -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 shadow-xs flex items-start gap-3">
                <span class="p-1 rounded-full bg-emerald-200 text-emerald-800 shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                <div class="min-w-0 flex-1 text-xs sm:text-sm font-semibold">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 shadow-xs flex items-start gap-3">
                <span class="p-1 rounded-full bg-rose-200 text-rose-800 shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </span>
                <div class="min-w-0 flex-1 text-xs sm:text-sm font-semibold">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 shadow-xs text-xs sm:text-sm">
                <p class="font-bold mb-1">Periksa kembali data Anda:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- ===================================================================== -->
        <!-- KONDISI A: JIKA WAKTU SHOLAT SEDANG DITUTUP (DI LUAR JAM SHOLAT)       -->
        <!-- ===================================================================== -->
        @if (!$isBisaAbsen)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <!-- Header Kartu Status Tutup (Dark Navy Solid dengan Teks Putih Kontras) -->
                <div class="p-5 sm:p-6 sap-bg-card-closed text-white" style="background-color: #0f172a; color: #ffffff;">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-inner" style="background-color: rgba(255,255,255,0.12);">
                                <svg class="w-6 h-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-300 block">Status Presensi Saat Ini</span>
                                <h2 class="text-lg sm:text-xl font-black text-white tracking-tight mt-0.5">
                                    Presensi Sedang Ditutup
                                </h2>
                            </div>
                        </div>

                        <span class="px-3 py-1 rounded-full text-xs font-black shrink-0 shadow-xs" style="background-color: #ef4444; color: #ffffff;">
                            Ditutup
                        </span>
                    </div>

                    <!-- Keterangan Sholat Berikutnya -->
                    <div class="mt-4 pt-4 border-t border-slate-700/80">
                        <p class="text-xs sm:text-sm text-slate-200 leading-relaxed">
                            Form presensi terdekat berikutnya adalah <strong class="text-amber-300 font-extrabold uppercase">Sholat {{ $namaSholat }}</strong>. Form akan otomatis dibuka pada pukul <strong class="text-white font-mono font-bold">{{ substr($sholatTerpilih->jam_buka ?? '00:00', 0, 5) }} WIB</strong>.
                        </p>
                    </div>
                </div>

                <!-- HITUNGAN MUNDUR MENUJU BUKA (HERO COUNTDOWN) -->
                <div class="p-5 sm:p-6 bg-slate-50 border-t border-slate-200 text-center space-y-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">
                        Hitungan Mundur Menuju Buka:
                    </span>
                    <p class="text-sm sm:text-base font-black text-slate-800">
                        Form Sholat {{ ucfirst($namaSholat) }} Dibuka Dalam:
                    </p>

                    <!-- ANGKA HITUNGAN MUNDUR (DIGITAL TIMER BESAR) -->
                    <div class="inline-flex items-center justify-center gap-2 font-mono select-none py-1">
                        <!-- KOTAK JAM -->
                        <div class="bg-white border-2 border-slate-300 rounded-2xl px-3.5 py-2.5 shadow-xs text-center min-w-[56px]">
                            <span class="text-xl sm:text-2xl font-black text-slate-900 block leading-none" x-text="hours">00</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight mt-1 block">Jam</span>
                        </div>
                        <span class="text-slate-400 font-black text-xl">:</span>
                        <!-- KOTAK MENIT -->
                        <div class="bg-white border-2 border-slate-300 rounded-2xl px-3.5 py-2.5 shadow-xs text-center min-w-[56px]">
                            <span class="text-xl sm:text-2xl font-black text-slate-900 block leading-none" x-text="minutes">00</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight mt-1 block">Menit</span>
                        </div>
                        <span class="text-slate-400 font-black text-xl">:</span>
                        <!-- KOTAK DETIK -->
                        <div class="bg-white border-2 border-emerald-600 rounded-2xl px-3.5 py-2.5 shadow-xs text-center min-w-[56px]">
                            <span class="text-xl sm:text-2xl font-black text-emerald-700 block leading-none" x-text="seconds">00</span>
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-tight mt-1 block">Detik</span>
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-500 max-w-md mx-auto">
                        Halaman ini akan otomatis menyegarkan diri dan mengaktifkan formulir ketika jam buka sholat telah tiba.
                    </p>
                </div>

                <!-- JADWAL 5 WAKTU SHOLAT LENGKAP HARI INI -->
                <div class="p-4 sm:p-5 bg-white border-t border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">
                            Jadwal Presensi 5 Waktu Sholat Hari Ini:
                        </span>
                        <span class="text-[11px] text-slate-400 font-mono">WIB</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-2">
                        @foreach (App\Models\AbsensiSholatJadwal::SHOLAT_LIST as $key => $label)
                            @php
                                $j = $jadwals->get($key);
                                $isTargetSholat = ($namaSholat === $key);
                                $jamSekarangStr = $now->format('H:i:s');
                                $sudahLewat = ($j && $jamSekarangStr > $j->jam_tutup && $j->is_buka_manual !== true);
                            @endphp
                            <div class="p-3 rounded-xl border text-center transition {{ $isTargetSholat ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20' : 'bg-slate-50 border-slate-200' }}">
                                <span class="text-xs font-black uppercase block {{ $isTargetSholat ? 'text-emerald-950 font-extrabold' : 'text-slate-800' }}">
                                    {{ $label }}
                                </span>
                                <span class="text-[11px] font-mono font-bold block mt-1 {{ $isTargetSholat ? 'text-emerald-700' : 'text-slate-600' }}">
                                    {{ substr($j->jam_buka ?? '00:00', 0, 5) }} - {{ substr($j->jam_tutup ?? '00:00', 0, 5) }}
                                </span>
                                <div class="mt-2">
                                    @if ($isTargetSholat)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-600 text-white">
                                            Sholat Berikutnya
                                        </span>
                                    @elseif ($sudahLewat)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-slate-200 text-slate-500">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-slate-200 text-slate-700">
                                            Menunggu
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- BANTUAN INFORMASI SEKRETARIAT -->
                <div class="p-3.5 bg-slate-50 border-t border-slate-200 text-center">
                    <p class="text-[11px] text-slate-500">
                        Punya kendala atau perlu pengisian khusus? Hubungi <strong>Sekretaris Pondok Pesantren Al-Falah Putra</strong>.
                    </p>
                </div>
            </div>

        <!-- ===================================================================== -->
        <!-- KONDISI B: JIKA WAKTU SHOLAT SEDANG DIBUKA (FORM PRESENSI AKTIF)      -->
        <!-- ===================================================================== -->
        @else
            <!-- KARTU UTAMA WAKTU SHOLAT SEDANG DIBUKA -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <!-- Header Kartu: Hijau Zamrud Solid Kontras Tinggi -->
                <div class="p-4 sm:p-5 {{ $fase === 'tepat' ? 'sap-bg-card-open' : 'sap-bg-card-late' }}" style="background-color: {{ $fase === 'tepat' ? '#064e3b' : '#78350f' }}; color: #ffffff;">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-200/90 block">Presensi Sholat Berlangsung</span>
                            <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                                <span>Sholat {{ ucfirst($namaSholat) }}</span>
                                @if ($sudahDiabsen)
                                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-white/20 text-white border border-white/30 backdrop-blur-xs">
                                        Sudah Diisi
                                    </span>
                                @endif
                            </h2>
                        </div>

                        <!-- Badge Status -->
                        <div>
                            @if ($fase === 'tepat')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black shadow-xs" style="background-color: #34d399; color: #022c22;">
                                    <span class="w-2 h-2 rounded-full bg-emerald-950 animate-pulse"></span>
                                    <span>Tepat Waktu</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black shadow-xs" style="background-color: #fde047; color: #422006;">
                                    <span class="w-2 h-2 rounded-full bg-amber-950 animate-pulse"></span>
                                    <span>Terlambat</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- 3 TITIK JAM PENTING -->
                    <div class="grid grid-cols-3 gap-2 mt-4 pt-3 border-t border-white/20 text-center">
                        <div class="rounded-xl p-2" style="background-color: rgba(0, 0, 0, 0.25);">
                            <span class="text-[10px] text-white/80 block font-semibold">1. Jam Buka</span>
                            <span class="text-xs sm:text-sm font-black font-mono text-white mt-0.5 block">
                                {{ substr($sholatTerpilih->jam_buka ?? '00:00', 0, 5) }} WIB
                            </span>
                        </div>
                        <div class="rounded-xl p-2" style="background-color: rgba(0, 0, 0, 0.25);">
                            <span class="text-[10px] text-white/80 block font-semibold">2. Batas Tepat</span>
                            <span class="text-xs sm:text-sm font-black font-mono text-white mt-0.5 block">
                                {{ substr($sholatTerpilih->jam_tepat ?? '00:00', 0, 5) }} WIB
                            </span>
                        </div>
                        <div class="rounded-xl p-2" style="background-color: rgba(0, 0, 0, 0.25);">
                            <span class="text-[10px] text-white/80 block font-semibold">3. Jam Tutup</span>
                            <span class="text-xs sm:text-sm font-black font-mono text-white mt-0.5 block">
                                {{ substr($sholatTerpilih->jam_tutup ?? '00:00', 0, 5) }} WIB
                            </span>
                        </div>
                    </div>
                </div>

                <!-- COUNTDOWN TIMER SISA WAKTU PRESENSI -->
                <div class="p-4 bg-slate-50 border-t border-slate-200">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-center sm:text-left">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">
                                Sisa Waktu Pengisian:
                            </span>
                            <p class="text-xs sm:text-sm font-extrabold text-slate-900 mt-0.5">
                                {{ $keteranganFase }}
                            </p>
                        </div>

                        <!-- ANGKA HITUNGAN MUNDUR REALTIME -->
                        <div class="inline-flex items-center justify-center gap-1.5 font-mono select-none">
                            <div class="bg-white border-2 border-slate-300 rounded-xl px-2.5 py-1.5 shadow-xs text-center min-w-[46px]">
                                <span class="text-base sm:text-lg font-black text-slate-900 block leading-none" x-text="hours">00</span>
                                <span class="text-[9px] font-bold text-slate-500 uppercase mt-0.5 block">Jam</span>
                            </div>
                            <span class="text-slate-400 font-black text-lg">:</span>
                            <div class="bg-white border-2 border-slate-300 rounded-xl px-2.5 py-1.5 shadow-xs text-center min-w-[46px]">
                                <span class="text-base sm:text-lg font-black text-slate-900 block leading-none" x-text="minutes">00</span>
                                <span class="text-[9px] font-bold text-slate-500 uppercase mt-0.5 block">Mnt</span>
                            </div>
                            <span class="text-slate-400 font-black text-lg">:</span>
                            <div class="bg-white border-2 border-emerald-600 rounded-xl px-2.5 py-1.5 shadow-xs text-center min-w-[46px]">
                                <span class="text-base sm:text-lg font-black text-emerald-700 block leading-none" x-text="seconds">00</span>
                                <span class="text-[9px] font-bold text-emerald-600 uppercase mt-0.5 block">Dtk</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORMULIR PRESENSI KOORDINATOR -->
            @if ($anggotas->isEmpty())
                <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center space-y-2 shadow-xs">
                    <p class="text-sm font-bold text-slate-800">Belum ada data Petugas Korlap yang aktif.</p>
                    <p class="text-xs text-slate-500">Sekretaris belum menambahkan daftar petugas di menu admin.</p>
                </div>
            @else
                <form id="form-absensi" method="POST" action="{{ route('absensi-sholat.store') }}" @submit="confirmSubmit($event)" class="space-y-4">
                    @csrf
                    <input type="hidden" name="waktu_sholat" value="{{ $namaSholat }}">

                    <!-- 1. PILIH NAMA KOORDINATOR YANG MENGISI -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            1. Nama Koordinator Pengabsen <span class="text-rose-500">*</span>
                        </label>
                        <select id="koordinator_id" name="koordinator_id" required class="w-full text-xs sm:text-sm font-semibold rounded-xl border-slate-300 py-2.5 px-3 bg-slate-50 focus:bg-white focus:border-emerald-600 focus:ring-emerald-600">
                            <option value="">-- Pilih Nama Anda (Koordinator) --</option>
                            @foreach ($koordinators as $k)
                                <option value="{{ $k->id }}" {{ (old('koordinator_id', $koordinatorTerpilihId) == $k->id) ? 'selected' : '' }}>
                                    {{ $k->nama }} ({{ ucfirst($k->bagian) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 italic">
                            * Koordinator yang bertugas otomatis dicatat hadir tepat/terlambat sesuai jam simpan.
                        </p>
                    </div>

                    <!-- 2. DAFTAR ANGGOTA: SEMUA DEFAULT HADIR -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 gap-2">
                            <div>
                                <h2 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide">
                                    2. Presensi Anggota Korlap ({{ $anggotas->count() }} Orang)
                                </h2>
                                <p class="text-[11px] text-emerald-700 font-semibold mt-0.5">
                                    Semua otomatis HADIR. Cukup klik nama yang Sakit, Izin, atau Alfa.
                                </p>
                            </div>
                            <button type="button" @click="resetSemuaHadir()" class="text-[11px] font-bold text-emerald-700 hover:underline shrink-0">
                                Set Semua Hadir
                            </button>
                        </div>

                        <!-- LIST CARD PETUGAS -->
                        <div class="divide-y divide-slate-100">
                            @foreach ($anggotas as $idx => $p)
                                @php
                                    $presensiLama = $riwayatHariIni->get($p->id);
                                    $statusAwal = $presensiLama ? $presensiLama->status : 'hadir';
                                    $ketAwal = $presensiLama ? $presensiLama->keterangan : '';
                                @endphp
                                <div class="py-3 first:pt-1 last:pb-1 space-y-2" x-data="{ status: '{{ old("presensi.{$p->id}.status", $statusAwal) }}' }">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-mono text-slate-400 w-5">{{ $idx + 1 }}.</span>
                                                <span class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ $p->nama }}</span>
                                                @if ($p->bagian === 'koordinator')
                                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-purple-100 text-purple-800">Korlap</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- 4 PILIHAN STATUS (RADIO PILLS) -->
                                        <div class="inline-flex rounded-xl p-0.5 bg-slate-100 border border-slate-200 shrink-0">
                                            <!-- HADIR -->
                                            <label class="cursor-pointer px-2.5 py-1 rounded-lg text-xs font-bold transition text-center"
                                                   :class="status === 'hadir' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                <input type="radio" name="presensi[{{ $p->id }}][status]" value="hadir" x-model="status" @change="recount()" class="sr-only">
                                                <span>Hadir</span>
                                            </label>

                                            <!-- SAKIT -->
                                            <label class="cursor-pointer px-2.5 py-1 rounded-lg text-xs font-bold transition text-center"
                                                   :class="status === 'sakit' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                <input type="radio" name="presensi[{{ $p->id }}][status]" value="sakit" x-model="status" @change="recount()" class="sr-only">
                                                <span>Sakit</span>
                                            </label>

                                            <!-- IZIN -->
                                            <label class="cursor-pointer px-2.5 py-1 rounded-lg text-xs font-bold transition text-center"
                                                   :class="status === 'izin' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                <input type="radio" name="presensi[{{ $p->id }}][status]" value="izin" x-model="status" @change="recount()" class="sr-only">
                                                <span>Izin</span>
                                            </label>

                                            <!-- ALFA -->
                                            <label class="cursor-pointer px-2.5 py-1 rounded-lg text-xs font-bold transition text-center"
                                                   :class="status === 'alfa' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                <input type="radio" name="presensi[{{ $p->id }}][status]" value="alfa" x-model="status" @change="recount()" class="sr-only">
                                                <span>Alfa</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- KOLOM KETERANGAN JIKA SAKIT ATAU IZIN -->
                                    <div x-show="status === 'sakit' || status === 'izin'" x-cloak class="pt-1">
                                        <input type="text"
                                               name="presensi[{{ $p->id }}][keterangan]"
                                               value="{{ old("presensi.{$p->id}.keterangan", $ketAwal) }}"
                                               placeholder="Tulis alasan izin / sakit..."
                                               class="w-full text-xs rounded-xl border-slate-300 py-1.5 px-3 bg-slate-50 focus:bg-white focus:border-emerald-600 focus:ring-emerald-600">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3. TOMBOL SIMPAN FIXED DI BAWAH (STICKY BOTTOM BAR) -->
                    <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-200 px-4 py-3 z-40 shadow-lg">
                        <div class="max-w-2xl mx-auto flex items-center justify-between gap-3">
                            <div class="text-[11px] font-semibold text-slate-600 min-w-0">
                                <span>Ringkasan:</span>
                                <span class="text-emerald-700 font-bold ml-1">H: <span x-text="countHadir">0</span></span> &bull;
                                <span class="text-amber-700 font-bold">S: <span x-text="countSakit">0</span></span> &bull;
                                <span class="text-blue-700 font-bold">I: <span x-text="countIzin">0</span></span> &bull;
                                <span class="text-rose-700 font-bold">A: <span x-text="countAlfa">0</span></span>
                            </div>

                            <button type="submit"
                                    class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white transition flex items-center gap-2 shadow-xs shrink-0 bg-emerald-700 hover:bg-emerald-800">
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
