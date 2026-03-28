@component('mail::message')
# Halo {{ $user->name }},

Ini adalah file cadangan (**Database Backup**) otomatis untuk sistem **Azventory** Anda.

File yang dilampirkan berformat `.sql` dan berisi seluruh data barang, stok, riwayat peminjaman, serta pengaturan sistem Anda saat ini.

@component('mail::panel')
**Informasi Backup:**
- **Waktu Pelaksanaan:** {{ now()->format('d F Y H:i:s') }}
- **Nama File:** `{{ $filename }}`
- **Tujuan:** Arsip Keamanan (Disarankan untuk diunduh dan disimpan di tempat aman).
@endcomponent

### 📝 Cara Menggunakan File Ini:
Jika suatu saat sistem Anda mengalami kerusakan data atau Anda berpindah hosting, Anda bisa langsung mengimpor file ini melalui menu **"Import"** di **phpMyAdmin** atau database manager lainnya.

@component('mail::button', ['url' => route('dashboard')])
Buka Dashboard Azventory
@endcomponent

*Keamanan data Anda adalah prioritas kami.*

Salam,<br>
**Robot Keamanan {{ config('app.name') }}**
@endcomponent
