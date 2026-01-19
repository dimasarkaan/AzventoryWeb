<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; color: #1e40af; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { bg-color: #f3f4f6; font-weight: bold; text-transform: uppercase; font-size: 8pt; }
        tr:nth-child(even) { bg-color: #f9fafb; }
        .text-success { color: #059669; font-weight: bold; }
        .text-danger { color: #dc2626; font-weight: bold; }
        .footer { text-align: right; margin-top: 30px; font-size: 8pt; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>AZVENTORY</h1>
        <p>Sistem Manajemen Inventaris & Stok Barang</p>
        <p><strong>{{ $title }}</strong></p>
        @if($startDate && $endDate)
            <p style="font-size: 10pt;">Periode: {{ $startDate->translatedFormat('d F Y') }} - {{ $endDate->translatedFormat('d F Y') }}</p>
        @else
            <p style="font-size: 10pt;">Periode: Semua Riwayat</p>
        @endif
        <p style="font-size: 10pt;">Lokasi: {{ $location == 'all' ? 'Semua Lokasi' : $location }}</p>
        <p style="font-size: 9pt;">Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 25%;">Barang</th>
                <th style="width: 10%;">Tipe</th>
                <th style="width: 10%; text-align: right;">Jumlah</th>
                <th style="width: 25%;">Keterangan</th>
                <th style="width: 15%;">User</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $log)
            <tr>
                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <strong>{{ $log->sparepart->name ?? 'Unknown' }}</strong><br>
                    <small>PN: {{ $log->sparepart->part_number ?? '-' }}</small>
                </td>
                <td>
                    @if($log->type == 'masuk')
                        <span style="color: green;">MASUK</span>
                    @else
                        <span style="color: red;">KELUAR</span>
                    @endif
                </td>
                <td style="text-align: right;">
                    @if($log->type == 'masuk')
                        <span class="text-success">+{{ $log->quantity }}</span>
                    @else
                        <span class="text-danger">-{{ $log->quantity }}</span>
                    @endif
                </td>
                <td>{{ $log->reason }}</td>
                <td>{{ $log->user->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->name }}</p>
    </div>
</body>
</html>
