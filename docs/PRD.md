# PRD: SAP | SEKRETARIS AL-FALAH PUTRA

**Versi:** 1.1 | **Tanggal:** 12 Oktober 2026 | **Stack:** Laravel 13 (MVC), PHP 8.3+, MySQL, Blade + Tailwind CSS

## 1. Ringkasan Produk

SAP | SEKRETARIS AL-FALAH PUTRA adalah aplikasi web untuk membantu sekretaris Pondok Pesantren Al Falah Putra mengelola administrasi harian: data santri, data pengurus, surat masuk dan keluar, agenda kegiatan, arsip dokumen, pembuatan laporan, serta halaman beranda dan pengumuman yang isi dan tata letaknya diatur sekretaris langsung dari website tanpa menulis kode. Tujuannya mengganti pencatatan manual (buku besar, Excel, berkas fisik) dengan sistem terpusat yang rapi, mudah dicari, dan bisa dicetak.

## 2. Latar Belakang dan Masalah

- Data santri dan pengurus tersebar di banyak buku dan file, sulit diperbarui dan dicari.
- Penomoran surat dilakukan manual, rawan nomor ganda atau terlewat.
- Surat masuk dan keluar sulit dilacak statusnya (sudah didisposisi atau belum).
- Dokumen penting hanya berbentuk kertas, berisiko hilang atau rusak.
- Pembuatan laporan bulanan atau tahunan memakan waktu lama.

## 3. Tujuan dan Metrik Keberhasilan

| Tujuan | Indikator |
| --- | --- |
| Digitalisasi data induk | 100% data santri aktif dan pengurus masuk sistem |
| Penomoran surat otomatis | 0 nomor surat ganda |
| Pencarian cepat | Dokumen ditemukan kurang dari 30 detik |
| Laporan efisien | Laporan bulanan dibuat kurang dari 5 menit |
| Adopsi | Sekretaris memakai sistem untuk seluruh surat baru dalam 1 bulan |

## 4. Pengguna dan Hak Akses

| Peran | Deskripsi | Akses |
| --- | --- | --- |
| **Sekretaris** | Satu-satunya pengguna yang login | Semua modul admin, editor beranda dan pengumuman, tanda tangan surat, pengaturan situs dan akun |

Sistem hanya memiliki **satu peran: Sekretaris**, jadi `RoleMiddleware` tidak diperlukan dan cukup middleware `auth`. Autentikasi memakai starter kit Laravel (Breeze) dengan pendaftaran publik dinonaktifkan; akun dibuat lewat seeder. Pengunjung umum (santri, wali, masyarakat) tanpa login hanya dapat melihat beranda, pengumuman, dan halaman verifikasi surat.

## 5. Ruang Lingkup

**Termasuk (MVP):** login sekretaris, dashboard, modul Santri (input manual dan impor Excel), Pengurus, Surat Masuk, Surat Keluar (tanda tangan digital dan basah), Agenda, Arsip, Laporan, serta halaman Beranda dan Pengumuman dengan editor visual.

**Tidak termasuk (tahap ini):** keuangan/SPP, absensi harian, aplikasi mobile native, portal wali santri, integrasi WhatsApp, dan peran pengguna selain sekretaris.

## 6. Kebutuhan Fungsional

### 6.1 Dashboard

- Ringkasan jumlah santri aktif, surat masuk bulan ini, surat keluar bulan ini, agenda mendatang.
- Daftar 5 agenda terdekat dan 5 surat terbaru.
- Surat masuk yang belum didisposisi ditandai.

### 6.2 Manajemen Santri

