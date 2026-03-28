@component('mail::message')
# Halo {{ $user->name }},

Berikut kami sampaikan laporan bulanan sistem **Azventory** untuk periode **{{ $monthName }}**.

@if(!empty($summary))
@component('mail::panel')
### 📊 Ringkasan Dasbor

<table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Inter', sans-serif;">
    <tr>
        <td style="padding: 10px 0; border-bottom: 1px solid #eef2f7;"><strong>Total Barang Inventaris</strong></td>
        <td align="right" style="padding: 10px 0; border-bottom: 1px solid #eef2f7;">{{ $summary['total_items'] }} item</td>
    </tr>
    <tr>
        <td style="padding: 10px 0; border-bottom: 1px solid #eef2f7;"><strong>Peminjaman Aktif</strong></td>
        <td align="right" style="padding: 10px 0; border-bottom: 1px solid #eef2f7;">{{ $summary['active_borrowings'] }} transaksi</td>
    </tr>
    <tr>
        <td style="padding: 10px 0; border-bottom: 1px solid #eef2f7;"><strong>Barang Perlu Restock</strong></td>
        <td align="right" style="padding: 10px 0; border-bottom: 1px solid #eef2f7; color: #ef4444;">{{ $summary['low_stock_count'] }} item</td>
    </tr>
    <tr>
        <td style="padding: 10px 0;"><strong>Aktivitas Bulan Ini</strong></td>
        <td align="right" style="padding: 10px 0;">{{ $summary['monthly_activities'] }} log</td>
    </tr>
</table>
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
