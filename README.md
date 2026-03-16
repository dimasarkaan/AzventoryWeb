<div align="center">

# Azventory
**Sistem Informasi Manajemen Stok dan Inventaris**

[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.4-38B2AC?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](#)

Sistem berbasis web untuk pencatatan pergudangan, manajemen aset, dan monitoring peminjaman barang. 
Dikembangkan sebagai implementasi sistem informasi pada lingkungan gudang atau kantor.

</div>

---

## Deskripsi Sistem
Azventory dirancang untuk mendokumentasikan alur masuk dan keluar barang serta pengelolaan aset perusahaan secara terintegrasi. Sistem ini menggunakan arsitektur Role-Based Access Control (RBAC) untuk mengatur batasan akses data berdasarkan peran pengguna (Superadmin, Admin, dan Operator).

## Fitur Tersedia

- **Manajemen Pengguna dan Hak Akses**: Pengaturan akses terbagi menjadi tiga level untuk menjaga akses data tetap sesuai dengan kewenangan masing-masing peran.
- **Pencatatan Stok Barang**: Mendukung pendataan barang masuk dan keluar dengan pencatatan log aktivitas untuk setiap transaksi stok.
- **Sistem Peminjaman**: Modul untuk mendata barang yang dipinjam oleh personil tertentu, lengkap dengan estimasi waktu pengembalian dan status kondisi barang.
- **Monitoring Aset**: Fitur untuk melihat status barang di berbagai lokasi dan kategori, serta identifikasi barang dengan jumlah stok di bawah batas minimum.
- **Dokumentasi API**: Menyediakan akses data melalui program pendukung untuk integrasi dengan sistem lain menggunakan standar REST API.
- **Pelaporan**: Fasilitas ekspor data dalam format dokumen PDF dan file Excel untuk kebutuhan dokumentasi manual atau audit periodik.

## Teknologi yang Digunakan

| Komponen | Teknologi |
| --- | --- |
| **Framework Utama** | [Laravel 11](https://laravel.com) |
| **Bahasa Pemrograman** | [PHP 8.2+](https://php.net) |
| **Frontend Styling** | [Tailwind CSS](https://tailwindcss.com) & [Alpine.js](https://alpinejs.dev) |
| **Database** | MySQL / MariaDB |
| **Autentikasi API** | Laravel Sanctum |

## Panduan Instalasi Lokal

Ikuti langkah-langkah di bawah ini untuk mengunduh dan menjalankan proyek ini:

### 1. Kebutuhan Sistem
- Koneksi Internet (untuk instalasi dependensi)
- PHP versi 8.2 ke atas
- Composer
- Node.js & NPM
- Layanan database MySQL

### 2. Pemasangan Kode
```bash
# Salin repositori ke direktori lokal
git clone https://github.com/Username-Anda/AzventoryWeb.git

# Masuk ke folder proyek
cd AzventoryWeb

# Instal dependensi backend
composer install

# Instal dependensi frontend dan kompilasi aset
npm install
npm run build
```

### 3. Konfigurasi Database
1. Gandakan file `.env.example` menjadi `.env`.
2. Generate Application Key menggunakan perintah:
   ```bash
   php artisan key:generate
   ```
3. Sesuaikan parameter database berikut pada file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### 4. Setup Database dan Akun
Jalankan migrasi untuk membuat tabel dan mengisi data awal:
```bash
php artisan migrate --force --seed
```

### 5. Menjalankan Aplikasi
Jalankan server pengembangan:
```bash
php artisan serve
```
Akses aplikasi melalui alamat `http://127.0.0.1:8000`.

---

## Akun Akses Default
Jika menggunakan data *seed* awal, gunakan kredensial berikut untuk masuk:
- **Superadmin**: superadmin@azventory.com (Password: password)
- **Admin**: admin@azventory.com (Password: password)
- **Operator**: operator@azventory.com (Password: password)

## Dokumentasi Tambahan
Untuk informasi operasional lebih lanjut, silakan merujuk ke dokumen berikut:
- [Panduan Integrasi API](./Panduan_Integrasi_API.md)
- [Panduan Pembaruan Sistem di cPanel](./Panduan_Pembaruan_Sistem.md)
- [Checklist Kesiapan Produksi](./Production_Readiness.md)

---
Dokumentasi ini disusun sebagai panduan teknis umum bagi pengembang dan administrator sistem.
