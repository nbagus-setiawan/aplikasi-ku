# SAP — Sistem Administrasi Pesantren
### Sekretariat Pondok Pesantren Al-Falah Putra

Aplikasi web administrasi resmi berbasis **Laravel 13** dan **Tailwind CSS** yang dirancang khusus untuk memfasilitasi operasional sekretaris Pondok Pesantren Al-Falah Putra. Aplikasi ini mengintegrasikan tata kelola santri, asatidz/pengurus, persuratan otomatis dengan tanda tangan digital dan QR verifikasi, agenda kegiatan peringatan H-1, pengarsipan dokumen, editor visual beranda, warta pengumuman publik, serta rekapitulasi laporan ber-kop resmi.

---

## 🛠️ Stack Teknologi

- **Backend Framework:** Laravel 13 (PHP 8.3+)
- **Database:** MySQL
- **Frontend:** Blade Templating + Tailwind CSS + Alpine.js (Tanpa SPA framework)
- **Autentikasi:** Laravel Breeze (Pendaftaran publik dimatikan; peran tunggal Sekretaris)
- **Cetak Dokumen & PDF:** `barryvdh/laravel-dompdf` (Didukung penyesuaian SVG & kompatibel lingkungan tanpa ekstensi GD)
- **QR Code Engine:** `chillerlan/php-qrcode` (Menghasilkan output SVG murni beresolusi tinggi tanpa dependensi `ext-gd`)
- **Impor Spreadsheet:** `maatwebsite/excel` (Excel & CSV)

---

## 🚀 Fitur Utama

### 1. Panel Admin & Pengaturan Lembaga (`/admin`)
- **Peran Tunggal (Sekretaris):** Semua akses administratif berada di bawah awalan `/admin` dengan middleware `auth`.
- **Pengaturan Dinamis:** Kustomisasi nama instansi, logo pondok, alamat, kontak, baris kop surat resmi, dan pola penomoran surat keluar tanpa perlu menyentuh kode.
- **Kategori & Kode Surat Keluar:** Pengelolaan kode jenis surat (misal: `SPm.01` Surat Permohonan, `SPb.02` Surat Pemberitahuan, dst).

### 2. Modul Santri & Pengurus (`/admin/santri`, `/admin/pengurus`)
- **CRUD Santri Lengkap:** Pencarian NIS/Nama, filter kamar & kelas, upload foto santri, soft delete.
- **Impor Excel & Template Unduhan:** Unduh template baku format Excel, validasi baris, penanganan baris gagal, dan pratinjau data.
- **Cetak Biodata & Rekap Santri:** Cetak biodata individual dan daftar santri ber-kop resmi format PDF.
- **Data Asatidz & Pengurus:** Pengelolaan data pengurus lengkap dengan jabatan, nomor telepon, dan status keaktifan sebagai Penanggung Jawab (PJ) agenda.

### 3. Persuratan Masuk & Keluar (`/admin/surat-masuk`, `/admin/surat-keluar`)
- **Surat Masuk:** Nomor agenda urut otomatis (`001/SM/2026`), pencatatan pengirim, disposisi pimpinan, lampiran berkas fisik, dan alur status (`baru`, `diproses`, `selesai`).
- **Surat Keluar & Penomoran Otomatis:**
  - Format nomor surat baku: `001/SPm.01/AF-PA/X/2026` (Urut / Kode Jenis / Kode Lembaga / Bulan Romawi / Tahun).
  - Penomoran bebas bentrok (*race-condition safe*) menggunakan database locking dan reset otomatis per tahun.
  - **Tanda Tangan Digital Langsung:** Kanvas gambar tanda tangan di layar tanpa upload gambar, ditempel ke PDF bersama QR verifikasi.
  - **Tanda Tangan Jarak Jauh:** Pembuatan tautan sekali pakai (*one-time link*) dengan token acak dan masa kedaluwarsa **30 menit** (`/tanda-tangan/{token}`).
  - **Tanda Tangan Basah:** Opsi cetak ruang tanda tangan kosong untuk tandatangan fisik, kemudian hasil pindai diunggah sebagai berkas final.
  - **Penguncian Surat Final:** Surat berstatus final terkunci permanen untuk mencegah manipulasi isi.
  - **Validasi QR Publik:** Pemindaian QR Code di surat fisik mengarahkan ke halaman verifikasi keaslian surat publik (`/verifikasi/{kode}`).

### 4. Agenda Kegiatan & Arsip Dokumen (`/admin/agenda`, `/admin/arsip`)
- **Agenda Kegiatan Dual-Mode:** Pilihan tampilan antara **Kalender Bulanan** interaktif atau **Daftar Tabel**.
- **Peringatan H-1 Otomatis:** Widget dasbor admin otomatis menampilkan peringatan agenda yang akan berlangsung besok hari (H-1).
- **Pengarsipan Dokumen:** Unggah berkas dokumen (PDF, Word, Gambar) hingga 5 MB, pencarian nomor/nama arsip, filter kategori (`sk`, `mou`, `sertifikat`, `proposal`, `laporan`, dll), serta penautan berkas arsip ke surat masuk atau surat keluar terkait.

