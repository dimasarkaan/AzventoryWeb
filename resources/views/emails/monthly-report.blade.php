@component('mail::message')
# Halo {{ $user->name }},

Berikut kami sampaikan laporan bulanan sistem **Azventory** untuk periode **{{ $monthName }}**.

@if(!empty($summary))
@component('mail::panel')
### 📊 Ringkasan Dasbor

| Indikator | Statistik |
| :--- | :--- |
| **Total Barang Inventaris** | {{ $summary['total_items'] }} item |
| **Peminjaman Aktif** | {{ $summary['active_borrowings'] }} transaksi |
| **Barang Perlu Restock** | {{ $summary['low_stock_count'] }} item |
| **Aktivitas Bulan Ini** | {{ $summary['monthly_activities'] }} log |
@endcomponent
@endif

Lampiran mencakup detail inventaris, mutasi stok, peminjaman, serta log aktivitas pengguna. Silakan lihat lampiran file Excel untuk detail lengkap.

@component('mail::button', ['url' => route('dashboard'), 'color' => 'primary'])
Akses Dashboard Azventory
@endcomponent

Terima kasih.

<hr style="border:none; border-top:1px solid #e2e8f0; margin: 30px 0;">

Salam,<br>
**Tim Sistem {{ config('app.name') }}**
@endcomponent
