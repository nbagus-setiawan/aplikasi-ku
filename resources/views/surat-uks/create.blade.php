<x-app-layout>
    @section('title', 'Buat Surat Izin UKS - SAP Al-Falah Putra')

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('admin.surat-uks.index') }}" class="hover:text-emerald-700 transition">Surat UKS</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-800">Buat Surat Izin</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Formulir Surat Izin Santri
                </h1>
            </div>

            <a href="{{ route('admin.surat-uks.index') }}"
               class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-bold rounded-xl transition">
                &larr; Kembali
            </a>
        </div>

        <form method="POST"
              action="{{ route('admin.surat-uks.store') }}"
              x-data="{
                  santriList: {{ json_encode($santris) }},
                  selectedSantriId: '{{ old('santri_id', $selectedSantriId) }}',
                  jenis: '{{ old('jenis', $defaultJenis) }}',
                  tglMulai: '{{ old('tanggal_mulai', date('Y-m-d')) }}',
                  tglSelesai: '{{ old('tanggal_selesai', date('Y-m-d', strtotime('+3 days'))) }}',
                  get selectedSantri() {
                      return this.santriList.find(s => String(s.id) === String(this.selectedSantriId)) || null;
                  },
                  tambahHari(n) {
                      if (!this.tglMulai) return;
                      let d = new Date(this.tglMulai);
                      d.setDate(d.getDate() + n);
                      this.tglSelesai = d.toISOString().split('T')[0];
                  }
              }"
              class="bg-white rounded-3xl p-5 sm:p-7 border border-slate-200/80 shadow-xs space-y-6">
            @csrf

            <!-- 1. Pilihan Jenis Surat Izin (Pill Switcher) -->
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

            <!-- 2. Pemilihan Santri (Searchable/Select) -->
            <div class="space-y-3 pt-2 border-t border-slate-100">
                <label for="santri_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Pilih Santri <span class="text-rose-600">*</span>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                    <div>
                        <select name="santri_id"
                                id="santri_id"
                                x-model="selectedSantriId"
                                required
                                class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">-- Cari / Pilih Santri Aktif --</option>
                            @foreach ($santris as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->nama }} (NIS: {{ $s->nis }}) - Kls {{ $s->kelas ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('santri_id')
                            <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kartu Info Santri Terpilih -->
                    <template x-if="selectedSantri">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-1.5 animate-fadeIn">
                            <div class="flex items-center justify-between">
                                <span class="font-extrabold text-slate-900 text-sm" x-text="selectedSantri.nama"></span>
                                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold text-[10px]" x-text="'NIS: ' + selectedSantri.nis"></span>
                            </div>
                            <div class="text-slate-600 flex flex-wrap gap-x-3 gap-y-0.5">
                                <span>Kelas: <strong class="text-slate-800" x-text="selectedSantri.kelas || '-'"></strong></span>
                                <span>&bull;</span>
                                <span>Kamar: <strong class="text-slate-800" x-text="selectedSantri.kamar || '-'"></strong></span>
                            </div>
                            <div class="text-slate-500 text-[11px] pt-1 border-t border-slate-200/60" x-show="selectedSantri.nama_wali">
                                Wali: <span class="text-slate-700 font-medium" x-text="selectedSantri.nama_wali"></span>
                                <span x-show="selectedSantri.no_hp_wali" x-text="' (' + selectedSantri.no_hp_wali + ')'"></span>
                            </div>
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
                        @error('tanggal_mulai')
                            <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <span class="block text-[11px] font-semibold text-slate-600 mb-1">Rencana Tanggal Kembali</span>
                        <input type="date"
                               name="tanggal_selesai"
                               x-model="tglSelesai"
                               class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @error('tanggal_selesai')
                            <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror

                        <!-- Tombol Cepat Tambah Hari -->
                        <div class="flex items-center gap-1.5 mt-2 text-[11px]">
                            <span class="text-slate-400">Pilihan cepat:</span>
                            <button type="button" @click="tambahHari(1)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">+1 Hari</button>
                            <button type="button" @click="tambahHari(3)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">+3 Hari</button>
                            <button type="button" @click="tambahHari(7)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">+7 Hari</button>
                        </div>
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
                           value="{{ old('keperluan') }}"
                           required
                           :placeholder="jenis === 'sakit' ? 'Contoh: Demam tinggi, flu berat, dan perlu istirahat total' : 'Contoh: Menghadiri pernikahan saudara kandung / berobat ke spesialis'"
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @error('keperluan')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Khusus Izin Sakit: Diagnosa Dokter / UKS -->
                <div x-show="jenis === 'sakit'">
                    <label for="diagnosa" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Diagnosa Medis / Obat Yang Diberikan (Opsional)
                    </label>
                    <input type="text"
                           name="diagnosa"
                           id="diagnosa"
                           value="{{ old('diagnosa') }}"
                           placeholder="Contoh: Febris H-2, Paracetamol 500mg 3x1, Dianjurkan bed rest 2 hari"
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Khusus Izin Pulang: Penjemput -->
                <div x-show="jenis === 'pulang'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="penjemput" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Penjemput / Wali
                        </label>
                        <input type="text"
                               name="penjemput"
                               id="penjemput"
                               value="{{ old('penjemput') }}"
                               placeholder="Nama orang yang menjemput santri"
                               class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label for="hubungan_penjemput" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Hubungan Keluarga
                        </label>
                        <input type="text"
                               name="hubungan_penjemput"
                               id="hubungan_penjemput"
                               value="{{ old('hubungan_penjemput') }}"
                               placeholder="Contoh: Orang Tua Kandung, Paman, Kakak"
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
                           value="{{ old('petugas', $defaultPetugas) }}"
                           required
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @error('petugas')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jabatan_petugas" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Jabatan Petugas <span class="text-rose-600">*</span>
                    </label>
                    <input type="text"
                           name="jabatan_petugas"
                           id="jabatan_petugas"
                           value="{{ old('jabatan_petugas', 'Pengurus UKS / Bag. Kesehatan') }}"
                           required
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @error('jabatan_petugas')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
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
                          placeholder="Catatan tambahan untuk pos keamanan, wali santri, atau arsip pondok..."
                          class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('keterangan') }}</textarea>
            </div>

            <!-- Tombol Aksi Simpan -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.surat-uks.index') }}"
                   class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:scale-95 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition">
                    Terbitkan Surat Izin &rarr;
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
