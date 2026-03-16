# Panduan Pembaruan Sistem Azventory (Tim IT)

Ikuti langkah-langkah berikut secara berurutan melalui **SSH Terminal di cPanel**.

---

## Langkah 0 — Persiapan Lokal (WAJIB jika server tidak punya Node/NPM)

Karena server produksi (cPanel) tidak bisa menjalankan perintah `npm`, Anda harus membangun file aset di **komputer lokal (Laptop)** sebelum melakukan push ke Git.

1. Di terminal laptop Anda, jalankan:
   ```bash
   npm install
   npm run build
   ```
2. Pastikan folder `public/build` sudah terupdate.
3. Commit dan Push perubahan tersebut ke repository (GitHub/GitLab).

---

## Langkah 1 — Ambil Kode Terbaru (Di Server)

```bash
git pull origin main
```

---

## Langkah 2 — Update Paket PHP

```bash
composer install --optimize-autoloader --no-dev
```

---

## Langkah 3 — Jalankan Migrasi Database

```bash
php artisan migrate --force
```

> **Catatan Iterasi 4**: Langkah ini akan otomatis membuat tabel baru untuk `locations`, `categories`, dan `brands` serta menambahkan kolom alasan penolakan pada log stok. Data lama tetap aman.

---

## Langkah 4 — Update Konfigurasi di File `.env` *(Jika Diminta Developer)*

> Lewati langkah ini jika developer tidak memberi instruksi khusus.

Developer akan mengirimkan nilai kunci baru via **WhatsApp pribadi**. Buka file `.env` di server (via cPanel File Manager atau SSH), lalu cari dan ganti baris berikut:

```ini
PUSHER_APP_KEY=<nilai yang dikasih developer>
PUSHER_APP_SECRET=<nilai yang dikasih developer>
```

Simpan file setelah selesai.

---

## Langkah 5 — Bersihkan Cache

```bash
php artisan optimize:clear
php artisan view:clear
```

---

## Langkah 6 — Konfigurasi Email (Fitur Lupa Password)

> Lakukan langkah ini **sekali saja** saat pertama kali deploy, atau saat ganti email server.

Fitur **Lupa Password** membutuhkan konfigurasi email agar bisa mengirim link reset ke pengguna. Ikuti salah satu opsi berikut.

### Opsi A — Pakai Email cPanel (Direkomendasikan)

1. **Buat akun email** di cPanel → **Email Accounts** (misal: `noreply@domainmu.com`)
2. Buka file `.env` di server (via **File Manager cPanel** → folder project → `.env`)
3. Cari dan **ubah baris-baris berikut**:

```ini
MAIL_MAILER=smtp
MAIL_HOST=mail.domainmu.com
MAIL_PORT=465
MAIL_SCHEME=ssl
MAIL_USERNAME=noreply@domainmu.com
MAIL_PASSWORD=password_email_yang_dibuat
MAIL_FROM_ADDRESS=noreply@domainmu.com
MAIL_FROM_NAME="Azventory"
```

> Ganti `domainmu.com` dengan domain asli dan `password_email_yang_dibuat` dengan password akun email tersebut.

4. Simpan file `.env`
5. Jalankan perintah berikut via SSH untuk membersihkan cache konfigurasi:
```bash
php artisan config:clear
```

### Opsi B — Pakai Sendmail (Lebih Simpel, Tanpa Akun Email Baru)

Jika tidak ingin membuat akun email baru, cPanel biasanya sudah punya Sendmail bawaan. Cukup ubah file `.env`:

```ini
MAIL_MAILER=sendmail
MAIL_FROM_ADDRESS=noreply@domainmu.com
MAIL_FROM_NAME="Azventory"
```

Lalu jalankan:
```bash
php artisan config:clear
```

### Cara Verifikasi Email Berhasil

1. Buka halaman `https://domainmu.com/forgot-password`
2. Masukkan email akun yang terdaftar di sistem
3. Klik **Kirim Link Reset**
4. Cek inbox email tersebut — seharusnya ada email berisi link reset password

> Jika email tidak masuk dalam 2–3 menit, cek folder **Spam/Junk** terlebih dahulu.

---

## Langkah 7 — Mengatur Cron Job di cPanel (Pembersihan Otomatis)

> **PENTING**: Azventory memiliki fitur `activitylog:clean` (menghapus log yang lebih tua dari 1 bulan) dan `image:clean` (menghapus foto barang *orphan* yang tidak ada di *database*). Kedua fitur ini berjalan secara otomatis jika **Cron Job** pada cPanel diaktifkan.

1. Masuk ke **cPanel** akun hosting Anda.
2. Cari dan klik menu **Cron Jobs** di bagian "Advanced".
3. Pada bagian **Add New Cron Job**:
   - **Common Settings**: Pilih `Once Per Minute (* * * * *)` (Atau atur sesuai standar server IT Anda).
   - **Command**: Masukkan _script_ pemanggilan artisan schedule Laravel:
     ```bash
     cd /path/to/folder/azventory && php artisan schedule:run >> /dev/null 2>&1
     ```
     *(Ganti `/path/to/folder/azventory` dengan *absolute path* asli lokasi Anda menaruh web aplikasi ini di server, misalnya `/home/username/public_html/azventory`)*
4. Klik **Add New Cron Job**. 
*(Cron job hanya perlu di-_setting_ satu kali selamanya, dan sistem akan mengurus semua jadwal pembersihan data dan log secara mandiri di belakang layar).*

---

## Langkah 8 — Panduan "One-Off" / Sinkronisasi Khusus 

Jika developer memberikan instruksi untuk **Sinkronisasi Otomatis Master Data (Kategori/Merk)** pada pembaruan spesifik, jalankan satu kali perintah berikut via SSH sebelum masuk web:

```bash
php artisan tinker sync_data.php
```

---

## Langkah 9 — Verifikasi

- Buka URL aplikasi Azventory di browser
- Pastikan tidak ada halaman Error 500
- Login dengan akun Superadmin dan cek semua fitur berjalan normal
