@extends('layouts.app')

@section('title', 'Editor Visual Beranda')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">Editor Visual Beranda</h1>
                <span class="px-3 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $halaman->status === 'terbit' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                    Status: {{ ucfirst($halaman->status) }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                Atur tata letak dan isi blok konten beranda tanpa koding. Perubahan terbit langsung terlihat di halaman depan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ url('/') }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                <span>Lihat Beranda</span>
            </a>
            <button type="button" onclick="document.getElementById('modal-revisi').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 transition shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Riwayat Revisi ({{ $revisis->count() }})</span>
            </button>
        </div>
    </div>



    <!-- Formulir Editor Blok -->
    <form action="{{ route('admin.halaman.beranda.update') }}" method="POST" id="form-editor">
        @csrf
        <input type="hidden" name="judul" value="{{ $halaman->judul }}">

        <div class="space-y-6">
            <!-- Toolbar Atas: Tambah Blok Baru -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tambah Blok:</span>
                    <select id="select-tambah-blok" class="rounded-xl border-gray-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600 py-2">
                        <option value="hero">Hero Banner (Utama)</option>
                        <option value="profil">Profil & Sambutan</option>
                        <option value="statistik">Statistik Santri</option>
                        <option value="program">Program Unggulan</option>
                        <option value="pengumuman">Warta & Pengumuman</option>
                        <option value="kontak">Kontak & Lokasi</option>
                    </select>
                    <button type="button" onclick="tambahBlokBaru()" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 transition shadow-2xs">
                        + Tambah ke Susunan
                    </button>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <input type="text" name="catatan_revisi" placeholder="Catatan revisi (opsional)" class="rounded-xl border-gray-300 text-xs py-2 px-3 focus:border-emerald-600 focus:ring-emerald-600 w-full sm:w-56">
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" name="status" value="draft" class="flex-1 sm:flex-initial px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 transition shadow-2xs">
                            Simpan Draf
                        </button>
                        <button type="submit" name="status" value="terbit" class="flex-1 sm:flex-initial px-5 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm transition">
                            Terbitkan Beranda
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kontainer Blok Susunan -->
            <div id="container-blok" class="space-y-4">
                @php $blocks = $halaman->konten ?? []; @endphp

                @foreach($blocks as $index => $block)
                    @php
                        $bId = $block['id'] ?? 'block_' . $index;
                        $bType = $block['type'] ?? 'hero';
                        $bData = $block['data'] ?? [];
                    @endphp

                    <div class="item-blok bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden transition" data-index="{{ $index }}">
                        <!-- Header Blok (Grip & Controls) -->
                        <div class="bg-gray-50 px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr($bType, 0, 2) }}
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">
                                        Blok: {{ ucfirst($bType) }}
                                    </h3>
                                    <span class="text-[11px] text-gray-400">ID: {{ $bId }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1">
                                <button type="button" onclick="geserBlok(this, -1)" title="Geser ke Atas" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-200 hover:text-gray-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                </button>
                                <button type="button" onclick="geserBlok(this, 1)" title="Geser ke Bawah" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-200 hover:text-gray-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <button type="button" onclick="hapusBlok(this)" title="Hapus Blok" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Body Editor Input Fields untuk Blok ini -->
                        <div class="p-6">
                            <input type="hidden" name="blocks[{{ $index }}][id]" value="{{ $bId }}">
                            <input type="hidden" name="blocks[{{ $index }}][type]" value="{{ $bType }}">

                            @if($bType === 'hero')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Badge Atas</label>
                                        <input type="text" name="blocks[{{ $index }}][data][badge]" value="{{ $bData['badge'] ?? '' }}" class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Utama Hero</label>
                                        <input type="text" name="blocks[{{ $index }}][data][judul]" value="{{ $bData['judul'] ?? '' }}" class="w-full rounded-xl border-gray-300 text-sm font-bold focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Subjudul / Deskripsi</label>
                                        <textarea name="blocks[{{ $index }}][data][subjudul]" rows="2" class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">{{ $bData['subjudul'] ?? '' }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Teks Tombol Utama</label>
                                        <input type="text" name="blocks[{{ $index }}][data][cta_text]" value="{{ $bData['cta_text'] ?? '' }}" class="w-full rounded-xl border-gray-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">URL Tombol Utama</label>
                                        <input type="text" name="blocks[{{ $index }}][data][cta_url]" value="{{ $bData['cta_url'] ?? '/pengumuman' }}" class="w-full rounded-xl border-gray-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Teks Tombol Sekunder</label>
                                        <input type="text" name="blocks[{{ $index }}][data][cta_secondary_text]" value="{{ $bData['cta_secondary_text'] ?? '' }}" class="w-full rounded-xl border-gray-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">URL Tombol Sekunder</label>
                                        <input type="text" name="blocks[{{ $index }}][data][cta_secondary_url]" value="{{ $bData['cta_secondary_url'] ?? '/verifikasi/contoh' }}" class="w-full rounded-xl border-gray-300 text-sm">
                                    </div>
                                </div>

                            @elseif($bType === 'profil')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Profil</label>
                                        <input type="text" name="blocks[{{ $index }}][data][judul]" value="{{ $bData['judul'] ?? '' }}" class="w-full rounded-xl border-gray-300 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kutipan / Motto</label>
                                        <input type="text" name="blocks[{{ $index }}][data][kutipan]" value="{{ $bData['kutipan'] ?? '' }}" class="w-full rounded-xl border-gray-300 text-sm italic">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi Lengkap</label>
                                        <textarea name="blocks[{{ $index }}][data][deskripsi]" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ $bData['deskripsi'] ?? '' }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Visi Pondok</label>
                                            <textarea name="blocks[{{ $index }}][data][visi]" rows="2" class="w-full rounded-xl border-gray-300 text-sm">{{ $bData['visi'] ?? '' }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Misi Pondok</label>
                                            <textarea name="blocks[{{ $index }}][data][misi]" rows="2" class="w-full rounded-xl border-gray-300 text-sm">{{ $bData['misi'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            @elseif($bType === 'statistik')
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div class="md:col-span-2 lg:col-span-4">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Bagian</label>
                                        <input type="text" name="blocks[{{ $index }}][data][judul]" value="{{ $bData['judul'] ?? 'Pondok Pesantren dalam Angka' }}" class="w-full rounded-xl border-gray-300 text-sm font-bold">
                                        <p class="text-xs text-emerald-600 mt-1">Catatan: Nilai angka di beranda publik terhitung otomatis dari basis data santri dan kepengurusan pesantren.</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Santri Putra Aktif</label>
                                        <input type="text" name="blocks[{{ $index }}][data][santri_aktif]" value="{{ $bData['santri_aktif'] ?? 'Otomatis Database' }}" class="w-full rounded-xl border-gray-300 text-sm bg-gray-50">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Dewan Asatidz & Pengurus Putra</label>
                                        <input type="text" name="blocks[{{ $index }}][data][pengurus_asatidz]" value="{{ $bData['pengurus_asatidz'] ?? 'Otomatis Database' }}" class="w-full rounded-xl border-gray-300 text-sm bg-gray-50">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kamar Santri Putra</label>
                                        <input type="text" name="blocks[{{ $index }}][data][asrama_kamar]" value="{{ $bData['asrama_kamar'] ?? 'Otomatis Database' }}" class="w-full rounded-xl border-gray-300 text-sm bg-gray-50">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Angkatan Alumni Putra</label>
                                        <input type="text" name="blocks[{{ $index }}][data][angkatan_alumni]" value="{{ $bData['angkatan_alumni'] ?? 'Otomatis Database' }}" class="w-full rounded-xl border-gray-300 text-sm bg-gray-50">
                                    </div>
                                </div>

                            @elseif($bType === 'program')
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Bagian</label>
                                            <input type="text" name="blocks[{{ $index }}][data][judul]" value="{{ $bData['judul'] ?? 'Program Unggulan' }}" class="w-full rounded-xl border-gray-300 text-sm font-bold">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Subjudul</label>
                                            <input type="text" name="blocks[{{ $index }}][data][subjudul]" value="{{ $bData['subjudul'] ?? '' }}" class="w-full rounded-xl border-gray-300 text-sm">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
                                            <span class="text-xs font-bold text-emerald-700">Program 1</span>
                                            <input type="text" name="blocks[{{ $index }}][data][item_1_judul]" value="{{ $bData['item_1_judul'] ?? 'Kajian Kitab Kuning' }}" class="w-full rounded-lg border-gray-300 text-xs font-bold">
                                            <textarea name="blocks[{{ $index }}][data][item_1_desk]" rows="2" class="w-full rounded-lg border-gray-300 text-xs">{{ $bData['item_1_desk'] ?? '' }}</textarea>
                                        </div>
                                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
                                            <span class="text-xs font-bold text-emerald-700">Program 2</span>
                                            <input type="text" name="blocks[{{ $index }}][data][item_2_judul]" value="{{ $bData['item_2_judul'] ?? 'Tahfidzul Quran' }}" class="w-full rounded-lg border-gray-300 text-xs font-bold">
                                            <textarea name="blocks[{{ $index }}][data][item_2_desk]" rows="2" class="w-full rounded-lg border-gray-300 text-xs">{{ $bData['item_2_desk'] ?? '' }}</textarea>
                                        </div>
                                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
                                            <span class="text-xs font-bold text-emerald-700">Program 3</span>
                                            <input type="text" name="blocks[{{ $index }}][data][item_3_judul]" value="{{ $bData['item_3_judul'] ?? 'Madrasah Diniyah' }}" class="w-full rounded-lg border-gray-300 text-xs font-bold">
                                            <textarea name="blocks[{{ $index }}][data][item_3_desk]" rows="2" class="w-full rounded-lg border-gray-300 text-xs">{{ $bData['item_3_desk'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            @elseif($bType === 'pengumuman')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Bagian</label>
                                        <input type="text" name="blocks[{{ $index }}][data][judul]" value="{{ $bData['judul'] ?? 'Informasi & Pengumuman Terbaru' }}" class="w-full rounded-xl border-gray-300 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Subjudul</label>
                                        <input type="text" name="blocks[{{ $index }}][data][subjudul]" value="{{ $bData['subjudul'] ?? '' }}" class="w-full rounded-xl border-gray-300 text-sm">
                                    </div>
                                    <div class="md:col-span-2">
                                        <p class="text-xs text-gray-500 italic">
                                            * Blok ini secara otomatis menampilkan 3 pengumuman resmi terbaru yang berstatus terbit dari sistem.
                                        </p>
                                    </div>
                                </div>

                            @elseif($bType === 'kontak')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Bagian</label>
                                        <input type="text" name="blocks[{{ $index }}][data][judul]" value="{{ $bData['judul'] ?? 'Hubungi Sekretariat' }}" class="w-full rounded-xl border-gray-300 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Subjudul</label>
                                        <input type="text" name="blocks[{{ $index }}][data][subjudul]" value="{{ $bData['subjudul'] ?? '' }}" class="w-full rounded-xl border-gray-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat</label>
                                        <input type="text" name="blocks[{{ $index }}][data][alamat]" value="{{ $bData['alamat'] ?? '' }}" class="w-full rounded-xl border-gray-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Telepon</label>
                                        <input type="text" name="blocks[{{ $index }}][data][telepon]" value="{{ $bData['telepon'] ?? '' }}" class="w-full rounded-xl border-gray-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                                        <input type="text" name="blocks[{{ $index }}][data][email]" value="{{ $bData['email'] ?? '' }}" class="w-full rounded-xl border-gray-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Pelayanan</label>
                                        <input type="text" name="blocks[{{ $index }}][data][jam_kerja]" value="{{ $bData['jam_kerja'] ?? '' }}" class="w-full rounded-xl border-gray-300 text-sm">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Toolbar Bawah Simpan -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3.5">
                <div class="flex items-center gap-2.5 text-xs text-gray-500 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <span>Pastikan menyimpan draf atau menerbitkan perubahan setelah mengubah tata letak.</span>
                </div>
                <div class="flex items-center gap-2.5 w-full sm:w-auto">
                    <button type="submit" name="status" value="draft" class="flex-1 sm:flex-initial px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 transition shadow-2xs text-center">
                        Simpan Draf
                    </button>
                    <button type="submit" name="status" value="terbit" class="flex-1 sm:flex-initial px-6 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm transition text-center">
                        Terbitkan Beranda
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Riwayat Revisi -->
<div id="modal-revisi" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-6 max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Riwayat Revisi Beranda</h3>
                <p class="text-xs text-gray-500">Pilih versi sebelumnya untuk memulihkan susunan konten.</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-revisi').classList.add('hidden')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="overflow-y-auto flex-grow divide-y divide-gray-100">
            @forelse($revisis as $rev)
                <div class="py-4 flex items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-900">Revisi #{{ $rev->id }}</span>
                            <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-600">{{ $rev->created_at->format('d-m-Y H:i') }}</span>
                        </div>
                        <p class="text-xs text-gray-600">{{ $rev->catatan ?: 'Perubahan konten beranda' }}</p>
                        <span class="text-[11px] text-gray-400">Oleh: {{ $rev->user->name ?? 'Sekretaris' }} • {{ is_array($rev->konten) ? count($rev->konten) : 0 }} blok</span>
                    </div>

                    <form action="{{ route('admin.halaman.revisi.restore', [$halaman->id, $rev->id]) }}" method="POST"
                          data-konfirmasi="Pulihkan beranda ke revisi ini? Versi saat ini akan otomatis dicadangkan."
                          data-judul="Pulihkan Versi Beranda"
                          data-tombol="Ya, Pulihkan Versi">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 transition shadow-2xs">
                            Pulihkan Versi Ini
                        </button>
                    </form>
                </div>
            @empty
                <div class="py-12 text-center text-gray-400 text-sm">
                    Belum ada riwayat revisi beranda tersimpan.
                </div>
            @endforelse
        </div>

        <div class="pt-4 border-t border-gray-200 flex justify-end">
            <button type="button" onclick="document.getElementById('modal-revisi').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 transition shadow-2xs">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function geserBlok(btn, direction) {
        const item = btn.closest('.item-blok');
        if (direction === -1) {
            const prev = item.previousElementSibling;
            if (prev) {
                item.parentNode.insertBefore(item, prev);
            }
        } else if (direction === 1) {
            const next = item.nextElementSibling;
            if (next) {
                item.parentNode.insertBefore(next, item);
            }
        }
        refreshNames();
    }

    function hapusBlok(btn) {
        if (window.bukaKonfirmasi) {
            window.bukaKonfirmasi({
                judul: 'Hapus Blok Beranda',
                pesan: 'Apakah Anda yakin ingin menghapus blok konten ini dari tata letak beranda?',
                tombolTeks: 'Ya, Hapus Blok',
                onConfirm: function() {
                    const item = btn.closest('.item-blok');
                    if (item) {
                        item.remove();
                        refreshNames();
                    }
                }
            });
        } else {
            const item = btn.closest('.item-blok');
            if (item) {
                item.remove();
                refreshNames();
            }
        }
    }

    function tambahBlokBaru() {
        const select = document.getElementById('select-tambah-blok');
        const type = select.value;
        const container = document.getElementById('container-blok');
        const index = container.querySelectorAll('.item-blok').length;
        const id = type + '_' + Date.now();

        let innerFields = '';
        if (type === 'hero') {
            innerFields = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Badge Atas</label>
                        <input type="text" name="blocks[${index}][data][badge]" value="Selamat Datang" class="w-full rounded-xl border-gray-300 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Utama Hero</label>
                        <input type="text" name="blocks[${index}][data][judul]" value="Pondok Pesantren Al-Falah Putra" class="w-full rounded-xl border-gray-300 text-sm font-bold">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Subjudul / Deskripsi</label>
                        <textarea name="blocks[${index}][data][subjudul]" rows="2" class="w-full rounded-xl border-gray-300 text-sm">Mencetak Generasi Berakhlak Mulia dan Berilmu Luas.</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Teks Tombol Utama</label>
                        <input type="text" name="blocks[${index}][data][cta_text]" value="Lihat Pengumuman" class="w-full rounded-xl border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">URL Tombol Utama</label>
                        <input type="text" name="blocks[${index}][data][cta_url]" value="/pengumuman" class="w-full rounded-xl border-gray-300 text-sm">
                    </div>
                </div>`;
        } else if (type === 'profil') {
            innerFields = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Profil</label>
                        <input type="text" name="blocks[${index}][data][judul]" value="Sekilas Tentang Pondok" class="w-full rounded-xl border-gray-300 text-sm font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi Lengkap</label>
                        <textarea name="blocks[${index}][data][deskripsi]" rows="3" class="w-full rounded-xl border-gray-300 text-sm">Lembaga pendidikan Islam berlandaskan aqidah Ahlussunnah wal Jamaah.</textarea>
                    </div>
                </div>`;
        } else if (type === 'statistik') {
            innerFields = `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="md:col-span-2 lg:col-span-4">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Bagian</label>
                        <input type="text" name="blocks[${index}][data][judul]" value="Pondok Pesantren dalam Angka" class="w-full rounded-xl border-gray-300 text-sm font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Santri Aktif</label>
                        <input type="text" name="blocks[${index}][data][santri_aktif]" value="1.250+" class="w-full rounded-xl border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pengurus & Asatidz</label>
                        <input type="text" name="blocks[${index}][data][pengurus_asatidz]" value="65" class="w-full rounded-xl border-gray-300 text-sm">
                    </div>
                </div>`;
        } else if (type === 'program') {
            innerFields = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Bagian</label>
                        <input type="text" name="blocks[${index}][data][judul]" value="Program Unggulan" class="w-full rounded-xl border-gray-300 text-sm font-bold">
                    </div>
                </div>`;
        } else if (type === 'pengumuman') {
            innerFields = `
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Bagian</label>
                    <input type="text" name="blocks[${index}][data][judul]" value="Informasi & Pengumuman Terbaru" class="w-full rounded-xl border-gray-300 text-sm font-bold">
                </div>`;
        } else if (type === 'kontak') {
            innerFields = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Bagian</label>
                        <input type="text" name="blocks[${index}][data][judul]" value="Hubungi Sekretariat" class="w-full rounded-xl border-gray-300 text-sm font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Telepon</label>
                        <input type="text" name="blocks[${index}][data][telepon]" value="0812-7890-3117" class="w-full rounded-xl border-gray-300 text-sm">
                    </div>
                </div>`;
        }

        const div = document.createElement('div');
        div.className = 'item-blok bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden transition';
        div.setAttribute('data-index', index);
        div.innerHTML = `
            <div class="bg-gray-50 px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs uppercase">
                        ${type.substring(0, 2)}
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Blok: ${type}</h3>
                        <span class="text-[11px] text-gray-400">ID: ${id}</span>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" onclick="geserBlok(this, -1)" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg></button>
                    <button type="button" onclick="geserBlok(this, 1)" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></button>
                    <button type="button" onclick="hapusBlok(this)" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                </div>
            </div>
            <div class="p-6">
                <input type="hidden" name="blocks[${index}][id]" value="${id}">
                <input type="hidden" name="blocks[${index}][type]" value="${type}">
                ${innerFields}
            </div>
        `;

        container.appendChild(div);
        refreshNames();
    }

    function refreshNames() {
        const items = document.querySelectorAll('#container-blok .item-blok');
        items.forEach((item, idx) => {
            item.setAttribute('data-index', idx);
            const inputs = item.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name && name.startsWith('blocks[')) {
                    input.setAttribute('name', name.replace(/blocks\[\d+\]/, `blocks[${idx}]`));
                }
            });
        });
    }
</script>
@endsection
