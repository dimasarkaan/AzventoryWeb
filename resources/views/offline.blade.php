<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('ui.offline_title') }} - Azventory</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --bg: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top right, #eff6ff 0%, #f8fafc 100%);
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
        }

        /* Background Decorations */
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(37, 99, 235, 0.05) 100%);
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
        }
        .blob-1 { top: -100px; right: -100px; }
        .blob-2 { bottom: -100px; left: -100px; }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 2rem;
            padding: 3rem 2rem;
            max-width: 440px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-container {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.1);
            position: relative;
        }

        .icon-pulse {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 1.5rem;
            background: var(--primary);
            opacity: 0.2;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.2; }
            70% { transform: scale(1.3); opacity: 0; }
            100% { transform: scale(1.3); opacity: 0; }
        }

        h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1rem;
            letter-spacing: -0.025em;
        }

        p {
            color: var(--secondary);
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary);
            color: white;
            font-weight: 600;
            padding: 0.875rem 2rem;
            border-radius: 1rem;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .logo-footer {
            margin-top: 3rem;
            opacity: 0.4;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="glass-card">
        <div class="icon-container">
            <div class="icon-pulse"></div>
            <svg class="w-12 h-12 text-primary-600" style="width: 48px; height: 48px; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m1.414 2.83l2.829-2.83m-2.829 2.83L3 21M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h1>{{ __('ui.offline_title') }}</h1>
        <p>{{ __('ui.offline_desc') }}</p>

        <button onclick="if(navigator.onLine) { window.location.reload(); } else { showToast(); }" class="btn-primary" style="border: none; cursor: pointer; width: 100%;">
            <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            {{ __('ui.offline_retry') }}
        </button>

        <div class="logo-footer">
            <img src="{{ asset('logo.svg') }}" alt="Azventory" style="width: 20px;">
            <span>Azventory System</span>
        </div>
    </div>

    <!-- Custom Toast -->
    <div id="offline-toast" style="position: fixed; bottom: 96px; left: 50%; transform: translateX(-50%) translateY(100px); background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(10px); color: white; padding: 14px 20px; border-radius: 1.25rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); display: flex; align-items: center; gap: 12px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); z-index: 1000; border: 1px solid rgba(255,255,255,0.1); font-weight: 600; width: calc(100% - 2rem); max-width: 360px; pointer-events: none; opacity: 0;">
        <div style="flex-shrink: 0; width: 10px; height: 10px; background: #f97316; border-radius: 50%; box-shadow: 0 0 10px rgba(249, 115, 22, 0.5);"></div>
        <span style="font-size: 14px; letter-spacing: 0.025em;">Koneksi masih terputus...</span>
    </div>

    <style>
        @media (min-width: 640px) {
            #offline-toast { bottom: 48px !important; }
        }
    </style>

    <script>
        function showToast() {
            const toast = document.getElementById('offline-toast');
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(-50%) translateY(0)';
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(-50%) translateY(100px)';
            }, 3000);
        }

        // Auto-refresh when back online
        window.addEventListener('online', () => {
            window.location.href = '/';
        });
    </script>
</body>
</html>

