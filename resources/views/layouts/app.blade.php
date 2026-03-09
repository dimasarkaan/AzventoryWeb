<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
        <div class="min-h-screen bg-gray-100 transition-opacity duration-300 opacity-0"
             x-data="{}"
             x-init="$el.classList.remove('opacity-0')">
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
            <main>
                {{ $slot }}
            </main>
        </div>
        
        <x-spotlight-search />
        <x-toast />

        @stack('scripts')
        <script>
            // Teruskan Pesan Flash ke JS Kustom
            window.flashMessages = {
                @if(session('success'))
                    success: "{{ session('success') }}",
                @endif
                @if(session('error'))
                    error: "{{ session('error') }}",
                @endif
                @if(session('warning'))
                    warning: "{{ session('warning') }}",
                @endif
            };

            // Informasi User saat ini untuk filter realtime
            window.currentUser = {
                name: "{{ auth()->check() ? auth()->user()->name : '' }}"
            };
        </script>
    </body>
</html>
