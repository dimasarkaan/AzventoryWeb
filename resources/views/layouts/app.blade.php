<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
        <style>
            /* Solid Premium Toast Override */
            
            /* GLOBAL CONTAINER OVERRIDE for Toasts */
            div.swal2-container.swal2-top-end,
            div.swal2-container.swal2-top-right {
                align-items: flex-start !important;
                justify-content: flex-end !important;
            }

            div.swal2-popup.swal2-toast.solid-toast-popup {
                /* Visual Style */
                background: #ffffff !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
                border: 1px solid #f3f4f6 !important;
                border-left: 4px solid #3b82f6 !important; /* Thinner accent */
                border-radius: 6px !important; /* Slightly sharper */
                padding: 0.75rem 1rem !important; /* Compact padding */
                
                /* Layout */
                display: flex !important; /* Back to flex for simplicity */
                align-items: center !important;
                gap: 0.75rem !important;
                
                /* Sizing */
                width: auto !important;
                min-width: 250px !important; /* Smaller min-width */
                max-width: 350px !important;
                
                /* Positioning */
                margin: 1rem !important;
                transform: translateY(0) !important;
            }

            /* Custom Icon Styling */
            div.swal2-popup.swal2-toast.solid-toast-popup .swal2-icon {
                border: none !important; /* Remove circle border */
                margin: 0 !important;
                min-width: 20px !important;
                width: 20px !important;
                height: 20px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                background: transparent !important;
            }
            
            /* Remove check for standard classes instead of wildcard to avoid hiding our SVG */
            div.swal2-popup.swal2-toast.solid-toast-popup .swal2-icon .swal2-success-ring,
            div.swal2-popup.swal2-toast.solid-toast-popup .swal2-icon .swal2-success-line-tip,
            div.swal2-popup.swal2-toast.solid-toast-popup .swal2-icon .swal2-success-line-long,
            div.swal2-popup.swal2-toast.solid-toast-popup .swal2-icon .swal2-success-circular-line-left,
            div.swal2-popup.swal2-toast.solid-toast-popup .swal2-icon .swal2-success-circular-line-right,
            div.swal2-popup.swal2-toast.solid-toast-popup .swal2-icon .swal2-success-fix,
            div.swal2-popup.swal2-toast.solid-toast-popup .swal2-icon .swal2-x-mark {
                display: none !important;
            }
            
            /* Success Variation */
            div.swal2-popup.swal2-toast.solid-toast-popup.toast-success {
                border-left-color: #10b981 !important;
            }
            div.swal2-popup.swal2-toast.solid-toast-popup.toast-success .swal2-icon {
                color: #10b981 !important;
            }

            /* Error Variation */
            div.swal2-popup.swal2-toast.solid-toast-popup.toast-error {
                border-left-color: #ef4444 !important;
            }
            div.swal2-popup.swal2-toast.solid-toast-popup.toast-error .swal2-icon {
                color: #ef4444 !important;
            }

            /* Warning Variation */
            div.swal2-popup.swal2-toast.solid-toast-popup.toast-warning {
                border-left-color: #f59e0b !important;
            }
            div.swal2-popup.swal2-toast.solid-toast-popup.toast-warning .swal2-icon {
                color: #f59e0b !important;
            }
            div.swal2-popup.swal2-toast.solid-toast-popup.toast-warning .swal2-timer-progress-bar {
                background: #f59e0b !important;
            }

            /* Typography */
            div.swal2-popup.swal2-toast.solid-toast-popup .swal2-title {
                color: #374151 !important;
                font-family: 'Inter', sans-serif !important;
                font-size: 0.875rem !important;
                font-weight: 500 !important;
                margin: 0 !important;
                text-align: left !important;
                line-height: 1.25 !important;
            }

            /* Progress Bar Styling */
            div.swal2-popup.swal2-toast.solid-toast-popup .swal2-timer-progress-bar {
                height: 2px !important;
                bottom: 0 !important;
            }

            div.swal2-popup.swal2-toast.solid-toast-popup.toast-success .swal2-timer-progress-bar {
                background: #10b981 !important;
            }
            div.swal2-popup.swal2-toast.solid-toast-popup.toast-error .swal2-timer-progress-bar {
                background: #ef4444 !important;
            }

            /* Hide unnecessary elements */
            div.swal2-popup.swal2-toast.solid-toast-popup .swal2-close {
                display: none !important;
            }
        </style>
        <script>
            // Solid Premium Toast Config
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end', // Moved to top-right for standard feel
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                customClass: {
                    popup: 'solid-toast-popup',
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Handle Flash Messages
            @if(session('success'))
                Toast.fire({
                    icon: 'success', // Keep for fallback a11y, though visual is overridden
                    iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                    title: "{{ session('success') }}",
                    customClass: {
                        popup: 'solid-toast-popup toast-success',
                    }
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 15.75h.008v.008H12v-.008z" /></svg>',
                    title: "{{ session('error') }}",
                    customClass: {
                        popup: 'solid-toast-popup toast-error',
                    }
                });
            @endif

            @if(session('warning'))
                Toast.fire({
                    icon: 'warning',
                    iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>',
                    title: "{{ session('warning') }}",
                    customClass: {
                        popup: 'solid-toast-popup toast-warning',
                    }
                });
            @endif

            // Global Delete Confirmation
            function confirmDelete(event) {
                event.preventDefault();
                const form = event.target.closest('form');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak akan bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: '!rounded-2xl !font-sans',
                        title: '!text-secondary-900 !text-xl !font-bold',
                        htmlContainer: '!text-secondary-500 !text-sm',
                        confirmButton: 'btn btn-danger px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200',
                        cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                    },
                    buttonsStyling: false,
                    width: '24em',
                    iconColor: '#ef4444',
                    padding: '2em',
                    backdrop: `rgba(0,0,0,0.4)`
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            }
        </script>
    </body>
</html>