- Tambah, ubah, lihat, hapus (soft delete) data santri.
- Field: NIS, nama lengkap, tempat dan tanggal lahir, alamat, nama wali, no. HP wali, kamar/asrama, kelas, tahun masuk, status (aktif/lulus/keluar/pindah), foto.
- Pencarian (nama, NIS), filter (status, kelas, kamar), pagination.
- Cetak daftar santri dan kartu/biodata santri (PDF).
- Input data dengan dua cara: manual lewat formulir, atau impor massal dari file Excel (.xlsx/.csv) memakai template yang disediakan, dengan pratinjau dan laporan baris yang gagal. Karena file Excel belum ada, sistem menyediakan template standar dengan kolom: NIS, nama lengkap, tempat lahir, tanggal lahir, alamat, nama wali, no. HP wali, kamar, kelas, tahun masuk, status.

### 6.3 Manajemen Pengurus

- CRUD pengurus: nama, jabatan, divisi, no. HP, periode mulai dan selesai, status aktif.
- Tampilan struktur pengurus per periode.

### 6.4 Surat Masuk

- Catat: nomor agenda (otomatis), nomor surat asal, tanggal surat, tanggal diterima, pengirim, perihal, kategori, lampiran (PDF/gambar).
- Disposisi: tujuan disposisi, catatan, status (baru, diproses, selesai).
- Pencarian dan filter berdasarkan tanggal, kategori, status.

### 6.5 Surat Keluar

- Nomor surat otomatis lewat `NomorSuratService` dengan format baku pondok, contoh `001/SPm.01/AF-PA/X/2026` (urut / kode jenis surat / kode lembaga / bulan romawi / tahun), urutan reset tiap tahun, pola dapat diatur di Pengaturan.
- Field: tujuan, perihal, tanggal surat, kategori, isi surat, penandatangan (nama dan jabatan diketik manual saat membuat surat), lampiran.
- Cetak surat ke PDF dengan kop pondok pesantren.
- Status: draft, final, terkirim.

### 6.6 Agenda Kegiatan

- CRUD agenda: judul, deskripsi, tanggal dan jam mulai/selesai, lokasi, penanggung jawab, status.
- Tampilan daftar dan kalender bulanan.
- Peringatan agenda H-1 pada dashboard.

### 6.7 Arsip Dokumen

- Unggah dokumen (PDF, gambar, Word) dengan judul, kategori, tahun, keterangan.
- Pencarian, unduh, dan pratinjau.
- Surat masuk/keluar dapat ditautkan ke arsip.

### 6.8 Laporan

- Rekap surat masuk/keluar per periode, rekap agenda, rekap santri per status/kelas.
- Filter periode (bulan, tahun, rentang tanggal).
- Ekspor dan cetak PDF (Excel opsional).

### 6.9 Akun Sekretaris

- Satu akun sekretaris, dibuat lewat seeder saat instalasi; tidak ada manajemen user lain.
- Sekretaris dapat mengubah profil, email, dan kata sandi, serta reset kata sandi lewat email.

### 6.10 Tanda Tangan Surat (Digital dan Basah)

Setiap surat keluar dapat difinalisasi dengan salah satu dari dua metode yang dipilih sekretaris (kolom `metode_ttd`).

1. **Tanda tangan digital langsung di website:** penandatangan menggambar tanda tangan di kanvas pada layar (dengan jari di HP/tablet atau mouse di komputer), tanpa mengunggah gambar. Sistem menempelkan hasilnya ke PDF surat beserta QR code verifikasi (nomor surat, tanggal, penandatangan, waktu).
2. **Tanda tangan basah:** PDF dicetak dengan ruang tanda tangan kosong, ditandatangani manual, lalu hasil pindaian dapat diunggah sebagai lampiran final.

- Penandatangan menandatangani di perangkat sekretaris yang sedang login, atau lewat tautan tanda tangan sekali pakai (berlaku 30 menit) yang dikirim sekretaris, sehingga Ketua atau Pengasuh tidak perlu akun.
- Setiap tanda tangan digital dibuat atas persetujuan penandatangan dan tercatat (waktu, alamat IP, nomor surat); tanda tangan tidak disimpan untuk dipakai ulang.
- Surat yang sudah final tidak bisa diubah isinya; perubahan membuat revisi baru.
- Ini tanda tangan elektronik internal, bukan tanda tangan tersertifikasi; pihak yang mewajibkan tanda tangan basah dilayani lewat opsi 2.

