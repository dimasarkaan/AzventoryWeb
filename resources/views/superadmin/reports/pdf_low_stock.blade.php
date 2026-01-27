<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #dc2626; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; color: #dc2626; } /* Red for warning */
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #991b1b; color: #ffffff; font-weight: bold; text-transform: uppercase; font-size: 9pt; text-align: center; } /* Dark Red */
        tr:nth-child(even) { background-color: #fef2f2; }
        .critical { color: #dc2626; font-weight: bold; }
        .footer { text-align: right; margin-top: 30px; font-size: 8pt; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="text-transform: uppercase;">{{ $title }}</h1>
        <p style="font-size: 10pt; margin-top: 5px;">
            Periode: Saat Ini
            &nbsp; | &nbsp;
            Lokasi: {{ $location == 'all' ? 'Semua Gudang' : $location }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kode Part</th>
                <th style="width: 30%;">Nama Barang</th>
                <th style="width: 20%;">Lokasi</th>
                <th style="width: 10%;">Sisa Stok</th>
                <th style="width: 10%;">Min. Stok</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->part_number }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->location }}</td>
                <td class="critical" style="text-align: center; font-size: 11pt;">{{ $row->stock }}</td>
                <td style="text-align: center;">{{ $row->minimum_stock }}</td>
                <td class="critical">KRITIS</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Azventory &bull; Dicetak oleh {{ auth()->user()->name }} pada {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>
</body>
</html>
