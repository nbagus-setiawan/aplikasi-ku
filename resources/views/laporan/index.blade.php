@extends('layouts.app')

@section('title', 'Rekapitulasi & Laporan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rekapitulasi & Laporan Administrasi</h1>
            <p class="text-sm text-gray-500 mt-1">
                Pilih modul data, atur periode dan filter, pratinjau data, dan unduh berkas laporan format PDF ber-kop resmi.
            </p>
        </div>
    </div>

    <!-- Navigasi Tab Jenis Laporan -->
    <!-- Navigasi Tab Jenis Laporan -->
    <div class="bg-white p-2 rounded-2xl border border-gray-200/90 shadow-sm flex items-center gap-2 overflow-x-auto no-scrollbar">
        <a href="{{ route('admin.laporan.index', ['tab' => 'surat-masuk']) }}"
           class="whitespace-nowrap px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2 {{ $tab === 'surat-masuk' ? 'bg-emerald-700 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            Surat Masuk
        </a>

        <a href="{{ route('admin.laporan.index', ['tab' => 'surat-keluar']) }}"
           class="whitespace-nowrap px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2 {{ $tab === 'surat-keluar' ? 'bg-emerald-700 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            Surat Keluar
        </a>

        <a href="{{ route('admin.laporan.index', ['tab' => 'santri']) }}"
           class="whitespace-nowrap px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2 {{ $tab === 'santri' ? 'bg-emerald-700 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Data Santri
        </a>

        <a href="{{ route('admin.laporan.index', ['tab' => 'agenda']) }}"
           class="whitespace-nowrap px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2 {{ $tab === 'agenda' ? 'bg-emerald-700 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Agenda Kegiatan
        </a>
    </div>

    <!-- Filter Form & Tombol Cetak PDF -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
        <form action="{{ route('admin.laporan.index') }}" method="GET" class="space-y-4">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                @if($tab !== 'santri')
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Dari Tanggal</label>
                        <input type="date" name="dari_tanggal" value="{{ $dari }}" class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                        <input type="date" name="sampai_tanggal" value="{{ $sampai }}" class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                @endif

                @if($tab === 'surat-masuk')
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Status Disposisi</label>
                        <select name="status_disposisi" class="w-full rounded-xl border-gray-300 text-sm">
                            <option value="">Semua Status</option>
                            <option value="baru" {{ request('status_disposisi') === 'baru' ? 'selected' : '' }}>Baru</option>
                            <option value="diproses" {{ request('status_disposisi') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="selesai" {{ request('status_disposisi') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                @elseif($tab === 'surat-keluar')
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Status Surat</label>
                        <select name="status" class="w-full rounded-xl border-gray-300 text-sm">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="final" {{ request('status') === 'final' ? 'selected' : '' }}>Final</option>
                            <option value="terkirim" {{ request('status') === 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Metode TTD</label>
                        <select name="metode_ttd" class="w-full rounded-xl border-gray-300 text-sm">
                            <option value="">Semua Metode</option>
                            <option value="digital" {{ request('metode_ttd') === 'digital' ? 'selected' : '' }}>Digital</option>
                            <option value="basah" {{ request('metode_ttd') === 'basah' ? 'selected' : '' }}>Basah</option>
                        </select>
                    </div>
                @elseif($tab === 'santri')
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Status Santri</label>
                        <select name="status" class="w-full rounded-xl border-gray-300 text-sm">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="alumni" {{ request('status') === 'alumni' ? 'selected' : '' }}>Alumni</option>
                            <option value="berhenti" {{ request('status') === 'berhenti' ? 'selected' : '' }}>Berhenti</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Kelas</label>
                        <input type="text" name="kelas" value="{{ request('kelas') }}" placeholder="Contoh: 1-A" class="w-full rounded-xl border-gray-300 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Kamar</label>
                        <input type="text" name="kamar" value="{{ request('kamar') }}" placeholder="Contoh: Asrama A" class="w-full rounded-xl border-gray-300 text-sm">
                    </div>
                @elseif($tab === 'agenda')
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Status Kegiatan</label>
                        <select name="status" class="w-full rounded-xl border-gray-300 text-sm">
                            <option value="">Semua Status</option>
                            <option value="rencana" {{ request('status') === 'rencana' ? 'selected' : '' }}>Rencana</option>
                            <option value="berjalan" {{ request('status') === 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="batal" {{ request('status') === 'batal' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>
                @endif

                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 transition">
                        Terapkan
                    </button>
                </div>
            </div>
        </form>

        <!-- Baris Tombol Cetak PDF & Info Total -->
        <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span>Ditemukan <strong>{{ $total }}</strong> baris data sesuai kriteria.</span>
            </div>

            <div>
                @php
                    $routeCetak = match($tab) {
                        'surat-masuk' => route('admin.laporan.cetak.surat-masuk', request()->query()),
                        'surat-keluar' => route('admin.laporan.cetak.surat-keluar', request()->query()),
                        'santri' => route('admin.laporan.cetak.santri', request()->query()),
                        'agenda' => route('admin.laporan.cetak.agenda', request()->query()),
                    };
                @endphp
                <a href="{{ $routeCetak }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-rose-600 hover:bg-rose-700 shadow-sm shadow-rose-600/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak / Unduh PDF Ber-Kop Resmi
                </a>
            </div>
        </div>
    </div>

    <!-- Pratinjau Tabel Data (Responsive Mobile Cards & Desktop Table) -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Pratinjau Data Laporan</h3>
            <span class="text-xs text-gray-400">Format: Kertas A4 Otomatis</span>
        </div>

        @if($tab === 'surat-masuk')
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-100 text-gray-700 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">No. Agenda</th>
                            <th class="py-3 px-4">Nomor Surat</th>
                            <th class="py-3 px-4">Pengirim</th>
                            <th class="py-3 px-4">Perihal</th>
                            <th class="py-3 px-4">Tgl Terima</th>
                            <th class="py-3 px-4">Status Disposisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $item)
                            <tr class="hover:bg-gray-50/80">
                                <td class="py-3 px-4 font-bold text-gray-900">{{ $item->no_agenda }}</td>
                                <td class="py-3 px-4 font-mono">{{ $item->no_surat }}</td>
                                <td class="py-3 px-4 font-semibold text-gray-900">{{ $item->pengirim }}</td>
                                <td class="py-3 px-4">{{ $item->perihal }}</td>
                                <td class="py-3 px-4">{{ $item->tgl_diterima ? $item->tgl_diterima->format('d-m-Y') : '-' }}</td>
                                <td class="py-3 px-4 capitalize">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item->status_disposisi === 'selesai' ? 'bg-emerald-100 text-emerald-800' : ($item->status_disposisi === 'diproses' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $item->status_disposisi }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-gray-400">Tidak ada data untuk periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="block md:hidden divide-y divide-gray-100">
                @forelse($data as $item)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                Agenda #{{ $item->no_agenda }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item->status_disposisi === 'selesai' ? 'bg-emerald-100 text-emerald-800' : ($item->status_disposisi === 'diproses' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ ucfirst($item->status_disposisi) }}
                            </span>
                        </div>
                        <p class="font-bold text-gray-900 text-sm leading-snug">{{ $item->perihal }}</p>
                        <div class="text-xs text-gray-500 space-y-0.5">
                            <div><strong class="text-gray-700">Pengirim:</strong> {{ $item->pengirim }}</div>
                            <div><strong class="text-gray-700">No. Surat:</strong> <span class="font-mono">{{ $item->no_surat }}</span></div>
                            <div><strong class="text-gray-700">Diterima:</strong> {{ $item->tgl_diterima ? $item->tgl_diterima->format('d-m-Y') : '-' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-400 text-xs">Tidak ada data untuk periode ini.</div>
                @endforelse
            </div>

        @elseif($tab === 'surat-keluar')
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-100 text-gray-700 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Nomor Surat</th>
                            <th class="py-3 px-4">Tujuan</th>
                            <th class="py-3 px-4">Perihal</th>
                            <th class="py-3 px-4">Tgl Surat</th>
                            <th class="py-3 px-4">Metode TTD</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $item)
                            <tr class="hover:bg-gray-50/80">
                                <td class="py-3 px-4 font-mono font-bold text-emerald-800">{{ $item->no_surat }}</td>
                                <td class="py-3 px-4 font-semibold text-gray-900">{{ $item->tujuan }}</td>
                                <td class="py-3 px-4">{{ $item->perihal }}</td>
                                <td class="py-3 px-4">{{ $item->tgl_surat ? $item->tgl_surat->format('d-m-Y') : '-' }}</td>
                                <td class="py-3 px-4 capitalize">{{ $item->metode_ttd }}</td>
                                <td class="py-3 px-4 capitalize">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item->status === 'final' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'terkirim' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-gray-400">Tidak ada data untuk periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="block md:hidden divide-y divide-gray-100">
                @forelse($data as $item)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-xs text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                {{ $item->no_surat }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item->status === 'final' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'terkirim' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>
                        <p class="font-bold text-gray-900 text-sm leading-snug">{{ $item->perihal }}</p>
                        <div class="text-xs text-gray-500 space-y-0.5">
                            <div><strong class="text-gray-700">Tujuan:</strong> {{ $item->tujuan }}</div>
                            <div><strong class="text-gray-700">Tgl Surat:</strong> {{ $item->tgl_surat ? $item->tgl_surat->format('d-m-Y') : '-' }} &bull; TTD: <span class="capitalize">{{ $item->metode_ttd }}</span></div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-400 text-xs">Tidak ada data untuk periode ini.</div>
                @endforelse
            </div>

        @elseif($tab === 'santri')
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-100 text-gray-700 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">NIS</th>
                            <th class="py-3 px-4">Nama Lengkap</th>
                            <th class="py-3 px-4">Kelas</th>
                            <th class="py-3 px-4">Kamar</th>
                            <th class="py-3 px-4">Nama Wali</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $item)
                            <tr class="hover:bg-gray-50/80">
                                <td class="py-3 px-4 font-mono font-bold text-gray-900">{{ $item->nis }}</td>
                                <td class="py-3 px-4 font-semibold text-gray-900">{{ $item->nama }}</td>
                                <td class="py-3 px-4">{{ $item->kelas ?: '-' }}</td>
                                <td class="py-3 px-4">{{ $item->kamar ?: '-' }}</td>
                                <td class="py-3 px-4">{{ $item->nama_wali ?: '-' }}</td>
                                <td class="py-3 px-4 capitalize">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-gray-400">Tidak ada data santri ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="block md:hidden divide-y divide-gray-100">
                @forelse($data as $item)
                    <div class="p-4 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-xs text-gray-900 bg-gray-100 px-2 py-0.5 rounded">
                                NIS: {{ $item->nis }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>
                        <p class="font-extrabold text-gray-900 text-sm">{{ $item->nama }}</p>
                        <div class="text-xs text-gray-500 space-y-0.5">
                            <div><strong class="text-gray-700">Kelas / Kamar:</strong> {{ $item->kelas ?: '-' }} / {{ $item->kamar ?: '-' }}</div>
                            <div><strong class="text-gray-700">Wali:</strong> {{ $item->nama_wali ?: '-' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-400 text-xs">Tidak ada data santri ditemukan.</div>
                @endforelse
            </div>

        @elseif($tab === 'agenda')
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-100 text-gray-700 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Nama Agenda</th>
                            <th class="py-3 px-4">Lokasi</th>
                            <th class="py-3 px-4">Waktu Mulai</th>
                            <th class="py-3 px-4">Penanggung Jawab</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $item)
                            <tr class="hover:bg-gray-50/80">
                                <td class="py-3 px-4 font-bold text-gray-900">{{ $item->judul }}</td>
                                <td class="py-3 px-4">{{ $item->lokasi ?: '-' }}</td>
                                <td class="py-3 px-4">{{ $item->mulai ? $item->mulai->format('d-m-Y H:i') : '-' }}</td>
                                <td class="py-3 px-4">{{ $item->pj->nama ?? '-' }}</td>
                                <td class="py-3 px-4 capitalize">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item->status === 'selesai' ? 'bg-gray-100 text-gray-700' : ($item->status === 'berjalan' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center text-gray-400">Tidak ada data agenda untuk periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="block md:hidden divide-y divide-gray-100">
                @forelse($data as $item)
                    <div class="p-4 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 font-medium">
                                {{ $item->mulai ? $item->mulai->format('d-m-Y H:i') : '-' }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item->status === 'selesai' ? 'bg-gray-100 text-gray-700' : ($item->status === 'berjalan' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>
                        <p class="font-extrabold text-gray-900 text-sm leading-snug">{{ $item->judul }}</p>
                        <div class="text-xs text-gray-500 space-y-0.5">
                            <div><strong class="text-gray-700">Lokasi:</strong> {{ $item->lokasi ?: '-' }}</div>
                            <div><strong class="text-gray-700">PJ:</strong> {{ $item->pj->nama ?? '-' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-400 text-xs">Tidak ada data agenda untuk periode ini.</div>
                @endforelse
            </div>
        @endif
    </div>
</div>
@endsection
