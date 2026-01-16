<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Azventory') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-secondary-900 antialiased bg-white selection:bg-primary-500 selection:text-white">
    <div class="relative min-h-screen">
        <!-- Background Gradients -->
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-primary-50/50 rounded-full blur-3xl opacity-50 transform translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-secondary-100/50 rounded-full blur-3xl opacity-50 transform -translate-x-1/4 translate-y-1/4"></div>
        </div>

        <!-- Navbar -->
        <nav class="fixed top-0 w-full z-50 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-secondary-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-primary-500/30">
                            A
                        </div>
                        <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-700 to-primary-500">Azventory</span>
                    </div>
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary shadow-lg shadow-primary-500/30">Masuk</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="relative z-10 pt-20">
            <!-- Hero Section -->
            <section class="min-h-[calc(100vh-80px)] flex items-center justify-center relative overflow-hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative w-full">
                    <div class="text-center max-w-5xl mx-auto">
                        <h1 class="text-5xl md:text-7xl font-extrabold text-secondary-900 leading-tight tracking-tight mb-8">
                            Solusi Manajemen Stok <br />
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-primary-400">Azzahra Computer</span>
                        </h1>
                        <p class="text-xl text-secondary-600 mb-10 leading-relaxed max-w-2xl mx-auto">
                            Platform terintegrasi untuk mengelola stok komputer, laptop, sparepart, dan tracking servis di Azzahra Computer Tegal.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-8 py-4 text-lg shadow-xl shadow-primary-500/20 hover:shadow-primary-500/40 transform hover:-translate-y-1 transition-all duration-200">
                                Masuk Aplikasi
                            </a>
                            <a href="#features" class="btn btn-secondary btn-lg px-8 py-4 text-lg bg-white shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                                Fitur Utama
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Grid -->
            <section id="features" class="py-20 bg-white/50 relative">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Card 1 -->
                        <div class="group p-8 bg-white rounded-2xl border border-secondary-200 shadow-sm hover:shadow-card hover:border-primary-200 transition-all duration-300">
                            <div class="w-14 h-14 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-secondary-900 mb-3">Manajemen Stok Terpusat</h3>
                            <p class="text-secondary-500 leading-relaxed">Pantau ketersediaan barang di berbagai lokasi gudang secara real-time dengan akurasi tinggi.</p>
                        </div>
                        
                        <!-- Card 2 -->
                        <div class="group p-8 bg-white rounded-2xl border border-secondary-200 shadow-sm hover:shadow-card hover:border-primary-200 transition-all duration-300">
                            <div class="w-14 h-14 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-secondary-900 mb-3">QR Code Scanner</h3>
                            <p class="text-secondary-500 leading-relaxed">Identifikasi aset instan dengan teknologi pemindaian QR Code yang terintegrasi langsung.</p>
                        </div>

                        <!-- Card 3 -->
                        <div class="group p-8 bg-white rounded-2xl border border-secondary-200 shadow-sm hover:shadow-card hover:border-primary-200 transition-all duration-300">
                            <div class="w-14 h-14 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-secondary-900 mb-3">Monitoring Aktivitas</h3>
                            <p class="text-secondary-500 leading-relaxed">Rekam jejak digital lengkap untuk setiap pergerakan barang (masuk/keluar) oleh pengguna.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-secondary-100 relative z-10">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 flex justify-center items-center">
                <p class="text-secondary-500 text-sm font-medium">
                    &copy; {{ date('Y') }} Azzahra Computer. dibuat oleh : dimasarkaan
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
