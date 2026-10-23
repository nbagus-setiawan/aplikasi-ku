<x-app-layout>
    @section('title', 'Agenda Kegiatan')

    <div class="space-y-6 pb-12">
        <!-- Header Judul & Tombol Aksi -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                    <span>Master Data & Kegiatan</span>
                    <span>/</span>
                    <span class="text-emerald-800 font-semibold">Agenda Kegiatan</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Agenda Kegiatan Pesantren</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Jadwal acara, peringatan H-1 otomatis, penanggung jawab, dan kalender kegiatan bulanan.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.agenda.create') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-emerald-700/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Agenda</span>
                </a>
            </div>
        </div>

        <!-- 4 Kartu Ringkasan Kegiatan Agenda -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Total Agenda -->
            <div class="bg-white rounded-2xl border border-gray-200/90 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Total Agenda</span>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 mt-0.5">{{ $statistik['total'] ?? 0 }}</div>
                    <span class="text-[11px] text-gray-500 font-medium">Seluruh kegiatan</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200/60 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <!-- Rencana -->
            <div class="bg-white rounded-2xl border border-emerald-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Rencana</span>
                    <div class="text-2xl sm:text-3xl font-black text-emerald-950 mt-0.5">{{ $statistik['rencana'] ?? 0 }}</div>
                    <span class="text-[11px] text-emerald-800 font-medium">Jadwal mendatang</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Berjalan -->
            <div class="bg-white rounded-2xl border border-blue-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Berjalan</span>
                    <div class="text-2xl sm:text-3xl font-black text-blue-950 mt-0.5">{{ $statistik['berjalan'] ?? 0 }}</div>
                    <span class="text-[11px] text-blue-800 font-medium">Sedang berlangsung</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>

            <!-- Selesai -->
            <div class="bg-white rounded-2xl border border-gray-200/90 p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Selesai</span>
                    <div class="text-2xl sm:text-3xl font-black text-gray-700 mt-0.5">{{ $statistik['selesai'] ?? 0 }}</div>
                    <span class="text-[11px] text-gray-500 font-medium">Telah terlaksana</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-gray-100 text-gray-600 border border-gray-200 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Banner Peringatan Agenda H-1 (Besok Hari) -->
        @if ($agendasBesok->count() > 0)
            <div class="bg-gradient-to-r from-amber-500/10 via-amber-50 to-amber-500/5 border border-amber-300 rounded-2xl p-5 shadow-xs space-y-3">
                <div class="flex items-center gap-3 text-amber-950 font-extrabold text-sm sm:text-base">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-xs shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div>
                        <span class="block">Peringatan Agenda H-1: Ada {{ $agendasBesok->count() }} Kegiatan Besok Hari!</span>
                        <span class="text-xs text-amber-800/80 font-normal">Pastikan persiapan tempat, sarana, dan koordinasi panitia telah siap.</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                    @foreach ($agendasBesok as $ab)
                        <div class="p-4 bg-white rounded-xl border border-amber-200 flex items-center justify-between text-xs sm:text-sm shadow-2xs">
                            <div class="min-w-0 pr-3">
                                <h4 class="font-extrabold text-gray-900 truncate">{{ $ab->judul }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Waktu: <strong class="text-gray-800">{{ $ab->mulai->format('H:i') }} WIB</strong> &bull; Lokasi: {{ $ab->lokasi }}
                                </p>
                                <p class="text-[11px] text-amber-800 font-bold mt-1">PJ: {{ $ab->pj ? $ab->pj->nama : '-' }}</p>
                            </div>
                            <a href="{{ route('admin.agenda.show', $ab) }}" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-bold rounded-lg text-xs transition shrink-0">
                                Detail
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Tab Pemilihan Mode Tampilan: Daftar vs Kalender -->
        <div class="flex items-center border-b border-gray-200 pb-2">
            <div class="inline-flex p-1 bg-gray-100 rounded-xl gap-1">
                <a href="{{ route('admin.agenda.index', ['view' => 'daftar']) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-bold rounded-lg transition {{ $viewMode === 'daftar' ? 'bg-white text-emerald-800 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span>Daftar Tabel</span>
                </a>
                <a href="{{ route('admin.agenda.index', ['view' => 'kalender', 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-bold rounded-lg transition {{ $viewMode === 'kalender' ? 'bg-white text-emerald-800 shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Kalender Bulanan</span>
                </a>
            </div>
        </div>

        @if ($viewMode === 'kalender')
            <!-- ================= KALENDER BULANAN ================= -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-4 sm:p-6 space-y-4">
                @php
                    $prevMonth = $currentDate->copy()->subMonth();
                    $nextMonth = $currentDate->copy()->addMonth();
                    $startOfMonth = $currentDate->copy()->startOfMonth();
                    $daysInMonth = $currentDate->daysInMonth;
                    $firstDayOfWeek = $startOfMonth->isoWeekday();
                @endphp

                <!-- Navigasi Bulan -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 gap-2">
                    <a href="{{ route('admin.agenda.index', ['view' => 'kalender', 'bulan' => $prevMonth->month, 'tahun' => $prevMonth->year]) }}"
                       class="px-3 sm:px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs sm:text-sm font-bold rounded-xl transition flex items-center gap-1">
                        &larr; <span class="hidden sm:inline">{{ $prevMonth->isoFormat('MMMM Y') }}</span>
                    </a>

                    <h2 class="text-sm sm:text-lg font-extrabold text-gray-900 uppercase tracking-wide text-center">
                        {{ $currentDate->isoFormat('MMMM Y') }}
                    </h2>

                    <a href="{{ route('admin.agenda.index', ['view' => 'kalender', 'bulan' => $nextMonth->month, 'tahun' => $nextMonth->year]) }}"
                       class="px-3 sm:px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs sm:text-sm font-bold rounded-xl transition flex items-center gap-1">
                        <span class="hidden sm:inline">{{ $nextMonth->isoFormat('MMMM Y') }}</span> &rarr;
                    </a>
                </div>

                <!-- Grid Kalender Bulanan -->
                <div class="grid grid-cols-7 gap-1 sm:gap-2 text-center text-xs">
                    <!-- Header Hari (Senin - Ahad) -->
                    @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'] as $hari)
                        <div class="py-2 bg-emerald-50 text-emerald-900 font-extrabold rounded-lg text-[10px] sm:text-xs uppercase tracking-wider truncate">
                            <span class="sm:hidden">{{ substr($hari, 0, 3) }}</span>
                            <span class="hidden sm:inline">{{ $hari }}</span>
                        </div>
                    @endforeach

                    <!-- Padding Hari Kosong Sebelum Tanggal 1 -->
                    @for ($i = 1; $i < $firstDayOfWeek; $i++)
                        <div class="min-h-[70px] sm:min-h-[110px] bg-gray-50/40 rounded-xl border border-transparent"></div>
                    @endfor

                    <!-- Kotak Tiap Tanggal -->
                    @for ($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $loopDate = $currentDate->copy()->day($d)->format('Y-m-d');
                            $isToday = $loopDate === date('Y-m-d');
                            $agendaHariIni = $agendasBulan->filter(function ($a) use ($loopDate) {
                                return $a->mulai->format('Y-m-d') <= $loopDate && $a->selesai->format('Y-m-d') >= $loopDate;
                            });
                        @endphp
                        <div class="min-h-[70px] sm:min-h-[110px] p-1.5 sm:p-2 rounded-xl border text-left flex flex-col justify-between transition {{ $isToday ? 'border-emerald-600 bg-emerald-50/40 ring-2 ring-emerald-500/20' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs {{ $isToday ? 'w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center bg-emerald-700 text-white font-black rounded-full text-[10px] sm:text-xs' : 'text-gray-700 font-bold' }}">
                                    {{ $d }}
                                </span>
                                @if ($agendaHariIni->count() > 0)
                                    <span class="text-[9px] sm:text-[10px] text-emerald-800 font-bold bg-emerald-100 px-1.5 py-0.2 rounded-full">
                                        {{ $agendaHariIni->count() }}
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-1 mt-1 overflow-y-auto max-h-16 sm:max-h-20">
                                @foreach ($agendaHariIni as $item)
                                    <a href="{{ route('admin.agenda.show', $item) }}"
                                       class="block px-1.5 py-0.5 rounded text-[10px] sm:text-[11px] font-bold truncate {{ $item->status === 'selesai' ? 'bg-gray-100 text-gray-700' : ($item->status === 'berjalan' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800') }}"
                                       title="{{ $item->judul }} ({{ $item->lokasi }})">
                                        {{ $item->judul }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @else
            <!-- ================= DAFTAR TABEL & CARDS ================= -->
            <!-- Filter & Pencarian (Live & Collapsible) -->
            <div x-data="liveFilter({
                hasActiveFilters: {{ request()->hasAny(['status', 'tgl_mulai', 'tgl_selesai']) ? 'true' : 'false' }},
                activeCount: {{ (request('status') ? 1 : 0) + (request('tgl_mulai') ? 1 : 0) + (request('tgl_selesai') ? 1 : 0) }}
            })" class="space-y-4">
                <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-4 sm:p-5 space-y-4">
                    <form x-ref="filterForm" method="GET" action="{{ route('admin.agenda.index') }}" @submit.prevent="applyLive()">
                        <input type="hidden" name="view" value="daftar">

                        <!-- Baris Utama: Pencarian Global + Filter Lanjutan + Reset -->
                        <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
                            <div class="relative flex-1">
                                <input type="text" name="q" value="{{ request('q') }}"
                                       placeholder="Cari judul kegiatan, lokasi, penanggung jawab, atau deskripsi..."
                                       @input="applyLive()"
                                       class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 pl-10 pr-10 py-2.5 shadow-2xs" />
                                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <div x-show="loading" x-cloak class="absolute right-3.5 top-3">
                                    <svg class="animate-spin w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Tombol Toggle Filter Lanjutan -->
                            <button type="button" @click="showFilters = !showFilters"
                                    :class="showFilters || activeCount > 0 ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border font-bold text-xs sm:text-sm shadow-2xs transition shrink-0">
                                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <span>Filter Lanjutan</span>
                                <template x-if="activeCount > 0">
                                    <span class="px-1.5 py-0.5 text-[11px] font-black rounded-full bg-emerald-700 text-white" x-text="activeCount"></span>
                                </template>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Tombol Reset Filter -->
                            <button type="button" @click="resetFilters()"
                                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs sm:text-sm shadow-2xs transition shrink-0"
                                    title="Bersihkan Semua Filter">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span class="hidden sm:inline">Reset</span>
                            </button>
                        </div>

                        <!-- Baris Filter Lanjutan (Status & Tanggal) -->
                        <div x-show="showFilters" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-3 mt-3 border-t border-gray-100">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Status Kegiatan</label>
                                <select name="status" @change="applyLive()" class="w-full text-xs rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2">
                                    <option value="">Semua Status</option>
                                    <option value="rencana" {{ request('status') === 'rencana' ? 'selected' : '' }}>Rencana</option>
                                    <option value="berjalan" {{ request('status') === 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="batal" {{ request('status') === 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Dari Tanggal</label>
                                <input type="date" name="tgl_mulai" value="{{ request('tgl_mulai') }}" @change="applyLive()"
                                       class="w-full text-xs rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2" />
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Sampai Tanggal</label>
                                <input type="date" name="tgl_selesai" value="{{ request('tgl_selesai') }}" @change="applyLive()"
                                       class="w-full text-xs rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2" />
                            </div>
                        </div>
                    </form>
                </div>

                <div id="data-container" class="relative space-y-4">
                    <!-- Overlay Indikator Saat Memuat Data -->
                    <div x-show="loading" x-cloak class="absolute inset-0 bg-white/60 z-10 backdrop-blur-[1px] flex items-center justify-center transition-opacity rounded-2xl">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white shadow-md border border-gray-200 text-xs font-bold text-emerald-800">
                            <svg class="animate-spin w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Menyaring agenda kegiatan...</span>
                        </div>
                    </div>

            <!-- Tampilan Desktop: Tabel -->
            <div class="hidden md:block bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-700">
                        <thead class="bg-gray-50/90 border-b border-gray-200 text-gray-800 font-extrabold uppercase text-xs tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Judul Agenda</th>
                                <th class="px-5 py-3.5">Waktu Mulai & Selesai</th>
                                <th class="px-5 py-3.5">Lokasi</th>
                                <th class="px-5 py-3.5">Penanggung Jawab</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-center w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($agendas as $item)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-extrabold text-gray-900">
                                            <a href="{{ route('admin.agenda.show', $item) }}" class="hover:text-emerald-700 transition">
                                                {{ $item->judul }}
                                            </a>
                                        </div>
                                        @if ($item->deskripsi)
                                            <p class="text-xs text-gray-500 truncate max-w-sm mt-0.5 font-normal">{{ $item->deskripsi }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900 text-xs">{{ $item->formatted_mulai }}</div>
                                        <div class="text-[11px] text-gray-500">s.d. {{ $item->formatted_selesai }}</div>
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-gray-800 text-xs">
                                        {{ $item->lokasi }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-gray-900 text-xs">{{ $item->pj ? $item->pj->nama : '-' }}</div>
                                        @if ($item->pj)
                                            <div class="text-[11px] text-gray-500">{{ $item->pj->jabatan }}</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if ($item->status === 'rencana')
                                            <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-300 font-bold rounded-full text-xs">
                                                Rencana
                                            </span>
                                        @elseif ($item->status === 'berjalan')
                                            <span class="px-2.5 py-0.5 bg-blue-50 text-blue-800 border border-blue-300 font-bold rounded-full text-xs">
                                                Berjalan
                                            </span>
                                        @elseif ($item->status === 'selesai')
                                            <span class="px-2.5 py-0.5 bg-gray-100 text-gray-700 font-bold rounded-full text-xs">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 bg-red-50 text-red-800 border border-red-300 font-bold rounded-full text-xs">
                                                Batal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('admin.agenda.show', $item) }}" title="Lihat Detail"
                                               class="p-2 text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200/60 rounded-xl transition shadow-2xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.agenda.edit', $item) }}" title="Ubah"
                                               class="p-2 text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-xl transition shadow-2xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('admin.agenda.destroy', $item) }}"
                                                  data-konfirmasi="Apakah Anda yakin ingin menghapus agenda kegiatan ini?"
                                                  data-judul="Hapus Agenda Kegiatan"
                                                  data-tombol="Ya, Hapus Agenda"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus"
                                                        class="p-2 text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl transition shadow-2xs">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p class="font-medium text-sm text-gray-500">Tidak ada agenda kegiatan yang ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tampilan Mobile: Cards -->
            <div class="block md:hidden space-y-3">
                @forelse ($agendas as $item)
                    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="mb-1">
                                    @if ($item->status === 'rencana')
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-300 font-bold rounded-full text-[10px]">Rencana</span>
                                    @elseif ($item->status === 'berjalan')
                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-300 font-bold rounded-full text-[10px]">Berjalan</span>
                                    @elseif ($item->status === 'selesai')
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 font-bold rounded-full text-[10px]">Selesai</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-red-50 text-red-800 border border-red-300 font-bold rounded-full text-[10px]">Batal</span>
                                    @endif
                                </div>
                                <a href="{{ route('admin.agenda.show', $item) }}" class="font-extrabold text-sm text-gray-900 block leading-tight">
                                    {{ $item->judul }}
                                </a>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400 text-[10px] uppercase font-bold">Waktu:</span>
                                <span class="font-bold text-gray-800 text-right">{{ $item->formatted_mulai }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400 text-[10px] uppercase font-bold">Lokasi:</span>
                                <span class="font-semibold text-gray-700 text-right">{{ $item->lokasi }}</span>
                            </div>
                            @if ($item->pj)
                                <div class="flex items-center justify-between pt-1 border-t border-gray-200/60">
                                    <span class="text-gray-400 text-[10px] uppercase font-bold">Penanggung Jawab:</span>
                                    <span class="font-bold text-emerald-800">{{ $item->pj->nama }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Tombol Aksi Mobile -->
                        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-100">
                            <a href="{{ route('admin.agenda.show', $item) }}"
                               class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 shadow-2xs">
                                <span>Detail</span>
                            </a>
                            <a href="{{ route('admin.agenda.edit', $item) }}"
                               class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold text-amber-900 bg-amber-100 border border-amber-300 rounded-xl hover:bg-amber-200 shadow-2xs">
                                <span>Ubah</span>
                            </a>
                            <form method="POST" action="{{ route('admin.agenda.destroy', $item) }}"
                                  data-konfirmasi="Apakah Anda yakin ingin menghapus agenda kegiatan ini?"
                                  data-judul="Hapus Agenda Kegiatan"
                                  data-tombol="Ya, Hapus Agenda"
                                  class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold text-rose-700 bg-rose-50 border border-rose-300 rounded-xl hover:bg-rose-100 shadow-2xs">
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center text-gray-400">
                        <p class="font-medium text-sm text-gray-500">Tidak ada agenda kegiatan yang ditemukan.</p>
                    </div>
                @endforelse
            </div>

            @if ($agendas->hasPages())
                <div class="p-4 bg-white rounded-2xl border border-gray-200 shadow-xs">
                    {{ $agendas->links() }}
                </div>
            @endif
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
