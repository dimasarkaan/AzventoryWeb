# Panduan Kesiapan Produksi (Production Readiness)

File ini berisi checklist kritikal untuk melakukan deployment Azventory Web ke server produksi (VPS/Shared Hosting).

## 1. Konfigurasi Lingkungan (`.env`)
Pastikan variabel berikut diset dengan benar di server produksi:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://nama-domain-anda.com`
- `LOG_CHANNEL=daily` (Agar file log tidak membengkak).

## 2. Optimasi Performa (WAJIB)
Jalankan perintah ini di server setelah deploy kode terbaru:
```bash
# Optimasi Route & Config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimasi Autoloader Composer
composer install --optimize-autoloader --no-dev
```

## 3. Keamanan & Database
- **APP_KEY**: Pastikan sudah digenerate (`php artisan key:generate`).
- **SSL/HTTPS**: Sangat disarankan menggunakan SSL (LetsEncrypt).
- **Security Headers**: Middleware `SecurityHeaders` sudah aktif secara global.
- **Queue Worker**: Karena laporan PDF menggunakan Queue, pastikan worker berjalan:
  ```bash
  php artisan queue:work --tries=3
  ```

## 4. Kebersihan Data (Opsional)
Jika ingin membersihkan data testing sebelum "Go Live":
```bash
php artisan migrate:fresh --seed
```
*Catatan: Pastikan seeder tidak berisi data sampah.*

## 5. Maintenance Mode
Gunakan perintah ini saat melakukan update di server:
```bash
php artisan down --secret="kode-rahasia"
php artisan up
```

---
Aplikasi telah melalui Deep Audit (Backend, Database, Security) dan siap digunakan secara profesional.