### 6.11 Beranda dan Pengumuman (Editor Visual)

Halaman publik di alamat utama website tampil seperti landing page. Sekretaris mengubah isi dan tata letaknya langsung dari website, tanpa kode.

- **Blok konten:** banner utama, teks, gambar, galeri, pengumuman terbaru, agenda mendatang, tombol/tautan, kontak dan peta, pembatas.
- **Tata letak:** seret dan lepas untuk mengubah urutan blok, atur jumlah kolom (1–3), warna, dan ukuran; pratinjau tampilan komputer dan HP sebelum terbit.
- **Alur penerbitan:** simpan sebagai draft, terbitkan, dan kembalikan ke versi sebelumnya lewat riwayat revisi.
- **Pengumuman:** judul, isi (editor teks kaya), gambar, kategori, tanggal tayang dan kedaluwarsa, sematkan di atas; tersedia halaman daftar dan halaman detail publik.
- **Pengaturan situs:** nama situs **SAP | SEKRETARIS AL-FALAH PUTRA**, logo, warna tema, kontak, dan kop surat dapat diubah sekretaris.
- **Teknis:** isi halaman disimpan sebagai data blok (JSON) dan dirender dengan Blade; editor memakai GrapesJS atau SortableJS; seluruh HTML disanitasi dan tidak ada input kode bebas.

### 6.12 Halaman Informasi Publik dan SEO (tambahan, disetujui pengguna 31-10-2026)

Tujuan: memperluas kata kunci pencarian Google dan memperbaiki pengindeksan situs. Fitur ini berada di luar cakupan awal dan ditambahkan atas persetujuan pemilik.

- **Halaman publik baru (tanpa login):**
  - `/profil`: sejarah, visi, misi, dan pengasuh pondok.
  - `/program-pendidikan`: program pendidikan pesantren dan madrasah.
  - `/pendaftaran-santri`: informasi penerimaan santri baru, alur pendaftaran, pengumuman pendaftaran terbaru, dan tombol hubungi sekretariat.
  - `/kontak`: alamat, telepon/WhatsApp, email, jam pelayanan, dan peta lokasi.
- **Sumber isi:** isi profil, program, dan kontak diambil dari blok beranda yang sudah ada (diubah lewat editor beranda), sehingga sekretaris tidak perlu modul baru dan tidak ada input kode bebas.
- **SEO teknis:**
  - Tiap halaman punya judul, meta description, canonical, satu H1, dan schema `BreadcrumbList`.
  - `sitemap.xml` dibuat dinamis oleh Laravel (bukan file statis), memuat semua halaman publik dan pengumuman terbit, dengan `lastmod` dari tanggal perubahan sebenarnya.
  - Semua akses `http://` dan `www.` dialihkan permanen (301) ke `https://` tanpa `www`.
  - Meta `keywords` dihapus karena tidak dipakai Google.
- Menu navigasi dan footer publik memuat tautan ke halaman-halaman di atas.

## 7. Kebutuhan Non-Fungsional

- **Keamanan:** hash password, proteksi CSRF, validasi lewat Form Request, otorisasi per role/Policy, file upload dibatasi tipe dan ukuran (maks 5 MB).
- **Kinerja:** halaman daftar kurang dari 2 detik untuk 5.000 data; gunakan pagination dan eager loading.
- **Ketersediaan data:** backup database harian ke penyimpanan terpisah; aplikasi di-hosting online dengan HTTPS.
- **Kegunaan:** antarmuka berbahasa Indonesia, responsif (HP dan laptop), mudah dipakai non-teknis.
- **Jejak audit:** pencatatan siapa membuat/mengubah data penting (`created_by`, `updated_by`).
- **Kompatibilitas:** Chrome, Firefox, Edge versi terbaru.

**Biaya dan hosting murah:**

