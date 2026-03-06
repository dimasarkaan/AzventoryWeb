# Panduan Pembaruan Sistem Azventory (Tim IT)

Ikuti langkah-langkah berikut secara berurutan melalui **SSH Terminal di cPanel**.

---

## Langkah 1 — Ambil Kode Terbaru

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

> Perintah ini aman. Data yang sudah ada tidak akan terhapus.

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

## Langkah 7 — Verifikasi

- Buka URL aplikasi Azventory di browser
- Pastikan tidak ada halaman Error 500
- Login dengan akun Superadmin dan cek semua fitur berjalan normal
