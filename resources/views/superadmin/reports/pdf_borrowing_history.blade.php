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
        th, td { border: 1px solid #000; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #1e40af; color: #ffffff; font-weight: bold; text-transform: uppercase; font-size: 9pt; text-align: center; }
        tr:nth-child(even) { background-color: #eff6ff; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 8pt; color: white; display: inline-block; font-weight: bold; }
        .borrowed { background-color: #3b82f6; } /* Blue */
        .returned { background-color: #10b981; } /* Green */
        .late { color: #dc2626; font-weight: bold; font-size: 9pt; display: block; margin-top: 2px; }
        .footer { text-align: right; margin-top: 30px; font-size: 8pt; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>AZVENTORY</h1>
        <p>Sistem Manajemen Inventaris & Stok Barang</p>
        <p><strong>{{ $title }}</strong></p>
        <div style="font-size: 10pt; margin-top: 10px;">
            <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} | User: {{ auth()->user()->name }}</p>
            @if(isset($startDate) && isset($endDate))
                <p>Periode: {{ $startDate->translatedFormat('d F Y') }} - {{ $endDate->translatedFormat('d F Y') }}</p>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Peminjam</th>
                <th style="width: 25%;">Barang</th>
                <th style="width: 12%;">Tgl Pinjam</th>
                <th style="width: 12%;">Jatuh Tempo</th>
                <th style="width: 12%;">Tgl Kembali</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Kondisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->user->name ?? $row->borrower_name }}</td>
                <td>
                    <b>{{ $row->sparepart->name ?? 'Deleted Item' }}</b>
                    <br><span style="color: #666; font-size: 8pt;">Jml: {{ $row->quantity }}</span>
                </td>
                <td>{{ $row->borrowed_at->translatedFormat('d F Y') }}</td>
                <td>
                    {{ $row->expected_return_at ? $row->expected_return_at->translatedFormat('d F Y') : '-' }}
                    @if($row->status == 'borrowed' && $row->expected_return_at && $row->expected_return_at < now())
                        <span class="late">Telat {{ (int) $row->expected_return_at->diffInDays(now()) }} hari</span>
                    @endif
                </td>
                <td>{{ $row->returned_at ? $row->returned_at->translatedFormat('d F Y H:i') : '-' }}</td>
                <td>
                    @php
                        $statusLabels = [
                            'borrowed' => 'Sedang Dipinjam',
                            'returned' => 'Dikembalikan',
                            'lost' => 'Hilang',
                        ];
                        $statusLabel = $statusLabels[$row->status] ?? ucfirst($row->status);
                    @endphp
                    <span class="badge {{ $row->status }}">{{ $statusLabel }}</span>
                </td>
                <td>
                    @php
                        $conditionLabels = [
                            'good' => 'Baik',
                            'broken' => 'Rusak',
                            'lost' => 'Hilang',
                        ];
                        $conditionLabel = $row->return_condition ? ($conditionLabels[$row->return_condition] ?? ucfirst($row->return_condition)) : '-';
                    @endphp
                    {{ $conditionLabel }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Azventory
    </div>
</body>
</html>
