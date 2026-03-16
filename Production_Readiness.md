# Panduan Kesiapan Produksi
Checklist persiapan akhir sebelum aplikasi diluncurkan ke server produksi.

Dokumen ini berisi daftar langkah penting untuk memastikan sistem Azventory berjalan optimal dan aman di lingkungan produksi.

---

## Pengaturan Lingkungan (.env)
Pastikan variabel lingkungan pada file `.env` di server sudah disesuaikan untuk mode produksi:

- APP_ENV: production
- APP_DEBUG: false
- APP_URL: https://domain-anda.com
- LOG_CHANNEL: daily
- LOG_DAILY_DAYS: 180

## Optimasi Performa
Jalankan perintah berikut di server produksi untuk mempercepat pemrosesan permintaan:

```bash
# Melakukan cache pada konfigurasi dan routing
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimasi autoloader PHP
composer install --optimize-autoloader --no-dev
```

## Keamanan dan Database
- APP_KEY: Pastikan sudah digenerate menggunakan perintah `php artisan key:generate`.
- Protokol HTTPS: Gunakan sertifikat SSL (seperti Let's Encrypt) untuk seluruh akses domain.
- Security Headers: Pastikan middleware keamanan sudah terpasang untuk melindungi aplikasi dari serangan umum.
- Antrean (Queue): Aktifkan queue worker jika terdapat proses berat seperti pembuatan laporan atau pengiriman email massal:
  ```bash
  php artisan queue:work --tries=3
  ```

## Pembersihan Data Awal
Jika diperlukan pembersihan data uji coba sebelum penggunaan resmi, gunakan perintah berikut:

```bash
php artisan migrate:fresh --seed
```
Peringatan: Perintah ini akan menghapus seluruh data yang ada. Pastikan seeder sudah dikonfigurasi hanya untuk data master yang diperlukan.

## Mode Perbaikan (Maintenance)
Saat melakukan pemeliharaan rutin di server, gunakan mode perbaikan untuk memberikan informasi kepada pengguna:

```bash
# Mengaktifkan mode perbaikan
php artisan down

# Mengaktifkan kembali aplikasi
php artisan up
```

---
Status: Seluruh komponen backend, database, dan modul keamanan telah melalui audit mendalam dan dinyatakan siap untuk penggunaan skala produksi.
