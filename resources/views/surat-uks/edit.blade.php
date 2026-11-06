<x-app-layout>
    @section('title', 'Edit Surat Izin - ' . $suratUk->nomor_surat)

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('admin.surat-uks.index') }}" class="hover:text-emerald-700 transition">Surat UKS</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('admin.surat-uks.show', $suratUk) }}" class="hover:text-emerald-700 transition">{{ $suratUk->nomor_surat }}</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-800">Edit</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Edit Surat Izin Santri
                </h1>
            </div>

            <a href="{{ route('admin.surat-uks.show', $suratUk) }}"
               class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-bold rounded-xl transition">
                &larr; Batal & Kembali
            </a>
        </div>

        <form method="POST"
              action="{{ route('admin.surat-uks.update', $suratUk) }}"
              x-data="{
                  santriList: {{ json_encode($santris) }},
                  selectedSantriId: '{{ old('santri_id', $suratUk->santri_id) }}',
                  jenis: '{{ old('jenis', $suratUk->jenis) }}',
                  tglMulai: '{{ old('tanggal_mulai', $suratUk->tanggal_mulai ? $suratUk->tanggal_mulai->format('Y-m-d') : '') }}',
                  tglSelesai: '{{ old('tanggal_selesai', $suratUk->tanggal_selesai ? $suratUk->tanggal_selesai->format('Y-m-d') : '') }}',
                  get selectedSantri() {
                      return this.santriList.find(s => String(s.id) === String(this.selectedSantriId)) || null;
                  }
              }"
              class="bg-white rounded-3xl p-5 sm:p-7 border border-slate-200/80 shadow-xs space-y-6">
            @csrf
            @method('PUT')

            <!-- Informasi Nomor Surat (Terkunci untuk integritas) -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Nomor Surat Resmi</span>
                    <span class="font-mono font-bold text-slate-900 text-sm">{{ $suratUk->nomor_surat }}</span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Status Saat Ini</span>
                    <select name="status" class="text-xs font-bold rounded-lg border border-slate-300 p-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="aktif" {{ old('status', $suratUk->status) === 'aktif' ? 'selected' : '' }}>Sedang Izin (Aktif)</option>
                        <option value="kembali" {{ old('status', $suratUk->status) === 'kembali' ? 'selected' : '' }}>Sudah Kembali</option>
                        <option value="dibatalkan" {{ old('status', $suratUk->status) === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
            </div>

            <!-- 1. Pilihan Jenis Surat Izin -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Jenis Surat Izin <span class="text-rose-600">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3 max-w-md">
                    <label class="relative flex items-center justify-center gap-2 p-3.5 rounded-2xl border-2 cursor-pointer transition select-none"
                           :class="jenis === 'pulang' ? 'border-emerald-600 bg-emerald-50/70 text-emerald-950 font-bold shadow-xs' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'">
                        <input type="radio" name="jenis" value="pulang" x-model="jenis" class="sr-only">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="text-xs sm:text-sm">Surat Izin Pulang</span>
                    </label>

                    <label class="relative flex items-center justify-center gap-2 p-3.5 rounded-2xl border-2 cursor-pointer transition select-none"
                           :class="jenis === 'sakit' ? 'border-blue-600 bg-blue-50/70 text-blue-950 font-bold shadow-xs' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'">
                        <input type="radio" name="jenis" value="sakit" x-model="jenis" class="sr-only">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span class="text-xs sm:text-sm">Surat Izin Sakit</span>
                    </label>
                </div>
            </div>

            <!-- 2. Pemilihan Santri -->
            <div class="space-y-3 pt-2 border-t border-slate-100">
                <label for="santri_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Santri <span class="text-rose-600">*</span>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                    <div>
                        <select name="santri_id"
                                id="santri_id"
                                x-model="selectedSantriId"
                                required
                                class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @foreach ($santris as $s)
                                <option value="{{ $s->id }}" {{ $s->id == $suratUk->santri_id ? 'selected' : '' }}>
                                    {{ $s->nama }} (NIS: {{ $s->nis }}) - Kls {{ $s->kelas ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <template x-if="selectedSantri">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block" x-text="selectedSantri.nama"></span>
                            <span class="text-slate-600">Kelas: <strong x-text="selectedSantri.kelas"></strong> &bull; Kamar: <strong x-text="selectedSantri.kamar"></strong></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 3. Periode Waktu Izin -->
            <div class="pt-2 border-t border-slate-100 space-y-3">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Jangka Waktu Izin <span class="text-rose-600">*</span>
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="block text-[11px] font-semibold text-slate-600 mb-1">Tanggal Mulai Izin</span>
                        <input type="date"
                               name="tanggal_mulai"
                               x-model="tglMulai"
                               required
                               class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-slate-600 mb-1">Rencana Tanggal Kembali</span>
                        <input type="date"
                               name="tanggal_selesai"
                               x-model="tglSelesai"
                               class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- 4. Keperluan & Diagnosa Penyakit -->
            <div class="pt-2 border-t border-slate-100 space-y-4">
                <div>
                    <label for="keperluan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        <span x-text="jenis === 'sakit' ? 'Keluhan Penyakit / Alasan Istirahat' : 'Alasan / Keperluan Pulang'"></span>
                        <span class="text-rose-600">*</span>
                    </label>
                    <input type="text"
                           name="keperluan"
                           id="keperluan"
                           value="{{ old('keperluan', $suratUk->keperluan) }}"
                           required
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div x-show="jenis === 'sakit'">
                    <label for="diagnosa" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Diagnosa Medis / Obat Yang Diberikan (Opsional)
                    </label>
                    <input type="text"
                           name="diagnosa"
                           id="diagnosa"
                           value="{{ old('diagnosa', $suratUk->diagnosa) }}"
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div x-show="jenis === 'pulang'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="penjemput" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Penjemput / Wali
                        </label>
                        <input type="text"
                               name="penjemput"
                               id="penjemput"
                               value="{{ old('penjemput', $suratUk->penjemput) }}"
                               class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label for="hubungan_penjemput" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Hubungan Keluarga
                        </label>
                        <input type="text"
                               name="hubungan_penjemput"
                               id="hubungan_penjemput"
                               value="{{ old('hubungan_penjemput', $suratUk->hubungan_penjemput) }}"
                               class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- 5. Data Petugas Penandatangan UKS -->
            <div class="pt-2 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="petugas" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Petugas Yang Mengizinkan <span class="text-rose-600">*</span>
                    </label>
                    <input type="text"
                           name="petugas"
                           id="petugas"
                           value="{{ old('petugas', $suratUk->petugas) }}"
                           required
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label for="jabatan_petugas" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Jabatan Petugas <span class="text-rose-600">*</span>
                    </label>
                    <input type="text"
                           name="jabatan_petugas"
                           id="jabatan_petugas"
                           value="{{ old('jabatan_petugas', $suratUk->jabatan_petugas) }}"
                           required
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Catatan Tambahan -->
            <div>
                <label for="keterangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Catatan Khusus (Opsional)
                </label>
                <textarea name="keterangan"
                          id="keterangan"
                          rows="2"
                          class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('keterangan', $suratUk->keterangan) }}</textarea>
            </div>

            <!-- Tombol Aksi Simpan -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.surat-uks.show', $suratUk) }}"
                   class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:scale-95 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition">
                    Simpan Perubahan &rarr;
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
