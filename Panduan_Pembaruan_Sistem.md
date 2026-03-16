# Panduan Pembaruan Sistem Azventory
Instruksi teknis untuk pemeliharaan rutin dan deployment di cPanel Shared Hosting.

Dokumen ini berisi prosedur standar untuk melakukan pembaruan aplikasi Azventory. Seluruh langkah di bawah ini dilakukan melalui koneksi SSH pada server hosting.

---

## Persiapan Aset (Build Lokal)
Proses kompilasi aset frontend (CSS dan JavaScript) dilakukan di komputer lokal sebelum kode dikirim ke server untuk menjaga kinerja hosting.

1. Jalankan perintah berikut pada terminal lokal:
   ```bash
   npm install
   npm run build
   ```
2. Pastikan file di dalam direktori `public/build` telah diperbarui.
3. Lakukan push perubahan ke repositori Git yang digunakan.

---

## Prosedur Pembaruan di Server
Hubungkan terminal SSH ke server cPanel, kemudian jalankan perintah berikut secara berurutan:

### 1. Mode Pemeliharaan
Aktifkan mode pemeliharaan agar pengguna tidak dapat mengakses sistem selama proses update:
```bash
php artisan down
```

### 2. Penarikan Kode Terbaru
Ambil perubahan kode terbaru dari repositori:
```bash
git pull origin main
```

### 3. Pembaruan Dependensi
Instal pembaruan paket PHP tanpa menyertakan library pengembangan:
```bash
composer install --optimize-autoloader --no-dev
```

### 4. Pembaruan Struktur Database
Jalankan migrasi database untuk menerapkan perubahan struktur tabel atau indeks baru:
```bash
php artisan migrate --force
```

### 5. Optimasi Sistem dan Pembersihan Cache
Pastikan konfigurasi dan tampilan terbaru terbaca oleh sistem:
```bash
php artisan optimize
php artisan view:cache
```

### 6. Menonaktifkan Mode Pemeliharaan
Kembalikan aplikasi ke kondisi aktif:
```bash
php artisan up
```

---

## Konfigurasi Penting pada File .env
Periksa file `.env` di direktori utama aplikasi menggunakan File Manager cPanel untuk memastikan pengaturan produksi sudah tepat:

- APP_DEBUG: Set ke `false` untuk menyembunyikan detail error dari publik.
- LOG_CHANNEL: Gunakan `daily` untuk membagi file log per hari.
- LOG_DAILY_DAYS: Set ke `180` untuk menyimpan riwayat log selama 6 bulan.
- QUEUE_CONNECTION: Set ke `database` jika menggunakan sistem antrean untuk pengiriman email.

---

## Optimasi Keamanan Akses (.htaccess)
Gunakan aturan berikut pada file `public/.htaccess` untuk melindungi file sensitif dan meningkatkan kecepatan akses:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Membatasi akses langsung ke file sensitif
    RewriteRule ^\.(env|log) - [F,L]

    # Mengaktifkan kompresi Gzip
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/plain text/html text/xml text/css application/javascript
    </IfModule>

    # Pengaturan cache browser untuk aset statis
    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresDefault "access plus 1 month"
    </IfModule>

    # Aturan dasar routing Laravel
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## Konfigurasi Penjadwal (Cron Job)
Agar fitur otomatis seperti pembersihan log dan pemrosesan antrean berjalan, daftarkan perintah berikut pada menu Cron Jobs di cPanel:

Jadwal: Sekali setiap menit (`* * * * *`)
Perintah:
```bash
/usr/local/bin/php /home/USER/public_html/artisan schedule:run >> /dev/null 2>&1
```
Sesuaikan path `/home/USER/public_html/` dengan lokasi direktori aplikasi Anda di server.

---

## Validasi Akhir
Pastikan poin-poin berikut terpenuhi setelah proses selesai:
1. Website dapat diakses secara normal melalui browser.
2. Login Superadmin berjalan lancar.
3. Tidak ada pesan error yang muncul pada menu utama.
