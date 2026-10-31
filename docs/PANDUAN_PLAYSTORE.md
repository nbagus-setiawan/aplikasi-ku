# Panduan Rilis Aplikasi SAP Al-Falah Putra ke Google Play Store

Dokumen ini menjelaskan cara menguji file APK langsung di HP Android, membuat tanda tangan digital (*Release Keystore*), dan mengunggah aplikasi ke **Google Play Console**.

---

## 1. Lokasi File Hasil Build Saat Ini

Setelah proses build, file installer berada di folder `dist-apk/`:
* **`dist-apk/SAP-Al-Falah-Putra.apk`** (Ukuran ~6.0 MB): File APK untuk pengujian langsung (*sideload*) di perangkat HP Android tanpa melalui Play Store.
* **`dist-apk/SAP-Al-Falah-Putra.aab`** (Ukuran ~5.5 MB): File Android App Bundle yang siap untuk diunggah ke Google Play Console.

### Cara Mencoba APK di HP Android:
1. Hubungkan HP Android ke laptop via kabel USB atau kirim file `SAP-Al-Falah-Putra.apk` ke HP (lewat WhatsApp Web, Google Drive, atau Telegram).
2. Di HP Android, buka file APK tersebut dan pilih **Install**.
3. Jika muncul peringatan *"Install from unknown source"* (Sumber tidak dikenal), aktifkan izin instalasi untuk file manager/browser tersebut.
4. Buka aplikasi **SAP Al-Falah Putra**. Aplikasi akan berjalan fullscreen dengan logo resmi pesantren dan terhubung langsung ke sistem.

---

## 2. Cara Mem-build Ulang APK & AAB di Masa Depan

Jika ada perubahan tampilan atau konfigurasi, Anda cukup menjalankan satu perintah di terminal:

```bash
./build-apk.sh
```

Skrip ini akan secara otomatis menyinkronkan aset web dan menghasilkan file APK & AAB terbaru di folder `dist-apk/`.

---

## 3. Persiapan Upload ke Google Play Store

### A. Syarat Akun Google Play Console
1. Memiliki akun Google dan mendaftar di [Google Play Console](https://play.google.com/console/signup).
2. Membayar biaya pendaftaran resmi Google sebesar **\$25 USD** (sekali bayar seumur hidup).
3. Melengkapi verifikasi identitas (KTP/Paspor pengelola pesantren).

### B. Membuat Release Keystore (Kunci Tanda Tangan Digital)
Google mewajibkan aplikasi rilis ditandatangani dengan kunci digital (*Release Keystore*). Jalankan perintah berikut di terminal:

```bash
keytool -genkey -v -keystore alfalah-release-key.jks -keyalg RSA -keysize 2048 -validity 10000 -alias alfalah-key
```

* Simpan file `alfalah-release-key.jks` dan kata sandi yang Anda masukkan dengan sangat aman (jangan sampai hilang, karena diperlukan setiap kali merilis pembaruan versi).

### C. Menyiapkan Aset Visual Toko Aplikasi (Store Listing)
Google Play Console meminta aset grafis berikut saat mendaftarkan aplikasi:
1. **Ikon Aplikasi:** PNG resolusi 512 x 512 px, 32-bit (Tersedia di `public/images/icon-512x512.png`).
2. **Gambar Fitur (Feature Graphic):** Banner horizontal resolusi 1024 x 500 px.
3. **Tangkapan Layar (Screenshots):** Minimal 2 screenshot antarmuka aplikasi pada layar HP (rasio 16:9 atau 9:16).
4. **Kebijakan Privasi (Privacy Policy):** Tautan halaman web yang menjelaskan kebijakan privasi data santri (misal: `https://ponpesalfalahputak.my.id/privacy-policy`).

---

## 4. Langkah Pengunggahan di Google Play Console

1. Masuk ke **Google Play Console** & klik tombol **Buat Aplikasi (Create App)**.
2. Masukkan identitas:
   * **Nama Aplikasi:** SAP Al-Falah Putra
   * **Bahasa default:** Bahasa Indonesia
   * **Jenis:** Aplikasi (App)
   * **Gratis/Berbayar:** Gratis
3. Pada menu **Penyiapan Aplikasi (Set up your app)**, lengkapi kuesioner:
   * **Akses Aplikasi:** Berikan keterangan jika ada halaman yang membutuhkan login (Panel Sekretaris).
   * **Iklan:** Pilih "Tidak, aplikasi saya tidak berisi iklan".
   * **Target Usia:** Pilih target audiens (misal 13 tahun ke atas / umum).
   * **Kebijakan Privasi:** Masukkan tautan URL kebijakan privasi.
4. Pada menu **Rilis (Production / Closed Testing)**:
   * Buat rilis baru (*Create new release*).
   * Unggah file **`dist-apk/SAP-Al-Falah-Putra.aab`**.
   * Masukkan Catatan Rilis (Release Notes): *"Rilis perdana aplikasi administrasi dan informasi SAP Al-Falah Putra"*.
   * Tinjau dan simpan.
5. Kirim untuk peninjauan (*Submit for Review*). Proses verifikasi oleh tim Google Play biasanya memakan waktu 1 hingga 3 hari kerja sebelum aplikasi resmi muncul dan bisa diunduh di Google Play Store.
