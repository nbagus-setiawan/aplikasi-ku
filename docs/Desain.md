# desain.md — Panduan Desain SAP | SEKRETARIS AL-FALAH PUTRA

Panduan UI/UX untuk **seluruh halaman** (publik, login, admin). Pakai bersama `AGENTS.md` dan `docs/PRD.md`.
Stack tampilan: Blade + Tailwind CSS. Jangan memakai framework SPA.

> **Aturan emas:** bila ragu antara dua jarak, pilih yang **lebih besar**. Halaman yang lega selalu terlihat lebih rapi daripada halaman yang padat.

---

## 1. Prinsip

- **Lega, bersih, elegan.** Ruang kosong adalah bagian dari desain. Satu aksi utama per layar.
- **Mudah untuk non-teknis.** Label jelas berbahasa Indonesia, tombol ikon + teks, tanpa istilah teknis.
- **Konsisten.** Warna, sudut, jarak, dan ikon sama di semua halaman. Jangan membuat nilai jarak baru di luar skala (bagian 4).
- **Responsif dulu.** Rancang untuk HP, lalu lebarkan ke laptop.
- **Isi bukan karangan.** Data contoh memakai tanda kurung siku, misalnya `[Judul pengumuman]`. Jangan mengarang angka, nama, alamat, atau kontak. Semua data identitas diambil dari Pengaturan (database), bukan ditulis langsung di Blade.
- **Satu sumber gaya.** Semua warna dan font berasal dari `tailwind.config.js`. Dilarang memakai warna bawaan Tailwind (`emerald-*`, `teal-*`, `amber-*`, `gray-*`) dan dilarang memakai nilai bebas seperti `p-[13px]`.

---

## 2. Warna

Tema pesantren: hijau tua dan emas. Daftarkan di `tailwind.config.js` (`theme.extend.colors`).

| Token | Hex | Pemakaian |
|---|---|---|
| `hijau-900` | `#0f3d21` | Footer, latar banner paling gelap |
| `hijau-800` | `#14532d` | Warna utama: tombol, menu samping, banner, judul aksen |
| `hijau-700` | `#1e6b3a` | Hover menu samping dan tombol |
| `hijau-300` | `#7fb08e` | Garis tepi tombol di atas hijau, placeholder foto |
| `hijau-100` | `#e6f0e8` | Latar ikon, label, tanggal |
| `hijau-50` | `#f5f8f4` | Latar halaman dan kepala tabel |
| `emas-400` | `#f2c14e` | Aksen: tombol utama di atas hijau, menu aktif, logo |
| `emas-100` | `#fdf1cc` | Latar label "Penting" / "Baru" |
| `emas-800` | `#7a5200` | Teks pada label emas |
| `tinta` | `#14231a` | Teks utama |
| `tinta-lembut` | `#4b6355` | Teks sekunder, keterangan |
| `garis` | `#dbe5dd` | Garis tepi kartu dan pemisah |
| `bahaya` | `#9b1c1c` | Galat, tombol hapus |
| `bahaya-100` | `#fdeaea` | Latar pesan galat |
| `putih` | `#ffffff` | Kartu, input |

Aturan:
- Teks biasa minimal kontras 4,5:1.
- Emas hanya untuk latar dengan teks gelap, jangan untuk teks di atas putih.
- Status tidak boleh dibedakan dengan warna saja; sertakan teks (Baru, Diproses, Final).
- Pergantian latar antar seksi bergantian **putih → hijau-50 → putih** agar batas seksi terlihat tanpa garis tambahan.

---

## 3. Tipografi

- Font: **Plus Jakarta Sans** (bobot 400, 600, 700, 800), cadangan `sans-serif`. Daftarkan sebagai `font-sans` di `tailwind.config.js` dan muat lewat `<link>` Google Fonts di kedua layout.
- Isi bacaan dibatasi lebar **maks. 68 karakter** (`max-w-prose`) agar nyaman dibaca.

