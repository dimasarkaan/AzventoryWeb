@component('mail::message')
# Halo {{ $user->name }},

Berikut adalah laporan bulanan sistem **Azventory** untuk periode bulan **{{ $monthName }}**.

@if(!empty($summary))
@component('mail::panel')
### 📊 Ringkasan Dasbor (Cepat)
| Indikator | Statistik |
| :--- | :--- |
| **Total Barang Inventaris** | {{ $summary['total_items'] }} Item |
| **Peminjaman Aktif** | {{ $summary['active_borrowings'] }} Transaksi |
| **Barang Perlu Restock** | {{ $summary['low_stock_count'] }} Item |
| **Aktivitas Bulan Ini** | {{ $summary['monthly_activities'] }} Log |
@endcomponent
@endif

Laporan ini mencakup lampiran detail:
*   **Daftar Inventaris Terkini** (Stok & Lokasi)
*   **Riwayat Mutasi Stok** (Masuk/Keluar)
*   **Riwayat Peminjaman** (Lengkap dengan Status)
*   **Laporan Stok Menipis** (Prioritas Restock)
*   **Log Aktivitas Sistem** (Jejak Audit User)

Silakan periksa lampiran file Excel pada email ini untuk analisis lebih mendalam.

@component('mail::button', ['url' => route('dashboard'), 'color' => 'primary'])
Buka Dashboard Azventory
@endcomponent

Terima kasih atas dedikasi Anda dalam menjaga integritas data inventaris perusahaan.

Salam Hangat,<br>
**Tim Sistem {{ config('app.name') }}**
@endcomponent
