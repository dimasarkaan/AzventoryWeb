<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title') - {{ config('app.name', 'Azventory') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="icon" href="{{ asset('logo.svg') }}?v=2" type="image/svg+xml">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-secondary-900 antialiased bg-secondary-50 flex items-center justify-center min-h-screen p-4 relative">
        
        <!-- Grid/Dot Pattern Background -->
        <div class="absolute inset-0 z-0 opacity-[0.3] pointer-events-none" style="background-image: radial-gradient(#94a3b8 1px, transparent 1px); background-size: 32px 32px;"></div>

        <div class="relative w-full max-w-lg bg-white shadow-2xl rounded-3xl overflow-hidden border border-secondary-100 z-10">
            
            <!-- Top Pattern/Color Section -->
            <div class="relative h-40 bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center overflow-hidden">
                <!-- Decorative Circles in Header -->
                <div class="absolute top-0 left-0 w-32 h-32 bg-white opacity-20 rounded-full mix-blend-overlay -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-48 h-48 bg-primary-200 opacity-20 rounded-full mix-blend-multiply translate-x-1/3 translate-y-1/3"></div>
                
                <!-- Wave SVG Separator -->
                <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none z-10">
                    <svg class="relative block w-[calc(100%+1.3px)] h-[50px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="fill-white"></path>
                    </svg>
                </div>
            </div>

            <!-- Main Content -->
            <div class="relative px-8 pb-10 text-center">
                
                <!-- Overlapping Icon -->
                <div class="relative -mt-20 mb-6 flex justify-center z-20">
                     @yield('image')
                </div>

                <h1 class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-primary-800 mb-2 font-outfit tracking-tight">
                    @yield('code')
                </h1>
                
                <h2 class="text-2xl font-bold text-secondary-800 mb-3 px-4">
                    @yield('message')
                </h2>
                
                <p class="text-secondary-500 mb-8 max-w-sm mx-auto leading-relaxed text-sm lg:text-base">
                    @yield('description')
                </p>

                <div class="flex items-center justify-center w-full px-4">
                    @php
                        $previous = url()->previous();
                        $backUrl = ($previous === url()->current() || $previous === '') ? url('/') : $previous;
                    @endphp
                    <a href="{{ $backUrl }}" class="btn btn-primary w-full sm:w-auto flex items-center justify-center gap-2 py-3 px-8 text-base font-medium shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 hover:-translate-y-1 transition-all duration-300">
                        <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        {{ __('ui.error_btn_back') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Identitas Brand (Watermark Footer) -->
        <div class="absolute bottom-6 w-full text-center pointer-events-none">
            <div class="flex items-center justify-center gap-2 opacity-60">
                <img src="{{ asset('logo.svg') }}" alt="Icon" class="h-5 w-auto grayscale" />
                <span class="text-xs font-bold text-secondary-500 tracking-widest uppercase">
                    Azzahra Computer
                </span>
            </div>
        </div>
    </body>
</html>
