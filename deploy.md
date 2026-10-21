# Panduan Lengkap Deployment Produksi
## SAP — Sistem Administrasi Pesantren Al-Falah Putra

Dokumen ini memuat panduan komprehensif langkah demi langkah (*step-by-step*) untuk melakukan *deployment* aplikasi **SAP | SEKRETARIS AL-FALAH PUTRA** ke lingkungan server produksi (*production*).

Tersedia dua skenario panduan utama:
1. **Opsi A: VPS (Virtual Private Server — Ubuntu 22.04 / 24.04 + Nginx + PHP 8.3 + MySQL)** — *Sangat Direkomendasikan.*
2. **Opsi B: Shared Hosting / Cloud Hosting berbasis cPanel** — *Solusi Ekonomis / Terjangkau.*

---

## 📋 Daftar Isi
1. [Prasyarat Server](#1-prasyarat-server)
2. [Opsi A: Deployment di VPS (Ubuntu + Nginx)](#2-opsi-a-deployment-di-vps-ubuntu--nginx)
   - [Langkah 1: Persiapan Server & Paket Pendukung](#langkah-1-persiapan-server--paket-pendukung)
   - [Langkah 2: Pembuatan Database MySQL](#langkah-2-pembuatan-database-mysql)
   - [Langkah 3: Clone Repository Kode](#langkah-3-clone-repository-kode)
   - [Langkah 4: Konfigurasi Hak Akses File (Permissions)](#langkah-4-konfigurasi-hak-akses-file-permissions)
   - [Langkah 5: Konfigurasi Environment Produksi (.env)](#langkah-5-konfigurasi-environment-produksi-env)
   - [Langkah 6: Instalasi Dependensi PHP & Frontend Build](#langkah-6-instalasi-dependensi-php--frontend-build)
   - [Langkah 7: Migrasi Database & Seeding Akun Sekretaris](#langkah-7-migrasi-database--seeding-akun-sekretaris)
   - [Langkah 8: Pembuatan Symlink Storage](#langkah-8-pembuatan-symlink-storage)
   - [Langkah 9: Optimasi Performa Cache Laravel](#langkah-9-optimasi-performa-cache-laravel)
   - [Langkah 10: Konfigurasi Web Server Nginx](#langkah-10-konfigurasi-web-server-nginx)
   - [Langkah 11: Pemasangan SSL / HTTPS (Let's Encrypt / Certbot)](#langkah-11-pemasangan-ssl--https-lets-encrypt--certbot)
   - [Langkah 12: Konfigurasi Cronjob (Task Scheduling)](#langkah-12-konfigurasi-cronjob-task-scheduling)
3. [Opsi B: Deployment di Shared Hosting (cPanel)](#3-opsi-b-deployment-di-shared-hosting-cpanel)
4. [Skrip Otomasi Pembaruan (Deployment Script)](#4-skrip-otomasi-pembaruan-deployment-script)
5. [Prosedur Cadangan Data (Backup Rutin)](#5-prosedur-cadangan-data-backup-rutin)
6. [Troubleshooting & Solusi Kendala Umum](#6-troubleshooting--solusi-kendala-umum)

---

## 1. Prasyarat Server

Pastikan server memenuhi spesifikasi berikut:
- **Sistem Operasi:** Linux (Ubuntu 22.04 LTS / 24.04 LTS atau distro Linux setara)
- **PHP:** Versi **8.3** atau lebih baru
- **Ekstensi PHP Wajib:**
  - `php8.3-cli`, `php8.3-fpm`
  - `php8.3-mysql` (Koneksi database)
  - `php8.3-mbstring`, `php8.3-xml`, `php8.3-curl`
  - `php8.3-bcmath` (Kalkulasi angka/penomoran)
  - `php8.3-zip` (Impor/ekspor dokumen & template)
  - `php8.3-intl` (Format tanggal & lokalisasi Indonesia)
  - `php8.3-fileinfo` (Validasi berkas unggahan PDF/Gambar/Word)
- **Database:** MySQL 8.0+ atau MariaDB 10.5+
- **Composer:** Versi 2.x
- **Node.js & NPM:** Node.js v18+ atau v20+ LTS (untuk kompilasi Tailwind CSS & Vite)
- **Web Server:** Nginx (direkomendasikan) atau Apache 2.4
- **Domain & DNS:** Nama domain yang sudah diarahkan (*A Record*) ke IP publik server (misal: `sekretariat.alfalahputra.sch.id`).

---

## 2. Opsi A: Deployment di VPS (Ubuntu + Nginx)

### Langkah 1: Persiapan Server & Paket Pendukung

Masuk ke server VPS via SSH:
```bash
ssh root@IP_SERVER_ANDA
```

Perbarui daftar paket sistem:
```bash
sudo apt update && sudo apt upgrade -y
```

Pasang repositori PHP PPA (Ondřej Surý) untuk mendapatkan PHP 8.3:
```bash
sudo apt install -y software-properties-common curl git unzip ufw
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
```

Pasang Nginx, PHP 8.3-FPM, ekstensi terkait, dan MySQL Server:
```bash
sudo apt install -y nginx mysql-server \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring \
  php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip \
  php8.3-intl php8.3-fileinfo
```

Pasang Composer:
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Pasang Node.js & NPM (NodeSource v20 LTS):
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

Aktifkan firewall UFW:
```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable
```

---

### Langkah 2: Pembuatan Database MySQL

Jalankan perintah pengamanan MySQL:
```bash
sudo mysql_secure_installation
```

Masuk ke konsol MySQL:
```bash
sudo mysql -u root -p
```

Jalankan query berikut untuk membuat basis data dan akun pengguna khusus aplikasi:
```sql
CREATE DATABASE sap_pesantren CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sap_user'@'localhost' IDENTIFIED BY 'KATA_SANDI_DATABASE_YANG_KUAT';
GRANT ALL PRIVILEGES ON sap_pesantren.* TO 'sap_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```
> [!IMPORTANT]
> Simpan `nama database`, `username`, dan `password` di atas untuk diisikan ke berkas `.env`.

---

### Langkah 3: Clone Repository Kode

Arahkan ke direktori `/var/www/` dan clone repositori aplikasi:
```bash
cd /var/www
sudo git clone https://github.com/USERNAME/REPO_ANDA.git sap-alfalahputra
cd sap-alfalahputra
```

---

### Langkah 4: Konfigurasi Hak Akses File (Permissions)

Beri kepemilikan direktori kepada pengguna web server (`www-data`):
```bash
sudo chown -R www-data:www-data /var/www/sap-alfalahputra
sudo chmod -R 755 /var/www/sap-alfalahputra
```

Berikan hak tulis khusus untuk direktori `storage` dan `bootstrap/cache`:
```bash
sudo chmod -R 775 /var/www/sap-alfalahputra/storage
sudo chmod -R 775 /var/www/sap-alfalahputra/bootstrap/cache
```

Jika Anda login menggunakan user non-root (misal: `ubuntu`), tambahkan user Anda ke grup `www-data`:
```bash
sudo usermod -a -G www-data $USER
```

---

### Langkah 5: Konfigurasi Environment Produksi (.env)

Salin berkas contoh environment:
```bash
sudo cp .env.example .env
sudo chown www-data:www-data .env
sudo chmod 640 .env
```

Buka dan sesuaikan berkas `.env` menggunakan editor teks (misal: `nano`):
```bash
sudo nano .env
```

Pastikan variabel-variabel kunci berikut disesuaikan untuk lingkungan **PRODUKSI**:

```env
APP_NAME="SAP | SEKRETARIS AL-FALAH PUTRA"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sekretariat.alfalahputra.sch.id

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sap_pesantren
DB_USERNAME=sap_user
DB_PASSWORD=KATA_SANDI_DATABASE_YANG_KUAT

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true

FILESYSTEM_DISK=public
QUEUE_CONNECTION=database

# =====================================================================
# Kredensial Akun Sekretaris (WAJIB DIISI PADA PRODUCTION)
# =====================================================================
# Seeder otomatis gagal jika email atau password sekretaris kosong di production.
SEKRETARIS_NAME="Sekretariat Pesantren Al-Falah Putra"
SEKRETARIS_EMAIL=sekretaris@alfalahputra.sch.id
SEKRETARIS_PASSWORD="BuatPasswordYangSangatKuatDanUnik123!"

# Matikan data dummy agar tidak ada data palsu di database produksi
SEED_DUMMY_DATA=false
```

Simpan berkas (`Ctrl + O`, lalu `Enter`, kemudian keluar `Ctrl + X`).

---

### Langkah 6: Instalasi Dependensi PHP & Frontend Build

Jalankan instalasi dependensi PHP tanpa paket pengembangan (*no-dev*):
```bash
composer install --no-dev --optimize-autoloader
```

Generate application key:
```bash
php artisan key:generate --force
```

Instal dependensi JavaScript dan lakukan build aset (Tailwind CSS & Vite):
```bash
npm install --frozen-lockfile
npm run build
```

---

### Langkah 7: Migrasi Database & Seeding Akun Sekretaris

Jalankan migrasi tabel database dan seeder data awal produksi:
```bash
php artisan migrate --force
php artisan db:seed --class=ProduksiSeeder --force
```

> [!NOTE]
> `ProduksiSeeder` hanya akan memasukkan data inti yang diperlukan sistem:
> 1. Akun login Sekretaris resmi (dari `SEKRETARIS_EMAIL` & `SEKRETARIS_PASSWORD` di `.env`).
> 2. Pengaturan awal nama instansi, logo, dan pola nomor surat.
> 3. Kategori jenis surat standar.
> 4. Template berkas surat resmi.
> 5. Tata letak bawaan beranda publik.
> *(Tidak ada data santri palsu atau surat dummy yang dimasukkan).*

---

### Langkah 8: Pembuatan Symlink Storage

Aplikasi menyimpan foto santri, dokumen arsip, dan lampiran surat di direktori `storage/app/public`. Buat tautan simbolik (*symlink*) ke direktori `public/storage`:
```bash
php artisan storage:link
```

Pastikan kepemilikan tetap pada `www-data`:
```bash
sudo chown -R www-data:www-data /var/www/sap-alfalahputra/storage
```

---

### Langkah 9: Optimasi Performa Cache Laravel

Di server produksi, kunci semua konfigurasi, rute, dan tampilan ke dalam cache untuk kecepatan akses maksimal:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

### Langkah 10: Konfigurasi Web Server Nginx

Buat berkas konfigurasi Nginx baru untuk domain aplikasi:
```bash
sudo nano /etc/nginx/sites-available/sap-alfalahputra
```

Masukkan konfigurasi berikut (ganti `sekretariat.alfalahputra.sch.id` dengan domain Anda):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name sekretariat.alfalahputra.sch.id;

    # Document Root mengarah langsung ke folder /public
    root /var/www/sap-alfalahputra/public;
    index index.php index.html index.htm;

    charset utf-8;

    # Batas ukuran unggahan dokumen & berkas arsip (maksimal 10 MB)
    client_max_body_size 10M;

    # Header Keamanan
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    # Pemrosesan skrip PHP via PHP-FPM 8.3
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Blokir akses langsung ke file tersembunyi (.env, .git, dll)
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache aset statis (CSS, JS, Gambar, Font)
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
}
```

Aktifkan konfigurasi situs dan uji sintaks Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/sap-alfalahputra /etc/nginx/sites-enabled/
sudo nginx -t
```

Jika pengujian menampilkan pesan `syntax is ok` dan `test is successful`, restart Nginx dan PHP-FPM:
```bash
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

---

### Langkah 11: Pemasangan SSL / HTTPS (Let's Encrypt / Certbot)

Pasang Certbot untuk mengaktifkan HTTPS gratis dengan perpanjangan otomatis:
```bash
sudo apt install -y certbot python3-certbot-nginx
```

Jalankan Certbot untuk domain Anda:
```bash
sudo certbot --nginx -d sekretariat.alfalahputra.sch.id
```

Ikuti instruksi di layar (masukkan email pemberitahuan kedaluwarsa dan setujui ToS). Certbot akan secara otomatis memperbarui konfigurasi Nginx dengan sertifikat SSL dan mengalihkan seluruh lalu lintas HTTP ke HTTPS.

Uji otomatisasi perpanjangan sertifikat:
```bash
sudo certbot renew --dry-run
```

---

### Langkah 12: Konfigurasi Cronjob (Task Scheduling)

Aplikasi Laravel memiliki penjadwal tugas (*Task Scheduler*) otomatis untuk membersihkan token tanda tangan yang kedaluwarsa (>30 menit) dan pengecekan agenda mendatang.

Buka crontab milik pengguna `www-data`:
```bash
sudo crontab -u www-data -e
```

Tambahkan baris berikut di akhir berkas:
```cron
* * * * * cd /var/www/sap-alfalahputra && php artisan schedule:run >> /dev/null 2>&1
```

Simpan dan tutup editor.

---

## 3. Opsi B: Deployment di Shared Hosting (cPanel)

Jika menggunakan penyedia hosting bersama (*Shared Hosting / Cloud Hosting cPanel*):

### 1. Struktur Folder Aman di cPanel
Secara default, cPanel menyajikan file publik dari folder `public_html`. **JANGAN** meletakkan seluruh file Laravel langsung di dalam `public_html` karena berkas `.env` dan kode sumber akan rentan diunduh publik!

Gunakan struktur berikut:
1. Buat folder baru di luar folder web root, misalnya `/home/username/sap-app`.
2. Unggah seluruh isi repositori proyek Anda ke dalam `/home/username/sap-app`.
3. Pindahkan seluruh isi folder `/home/username/sap-app/public` ke dalam `/home/username/public_html`.
4. Buka file `/home/username/public_html/index.php`, lalu ubah path autoload dan bootstrap:
   ```php
   // Ubah dari:
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';

   // Menjadi:
   require __DIR__.'/../sap-app/vendor/autoload.php';
   $app = require_once __DIR__.'/../sap-app/bootstrap/app.php';
   ```

*(Alternatif modern pada cPanel yang mendukung pemilihan Document Root: cukup ubah Document Root domain/subdomain langsung mengarah ke `/home/username/sap-app/public`).*

### 2. Konfigurasi PHP & Ekstensi di cPanel
- Buka menu **Select PHP Version** atau **MultiPHP Manager**.
- Pilih **PHP 8.3**.
- Aktifkan ekstensi: `pdo_mysql`, `mbstring`, `fileinfo`, `intl`, `zip`, `xml`, `bcmath`, `curl`.
- Naikkan limit memori dan berkas:
  - `memory_limit` = `256M`
  - `upload_max_filesize` = `10M`
  - `post_max_size` = `12M`

### 3. Setup Database di cPanel
1. Buka menu **MySQL Database Wizard**.
2. Buat database: `username_sap`.
3. Buat user database dan password yang kuat.
4. Berikan izin **ALL PRIVILEGES** kepada user tersebut ke database.

### 4. Konfigurasi `.env`
Buka File Manager di cPanel, pastikan opsi *Show Hidden Files* aktif, buat/edit berkas `/home/username/sap-app/.env` sesuai konfigurasi produksi (database, URL, `SEKRETARIS_EMAIL`, `SEKRETARIS_PASSWORD`, `SEED_DUMMY_DATA=false`).

### 5. Menjalankan Perintah Terminal / SSH
Jika cPanel menyediakan fitur **Terminal** atau akses SSH:
```bash
cd /home/username/sap-app
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --class=ProduksiSeeder --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> [!TIP]
> Jika tidak ada akses Terminal/SSH di cPanel, Anda dapat menjalankan `composer install` dan `npm run build` di komputer lokal terlebih dahulu, kemudian mengompres seluruh folder (termasuk `vendor` dan `public/build`) menjadi file `.zip` lalu mengekstraknya di File Manager cPanel.

---

## 4. Skrip Otomasi Pembaruan (Deployment Script)

Untuk mempermudah pembaruan kode di kemudian hari tanpa *downtime* yang mengganggu, buat skrip `deploy.sh` di server VPS Anda:

```bash
sudo nano /var/www/sap-alfalahputra/deploy.sh
```

Isi dengan skrip berikut:
```bash
#!/bin/bash
set -e

echo "=== Memulai Proses Pembaruan Aplikasi ==="

cd /var/www/sap-alfalahputra

# 1. Aktifkan mode pemeliharaan (Maintenance Mode)
php artisan down --render="errors::503" --secret="rahasia-akses-admin"

# 2. Ambil perubahan terbaru dari Git
git pull origin main

# 3. Instal dependensi PHP terbaru
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 4. Build ulang frontend (jika ada pembaruan tampilan)
npm install --frozen-lockfile
npm run build

# 5. Jalankan migrasi database baru jika ada
php artisan migrate --force

# 6. Bersihkan dan perbarui cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Restart PHP-FPM
sudo systemctl restart php8.3-fpm

# 8. Matikan mode pemeliharaan
php artisan up

echo "=== Deployment Selesai Berhasil! ==="
```

Beri izin eksekusi:
```bash
sudo chmod +x /var/www/sap-alfalahputra/deploy.sh
```

Kapan saja ada pembaruan kode di GitHub, Anda cukup menjalankan:
```bash
./deploy.sh
```

---

## 5. Prosedur Cadangan Data (Backup Rutin)

Untuk menjamin ketersediaan data dan kepatuhan terhadap PRD:

### 1. Cadangan Database Otomatis (MySQL Dump)
Buat skrip pencadangan harian:
```bash
sudo nano /usr/local/bin/backup-sap.sh
```

Isi dengan:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/sap"
DATE=$(date +"%Y%m%d_%H%M%S")
DB_NAME="sap_pesantren"
DB_USER="sap_user"
DB_PASS="KATA_SANDI_DATABASE"

mkdir -p $BACKUP_DIR

# Dump database MySQL dan kompres ke file .gz
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_${DATE}.sql.gz

# Simpan juga arsip berkas unggahan storage
tar -czf $BACKUP_DIR/storage_${DATE}.tar.gz /var/www/sap-alfalahputra/storage/app/public

# Hapus backup yang lebih tua dari 14 hari
find $BACKUP_DIR -type f -name "*.gz" -mtime +14 -exec rm {} \;
```

Beri izin eksekusi dan pasang di Crontab root setiap jam 02:00 pagi:
```bash
sudo chmod +x /usr/local/bin/backup-sap.sh
sudo crontab -e
```
Tambahkan:
```cron
0 2 * * * /usr/local/bin/backup-sap.sh >> /var/log/backup-sap.log 2>&1
```

---

## 6. Troubleshooting & Solusi Kendala Umum

| Masalah | Penyebab Umum | Solusi |
| --- | --- | --- |
| **500 Server Error** | Cache lama bermasalah, berkas `.env` belum ada, atau permission folder salah. | Periksa log di `storage/logs/laravel.log`. Jalankan `sudo chown -R www-data:www-data storage bootstrap/cache` dan `php artisan config:clear`. |
| **403 Forbidden** | Nginx Document Root salah mengarah ke root project, bukan ke `/public`. | Pastikan baris `root /var/www/sap-alfalahputra/public;` pada konfigurasi Nginx. |
| **Foto Santri / PDF Tidak Tampil (404)** | Symlink storage belum dibuat atau kepemilikan folder tidak tepat. | Jalankan `php artisan storage:link` dan pastikan folder `storage/app/public` dapat dibaca web server. |
| **Seeder Error: `Kredensial akun Sekretaris belum diatur!`** | `SEKRETARIS_EMAIL` atau `SEKRETARIS_PASSWORD` pada file `.env` masih kosong di lingkungan `production`. | Buka `.env`, isi kedua variabel tersebut dengan email dan kata sandi asli sekretaris, lalu ulangi seeder. |
| **Tampilan CSS Berantakan / Polos** | Aset Vite belum di-build ke folder `public/build`. | Jalankan `npm run build` di server. |
| **Gagal Unggah Berkas > 2 MB** | Batas ukuran upload di PHP (`upload_max_filesize`) atau Nginx (`client_max_body_size`) terlalu kecil. | Naikkan nilai `upload_max_filesize = 10M` dan `post_max_size = 12M` di `php.ini` (`/etc/php/8.3/fpm/php.ini`), serta `client_max_body_size 10M;` di blok server Nginx, lalu restart Nginx & PHP-FPM. |