| Elemen | Ukuran | Bobot | Tinggi baris | Jarak bawah |
|---|---|---|---|---|
| Hero (beranda) | 44 px (HP 32 px) | 800 | 1,2 | 24 px |
| Judul halaman | 28 px | 800 | 1,25 | 8 px (sebelum deskripsi) |
| Judul seksi | 28 px (HP 24 px) | 800 | 1,25 | 12 px |
| Deskripsi seksi | 16–17 px | 400 | 1,7 | — |
| Judul kartu | 18 px | 700 | 1,35 | 8–12 px |
| Isi | 16 px | 400 | 1,7 | 16 px antar paragraf |
| Keterangan | 13–14 px | 400–600 | 1,5 | — |
| Label formulir | 14 px | 700 | 1,4 | 8 px |
| Angka statistik | 36 px | 800 | 1,1 | 8 px |

Aturan: jangan memakai teks lebih kecil dari 13 px. Judul tidak boleh lebih dari 2 baris di kartu (`line-clamp-2`).

---

## 4. Jarak (inti dari kerapian)

### 4.1 Skala jarak
Hanya pakai nilai ini: **4, 8, 12, 16, 20, 24, 32, 40, 48, 64, 80, 96 px**.

| Token | px | Kelas Tailwind | Dipakai untuk |
|---|---|---|---|
| xs | 8 | `2` | Ikon ke teks, label ke input |
| sm | 12 | `3` | Judul ke keterangan di dalam kartu |
| md | 16 | `4` | Antar elemen kecil, antar paragraf |
| lg | 24 | `6` | **Padding kartu standar**, jarak antar kartu |
| xl | 32 | `8` | Padding kartu besar, jarak antar blok |
| 2xl | 48 | `12` | Judul seksi ke isi seksi |
| 3xl | 64 | `16` | Padding seksi (HP) |
| 4xl | 96 | `24` | Padding seksi (komputer) |

### 4.2 Kontainer halaman
- Lebar konten maksimum **1200 px**, di tengah (`max-w-[1200px] mx-auto`). Untuk halaman bacaan (detail pengumuman, surat) maksimum **760 px**.
- Padding samping kontainer: **20 px (HP) → 32 px (`sm`) → 48 px (`lg`)**.
  Kelas: `px-5 sm:px-8 lg:px-12`.
- Konten **tidak boleh menempel** ke tepi layar di ukuran apa pun.

### 4.3 Ritme seksi (halaman publik)
| Bagian | HP | Komputer |
|---|---|---|
| Padding atas–bawah seksi biasa | 64 px (`py-16`) | 96 px (`lg:py-24`) |
| Padding atas–bawah banner/hero | 72 px (`py-[72px]`, pengecualian resmi) | 112 px (`lg:py-28`) |
| Judul seksi → isi seksi | 40 px (`mb-10`) | 56 px (`lg:mb-14`) |
| Label kecil → judul → deskripsi | 12 px (`space-y-3`) | 16 px (`lg:space-y-4`) |
| Jarak antar kartu dalam grid | 20 px (`gap-5`) | 24–32 px (`lg:gap-8`) |
| Tombol aksi di bawah teks | 32 px di atas tombol | 40 px |

Aturan: dua seksi yang bersebelahan **tidak boleh** memakai warna latar yang sama.

### 4.4 Kartu
- Padding dalam: **24 px** (kartu biasa), **32 px** (kartu profil/visi/misi), **20 px** hanya untuk kartu ringkas di HP.
- Jarak elemen di dalam kartu: ikon → judul 20 px; judul → isi 12 px; isi → tautan/aksi 24 px.
- Pemisah dalam kartu (garis atas footer kartu): jarak 20 px di atas garis, 16 px di bawah garis.
- Gambar/kepala kartu pengumuman: tinggi 160 px dengan padding 24 px; jangan kurang.

### 4.5 Formulir
- Jarak antar kolom/field: **24 px** (`gap-6`). Jarak antar grup field: **32 px**.
- Label → input: 8 px. Input → pesan galat/bantuan: 8 px.
- Padding input: **12 px atas–bawah, 16 px kiri–kanan**, tinggi minimum 48 px.
- Padding kartu formulir: 32 px (HP 20 px). Tombol Simpan dipisah dari field terakhir dengan 32 px dan garis pemisah.
- Jarak antar tombol (Simpan, Batal): 12 px.

