<div align="center">

# 📦 Azventory
**Automated Inventory & Asset Management System**

[![Laravel v11](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](#)
[![PHP v8.3](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](#)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](#)
[![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)](#)

*Sistem Manajemen Persediaan Barang dan Peminjaman Inventaris Berbasis Web yang Sangat Andal, Dibekali dengan Sistem Keamanan Kuat (Role-Based Access Control) dan Fitur Dokumentasi API.*

<br/>
<br/>
</div>

---

## 🎯 Tentang Proyek Ini

**Azventory** bukan sekadar sistem pencatatan gudang biasa. Ini adalah sebuah platform produksi tingkat lanjut untuk manajemen inventaris. Dirancang khusus dengan UI/UX yang dinamis (menggunakan *Tailwind CSS* & *Alpine.js*), sistem ini mampu menangani **puluhan ribu data barang**, **pergerakan stok (*stock mutation*)**, hingga **peminjaman aset**. 

Aplikasi ini sudah melalui proses pengujian (*Automated Testing*) ekstensif, memastikan keamanan dari *cyberattacks* seperti pergantian paksa URL (IDOR & Authorization Bypass) serta aman dari permasalahan performa seperti `N+1 Query Problem` saat melayani jutaan baris data secara bersamaan berkat penerapan **Composite Index DB**, **Caching**, dan **Eager Loading**.

## ✨ Fitur Unggulan

- 🔐 **Super-RBAC (Role-Based Access Control):** 
  - **Superadmin:** Kontrol mutlak (Kelola seluruh barang, hapus paksa/pulihkan data dari tong sampah, kelola seluruh pengguna, dan akses pembuatan `Token API`).
  - **Admin:** Pengawas harian (Kelola persediaan barang reguler, lihat log laporan bawahan, kelola peminjaman).
  - **Operator:** Pekerja gudang (Hanya dapat melihat stok, meminjam, dan mencatat pergerakan tanpa hak merusak/menghapus data).
- 📊 **Smart Dashboard:** Analitik yang ditenagai oleh caching bawaan. Memuat metrik seperti pergerakan barang (Sering Masuk & Sering Keluar), aset kedaluwarsa, peminjam yang belum kembali, dan nilai finansial gudang secara instan.
- 📦 **Advance Inventory System:** Mendukung multi-kategori, multi-lokasi, indikator stok menipis darurat, dan pencetakan massal *Barcode/Label QR* (.svg).
- 🧾 **Export & Reports:** Logika pencetakan file (Excel & PDF) Laporan Mutasi Stok, Laporan Peminjaman, dan Riwayat Aktifitas yang disajikan secara korporat lengkap dengan Header (Kop Surat) dan nama file otomatis.
- 🔗 **REST API Standardized:** Endpoints (JSON) siap pakai untuk diintegrasikan dengan Front-End React/Vue/Mobile App, diamankan dengan Laravel Sanctum via *Bearer Tokens*.

---

## 🛠 Instalasi dan Konfigurasi

Ikuti langkah-langkah di bawah ini untuk menjalankan Azventory di komputer lokal Anda:

### 1. Kebutuhan Sistem (Prerequisites)
- PHP >= 8.2
- Composer Version 2+
- Node.js & NPM (Untuk *Tailwind Build*)
- MySQL Database

### 2. Kloning dan Setup Dasar
Buka Terminal/Git Bash di folder tujuan Anda lalu jalankan perintah berurutan:

```bash
# 1. Clone repository ini
git clone https://github.com/UsernameAnda/AzventoryWeb.git

# 2. Masuk ke direktori
cd AzventoryWeb

# 3. Instal semua dependensi PHP & Backend
composer install

# 4. Instal dependensi Frontend & Lakukan Compile Desain 
npm install
npm run build
```

### 3. Konfigurasi Lingkungan (Environment)
```bash
# Salin file environment bawaan
cp .env.example .env

# Jalankan perintah generate kunci keamanan Laravel
php artisan key:generate
```
Edit file `.env` milik Anda. Sesuaikan bagian database (Penting!):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Menyiapkan Database dan Seeder (Akun Bawaan)
```bash
# Menjalankan struktur tabel dan mengisi data palsu untuk demonstrasi
php artisan migrate:fresh --seed
```
*Catatan: Pastikan Anda segera mengubah email/password bawaan setelah masuk.*

### 5. Jalankan Aplikasi
```bash
php artisan serve
```
Buka browser dan akses terminal Anda di `http://127.0.0.1:8000`.

---

## 🔑 Akun Uji Coba (Demo Credentials)
Gunakan email berikut jika Anda menggunakan perintah *seed* di atas:
- **Hak Akses Superadmin:** `superadmin@azventory.com` (Sandi: `password`)
- **Hak Akses Admin:** `admin@azventory.com` (Sandi: `password`)
- **Hak Akses Operator:** `operator@azventory.com` (Sandi: `password`)

---

## 🔌 API dan Integrasi Eksternal
Untuk panduan detail tentang pengiriman Request beserta Headers *Bearer Token* ke `http://127.0.0.1:8000/api/v1`, silakan lihat file panduan resmi di:
👉 **[Panduan_Integrasi_API.md](./Panduan_Integrasi_API.md)**

Anda juga dapat secara langsung meng-import Postman Collection yang sudah saya bagikan di repositori ini (`Azventory_API_Collection.postman_collection.json`) untuk segera mencoba tes respon JSON.

<div align="center">
<br/>

**❤️ Dikembangkan dengan sepenuh hati sebagai Mahakarya Sistem Inventaris.**
Terima kasih telah berkunjung, silakan beri 🌟 jika menurut Anda proyek ini bagus!

</div>