- **Perangkat lunak gratis semua:** Laravel, Breeze, DomPDF, maatwebsite/excel, signature\_pad, GrapesJS, dan QR code bersifat open source, tanpa biaya lisensi.
- **Hosting termurah:** shared/cloud hosting yang mendukung PHP 8.3+, MySQL, SSL gratis, dan akses SSH/Composer; cukup untuk satu pengguna dan beberapa ribu data. Bandingkan harga beberapa penyedia saat memesan, karena tarif berubah-ubah.
- **Alternatif:** VPS kecil (1 vCPU, 1–2 GB RAM) lebih fleksibel tetapi lebih mahal dan perlu perawatan server; dipilih hanya jika hosting biasa tidak cocok.
- **Biaya rutin:** hosting dan domain per tahun; backup harian ke Google Drive gratis; tanpa biaya layanan tanda tangan karena tanda tangan digambar sendiri di website.

## 8. Rancangan Data (Ringkas)

| Tabel | Kolom utama |
| --- | --- |
| `users` | id, name, email, password, role, is\_active |
| `santris` | id, nis, nama, tempat\_lahir, tanggal\_lahir, alamat, nama\_wali, hp\_wali, kamar, kelas, tahun\_masuk, status, foto, deleted\_at |
| `penguruses` | id, nama, jabatan, divisi, no\_hp, periode\_mulai, periode\_selesai, is\_aktif |
| `kategori_surats` | id, nama, kode (misalnya SPm.01), dikelola manual dari halaman Pengaturan |
| `surat_masuks` | id, no\_agenda, no\_surat, tgl\_surat, tgl\_diterima, pengirim, perihal, kategori\_id, file, disposisi, status\_disposisi, user\_id |
| `surat_keluars` | id, no\_surat, tgl\_surat, tujuan, perihal, kategori\_id, isi, nama\_penandatangan, jabatan\_penandatangan, file, status, user\_id |
| `agendas` | id, judul, deskripsi, mulai, selesai, lokasi, pj\_id, status |
| `arsips` | id, judul, kategori, tahun, file, keterangan, user\_id |

**Relasi:** KategoriSurat 1–N SuratMasuk/SuratKeluar; Pengurus 1–N Agenda (PJ); User 1–N semua catatan yang dibuatnya.

**Tabel tambahan dan perubahan:**

| Tabel | Kolom utama |
| --- | --- |
| `halamans` | id, slug, judul, konten (JSON blok), status (draft/terbit), is\_beranda, published\_at |
| `halaman_revisis` | id, halaman\_id, konten, created\_at |
| `pengumumans` | id, judul, slug, isi, gambar, kategori, tgl\_tayang, tgl\_kedaluwarsa, is\_pinned, status |
| `pengaturans` | key, value (nama situs, logo, warna tema, kontak, pola nomor surat) |
| `tanda_tangans` | id, surat\_keluar\_id, nama\_penandatangan, jabatan\_penandatangan, data\_ttd (hasil gambar di kanvas), token\_sekali\_pakai, waktu\_ttd, ip\_address |
| `surat_keluars` (tambahan) | metode\_ttd (digital/basah), status\_ttd, file\_final, kode\_verifikasi |
| `users` (perubahan) | kolom `role` dihapus karena hanya ada satu peran |

## 9. Arsitektur dan Struktur Proyek

Pola **MVC** Laravel 13:

- **Model** (`app/Models`): Santri, Pengurus, SuratMasuk, SuratKeluar, Agenda, Arsip, KategoriSurat, User.
- **View** (`resources/views`): layouts (app, sidebar, navbar), dashboard, serta folder per modul (santri, pengurus, surat-masuk, surat-keluar, agenda, arsip, laporan).
- **Controller** (`app/Http/Controllers`): Dashboard, Santri, Pengurus, SuratMasuk, SuratKeluar, Agenda, Arsip, Laporan.
- **Request** (`app/Http/Requests`): SantriRequest, SuratMasukRequest, SuratKeluarRequest, AgendaRequest.
- **Service:** `NomorSuratService` untuk penomoran otomatis.
- **Middleware:** `RoleMiddleware` untuk pembatasan akses.
- **Penyimpanan file:** `storage/app/public` (foto-santri, surat-masuk, arsip) dengan `storage:link`.
- **Paket tambahan:** `barryvdh/laravel-dompdf` (PDF), opsional `maatwebsite/excel`.

