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
        .badges { font-size: 8pt; padding: 2px 5px; border-radius: 4px; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
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
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Barang</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 15%;">Merk</th>
                <th style="width: 15%;">Lokasi</th>
                <th style="width: 10%;">Stok</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->name }}</strong><br>
                    <small style="color: #666;">PN: {{ $item->part_number }}</small>
                </td>
                <td>{{ $item->category }}</td>
                <td>{{ $item->brand }}</td>
                <td>{{ $item->location }}</td>
                <td style="text-align: center;">{{ $item->stock }} {{ $item->unit }}</td>
                <td>
                    @if($item->stock == 0)
                        <span class="badges badge-danger">Habis</span>
                    @elseif($item->minimum_stock > 0 && $item->stock <= $item->minimum_stock)
                        <span class="badges badge-warning">Menipis</span>
                    @else
                        <span class="badges badge-success">Aman</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->name }}</p>
    </div>
</body>
</html>
