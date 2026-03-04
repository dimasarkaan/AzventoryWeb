# Panduan Pembaruan Sistem dan Sinkronisasi Data Master Azventory

Dokumen ini merupakan panduan resmi langkah demi langkah untuk melakukan pembaruan (update) aplikasi Azventory pada server produksi (production server) berbasis cPanel. 

Panduan ini disusun untuk memastikan bahwa fitur-fitur baru (seperti Master Data Kategori, Merk, dan Lokasi) dapat diterapkan dengan aman tanpa merusak atau menghilangkan data inventaris yang telah diinput sebelumnya.

---

## 1. Persiapan Pembaruan (Local Environment)
Langkah ini dilakukan pada lingkungan pengembangan sebelum file diunggah ke repositori GitHub.

### A. Kompilasi Aset Visual (Frontend)
Aplikasi Azventory menggunakan Vite untuk mengelola aset CSS dan JavaScript. Karena server cPanel standar tidak menjalankan Node.js, kompilasi harus dilakukan secara mandiri.
1. Buka terminal pada komputer pengembangan.
2. Arahkan direktori terminal ke dalam *root* proyek Azventory.
3. Jalankan perintah kompilasi aset berikut:
   ```bash
   npm run build
   ```
4. Pastikan folder `public/build/` telah dikonsolidasi dan terdaftar untuk masuk ke repositori.

### B. Pembaruan Repositori
Kirimkan struktur kode terbaru ke repositori utama (GitHub):
1. Tambahkan semua perubahan:
   ```bash
   git add .
   ```
2. Berikan pesan *commit* yang deskriptif:
   ```bash
   git commit -m "Pembaruan Sistem Utama: Modul Sinkronisasi Master Data"
   ```
3. Unggah (push) perubahan tersebut:
   ```bash
   git push origin main
   ```

---

## 2. Eksekusi Pembaruan di Server (Production Environment)
Seluruh instruksi di bawah ini dieksekusi melalui antarmuka Terminal (SSH) langsung di dalam lingkungan server cPanel.

### A. Pengambilan Kode Akses Terkini
Arahkan direktori terminal menuju modul instalasi aplikasi Azventory, kemudian eksekusi perintah sinkronisasi Git:
```bash
git pull origin main
```

### B. Pembaruan Paket Eksternal (Langkah Tentatif)
Untuk menjaga integritas dan stabilitas kode PHP, lakukan pembaruan terhadap modul *Composer* secara optimal (tanpa modul pengujian/pengembangan):
```bash
composer install --optimize-autoloader --no-dev
```

### C. Pemrosesan Migrasi dan Sinkronisasi Basis Data (Esensial)
Sistem terbaru Azventory mencakup mekanisme sinkronisasi otomatis. Langkah ini dijamin aman dan hanya berfungsi mengekstrak rekaman lama (teks statis) ke dalam modul relasi Master Data baru secara otomatis**.

Eksekusi perintah migrasi dasar berikut, dan tambahkan parameter paksa (`--force`) yang diwajibkan oleh struktur *Production*:
```bash
php artisan migrate --force
```
*Catatan Keamanan: Perintah di atas telah diprogram secara spesifik agar tidak menghapus satu pun baris data dari inventaris aslinya.*

### D. Rekonfigurasi Berkas Simpanan (Cache)
Jalankan instruksi teknis ini untuk membersihkan memori sistem. Hal ini bertujuan agar seluruh rute, tampilan desain baru, serta pembacaan basis data baru dapat diimplementasikan sesecepat mungkin.
```bash
php artisan optimize:clear
php artisan view:clear
```

---

## 3. Validasi Akhir Pembaruan
Setelah seluruh pembaruan tereksekusi, lakukan tahap verifikasi untuk memastikan sistem berjalan ideal:
- Akses halaman depan sistem Azventory dan pastikan tidak ada kode galat (seperti Error 500).
- Lakukan proses autentikasi (Login) dengan kredensial "Superadmin".
- Identifikasi panel navigasi dan akses halaman khusus Master Data.
- Periksa kesesuaian data: Validasi bahwa seluruh teks (contoh: status kondisi, lokasi rak) yang pernah diisi secara acak kini sudah terekstraksi dan terkumpul dengan terstruktur pada masing-masing tabel antarmuka Master Data.