**Tambahan untuk SAP:**

- **Model baru:** Halaman, HalamanRevisi, Pengumuman, Pengaturan, TandaTangan.
- **Controller baru:** PublicController (beranda, pengumuman publik, verifikasi surat), HalamanController (editor), PengumumanController, PengaturanController, SantriImportController.
- **Service baru:** TandaTanganService (menempel tanda tangan dan QR ke PDF), SantriImportService.
- **View baru:** `resources/views/public/` (beranda, pengumuman, verifikasi), `halaman/` (editor), `pengumuman/`, `pengaturan/`.
- **Paket tambahan:** `maatwebsite/excel` (impor Excel), `simplesoftwareio/simple-qrcode` (QR code), signature\_pad (kanvas tanda tangan), GrapesJS atau SortableJS (editor visual).
- **Middleware:** `RoleMiddleware` dihapus; cukup `auth`.

## 10. Peta Rute Utama

| Rute | Fungsi |
| --- | --- |
| `GET /` | Dashboard |
| `resource santri` | CRUD santri |
| `resource pengurus` | CRUD pengurus |
| `resource surat-masuk` | CRUD surat masuk + disposisi |
| `resource surat-keluar` | CRUD surat keluar |
| `GET surat-keluar/{id}/cetak` | Cetak PDF surat |
| `resource agenda` | CRUD agenda |
| `resource arsip` | CRUD arsip |
| `GET laporan` | Halaman dan cetak laporan |

**Rute tambahan dan perubahan:** dashboard dipindah dari `/` ke `/admin`, dan semua rute modul di atas berada di bawah awalan `/admin` dengan middleware `auth`.

| Rute | Fungsi |
| --- | --- |
| `GET /` | Beranda publik (halaman rancangan sekretaris) |
| `GET /pengumuman`, `/pengumuman/{slug}` | Daftar dan detail pengumuman publik |
| `GET /verifikasi/{kode}` | Cek keaslian surat lewat QR code |
| `resource halaman` | Editor visual beranda dan halaman |
| `resource pengumuman` | CRUD pengumuman |
| `POST santri/impor`, `GET santri/template` | Impor Excel dan unduh template |
| `POST surat-keluar/{id}/ttd` | Finalisasi tanda tangan digital atau basah |
| `GET pengaturan` | Pengaturan situs dan akun |

Rute publik tambahan: `GET/POST /tanda-tangan/{token}` untuk tanda tangan jarak jauh lewat tautan sekali pakai.

Rute publik informasi dan SEO (bagian 6.12): `GET /profil`, `GET /program-pendidikan`, `GET /pendaftaran-santri`, `GET /kontak`, dan `GET /sitemap.xml` (dinamis).

## 11. Alur Pengguna Utama

1. **Surat masuk:** Sekretaris menerima surat, input data dan pindai lampiran, sistem membuat nomor agenda, pimpinan memberi disposisi, sekretaris memperbarui status hingga selesai.
2. **Surat keluar:** Sekretaris memilih kategori, sistem membuat nomor otomatis, isi surat disusun, cetak PDF ber-kop, ditandatangani, status diubah menjadi terkirim, arsip tersimpan.
3. **Laporan:** Pilih jenis dan periode, tinjau tampilan, cetak atau unduh PDF.

**Alur tambahan:** (a) *Mengubah beranda:* sekretaris membuka editor, menambah atau menggeser blok, mengisi konten, melihat pratinjau, lalu menerbitkan. (b) *Tanda tangan:* pada surat keluar, sekretaris memilih metode digital (tanda tangan digambar langsung di website, lalu sistem menempel hasilnya dan QR) atau basah (cetak lalu tanda tangan manual), kemudian status menjadi final.