### 5. Editor Visual Beranda & Warta Publik (`/admin/halaman/beranda`, `/admin/pengumuman`)
- **Editor Beranda Berbasis Blok Tanpa Kode:** Sekretaris dapat menyusun tata letak blok konten beranda secara visual:
  - *Hero Banner*, *Profil & Sambutan*, *Statistik Santri*, *Program Unggulan*, *Warta Pengumuman*, dan *Kontak Layanan*.
  - Tambah, geser urutan, hapus blok, serta mode Simpan Draf atau Terbitkan.
- **Riwayat Revisi & Restore:** Setiap pembaruan atau publikasi tercatat dalam riwayat revisi dan dapat dipulihkan (*restore*) ke versi terdahulu kapan saja.
- **Sanitasi HTML Anti-XSS:** Semua input blok tersaring ketat melalui `HalamanBlockService` untuk mencegah eksekusi skrip berbahaya.
- **Warta Pengumuman Publik:** Publikasi berita resmi dengan thumbnail, status draf/terbit, tanggal tayang & kedaluwarsa, serta fitur pin (sematkan di atas).
- **Portal Publik Responsif:** Halaman beranda dinamis (`/`), warta pengumuman (`/pengumuman`, `/pengumuman/{slug}`), dan verifikasi surat.

### 6. Rekapitulasi & Laporan PDF (`/admin/laporan`)
- **Pusat Rekapitulasi Terpadu:** Filter periode tanggal atau atribut spesifik untuk 4 pilar data:
  1. Rekapitulasi Surat Masuk
  2. Rekapitulasi Surat Keluar
  3. Rekapitulasi Data Santri
  4. Rekapitulasi Agenda Kegiatan
- **Ekspor PDF Resmi:** Hasil cetak PDF ber-kop surat resmi pondok pesantren, tabel data terstruktur, ringkasan jumlah dokumen, dan kolom pengesahan tanda tangan sekretariat.

---

## 💻 Panduan Instalasi & Menjalankan Proyek

### 1. Kebutuhan Sistem
- PHP >= 8.3
- Composer >= 2.0
- Node.js & NPM
- MySQL Server

### 2. Langkah Instalasi

1. **Clone atau Masuk ke Direktori Proyek:**
   ```bash
   cd "SAP | SEKRETARIS AL-FALAH PUTRA"
   ```

2. **Pasang Dependensi PHP (Composer):**
   ```bash
   composer install
   ```

3. **Pasang Dependensi Frontend & Build Aset:**
   ```bash
   npm install
   npm run build
   ```

4. **Konfigurasi Berkas Lingkungan (`.env`):**
   Salin berkas `.env.example` ke `.env` (jika belum ada) dan sesuaikan konfigurasi database:
   ```env
   APP_NAME="SAP | SEKRETARIS AL-FALAH PUTRA"
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_pesantren
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate Kunci Aplikasi & Symlink Storage:**
   ```bash
   php artisan key:generate
   php artisan storage:link
   ```

6. **Migrasi Database & Jalankan Seeder:**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser pada: `http://localhost:8000`.

---

## 🔐 Akun Default Sekretaris (Seeder)

Setelah menjalankan `migrate:fresh --seed`, gunakan kredensial berikut untuk masuk ke panel sekretaris:

- **URL Login:** `http://localhost:8000/login`
- **Email:** `sekretaris@alfalahputra.test`
- **Password:** `password`

---

## 🧪 Menjalankan Pengujian Otomatis (Test Suite)

Seluruh fitur dilengkapi dengan suite pengujian otomatis (Feature & Unit Tests) yang mencakup otentikasi, CRUD modul, validasi penomoran surat, impor santri, tanda tangan digital, sanitasi editor beranda, serta ekspor PDF laporan:

```bash
php artisan test
```

**Status Pengujian Terakhir:**
```text
Pass: 110 tests, 351 assertions (100% Passed)
```

---

## 📄 Struktur Modul Utama

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── AgendaController.php
│   │   ├── ArsipController.php
│   │   ├── DashboardController.php
│   │   ├── HalamanController.php
│   │   ├── KategoriSuratController.php
│   │   ├── LaporanController.php
│   │   ├── PengaturanController.php
│   │   ├── PengumumanController.php
│   │   ├── PengurusController.php
│   │   ├── PublicController.php
│   │   ├── SantriController.php
│   │   ├── SantriImportController.php
│   │   ├── SuratKeluarController.php
│   │   ├── SuratMasukController.php
│   │   ├── TandaTanganPublikController.php
│   │   └── VerifikasiPublikController.php
│   └── Requests/
├── Models/
│   ├── Agenda.php
│   ├── Arsip.php
│   ├── Halaman.php
│   ├── HalamanRevisi.php
│   ├── KategoriSurat.php
│   ├── NomorSuratCounter.php
│   ├── Pengaturan.php
│   ├── Pengumuman.php
│   ├── Pengurus.php
│   ├── Santri.php
│   ├── SuratKeluar.php
│   ├── SuratMasuk.php
│   ├── TandaTangan.php
│   └── User.php
└── Services/
    ├── HalamanBlockService.php
    ├── NomorSuratService.php
    ├── SantriImportService.php
    └── TandaTanganService.php
```

---

## ⚖️ Lisensi & Hak Cipta

Dikembangkan khusus untuk **Sekretariat Pondok Pesantren Al-Falah Putra**. Hak cipta dilindungi undang-undang.
# aplikasi-ku
