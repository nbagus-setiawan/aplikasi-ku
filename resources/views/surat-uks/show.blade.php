<x-app-layout>
    @section('title', 'Detail Surat Izin - ' . $suratUk->nomor_surat)

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header & Breadcrumb -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('admin.surat-uks.index') }}" class="hover:text-emerald-700 transition">Surat UKS</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-800">{{ $suratUk->nomor_surat }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>{{ $suratUk->jenis_label }}</span>
                    @if ($suratUk->status === 'kembali')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Sudah Kembali
                        </span>
                    @elseif ($suratUk->status === 'aktif')
                        @if ($suratUk->is_overdue)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                Melewati Batas Kembali
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                Sedang Izin
                            </span>
                        @endif
                    @endif
                </h1>
                <p class="font-mono text-xs text-slate-500 mt-0.5">{{ $suratUk->nomor_surat }}</p>
            </div>

            <!-- Tombol Aksi Utama -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.surat-uks.cetak', $suratUk) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak PDF (A4)</span>
                </a>

                <a href="{{ route('admin.surat-uks.cetak', ['suratUk' => $suratUk, 'format' => 'slip']) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition active:scale-95"
                   title="Cetak format slip setengah A4 rangkap dua (1 arsip UKS, 1 santri/satpam)">
                    <span>Slip Rangkap 2</span>
                </a>

                <a href="{{ route('admin.surat-uks.word', $suratUk) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    <span>Unduh Word (.doc)</span>
                </a>

                <a href="{{ route('admin.surat-uks.edit', $suratUk) }}"
                   class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-bold rounded-xl transition">
                    Edit
                </a>

                @if ($suratUk->status === 'aktif')
                    <form action="{{ route('admin.surat-uks.kembali', $suratUk) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                data-konfirmasi="Tandai santri telah kembali ke pondok sekarang?"
                                data-judul="Konfirmasi Santri Kembali"
                                data-tombol="Ya, Sudah Kembali"
                                class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs sm:text-sm font-bold rounded-xl transition shadow-2xs">
                            ✓ Tandai Kembali
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Kartu Rincian Informasi -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- Header Kartu -->
            <div class="p-5 sm:p-6 bg-slate-50/70 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 {{ $suratUk->jenis === 'sakit' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $suratUk->jenis === 'sakit' ? '🩺' : '🏠' }}
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">{{ $suratUk->santri?->nama ?? 'Santri' }}</h3>
                        <p class="text-xs text-slate-500">
                            NIS: <strong class="text-slate-700">{{ $suratUk->santri?->nis ?? '-' }}</strong> &bull;
                            Kelas: <strong class="text-slate-700">{{ $suratUk->santri?->kelas ?? '-' }}</strong> &bull;
                            Kamar: <strong class="text-slate-700">{{ $suratUk->santri?->kamar ?? '-' }}</strong>
                        </p>
                    </div>
                </div>

                <div class="text-left sm:text-right">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Diterbitkan Oleh</span>
                    <span class="text-xs font-bold text-slate-800">{{ $suratUk->petugas }}</span>
                    <span class="text-[11px] text-slate-500 block">{{ $suratUk->jabatan_petugas }}</span>
                </div>
            </div>

            <!-- Konten Grid Data -->
            <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-xs sm:text-sm">
                <!-- Sisi Kiri: Waktu & Status -->
                <div class="space-y-4">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Masa Berlaku Izin</span>
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Mulai Izin:</span>
                                <strong class="text-slate-900">{{ $suratUk->tanggal_mulai ? $suratUk->tanggal_mulai->translatedFormat('l, d F Y') : '-' }}</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Rencana Kembali:</span>
                                <strong class="text-slate-900">{{ $suratUk->tanggal_selesai ? $suratUk->tanggal_selesai->translatedFormat('l, d F Y') : '-' }}</strong>
                            </div>
                            @if ($suratUk->tanggal_kembali_riil)
                                <div class="flex items-center justify-between pt-1.5 border-t border-slate-200/60 text-emerald-700">
                                    <span>Realisasi Kembali:</span>
                                    <strong class="font-bold">{{ $suratUk->tanggal_kembali_riil->translatedFormat('d-m-Y H:i') }} WIB</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Keperluan / Alasan</span>
                        <p class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-slate-800 font-semibold leading-relaxed">
                            {{ $suratUk->keperluan }}
                        </p>
                    </div>

                    @if ($suratUk->diagnosa)
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Diagnosa / Tindakan Medis UKS</span>
                            <p class="p-3.5 rounded-2xl bg-blue-50/60 border border-blue-100 text-blue-900 font-semibold leading-relaxed">
                                🩺 {{ $suratUk->diagnosa }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Sisi Kanan: Penjemput, Wali, & Keterangan -->
                <div class="space-y-4">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Informasi Wali & Penjemput</span>
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Wali Santri:</span>
                                <strong class="text-slate-900">{{ $suratUk->santri?->nama_wali ?? '-' }}</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">No. HP Wali:</span>
                                <strong class="text-slate-900">{{ $suratUk->santri?->no_hp_wali ?? '-' }}</strong>
                            </div>
                            @if ($suratUk->penjemput)
                                <div class="flex items-center justify-between pt-1.5 border-t border-slate-200/60">
                                    <span class="text-slate-500">Nama Penjemput:</span>
                                    <strong class="text-slate-900">{{ $suratUk->penjemput }} ({{ $suratUk->hubungan_penjemput ?? 'Wali' }})</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Catatan Tambahan</span>
                        <p class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-slate-700 leading-relaxed">
                            {{ $suratUk->keterangan ?: 'Tidak ada catatan tambahan.' }}
                        </p>
                    </div>

                    <div class="pt-2">
                        <span class="text-[11px] text-slate-400">
                            Dicatat oleh: <strong>{{ $suratUk->user?->name ?? 'Sekretaris' }}</strong> pada {{ $suratUk->created_at->translatedFormat('d-m-Y H:i') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Footer Aksi Hapus -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.surat-uks.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">
                    &larr; Kembali ke Daftar
                </a>

                <form action="{{ route('admin.surat-uks.destroy', $suratUk) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            data-konfirmasi="Yakin ingin menghapus surat izin {{ $suratUk->nomor_surat }}? Data dapat dipulihkan melalui soft delete."
                            data-judul="Hapus Surat Izin"
                            data-tombol="Ya, Hapus"
                            class="text-xs font-bold text-rose-600 hover:text-rose-800 transition">
                        Hapus Surat Izin
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
