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

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased min-h-screen pb-24">

    <!-- HEADER ELEGAN MOBILE-FIRST -->
    <header class="bg-gradient-to-r from-emerald-950 via-emerald-900 to-teal-950 text-white shadow-md border-b border-emerald-800">
        <div class="max-w-2xl mx-auto px-4 py-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <picture class="shrink-0">
                    <source srcset="{{ asset('images/logo-nav.webp') }}" type="image/webp">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10 object-contain drop-shadow-md">
                </picture>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-300 block">Pondok Pesantren Al-Falah Putra</span>
                    <h1 class="text-base sm:text-lg font-black tracking-tight text-white truncate">Presensi Korlap Sholat Jamaah</h1>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="text-xs font-mono font-bold text-emerald-200 block">{{ $now->locale('id')->translatedFormat('d M Y') }}</span>
                <span class="text-[11px] text-emerald-300/80 font-mono" id="realtime-clock">{{ $now->format('H:i') }} WIB</span>
            </div>
        </div>
    </header>

    <!-- KONTEN UTAMA -->
    <main class="max-w-2xl mx-auto px-3 sm:px-4 py-4 space-y-4" x-data="absensiPublikApp()">

        <!-- NOTIFIKASI SUKSES / ERROR -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 shadow-sm flex items-start gap-3">
                <span class="p-1 rounded-full bg-emerald-200 text-emerald-800 shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                <div class="min-w-0 flex-1 text-xs sm:text-sm font-semibold">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 shadow-sm flex items-start gap-3">
                <span class="p-1 rounded-full bg-rose-200 text-rose-800 shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </span>
                <div class="min-w-0 flex-1 text-xs sm:text-sm font-semibold">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 shadow-sm text-xs sm:text-sm">
                <p class="font-bold mb-1">Terjadi kesalahan pada input:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 1. PILIHAN WAKTU 5 SHOLAT (TOMBOL PILLS) -->
        <div class="bg-white p-2.5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block px-1 mb-2">Pilih Waktu Sholat:</span>
            <div class="grid grid-cols-5 gap-1.5">
                @foreach (App\Models\AbsensiSholatJadwal::SHOLAT_LIST as $key => $label)
                    @php
                        $jdw = $jadwals->get($key);
                        $isAktif = ($namaSholat === $key);
                    @endphp
                    <a href="{{ route('absensi-sholat.index', ['sholat' => $key]) }}"
                       class="py-2.5 px-1 rounded-xl text-center transition flex flex-col items-center justify-center {{ $isAktif ? 'bg-emerald-800 text-white shadow-xs font-bold ring-2 ring-emerald-600' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold' }}">
                        <span class="text-xs sm:text-sm">{{ $label }}</span>
                        <span class="text-[10px] mt-0.5 font-mono opacity-80 {{ $isAktif ? 'text-emerald-200' : 'text-slate-500' }}">
                            {{ substr($jdw->jam_buka ?? '00:00', 0, 5) }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- 2. KARTU STATUS JAM SHOLAT TERPILIH -->
        <div class="p-3.5 sm:p-4 rounded-2xl border shadow-xs flex items-center justify-between gap-3 {{ $isBisaAbsen ? ($kategoriWaktuSaatIni === 'tepat_waktu' ? 'bg-emerald-50 border-emerald-200 text-emerald-950' : 'bg-amber-50 border-amber-200 text-amber-950') : 'bg-slate-200/80 border-slate-300 text-slate-700' }}">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="p-2 rounded-xl {{ $isBisaAbsen ? ($kategoriWaktuSaatIni === 'tepat_waktu' ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white') : 'bg-slate-400 text-white' }} shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-xs font-black uppercase tracking-wider">Sholat {{ ucfirst($namaSholat) }}</span>
                        @if ($isBisaAbsen)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $kategoriWaktuSaatIni === 'tepat_waktu' ? 'bg-emerald-200 text-emerald-900' : 'bg-amber-200 text-amber-900' }}">
                                {{ $kategoriWaktuSaatIni === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }}
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-300 text-slate-800">Ditutup</span>
                        @endif
                    </div>
                    <p class="text-[11px] opacity-80 mt-0.5">
                        Buka: <strong class="font-mono">{{ substr($sholatTerpilih->jam_buka ?? '00:00', 0, 5) }}</strong> &bull;
                        Tepat: <strong class="font-mono">{{ substr($sholatTerpilih->jam_tepat ?? '00:00', 0, 5) }}</strong> &bull;
                        Tutup: <strong class="font-mono">{{ substr($sholatTerpilih->jam_tutup ?? '00:00', 0, 5) }}</strong>
                    </p>
                </div>
            </div>

            @if ($sudahDiabsen)
                <span class="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-white text-emerald-800 border border-emerald-300 shadow-2xs shrink-0">
                    Sudah Diisi
                </span>
            @endif
        </div>

        @if ($anggotas->isEmpty())
            <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center space-y-2">
                <p class="text-sm font-bold text-slate-700">Belum ada data Petugas Korlap yang aktif.</p>
                <p class="text-xs text-slate-500">Sekretaris belum menambahkan daftar petugas di menu admin.</p>
            </div>
        @else
            <!-- FORMULIR PRESENSI KOORDINATOR -->
            <form method="POST" action="{{ route('absensi-sholat.store') }}" @submit="confirmSubmit($event)" class="space-y-4">
                @csrf
                <input type="hidden" name="waktu_sholat" value="{{ $namaSholat }}">

                <!-- 3. PILIHAN KOORDINATOR YANG MENGISI -->
                <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        1. Nama Koordinator Pengabsen <span class="text-rose-500">*</span>
                    </label>
                    <select name="koordinator_id" required class="w-full text-xs sm:text-sm font-semibold rounded-xl border-slate-300 py-2.5 px-3 bg-slate-50 focus:bg-white focus:border-emerald-600 focus:ring-emerald-600">
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

                <!-- 4. DAFTAR ANGGOTA KORLAP: DEFAULT HADIR -->
                <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs space-y-3">
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

                <!-- 5. TOMBOL SIMPAN FIXED DI BAWAH (STICKY BOTTOM ACTION BAR) -->
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
                                @if (!$isBisaAbsen) disabled @endif
                                class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white transition flex items-center gap-2 shadow-xs shrink-0 {{ $isBisaAbsen ? 'bg-emerald-700 hover:bg-emerald-800' : 'bg-slate-400 cursor-not-allowed' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $sudahDiabsen ? 'Perbarui Presensi' : 'Simpan Presensi ' . ucfirst($namaSholat) }}</span>
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </main>

    <script>
        function absensiPublikApp() {
            return {
                countHadir: 0,
                countSakit: 0,
                countIzin: 0,
                countAlfa: 0,
                init() {
                    this.recount();
                    // Live clock
                    setInterval(() => {
                        const d = new Date();
                        const h = String(d.getHours()).padStart(2, '0');
                        const m = String(d.getMinutes()).padStart(2, '0');
                        const el = document.getElementById('realtime-clock');
                        if (el) el.innerText = h + ':' + m + ' WIB';
                    }, 1000);
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
                    if (!confirm('Yakin ingin menyimpan presensi sholat {{ ucfirst($namaSholat) }} ini?')) {
                        e.preventDefault();
                    }
                }
            }
        }
    </script>
</body>
</html>
