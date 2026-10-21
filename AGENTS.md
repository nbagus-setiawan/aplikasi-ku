# AGENTS.md — SAP | SEKRETARIS AL-FALAH PUTRA

Aplikasi web administrasi untuk sekretaris Pondok Pesantren Al Falah Putra.
Sumber kebutuhan lengkap: `docs/PRD.md`. Baca file itu sebelum mengerjakan apa pun.

## Cara kerja (wajib)

1. Sebelum menulis kode, buat rencana singkat dan tunggu persetujuan.
2. Kerjakan **satu fase per permintaan**. Jangan mengerjakan fase berikutnya sebelum diminta.
3. Setelah selesai, jalankan test dan `php artisan migrate:fresh --seed`, lalu laporkan hasilnya.
4. Jika PRD tidak jelas atau bertentangan dengan permintaan, **tanya dulu**, jangan menebak.
5. Jangan menambah fitur di luar PRD. Jangan mengganti stack tanpa izin.

## Stack

- Laravel 13, PHP 8.3+, pola MVC.
- Database MySQL.
- Frontend: Blade + Tailwind CSS (tanpa framework SPA).
- Autentikasi: Laravel Breeze (pendaftaran publik dimatikan).
- Paket: `barryvdh/laravel-dompdf` (PDF), `maatwebsite/excel` (impor Excel), `simplesoftwareio/simple-qrcode` (QR), `signature_pad` (kanvas tanda tangan), GrapesJS atau SortableJS (editor beranda).
- Gunakan hanya paket gratis dan open source.

## Aturan bisnis penting

- **Satu peran saja: Sekretaris.** Tidak ada kolom `role`, tidak ada `RoleMiddleware`. Semua halaman admin di bawah `/admin` dengan middleware `auth`.
- **Halaman publik** (tanpa login): beranda `/`, pengumuman `/pengumuman`, verifikasi surat `/verifikasi/{kode}`, tanda tangan jarak jauh `/tanda-tangan/{token}`.
- **Nomor surat keluar:** format `001/SPm.01/AF-PA/X/2026`
  = urut / kode jenis surat / kode lembaga / bulan romawi / tahun.
  - Urutan reset tiap tahun, dibuat otomatis oleh `NomorSuratService`, tidak boleh ganda (pakai transaksi dan kunci database).
  - `SPm` = Surat Permohonan; angka di belakangnya menunjukkan urutan jenis (SPm.01, SPm.02, dst).
  - Jenis surat dikelola manual oleh sekretaris dari halaman Pengaturan (CRUD `kategori_surats`).
  - Kode lembaga `AF-PA` dan pola nomor disimpan di tabel `pengaturans`, bukan ditulis tetap di kode.
- **Penandatangan:** nama dan jabatan diketik manual saat membuat surat. Tidak ada daftar tetap.
- **Tanda tangan, dua opsi:**
  1. Digital langsung di website: digambar di kanvas (jari atau mouse), **bukan unggah gambar**. Hasil ditempel ke PDF beserta QR verifikasi. Bisa di perangkat sekretaris, atau lewat tautan sekali pakai berlaku **30 menit**. Tanda tangan tidak disimpan untuk dipakai ulang. Catat waktu, IP, dan nomor surat.
  2. Basah: PDF dicetak dengan ruang tanda tangan kosong; hasil pindaian dapat diunggah sebagai lampiran final.
  - Surat berstatus final tidak boleh diubah isinya; perubahan membuat revisi baru.
- **Impor santri:** manual lewat formulir atau impor Excel. Belum ada file Excel, jadi sediakan template unduhan dengan kolom: NIS, nama lengkap, tempat lahir, tanggal lahir, alamat, nama wali, no. HP wali, kamar, kelas, tahun masuk, status. Tampilkan pratinjau dan daftar baris yang gagal.
- **Beranda dan pengumuman:** sekretaris mengubah isi dan tata letak dari website tanpa kode (seret dan lepas), ada draft/terbit dan riwayat revisi. Konten disimpan sebagai JSON blok, dirender dengan Blade. **Semua HTML wajib disanitasi**, tidak ada input kode bebas.
- Nama situs, logo, warna tema, kontak, kop surat, dan pola nomor surat diatur dari halaman Pengaturan.