### 4.6 Admin
- Padding area konten: **32 px (komputer), 20 px (HP)**.
- Header halaman (judul + tombol aksi utama): jarak bawah **32 px**.
- Jarak antar kartu/blok: **24 px**.
- Kartu statistik: padding 24 px, jarak antar kartu 24 px.
- Menu samping: padding 20 px; tinggi tiap item **44 px**, jarak antar item **4 px**, padding item 12 × 16 px. Grup menu dipisah 24 px.
- Tabel: padding sel **16 px vertikal × 20 px horizontal**, tinggi baris minimum 56 px, kepala tabel 48 px.
- Bilah pencarian + filter di atas tabel: jarak bawah 20 px, antar kontrol 12 px.
- Pagination: jarak atas 24 px.

### 4.7 Sudut dan bayangan
- Sudut: kartu 16 px, tombol dan input 12 px, banner 24 px, label bulat penuh.
- Kartu: latar putih, garis `garis` 1 px, tanpa bayangan; bayangan lembut hanya saat hover (`0 12px 28px rgba(20,83,45,.12)`).

---

## 5. Ikon

- Gaya garis (stroke), 20 px (24 px di dalam kotak ikon), tebal garis 1,8, ujung bulat, warna ikut teks (`currentColor`).
- Gunakan set **Lucide**, tanpa emoji dan tanpa karakter hias (jangan pakai ✦, ★, dan sejenisnya sebagai dekorasi).
- Kotak ikon: 48 × 48 px, sudut 12 px, latar `hijau-100`, ikon `hijau-800`.
- Ikon dekoratif wajib `aria-hidden="true"`; tombol hanya-ikon wajib `aria-label`.
- Jarak ikon ke teks di tombol/menu: 8–12 px.

| Fungsi | Ikon |
|---|---|
| Dashboard | home |
| Santri, Pengurus | users |
| Surat Masuk | mail |
| Surat Keluar | send |
| Agenda | calendar |
| Arsip | archive |
| Beranda & Pengumuman | megaphone |
| Laporan | file-text |
| Pengaturan | settings |
| Verifikasi surat | shield-check |
| Kontak | phone |
| Lokasi | map-pin |
| Tanda tangan | pen |
| Tautan tanda tangan | link |
| Unduh PDF | file-down |
| Kembali | arrow-left |
| Lanjut | arrow-right |

---

## 6. Komponen

**Tombol** — tinggi min. 48 px, padding 12 × 24 px, ikon di kiri (jarak 8 px), teks tebal 700.
- Utama (hijau): latar `hijau-800`, teks putih. Di atas banner hijau: latar `emas-400`, teks `tinta`.
- Sekunder (garis): latar transparan, garis 2 px `hijau-800`.
- Bahaya: latar `bahaya`, teks putih, hanya di dialog konfirmasi.
- Hover: naik 2 px dan bayangan lembut. Fokus: cincin emas 3 px.
- Dua tombol berdampingan dipisah 12–16 px; di HP boleh menumpuk penuh lebar dengan jarak 12 px.

**Kartu** — putih, garis, sudut 16 px, padding sesuai bagian 4.4. Kartu yang bisa diklik naik 3–4 px saat hover.

**Label (badge)** — bulat, 12 px / 700, padding 4 × 12 px. Penting/Baru = emas; Kegiatan/Umum/Diproses = hijau muda; Draft = emas; Final = hijau tua.

**Input dan formulir**
- Label di atas input (`<label for>`), 14 px / 700. Jangan pakai placeholder sebagai pengganti label.
- Input: garis 1,5 px `#c9d8cd`, sudut 12 px, ukuran sesuai bagian 4.5.
- Fokus: garis `hijau-800` dan cincin hijau 3 px (transparan 15%).
- Galat: teks `bahaya` di bawah input dengan ikon peringatan; pesan berbahasa Indonesia yang menyebut perbaikannya.
- Formulir dua kolom di komputer, satu kolom di HP.

