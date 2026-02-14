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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-secondary-900 antialiased bg-white">
        <div class="min-h-screen flex">
            <!-- Left Side - Content/Branding -->
            <div class="hidden lg:flex w-1/2 bg-primary-600 relative overflow-hidden items-center justify-center p-12">
                 <!-- Background Circle Decoration -->
                <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-primary-500 opacity-50 blur-3xl"></div>
                <div class="absolute bottom-0 right-0 w-[500px] h-[500px] rounded-full bg-primary-700 opacity-50 blur-3xl"></div>
                
                <div class="relative z-10 text-white max-w-lg">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30 shadow-lg">
                            <span class="text-2xl font-bold">A</span>
                        </div>
                        <h1 class="text-4xl font-bold tracking-tight">Azventory</h1>
                    </div>
                    <h2 class="text-3xl font-bold leading-tight mb-6">{{ __('ui.guest_welcome_title') }}</h2>
                    <p class="text-primary-100 text-lg leading-relaxed">
                        {{ __('ui.guest_welcome_desc') }}
                    </p>
                    
                     <div class="mt-12 flex gap-4 text-sm font-medium text-primary-200">
                        <div class="flex items-center gap-2">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                             <span>{{ __('ui.guest_feature_stock') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                             <span>{{ __('ui.guest_feature_qr') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-background">
                <div class="w-full max-w-md">
                     <!-- Mobile Logo (Visible only on small screens) -->
                    <div class="flex lg:hidden justify-center mb-8">
                         <a href="/" class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-lg bg-primary-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                A
                            </div>
                            <span class="text-2xl font-bold text-gray-900">Azventory</span>
                        </a>
                    </div>
                    
                    {{ $slot }}
                    
                    <div class="mt-8 text-center text-sm text-secondary-400">
                        &copy; {{ date('Y') }} Azventory Project.
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
