<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Code - {{ $sparepart->name }}</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            margin: 20px;
        }
        .container {
            border: 1px solid #ccc;
            padding: 20px;
            display: inline-block;
        }
        img {
            width: 200px;
            height: 200px;
        }
        h1 {
            margin: 0;
            font-size: 1.2em;
        }
        p {
            margin: 5px 0 0;
            font-size: 0.9em;
            color: #555;
        }
        @media print {
            body {
                margin: 0;
            }
            .no-print {
                display: none;
            }
            .container {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('storage/' . $sparepart->qr_code_path) }}" alt="QR Code">
        <h1>{{ $sparepart->name }}</h1>
        <p>{{ $sparepart->part_number }}</p>
    </div>

    <div class="no-print" style="margin-top: 20px;">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</body>
</html>
