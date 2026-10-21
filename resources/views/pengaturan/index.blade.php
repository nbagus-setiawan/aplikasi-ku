<x-app-layout>
    @section('title', 'Pengaturan Sistem')

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header Judul -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">Pengaturan Sistem</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola identitas pesantren, pengaturan standar kertas & orientasi dokumen, serta backup harian sistem.</p>
            </div>
        </div>

        <!-- Form Bagian 1 & 2: Identitas Pesantren + Standar Kertas & Orientasi Dokumen -->
        <form action="{{ route('admin.pengaturan.update') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="pola_nomor_surat" value="{{ $pengaturans['pola_nomor_surat'] ?? '{urut}/{jenis}/{lembaga}/{bulan}/{tahun}' }}">

            <!-- BAGIAN 1: Identitas Pesantren -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="border-b border-gray-100 pb-3 mb-5">
                    <h2 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        1. Identitas Pesantren
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Informasi nama aplikasi dan identitas resmi Pondok Pesantren Al Falah Putra.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Aplikasi Web <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_situs" value="{{ old('nama_situs', $pengaturans['nama_situs'] ?? 'SAP | SEKRETARIS AL-FALAH PUTRA') }}" required
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Lembaga <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lembaga" value="{{ old('nama_lembaga', $pengaturans['nama_lembaga'] ?? 'Pondok Pesantren Al Falah Putra') }}" required
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kode Lembaga <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_lembaga" value="{{ old('kode_lembaga', $pengaturans['kode_lembaga'] ?? 'AF-PA') }}" required
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm font-mono uppercase">
                        <p class="text-[11px] text-gray-400 mt-1">Digunakan pada nomor surat resmi (misal: AF-PA).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nomor Telepon</label>
                        <input type="text" name="telepon" value="{{ old('telepon', $pengaturans['telepon'] ?? '0812-7890-3117') }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Email Resmi</label>
                        <input type="email" name="email" value="{{ old('email', $pengaturans['email'] ?? 'sekretariat@alfalahputra.sch.id') }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alamat Lembaga</label>
                        <textarea name="alamat" rows="2"
                                  class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">{{ old('alamat', $pengaturans['alamat'] ?? 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 2: Pengaturan Standar Kertas & Orientasi Dokumen -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="border-b border-gray-100 pb-3 mb-5">
                    <h2 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        2. Pengaturan Standar Kertas & Orientasi Dokumen
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Standarisasi ukuran kertas, arah cetak, dan batas tepi (margin) seluruh dokumen pesantren.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-emerald-50/50 p-4 rounded-xl border border-emerald-200">
                    <!-- Ukuran Kertas Default -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Ukuran Kertas Utama</label>
                        <select name="ukuran_kertas" class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 bg-white font-semibold">
                            <option value="a4" {{ ($pengaturans['ukuran_kertas'] ?? 'a4') === 'a4' ? 'selected' : '' }}>A4 (210 × 297 mm)</option>
                            <option value="f4" {{ ($pengaturans['ukuran_kertas'] ?? 'a4') === 'f4' ? 'selected' : '' }}>F4 / Folio (215 × 330 mm)</option>
                        </select>
                        <p class="text-[11px] text-gray-500 mt-1">Dapat disetel A4 atau F4/Folio.</p>
                    </div>

                    <!-- Orientasi Kertas (Potret - Wajib / Terkunci) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Orientasi Cetak Dokumen</label>
                        <select name="orientasi_kertas" class="w-full text-xs rounded-lg border-emerald-300 focus:border-emerald-600 focus:ring-emerald-600 bg-emerald-100/70 font-bold text-emerald-950">
                            <option value="portrait" selected>📄 Potret (Tegak) &mdash; Bukan Lanskap</option>
                        </select>
                        <p class="text-[11px] text-emerald-800 font-semibold mt-1">✓ Wajib Potret (Tegak) untuk surat resmi.</p>
                    </div>

                    <!-- Margin Standar -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Batas Margin Lembar</label>
                        <select name="margin_kertas" class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 bg-white font-semibold">
                            <option value="standar" {{ ($pengaturans['margin_kertas'] ?? 'standar') === 'standar' ? 'selected' : '' }}>Standar Dinas (Kiri 2.5cm, Rata 2cm)</option>
                            <option value="ringkas" {{ ($pengaturans['margin_kertas'] ?? 'standar') === 'ringkas' ? 'selected' : '' }}>Ringkas (2cm Keliling)</option>
                            <option value="lebar" {{ ($pengaturans['margin_kertas'] ?? 'standar') === 'lebar' ? 'selected' : '' }}>Lebar (Kiri 3cm / Map Berkas)</option>
                        </select>
                        <p class="text-[11px] text-gray-500 mt-1">Batas tepi naskah surat dinas.</p>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm rounded-xl shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        </form>

        <!-- BAGIAN 3: Backup Data Harian (Hanya Simpan 1 File Terbaru — Anti Penuh) -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div class="border-b border-gray-100 pb-3 mb-5 flex flex-wrap items-center justify-between gap-2">
                <div>
        <!-- BAGIAN 3: Cadangan & Pemulihan Database (Backup & Restore) -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">
            <!-- Header Section 3 -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-100 pb-3">
                <div>
                    <h2 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        3. Cadangan & Pemulihan Database (Backup & Restore)
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Kelola cadangan data harian sistem dan pulihkan database saat diperlukan.
                    </p>
                </div>
                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full self-start sm:self-auto">
                    ✓ Hemat Ruang 100% (1 File Terbaru)
                </span>
            </div>

            <!-- Sub-bagian A: Backup Database -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Cadangan Database (Backup)
                </h3>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 font-extrabold text-lg">
                            ZIP
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-mono font-extrabold text-gray-900">
                                    {{ $backupInfo['filename'] ?? ('Backup' . now()->format('dmy') . '.zip') }}
                                </span>
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-[11px] font-bold">
                                    {{ $backupInfo['size_formatted'] ?? 'Siap' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Terakhir diperbarui: <strong class="text-gray-700">{{ $backupInfo['updated_at'] ?? now()->format('d-m-Y H:i') }}</strong>
                                &bull; Backup lama otomatis terhapus.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                        <!-- Tombol Backup Sekarang (Timpa Backup Lama) -->
                        <form action="{{ route('admin.pengaturan.backup') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-800 border border-gray-300 font-bold text-xs rounded-xl shadow-xs transition">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Perbarui Backup Sekarang</span>
                            </button>
                        </form>

                        <!-- Tombol Unduh File Backup Terbaru -->
                        <a href="{{ route('admin.pengaturan.backup.download') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Unduh Backup</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sub-bagian B: Pemulihan Database (Restore) -->
            <div class="border-t border-gray-100 pt-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Pemulihan Database (Restore)
                    </h3>
                </div>

                <!-- Alert Peringatan Restore -->
                <div class="p-4 bg-amber-50/90 border border-amber-200 rounded-xl flex items-start gap-3 text-xs text-amber-900 leading-relaxed">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <strong class="font-bold text-amber-950">PERINGATAN PENTING:</strong> Tindakan pemulihan (restore) akan <strong>menimpa dan mengganti seluruh data database saat ini</strong> dengan data dari file cadangan. Sangat disarankan untuk mengunduh backup terkini sebelum melakukan restore.
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Opsi 1: Unggah File Backup (.zip / .sql) -->
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 flex flex-col justify-between space-y-4">
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 uppercase">Opsi 1: Unggah File Cadangan</h4>
                            <p class="text-xs text-gray-500 mt-1">
                                Pilih file cadangan berformat <span class="font-mono font-semibold text-emerald-800">.zip</span> atau <span class="font-mono font-semibold text-emerald-800">.sql</span> (maksimal 20 MB).
                            </p>
                        </div>

                        <form action="{{ route('admin.pengaturan.restore') }}"
                              method="POST"
                              enctype="multipart/form-data"
                              onsubmit="return confirm('PERINGATAN: Memulihkan database akan menimpa seluruh data yang ada saat ini dengan file yang dipilih.\n\nApakah Anda yakin ingin melanjutkan proses pemulihan?');"
                              class="space-y-3">
                            @csrf
                            <input type="file"
                                   name="file_backup"
                                   accept=".zip,.sql,.txt"
                                   required
                                   class="block w-full text-xs text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-white file:text-gray-700 hover:file:bg-gray-100 file:shadow-xs border border-gray-300 rounded-lg bg-white cursor-pointer focus:outline-hidden">
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span>Pulihkan dari File Unggahan</span>
                            </button>
                        </form>
                    </div>

                    <!-- Opsi 2: Pulihkan dari Backup Server Terbaru -->
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 flex flex-col justify-between space-y-4">
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 uppercase">Opsi 2: Pulihkan dari Server</h4>
                            <p class="text-xs text-gray-500 mt-1">
                                Pulihkan langsung dari file cadangan terakhir yang ada di penyimpanan server:
                                <strong class="text-gray-800 font-mono block mt-1">{{ $backupInfo['filename'] ?? 'Belum ada file backup' }}</strong>
                            </p>
                        </div>

                        <form action="{{ route('admin.pengaturan.restore') }}"
                              method="POST"
                              onsubmit="return confirm('PERINGATAN: Anda akan memulihkan database dari file backup server ({{ $backupInfo['filename'] ?? '' }}).\n\nSemua perubahan data setelah tanggal backup tersebut akan ditimpa.\n\nApakah Anda yakin ingin melanjutkan?');">
                            @csrf
                            <input type="hidden" name="sumber" value="server">
                            <button type="submit"
                                    @if(empty($backupInfo['exists'])) disabled @endif
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 {{ !empty($backupInfo['exists']) ? 'bg-gray-800 hover:bg-gray-900 text-white cursor-pointer' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }} font-bold text-xs rounded-xl shadow-xs transition">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Pulihkan Backup Server Terbaru</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