**Menu samping (admin)** — lebar 264 px, latar `hijau-800`, ikon + teks. Item aktif berlatar `emas-400` teks `tinta`. Di HP berubah menjadi baris menu yang bisa digeser mendatar di atas halaman (jarak antar item 8 px, padding baris 12 × 20 px).

**Kartu statistik** — ikon dalam kotak `hijau-100`, angka besar, keterangan kecil. Jarak ikon → angka 16 px.

**Daftar baris** — pemisah garis tipis, padding baris 16 × 0 px, judul tebal, keterangan kecil 4 px di bawahnya, label status di kanan.

**Tabel data (admin)** — kepala tabel `hijau-50`, baris dengan garis pemisah, hover baris `hijau-50`; di HP berubah menjadi daftar kartu (jarak antar kartu 12 px). Selalu ada pencarian, filter, dan pagination.

**Kotak tanda tangan** — kanvas bergaris putus-putus `hijau-300`, tinggi 180 px, petunjuk kecil di dalamnya, tombol Ulangi, dan tombol Tautan 30 menit. Jarak kanvas ke tombol 16 px.

**Notifikasi** — toast di kanan atas (jarak 24 px dari tepi), hilang otomatis 4 detik; sukses hijau, galat merah tua, selalu disertai teks dan ikon.

**Dialog konfirmasi** — wajib untuk hapus dan finalisasi surat; padding 32 px; lebar maks. 480 px; tombol berbahaya jelas berlabel ("Ya, hapus").

**Keadaan kosong** — ikon besar, satu kalimat penjelasan, dan satu tombol aksi ("Tambah santri pertama"). Padding atas–bawah 64 px, jarak antar elemen 16 px.

**Pemuatan** — kerangka (skeleton) abu muda untuk daftar, spinner kecil pada tombol saat menyimpan.

---

## 7. Animasi

Halus dan singkat; tidak menghalangi kerja.

| Elemen | Efek | Durasi |
|---|---|---|
| Bagian halaman saat dibuka | Muncul naik 16 px + memudar masuk, berurutan jeda 0,1 detik | 0,6–0,7 dtk |
| Kartu dan tombol | Naik saat hover | 0,2–0,25 dtk |
| Input | Transisi warna garis dan cincin fokus | 0,2 dtk |
| Garis tanda tangan contoh | Tergambar perlahan | 2,4 dtk |
| Toast dan dialog | Masuk dengan memudar dan geser 8 px | 0,2 dtk |

- Wajib: hormati `prefers-reduced-motion: reduce` (matikan semua animasi dan transisi). Animasi hanya memakai `transform` dan `opacity`.
- **Dilarang** animasi berulang tanpa henti pada label atau teks (misalnya `animate-pulse` pada badge sambutan). Animasi berulang hanya untuk indikator pemuatan.

---

## 8. Responsif

Pendekatan mobile-first. Titik patah Tailwind: `sm 640`, `md 768`, `lg 1024`.

| Elemen | Komputer | HP (di bawah 768 px) |
|---|---|---|
| Menu atas publik | Tautan + tombol Login | Logo + tombol menu (buka panel menu), tombol Login masuk panel |
| Tinggi menu atas | 80 px | 64 px |
| Banner beranda | Teks kiri, foto kanan | Menumpuk, foto di bawah teks (jarak 40 px) |
| Kotak layanan | 4 kolom | 2 kolom |
| Kartu pengumuman | 3 kolom | 1 kolom |
| Menu samping admin | Tetap di kiri | Baris menu di atas, bisa digeser |
| Kartu statistik | 4 kolom | 2 kolom |
| Formulir surat | 2 kolom + panel kanan | 1 kolom |
| Tabel | Tabel penuh | Daftar kartu |
| Padding seksi | 96 px | 64 px |
| Padding samping | 48 px | 20 px |

