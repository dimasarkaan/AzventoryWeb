<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>
            @hasSection('title')
                @yield('title') - {{ config('app.name', 'Azventory') }}
            @else
                {{ config('app.name', 'Azventory') }} - Sistem Manajemen Stok
            @endif
        </title>

        <!-- Primary Meta Tags -->
        <meta name="title" content="@yield('title', config('app.name', 'Azventory') . ' - Sistem Manajemen Stok')">
        <meta name="description" content="@yield('description', 'Aplikasi digitalisasi pencatatan masuk, keluar, dan peminjaman stok di CV Azzahra Computer.')">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ request()->url() }}">
        <meta property="og:title" content="@yield('title', config('app.name', 'Azventory') . ' - Sistem Manajemen Stok')">
        <meta property="og:description" content="@yield('description', 'Sistem informasi manajemen stok barang untuk memantau ketersediaan, pemindaian QR Code, dan pergerakan aset gudang.')">
        <meta property="og:image" content="{{ asset('images/bannerazventory.png') }}">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ request()->url() }}">
        <meta property="twitter:title" content="@yield('title', config('app.name', 'Azventory') . ' - Sistem Manajemen Stok')">
        <meta property="twitter:description" content="@yield('description', 'Sistem informasi manajemen stok barang untuk memantau ketersediaan, pemindaian QR Code, dan pergerakan aset gudang.')">
        <meta property="twitter:image" content="{{ asset('images/bannerazventory.png') }}">

        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#2563eb">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Azventory">
        <link rel="apple-touch-icon" href="/logo.svg">
        <link rel="manifest" href="/build/manifest.webmanifest">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="icon" href="{{ asset('logo.svg') }}?v=2" type="image/svg+xml">

        <!-- Skrip -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-[99999] focus:p-4 focus:bg-white focus:text-primary-600 focus:font-bold">Skip to main content</a>
        <div class="min-h-screen bg-gray-100 transition-opacity duration-300 opacity-0"
             x-data="{ 
                isOffline: !navigator.onLine,
                showOfflineOverlay: false,
                showRetryError: false
             }"
             x-init="$el.classList.remove('opacity-0');
                     window.addEventListener('online', () => { isOffline = false; showOfflineOverlay = false; }); 
                     window.addEventListener('offline', () => isOffline = true);
                     
                     // Global Listener for Navigation while Offline
                     window.addEventListener('click', (e) => {
                         const link = e.target.closest('a');
                         if (link && isOffline && link.href && !link.href.startsWith('#') && !link.href.startsWith('javascript:')) {
                             e.preventDefault();
                             showOfflineOverlay = true;
                         }
                     }, true);
                     
                     window.addEventListener('submit', (e) => {
                         if (isOffline) {
                             e.preventDefault();
                             showOfflineOverlay = true;
                         }
                     }, true)">
            
            @include('layouts.partials.offline-overlay')

            @include('layouts.navigation')


            <!-- Judul Halaman -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Konten Halaman -->
            <main id="main-content">
                {{ $slot }}
            </main>
        </div>
        
        <x-pwa-install-prompt />
        <x-spotlight-search />
        <x-toast />

        @stack('scripts')
        <script>
            // Teruskan Pesan Flash ke JS Kustom
            window.flashMessages = {
                @if(session('success'))
                    success: @json(session('success')),
                @endif
                @if(session('error'))
                    error: @json(session('error')),
                @endif
                @if(session('warning'))
                    warning: @json(session('warning')),
                @endif
                @if(session('info'))
                    info: @json(session('info')),
                @endif
            };

            // Informasi User saat ini untuk filter realtime
            window.currentUser = {
                name: "{{ auth()->check() ? auth()->user()->name : '' }}"
            };
        </script>

        <!-- Back to Top Button -->
        <div x-data="{ showScrollTop: false }" 
             @scroll.window="showScrollTop = window.pageYOffset > 300"
             class="fixed bottom-24 sm:bottom-24 right-6 z-[90]">
            <button x-show="showScrollTop"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-8"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-8"
                    @click="window.scrollTo({top: 0, behavior: 'smooth'})"
                    class="p-3 bg-primary-600 text-white rounded-full shadow-[0_8px_30px_rgb(37,99,235,0.3)] hover:bg-primary-700 hover:shadow-[0_8px_30px_rgb(37,99,235,0.5)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all transform hover:-translate-y-1"
                    title="Kembali ke Atas"
                    x-cloak>
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
            </button>
        </div>
    </body>
</html>
