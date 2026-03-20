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
            
            <!-- Global Offline Overlay (Triggered on Action) -->
            <div x-show="showOfflineOverlay" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[99999] flex items-center justify-center p-4 overflow-hidden"
                 x-cloak>
                
                <!-- Animated Background Blobs -->
                <div class="absolute inset-0 bg-slate-50/80 backdrop-blur-xl"></div>
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>

                <!-- Premium Glass Card -->
                <div class="relative bg-white/70 backdrop-blur-2xl border border-white/50 rounded-[2.5rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] p-8 md:p-12 max-w-lg w-full text-center transform transition-all duration-500"
                     x-show="showOfflineOverlay"
                     x-transition:enter="transition ease-out duration-500 delay-100"
                     x-transition:enter-start="scale-90 opacity-0 translate-y-8"
                     x-transition:enter-end="scale-100 opacity-100 translate-y-0">
                    
                    <!-- Icon with Pulse -->
                    <div class="relative w-24 h-24 mx-auto mb-8 bg-white rounded-3xl shadow-[0_12px_24px_-8px_rgba(37,99,235,0.2)] flex items-center justify-center">
                        <div class="absolute inset-0 bg-primary-500/20 rounded-3xl animate-ping"></div>
                        <svg class="w-12 h-12 text-primary-600 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m1.414 2.83l2.829-2.83m-2.829 2.83L3 21M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <h2 class="text-3xl font-extrabold text-slate-900 mb-4 tracking-tight leading-tight">
                        {{ __('ui.offline_title') }}
                    </h2>
                    <p class="text-slate-500 text-lg leading-relaxed mb-6">
                        {{ __('ui.offline_desc') }}
                    </p>

                    <div class="flex flex-col gap-3">
                        <button @click="if(navigator.onLine) { window.location.reload(); } else { showRetryError = true; setTimeout(() => showRetryError = false, 3000); }" 
                                class="group relative inline-flex items-center justify-center bg-primary-600 hover:bg-primary-700 text-white font-bold py-4 px-10 rounded-2xl transition-all duration-300 shadow-[0_20px_40px_-10px_rgba(37,99,235,0.4)] hover:shadow-[0_25px_50px_-12px_rgba(37,99,235,0.5)] hover:-translate-y-1 active:translate-y-0 w-full overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                            <svg class="w-5 h-5 mr-3 transition-transform group-hover:rotate-180 duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ __('ui.offline_retry') }}
                        </button>
                        
                        <button @click="showOfflineOverlay = false" 
                                class="text-slate-400 hover:text-slate-600 font-medium py-2 transition-colors">
                            Kembali Lihat Halaman
                        </button>
                    </div>

                    <div class="mt-8 opacity-30 flex items-center justify-center gap-2 font-medium grayscale">
                        <img src="{{ asset('logo.svg') }}" alt="Azventory" class="w-5">
                        <span class="text-sm">Azventory System</span>
                    </div>
                </div>
            </div>

            <!-- Offline Indicator Banner (Always Shown when Offline) -->
            <div x-show="isOffline" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="-translate-y-full"
                 x-transition:enter-end="translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0"
                 x-transition:leave-end="-translate-y-full"
                 class="relative z-[10000] bg-orange-500/95 backdrop-blur-md text-white text-center py-2.5 text-xs sm:text-sm font-bold shadow-lg border-b border-white/20"
                 x-cloak>
                <div class="flex items-center justify-center gap-2 px-4">
                    <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                    <span>{{ __('ui.offline_banner_text') }}</span>
                </div>
            </div>

            <!-- Retry Error Toast (Built-in Alpine) -->
            <div x-show="showRetryError" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-10 opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0 opacity-100"
                 x-transition:leave-end="translate-y-10 opacity-0"
                 class="fixed bottom-24 sm:bottom-12 left-1/2 -translate-x-1/2 z-[100000] w-[calc(100%-2rem)] max-w-sm bg-slate-900/90 backdrop-blur-xl text-white px-5 py-4 rounded-2xl shadow-2xl flex items-center gap-3 border border-white/10"
                 x-cloak>
                <div class="flex-shrink-0 w-2.5 h-2.5 bg-orange-500 rounded-full animate-ping"></div>
                <span class="font-semibold tracking-wide text-sm sm:text-base">Koneksi masih terputus...</span>
            </div>

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
                @if(session('info'))
                    info: "{{ session('info') }}",
                @endif
            };

            // Informasi User saat ini untuk filter realtime
            window.currentUser = {
                name: "{{ auth()->check() ? auth()->user()->name : '' }}"
            };
        </script>
    </body>
</html>
