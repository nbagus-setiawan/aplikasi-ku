<x-app-layout>
    @section('title', 'Biodata Santri: ' . $santri->nama)

    <div class="space-y-6 max-w-5xl mx-auto pb-10">
        <!-- Top Navigation & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                    <a href="{{ route('admin.santri.index') }}" class="hover:text-emerald-700 transition">Santri</a>
                    <span>/</span>
                    <span class="text-emerald-800 font-semibold">Biodata Lengkap</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Biodata Santri</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Informasi lengkap data pokok dan kontak keluarga santri.</p>
            </div>

            <!-- Action Buttons Sesuai Aturan Desain -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.santri.biodata', $santri->id) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-pink-600 hover:bg-pink-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-sm transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak PDF</span>
                </a>
                <a href="{{ route('admin.santri.edit', $santri->id) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-400 hover:bg-amber-500 text-slate-900 text-xs sm:text-sm font-bold rounded-xl shadow-sm transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Ubah Data</span>
                </a>
                <a href="{{ route('admin.santri.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <!-- Profil Banner Card -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
            <!-- Header Kartu -->
            <div class="bg-gradient-to-r from-emerald-950 via-emerald-900 to-emerald-800 p-6 sm:p-8 text-white relative overflow-hidden">
                <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-emerald-700/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-start gap-6">
                    <!-- Foto Santri -->
                    <div class="shrink-0">
                        @if ($santri->foto && Storage::disk('public')->exists($santri->foto))
                            <img src="{{ asset('storage/' . $santri->foto) }}" alt="{{ $santri->nama }}"
                                 class="w-28 h-36 sm:w-32 sm:h-40 rounded-2xl object-cover border-4 border-white/20 shadow-xl ring-2 ring-emerald-500/30">
                        @else
                            <div class="w-28 h-36 sm:w-32 sm:h-40 rounded-2xl bg-emerald-800/80 text-emerald-200 flex flex-col items-center justify-center font-extrabold text-4xl border-4 border-white/20 shadow-xl ring-2 ring-emerald-500/30">
                                <span>{{ strtoupper(substr($santri->nama, 0, 1)) }}</span>
                                <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-300 mt-2">Tanpa Foto</span>
                            </div>
                        @endif
                    </div>

                    <!-- Ringkasan Info Utama -->
                    <div class="text-center sm:text-left flex-1 min-w-0 space-y-2">
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <span class="inline-flex items-center px-3 py-1 bg-white/10 backdrop-blur-xs text-emerald-200 text-xs font-mono font-bold rounded-lg border border-white/15">
                                NIS: {{ $santri->nis }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold {{ $santri->status_badge }} shadow-xs">
                                Status: {{ ucfirst($santri->status) }}
                            </span>
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white drop-shadow-xs truncate">
                            {{ $santri->nama }}
                        </h2>

                        <p class="text-xs sm:text-sm text-emerald-200/90 font-medium flex flex-wrap items-center justify-center sm:justify-start gap-x-2 gap-y-1">
                            <span>Kelas: <strong class="text-white">{{ $santri->kelas ?: 'Belum ditentukan' }}</strong></span>
                            <span>&bull;</span>
                            <span>Kamar: <strong class="text-white">{{ $santri->kamar ?: 'Belum ditentukan' }}</strong></span>
                            <span>&bull;</span>
                            <span>Angkatan: <strong class="text-white">{{ $santri->tahun_masuk ?: '-' }}</strong></span>
                        </p>

                        <div class="pt-2 text-xs text-emerald-300/80 flex items-center justify-center sm:justify-start gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Terdaftar pada: {{ $santri->created_at ? $santri->created_at->format('d-m-Y H:i') : '-' }} WIB</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Konten Grid Rincian -->
            <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/50">
                <!-- Kartu Identitas Pribadi -->
                <div class="bg-white rounded-2xl border border-gray-200/90 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-900">
                            Identitas Pribadi Santri
                        </h3>
                    </div>

                    <div class="space-y-3 text-xs sm:text-sm">
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-500">Nomor Induk Santri (NIS)</span>
                            <span class="font-mono font-bold text-gray-900">{{ $santri->nis }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-500">Nama Lengkap</span>
                            <span class="font-semibold text-gray-900">{{ $santri->nama }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-500">Tempat, Tanggal Lahir</span>
                            <span class="font-medium text-gray-800 text-right">
                                {{ $santri->tempat_lahir ? $santri->tempat_lahir . ', ' : '' }}{{ $santri->tanggal_lahir_formatted ?: '-' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-500">Tahun Masuk Pondok</span>
                            <span class="font-bold text-emerald-800">{{ $santri->tahun_masuk ?: '-' }}</span>
                        </div>
                        <div class="pt-1">
                            <span class="text-gray-500 block mb-1.5 font-medium">Alamat Asal Lengkap:</span>
                            <p class="text-xs text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-200/80 leading-relaxed font-normal">
                                {{ $santri->alamat ?: 'Tidak ada catatan alamat asal.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Kartu Penempatan & Wali -->
                <div class="space-y-6">
                    <!-- Penempatan Asrama -->
                    <div class="bg-white rounded-2xl border border-gray-200/90 p-5 sm:p-6 shadow-xs space-y-4">
                        <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-900">
                                Penempatan Asrama & Kelas
                            </h3>
                        </div>

                        <div class="space-y-3 text-xs sm:text-sm">
                            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                <span class="text-gray-500">Kelas / Tingkat</span>
                                <span class="font-bold text-gray-900">{{ $santri->kelas ?: '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                <span class="text-gray-500">Kamar / Paviliun Asrama</span>
                                <span class="font-bold text-gray-900">{{ $santri->kamar ?: '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Kontak Wali -->
                    <div class="bg-white rounded-2xl border border-gray-200/90 p-5 sm:p-6 shadow-xs space-y-4">
                        <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-900">
                                Hubungan Keluarga / Wali
                            </h3>
                        </div>

                        <div class="space-y-3 text-xs sm:text-sm">
                            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                <span class="text-gray-500">Nama Orang Tua / Wali</span>
                                <span class="font-bold text-gray-900">{{ $santri->nama_wali ?: '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                <span class="text-gray-500">No. WhatsApp / HP</span>
                                <div class="text-right">
                                    @if ($santri->hp_wali)
                                        @php
                                            $cleanHp = preg_replace('/[^0-9]/', '', $santri->hp_wali);
                                            if (str_starts_with($cleanHp, '0')) {
                                                $cleanHp = '62' . substr($cleanHp, 1);
                                            }
                                        @endphp
                                        <a href="https://wa.me/{{ $cleanHp }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 font-mono font-bold text-emerald-700 hover:text-emerald-800 hover:underline">
                                            <span>{{ $santri->hp_wali }}</span>
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.303c-.087.087-.177.181-.076.355.101.173.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400 font-mono">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
