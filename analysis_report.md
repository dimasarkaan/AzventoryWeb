# MERCILESS DEEP SCAN AUDIT REPORT
**Target:** AzventoryWeb Codebase | **Status:** 🔴 CRITICAL FINDINGS | **Date:** 2026-02-05

---

## Executive Summary
Sistem secara umum memiliki fondasi arsitektur **Laravel 11+** yang kuat, menggunakan pola **Service Pattern** dan **Enums** yang baik untuk maintainability. Namun, ditemukan celah **KEAMANAN KRITIKAL** pada manajemen user dan **HUTANG TEKNIS** besar pada lokalisasi (bahasa) yang bercampur aduk (hardcoded). Skalabilitas database cukup baik namun memiliki potensi masalah integritas data jangka panjang.

---

## 1. ARSITEKTUR & SKALABILITAS
**Status:** 🟡 **MODERATE RISK**

### 🔍 Temuan
*   **Routing (Good):** Routing sudah terpisah rapi dengan `middleware` dan `prefix` (`superadmin`, `admin`, `operator`). Penambahan role baru sangat mudah ("Plug-and-play").
*   **Separation of Concerns (Mixed):**
    *   ✅ Menggunakan `InventoryService` untuk logika berat.
    *   ❌ **Leak Logic:** Logika bisnis masih bocor ke Controller.
        *   `InventoryController.php` (Line 119): Logika *generating filename* dan *slug* untuk QR Code ada di Controller, bukan di Service/Model.
        *   `InventoryController.php` (Line 149): Return JSON API format hardcoded di Controller.
*   **Hardcoded Roles:**
    *   `routes/web.php` (Line 14): Pengecekan role menggunakan `if/elseif` manual untuk redirection. Sebaiknya menggunakan Middleware khusus `RedirectIfAuthenticatedByRole` agar lebih bersih.

### 💥 Dampak
Jika role baru masuk, Anda harus mengedit logika redirection di rute utama secara manual. Kebocoran logika di controller membuat testing unit menjadi sulit.

### 🛠 Rekomendasi
1.  Pindahkan logika nama file QR ke `QrCodeService`.
2.  Buat `DashboardRedirectionMiddleware` untuk menangani routing login.

---

## 2. AUDIT BAHASA & LOKALISASI (Total Indonesian)
**Status:** 🔴 **CRITICAL (HARDCODED MESSAGES)**

### 🔍 Temuan
*   **Pencampuran Fatal:** File View (`create.blade.php`) dan Service (`InventoryService.php`) memiliki teks bahasa Indonesia yang **HARDCODED** (Tulis mati).
    *   `resources/views/superadmin/inventory/create.blade.php`: "Isi detail sparepart...", "Tipe Barang".
    *   `app/Services/InventoryService.php`: "Stok sparepart... berhasil ditambahkan", "Semua Merk".
*   **Inkonsistensi:** Beberapa bagian menggunakan `{{ __('...') }}` (translatable), tapi mayoritas text mentah.

### 💥 Dampak
*   **Maintainability:** Jika ingin mengubah kata "Sparepart" menjadi "Suku Cadang", Anda harus mencari dan replace di **ratusan file**, bukan hanya satu file bahasa.
*   **User Experience:** Error message dari framework (Laravel default) mungkin masih bahasa Inggris jika validasi gagal, bercampur dengan bahasa Indonesia hardcoded Anda.

### 🛠 Rekomendasi
1.  **Wajib:** Pindahkan SEMUA string user-facing ke `lang/id/messages.php` atau `lang/id/ui.php`.
2.  Gunakan helper `__('messages.stock_added')`.

---

## 3. EFISIENSI PERFORMA & BIAYA HOSTING
**Status:** 🟢 **GOOD (WITH MINOR TWEAKS)**

### 🔍 Temuan
*   **✅ Caching:** `InventoryService` (Line 346) menggunakan Cache untuk dropdown options. Ini **sangat bagus** untuk mengurangi query database berulang.
*   **✅ Image Optimization:** Menggunakan `ImageOptimizationService` saat upload. Ini mencegah hosting penuh dengan gambar 5MB+.
*   **✅ Pagination:** Controller menggunakan `paginate(10)` standard.
*   **Database:** Tabel `spareparts` menggunakan indexing pada kolom vital (`part_number`, `category`, `brand`, `location`).

### 💥 Dampak
Sistem saat ini sangat efisien. Biaya hosting akan tetap rendah bahkan dengan ribuan item.