## 12. Rencana Rilis

| Fase | Cakupan | Estimasi |
| --- | --- | --- |
| 1 | Setup proyek, autentikasi, role, layout, dashboard dasar | 1 minggu |
| 2 | Modul Santri dan Pengurus | 1–2 minggu |
| 3 | Surat Masuk, Surat Keluar, penomoran, cetak PDF | 2 minggu |
| 4 | Agenda dan Arsip | 1 minggu |
| 5 | Laporan, pengujian, perbaikan, pelatihan pengguna | 1–2 minggu |
| 6 | Deploy dan serah terima | 1 minggu |

**Penyesuaian jadwal:** impor Excel masuk fase 2 (+3 hari), tanda tangan digital dan QR masuk fase 3 (+1 minggu), dan fase baru *Beranda dan Pengumuman (editor visual)* memakan 2–3 minggu setelah fase 4. Total estimasi menjadi sekitar 11–13 minggu.

## 13. Pengujian

- Feature test Laravel (PHPUnit/Pest) untuk CRUD tiap modul dan hak akses role.
- Unit test untuk `NomorSuratService` (reset tahunan, urutan, bulan romawi).
- Uji penerimaan pengguna (UAT) bersama sekretaris sebelum rilis.

## 14. Risiko dan Mitigasi

| Risiko | Mitigasi |
| --- | --- |
| Pengguna belum terbiasa sistem digital | Antarmuka sederhana, pelatihan singkat, panduan bergambar |
| Data lama tidak lengkap | Impor bertahap, field opsional di awal |
| Kehilangan data | Backup otomatis, soft delete |
| Akses tidak sah | Role, password kuat, HTTPS |
| Internet tidak stabil | Hosting lokal (server pondok) sebagai alternatif |

**Risiko tambahan:**

- Hosting online menyimpan data santri yang sensitif: wajib HTTPS, backup harian, kata sandi kuat, dan 2FA opsional untuk akun sekretaris.
- Editor visual rawan memuat skrip berbahaya (XSS): blok dibatasi, HTML disanitasi, tanpa input kode bebas.
- Tanda tangan digital dapat disalahgunakan: izin penandatangan, QR verifikasi, dan log penggunaan.

## 15. Keputusan dan Pertanyaan Terbuka

**Sudah diputuskan:**

- Format nomor surat: `001/SPm.01/AF-PA/X/2026` (urut / kode jenis surat / kode lembaga / bulan romawi / tahun); urutan reset tiap tahun dan pola dapat diatur di Pengaturan.
- Kode jenis surat: SPm = Surat Permohonan, angka di belakangnya menunjukkan urutan (SPm.01, SPm.02, dan seterusnya). Jenis surat lain ditambahkan sekretaris secara manual dari website.
- Penandatangan: nama dan jabatan diketik manual saat membuat surat, tanpa daftar tetap.
- Data santri lama: belum ada file Excel, jadi sistem menyediakan template; data bisa diisi manual atau diimpor.
- Hosting: online dan semurah mungkin; domain, penyedia, dan anggaran diputuskan setelah aplikasi selesai.
- Tanda tangan: dua opsi, digital langsung di website (digambar di layar, bukan unggah gambar) dan basah (cetak).
- Tautan tanda tangan jarak jauh: sekali pakai, berlaku 30 menit.
- Peran pengguna: satu saja, Sekretaris.
- Nama website: SAP | SEKRETARIS AL-FALAH PUTRA.
- Halaman publik Profil, Program Pendidikan, Pendaftaran Santri, dan Kontak ditambahkan untuk SEO (31-10-2026, lihat bagian 6.12).

**Masih perlu dijawab:**

- Tidak ada; semua pertanyaan sudah dijawab dan PRD siap dipakai untuk mulai pengembangan.
