@component('mail::message')
# Backup Database Berhasil Dibuat

Halo {{ $user->name }},

Berikut kami lampirkan file cadangan (**Database Backup**) otomatis untuk sistem **Azventory** Anda.

File berformat `.sql` ini merupakan rekam jejak lengkap sistem Anda saat ini, mencakup seluruh data barang, stok, riwayat peminjaman, hingga pengaturan aplikasi.

@component('mail::panel')
**Informasi Backup:**
- **Waktu Eksekusi:** {{ now()->format('d F Y H:i:s') }}
- **Nama File:** `{{ $filename }}`
- **Tindakan Lanjutan:** Silakan unduh dan simpan file ini di penyimpanan lokal atau *cloud drive* Anda sebagai langkah antisipasi.
@endcomponent

### 📝 Panduan Pemulihan (Restore) Data:
Jika suatu saat Anda perlu memulihkan sistem atau memindahkannya ke server baru, Anda cukup mengimpor file ini melalui menu **"Import"** di **phpMyAdmin** atau *database manager* pilihan Anda.

@component('mail::button', ['url' => route('dashboard')])
Buka Dashboard Azventory
@endcomponent

Salam hangat,<br>
**Tim {{ config('app.name') }}**
@endcomponent
