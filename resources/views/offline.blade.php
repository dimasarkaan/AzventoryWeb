<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline - Azventory</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            color: #1f2937;
            text-align: center;
        }
        .container {
            max-width: 400px;
            padding: 2rem;
            background: white;
            border-radius: 1rem;
            shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        img {
            width: 80px;
            margin-bottom: 1.5rem;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        p {
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('logo.svg') }}" alt="Azventory Logo">
        <h1>Anda Sedang Offline</h1>
        <p>Maaf, sepertinya koneksi internet Anda terputus. Beberapa fitur mungkin tidak tersedia sampai Anda terhubung kembali.</p>
        <a href="/" class="btn">Coba Lagi</a>
    </div>
</body>
</html>