### 🛠 Rekomendasi
*   **Timezone:** Config `app.php` masih `'timezone' => 'UTC'`. Ubah ke `'Asia/Jakarta'` agar log aktivitas dan timestamp akurat sesuai waktu Indonesia.

---

## 4. KEAMANAN & VULNERABILITY
**Status:** 🔴 **CRITICAL SECURITY RISK**

### 🔍 Temuan
*   **⛔ STATIC DEFAULT PASSWORD:**
    *   **File:** `app/Http/Controllers/SuperAdmin/UserController.php` (Line 72 & 130).
    *   **Masalah:** User baru dan User yang di-reset password-nya selalu diberi password **"password123"**.
    *   **Risiko:** Jika Superadmin membuat user Admin baru dan Admin tersebut lupa ganti password, **siapapun** bisa login dengan menebak username dan password "password123".
*   **Username Enumeration:** Pola username `nama + rand(100,999)`. Cukup mudah ditebak jika nama pegawai diketahui.

### 🛠 Rekomendasi (URGENT FIX)
1.  **Hapus Default Password:** Kirim email verifikasi/set password ke user baru, ATAU;
2.  **Force Change Password:** Tambahkan kolom `must_change_password` di tabel users. Jika `true`, user dipaksa ganti password saat login pertama.

---

## 5. DATABASE HEALTH
**Status:** AppleWebKit **MEDIUM DEBT**

### 🔍 Temuan
*   **Denormalisasi:** Kolom `category`, `brand`, `location` disimpan sebagai **STRING** (`VARCHAR`) di tabel `spareparts`.
    *   *Kelebihan:* Cepat, tidak perlu JOIN table.
    *   *Kekurangan:* Jika ingin ganti nama kategori "Elektronik" jadi "Elektronika", Anda harus update **SEMUA** baris data yang punya kategori itu.

### 🛠 Rekomendasi
Untuk skala sekarang, ini **OK**. Jangan refactor ke tabel terpisah dulu kecuali butuh fitur manajemen kategori yang kompleks.

---

## 6. KUALITAS KODE (Clean Code)
**Status:** 🟡 **IMPROVEMENTS NEEDED**

### 🔍 Temuan
*   **Spaghetti View:** `create.blade.php` memiliki 1200+ baris kode dalam satu file. Logic JavaScript (AlpineJS) bercampur aduk dengan HTML. Sangat sulit dibaca.
*   **Duplicate Routes:** Di `routes/web.php` ada duplikasi route profile (Line 78-80 sama dengan 87-89).

### 🛠 Rekomendasi
1.  **View Components:** Pecah form input menjadi komponen Blade (`x-inventory.form-basic`, `x-inventory.form-stock`).
2.  **Hapus Duplicate Code:** Cleaning `web.php`.

---

# 🚀 ROADMAP PRIORITAS (Step-by-Step)

Berikut langkah yang harus Anda ambil berdasarkan urgensi:

### 🚨 MINGGU 1: DARURAT (Security & Core Fixes)
1.  **[High Priority] Security Patch:** Implementasi mekanisme "Force Change Password" untuk user baru/reset. Jangan biarkan "password123" menjadi permanen.
2.  **[High Priority] Config Fix:** Ubah Timezone ke `Asia/Jakarta`.
3.  **[Medium Priority] Route Cleaning:** Hapus rute duplikat di `web.php` dan rapikan grouping.

### 🚧 MINGGU 2: STANDARDISASI (Localization Audit)
4.  **[High Effort] Extraction:** Mulai pindahkan teks hardcoded di `InventoryService` dan `UserController` ke file bahasa `lang/id/messages.php`.
5.  **[High Effort] View Extraction:** Pindahkan teks di `create.blade.php` ke file bahasa.
6.  **[Low Effort] Validation:** Pastikan pesan error validasi Form Request sudah Bahasa Indonesia semua.

### 🛠 MINGGU 3: REFACTORING (Code Structure)
7.  **Refactor View:** Pecah `create.blade.php` dan `edit.blade.php` menjadi komponen-komponen kecil agar mudah dimaintain.
8.  **Service Cleaning:** Pindahkan logic QR Code generator sepenuhnya ke Service, return object bersih ke Controller.

---

**Keputusan di Tangan Anda:** Apakah Anda ingin saya mulai dari **Security Patch** (User Password) atau **Localization** (Bahasa)?