- Target sentuh minimal 44 × 44 px; jarak antar target sentuh minimal 8 px.
- Jangan ada gulir mendatar pada badan halaman.
- Kartu statistik 2 kolom di HP: angka diperkecil ke 28 px dan padding kartu 20 px agar tidak sesak.

---

## 9. Daftar halaman

**Publik (tanpa login)**
1. **Beranda** `/` — menu atas lengket, banner nama SAP + sambutan + dua tombol, empat kotak layanan (Pengumuman, Agenda, Verifikasi Surat, Kontak), tiga pengumuman terbaru, daftar agenda, footer alamat dan kontak. Seluruh isi dan tata letak diatur sekretaris lewat editor visual.
2. **Daftar pengumuman** `/pengumuman` — pencarian, filter kategori, kartu 3 kolom, pengumuman tersemat di atas, pagination.
3. **Detail pengumuman** `/pengumuman/{slug}` — judul, tanggal, label kategori, gambar, isi, tombol Kembali. Lebar baca 760 px.
4. **Verifikasi surat** `/verifikasi/{kode}` — kartu hasil: ikon centang hijau + "Surat asli", nomor surat, tanggal, perihal, penandatangan, waktu tanda tangan; bila tidak ditemukan tampilkan kartu "Surat tidak ditemukan". Halaman `/verifikasi/cek` berisi formulir memasukkan kode.
5. **Tanda tangan jarak jauh** `/tanda-tangan/{token}` — ringkasan surat, kotak tanda tangan layar penuh di HP, tombol Kirim, hitung mundur 30 menit; halaman "Tautan kedaluwarsa" bila lewat waktu.
6. **Login** `/login` — kartu di tengah (lebar maks. 440 px, padding 32 px), email, kata sandi, tombol Masuk.

**Admin (Sekretaris, `/admin`)**
7. **Dashboard** — salam, tombol cepat (+ Surat Keluar, + Santri, Ubah Beranda), empat statistik, surat masuk belum didisposisi, agenda terdekat.
8. **Santri** — daftar (pencarian, filter, impor Excel, unduh template), formulir tambah/ubah, detail dengan foto.
9. **Pengurus** — daftar dan formulir.
10. **Surat Masuk** — daftar, formulir, detail + disposisi dan status.
11. **Surat Keluar** — daftar, formulir, **panel nomor dan tanda tangan** (pilihan Digital/Basah, kotak tanda tangan, tautan 30 menit, finalisasi dan unduh PDF), halaman pratinjau surat ber-kop.
12. **Agenda** — daftar dan kalender bulanan.
13. **Arsip** — unggah, cari, unduh.
14. **Beranda & Pengumuman** — **editor visual**: panel blok di kiri (banner, teks, gambar, galeri, pengumuman terbaru, agenda, tombol, kontak, pembatas), kanvas pratinjau di tengah (seret dan lepas), panel properti di kanan (warna, kolom, ukuran); bilah atas dengan Simpan draft, Terbitkan, Riwayat versi, dan pengalih pratinjau Komputer/HP.
15. **Laporan** — pilih jenis dan periode, pratinjau, unduh PDF.
16. **Pengaturan** — nama situs, logo, warna tema, kontak, kop surat, pola nomor surat, jenis surat, profil dan kata sandi.

---

## 10. Pola UX

- **Satu aksi utama** per halaman, ditaruh di kanan atas.
- **Pencarian di atas daftar**, hasil langsung berubah; filter dalam satu baris.
- **Simpan jelas:** tombol Simpan terlihat, ada pesan sukses, dan peringatan bila meninggalkan halaman dengan perubahan belum disimpan.
- **Surat final terkunci:** tampilkan ikon gembok dan label Final; hanya tersedia Unduh dan Buat Revisi.
- **Nomor surat** tampil otomatis dan tidak bisa diedit; tampilkan pratinjaunya sebelum finalisasi.
- **Impor Excel:** langkah 1 unduh template, langkah 2 unggah, langkah 3 pratinjau, langkah 4 hasil dengan daftar baris yang gagal.
- **Editor beranda:** ada Draft/Terbit yang jelas, riwayat versi, dan pratinjau HP sebelum terbit.
- Gunakan bahasa sapaan sopan dan singkat (contoh: "Assalamu'alaikum, Sekretaris").
- Teks panjang di kartu dipotong rapi (`line-clamp`), bukan dipotong dengan "..." yang ditulis manual di data.