## Konvensi kode

- Struktur folder mengikuti PRD bagian 9. Satu controller per modul, validasi lewat Form Request, logika berat di `app/Services`.
- Nama tabel jamak bahasa Inggris Laravel (`santris`, `surat_masuks`); nama kolom bahasa Indonesia, snake_case.
- Antarmuka dan pesan galat **berbahasa Indonesia**. Tanggal tampil `dd-mm-yyyy`.
- Gunakan eager loading dan pagination di semua daftar. Hindari query di dalam loop.
- Hapus data penting dengan soft delete. Catat `user_id` pembuat pada surat dan arsip.
- File unggahan: maksimal 5 MB; tipe PDF, gambar, Word. Simpan di `storage/app/public` (foto-santri, surat-masuk, arsip).
- Jangan menaruh rahasia di kode. Semua konfigurasi lewat `.env`.
- Tampilan harus responsif (HP dan laptop).

## Keamanan

- Pendaftaran publik dimatikan; akun sekretaris dibuat lewat seeder.
- CSRF aktif di semua form. Tautan tanda tangan sekali pakai: token acak, kedaluwarsa 30 menit, tidak bisa dipakai ulang.
- Sanitasi konten editor beranda. Validasi tipe dan ukuran file unggahan.

## Pengujian

- Tulis feature test untuk CRUD tiap modul dan test akses (halaman admin menolak tamu).
- Unit test wajib untuk `NomorSuratService`: urutan, reset tahun, bulan romawi, tidak ada nomor ganda.
- Test impor Excel: baris valid, baris gagal, template.
- Fase dianggap selesai hanya jika seluruh test lulus.

## Urutan fase dan kriteria selesai

**Fase 1 — Setup dan dasar**
Instal Laravel 13 dan Breeze (tanpa registrasi), seeder akun sekretaris, layout (sidebar, navbar), dashboard dasar, halaman Pengaturan (nama situs, kode lembaga, pola nomor, jenis surat).
Selesai jika: login berfungsi, tamu ditolak di `/admin`, pengaturan tersimpan.

**Fase 2 — Santri dan Pengurus**
CRUD santri (pencarian, filter, foto, soft delete), impor Excel dengan template, CRUD pengurus, cetak daftar santri (PDF).
Selesai jika: impor menampilkan baris gagal dan test lulus.

**Fase 3 — Surat masuk dan keluar**
Surat masuk (nomor agenda otomatis, disposisi, status), surat keluar (nomor otomatis, cetak PDF ber-kop), tanda tangan digital dan basah, QR verifikasi, tautan sekali pakai.
Selesai jika: nomor tidak pernah ganda, surat final terkunci, QR membuka halaman verifikasi.

**Fase 4 — Agenda dan Arsip**
CRUD agenda (daftar dan kalender bulanan), arsip dokumen (unggah, cari, unduh), tautan surat ke arsip.
Selesai jika: pencarian arsip dan peringatan agenda H-1 di dashboard berfungsi.

**Fase 5 — Beranda dan Pengumuman**
Editor visual beranda, blok konten, draft/terbit, riwayat revisi, CRUD pengumuman, halaman publik.
Selesai jika: sekretaris bisa mengubah beranda tanpa kode dan HTML tersanitasi.

**Fase 6 — Laporan dan penyelesaian**
Rekap surat, agenda, santri per periode (PDF), perapian antarmuka, uji menyeluruh, dokumentasi instalasi di `README.md`.
Selesai jika: seluruh test lulus dan `migrate:fresh --seed` berjalan bersih.

## Larangan

- Jangan membuat peran, fitur keuangan/SPP, absensi, portal wali, atau integrasi WhatsApp.
- Jangan memakai layanan berbayar atau API pihak ketiga berbayar.
- Jangan mengubah format nomor surat tanpa izin.
