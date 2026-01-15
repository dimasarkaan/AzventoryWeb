<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Azventory') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-secondary-800 antialiased">
    <div class="min-h-screen bg-white">
        <!-- Navbar -->
        <nav class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex-shrink-0">
                        <a href="/" class="text-2xl font-extrabold text-primary-600">Azventory</a>
                    </div>
                    <div>
                        <a href="{{ route('login') }}">
                            <x-button variant="primary">Masuk</x-button>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <!-- Hero Section -->
            <section class="py-20 md:py-32 bg-secondary-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div class="text-center md:text-left">
                            <h1 class="text-4xl md:text-5xl font-extrabold text-secondary-900 leading-tight mb-4">Sistem Inventaris Khusus untuk Azzahra Computer</h1>
                            <p class="text-lg text-secondary-600 mb-8">Azventory adalah sistem manajemen inventaris yang dirancang khusus untuk mengelola stok sparepart komputer di Azzahra Computer secara terpusat, rapi, dan efisien.</p>
                            <a href="{{ route('login') }}">
                                <x-button variant="primary" class="py-3 px-8 text-base">Mulai Kelola</x-button>
                            </a>
                        </div>
                        <div>
                            <img src="https://www.svgrepo.com/show/493422/computer-laptop-and-monitor.svg" alt="Inventory Illustration" class="w-full h-auto">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-extrabold text-secondary-900">Fitur Unggulan</h2>
                        <p class="mt-4 text-lg text-secondary-600">Semua yang Anda butuhkan dalam satu platform.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Feature 1 -->
                        <div class="p-8 border border-secondary-200 rounded-lg">
                            <h3 class="text-xl font-bold mb-2">Inventaris Terpusat</h3>
                            <p class="text-secondary-600">Kelola semua data sparepart dari berbagai lokasi dalam satu platform yang mudah diakses.</p>
                        </div>
                        <!-- Feature 2 -->
                        <div class="p-8 border border-secondary-200 rounded-lg">
                            <h3 class="text-xl font-bold mb-2">Identifikasi QR Code</h3>
                            <p class="text-secondary-600">Lacak dan identifikasi setiap barang dengan cepat dan akurat menggunakan teknologi QR Code.</p>
                        </div>
                        <!-- Feature 3 -->
                        <div class="p-8 border border-secondary-200 rounded-lg">
                            <h3 class="text-xl font-bold mb-2">Manajemen Multi-Role</h3>
                            <p class="text-secondary-600">Atur hak akses untuk Super Admin, Admin, dan Operator sesuai dengan tanggung jawab masing-masing.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-secondary-100 border-t border-secondary-200">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-secondary-600">&copy; {{ date('Y') }} Azventory. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>
