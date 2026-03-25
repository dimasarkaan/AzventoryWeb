<!DOCTYPE html>
<html>
<head>
    <title>Laporan Bulanan Azventory</title>
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #333; }
        .container { padding: 20px; max-width: 600px; margin: auto; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { background: #1e40af; color: white; padding: 15px; border-radius: 6px 6px 0 0; text-align: center; }
        .content { padding: 20px; }
        .footer { font-size: 12px; color: #718096; text-align: center; margin-top: 20px; }
        .highlight { font-weight: bold; color: #1e40af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Azventory</h1>
        </div>
        <div class="content">
            <p>Halo <span class="highlight">{{ $user->name }}</span>,</p>
            <p>Berikut adalah laporan bulanan sistem <span class="highlight">Azventory</span> untuk periode bulan <span class="highlight">{{ $monthName }}</span>.</p>
            <p>Laporan ini mencakup:</p>
            <ul>
                <li>Daftar Inventaris Terkini</li>
                <li>Riwayat Mutasi Stok (Bulan Lalu)</li>
                <li>Riwayat Peminjaman (Bulan Lalu)</li>
                <li>Laporan Stok Menipis</li>
            </ul>
            <p>Silakan periksa lampiran pada email ini untuk detail selengkapnya (dalam format Excel).</p>
            <p>Terima kasih atas dedikasi Anda dalam menggunakan sistem ini.</p>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem Azventory.</p>
        </div>
    </div>
</body>
</html>