---

## 11. Aksesibilitas

- Tag semantik: `<nav>`, `<main>`, `<button>`, `<a href>`, `<label for>`; jangan membuat tombol dari `div`.
- Semua fungsi bisa dipakai dengan keyboard; urutan Tab logis; cincin fokus emas terlihat jelas.
- Gambar bermakna punya `alt`; ikon dekoratif `aria-hidden`.
- Kontras teks 4,5:1; jangan andalkan warna saja untuk status.
- Tanda tangan digital wajib punya opsi basah sebagai alternatif.

---

## 12. Catatan penerapan Laravel

- Buat layout Blade: `layouts/publik.blade.php` (menu atas + footer) dan `layouts/admin.blade.php` (menu samping + header).
- Pecah komponen Blade: `x-tombol`, `x-kartu`, `x-label`, `x-input`, `x-ikon`, `x-statistik`, `x-toast`, `x-kosong`, `x-seksi` (pembungkus seksi dengan padding standar bagian 4.3), `x-judul-seksi` (label + judul + deskripsi dengan jarak standar).
- Daftar warna dan font di `tailwind.config.js`; font dimuat lewat `<link>` Google Fonts.
- Tambahkan pembungkus kontainer tunggal, misalnya kelas `.kontainer` = `max-w-[1200px] mx-auto px-5 sm:px-8 lg:px-12`, dan pakai di **semua** seksi agar tepi kiri-kanan selalu sejajar.
- Ikon: sprite SVG atau paket Blade Icons (Lucide).
- Animasi muncul memakai kelas Tailwind kustom (`@keyframes` di `app.css`); selalu bungkus dengan `motion-reduce:`.
- Editor beranda dan kotak tanda tangan memakai JavaScript ringan (SortableJS atau GrapesJS, dan `signature_pad`); sisanya Blade biasa.
- Setiap halaman diuji di lebar 390 px, 768 px, dan 1280 px sebelum dianggap selesai.

---

## 13. Daftar periksa sebelum halaman dianggap selesai

**Jarak dan tata letak**
- [ ] Semua seksi memakai `x-seksi` / padding 64 px (HP) dan 96 px (komputer).
- [ ] Semua kontainer memakai `.kontainer`; tidak ada konten menempel ke tepi layar.
- [ ] Padding kartu minimal 24 px; jarak antar kartu minimal 20 px.
- [ ] Jarak judul seksi ke isi minimal 40 px.
- [ ] Hanya nilai jarak dari skala bagian 4.1 yang dipakai.
- [ ] Teks bacaan tidak melebihi lebar 68 karakter.
- [ ] Tidak ada gulir mendatar di 390 px.

**Gaya**
- [ ] Hanya warna token (`hijau-*`, `emas-*`, `tinta*`, `garis`, `bahaya`); tidak ada `emerald-*`, `teal-*`, `amber-*`, `gray-*`.
- [ ] Font Plus Jakarta Sans termuat.
- [ ] Tidak ada emoji atau karakter hias; semua ikon Lucide.
- [ ] Tidak ada animasi berulang selain indikator pemuatan.

**Isi**
- [ ] Nama, alamat, telepon, email, jam layanan, dan angka statistik berasal dari Pengaturan/database, bukan ditulis langsung di Blade.
- [ ] Alamat dan kontak sama persis di menu, bagian kontak, dan footer.
- [ ] Semua tautan tombol mengarah ke halaman yang benar (contoh (hanya contoh kamu tidak perlu membuat contoh): "Verifikasi Surat" → `/verifikasi/cek`, bukan `/verifikasi/contoh`).
- [ ] Footer memakai `hijau-900`, bukan abu-abu.

## ini bukan untuk simulasi, contoh atau basa basi