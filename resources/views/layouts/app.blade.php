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
            .colored-toast.swal2-icon-success {
                background-color: #a5dc86 !important;
            }
            .colored-toast.swal2-icon-error {
                background-color: #f27474 !important;
            }
            .colored-toast.swal2-icon-warning {
                background-color: #f8bb86 !important;
            }
            .colored-toast.swal2-icon-info {
                background-color: #3fc3ee !important;
            }
            .colored-toast.swal2-icon-question {
                background-color: #87adbd !important;
            }
            .colored-toast .swal2-title {
                color: white;
            }
            .colored-toast .swal2-close {
                color: white;
            }
            .colored-toast .swal2-html-container {
                color: white;
            }
            
            /* Modern Clean Toast Override */
            /* Modern Clean Toast Override */
            div.swal2-container.swal2-top.swal2-backdrop-show {
                background: transparent !important;
                overflow: visible !important;
                padding: 2rem !important;
                display: flex !important;
                justify-content: center !important;
                z-index: 9999 !important;
            }
            div.swal2-popup.swal2-toast.clean-toast-popup {
                background-color: #ffffff !important;
                border: 1px solid #e5e7eb !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                border-radius: 1rem !important;
                padding: 0.75rem 1.25rem !important;
                display: flex !important;
                align-items: center !important;
                justify-content: flex-start !important;
                gap: 0.8rem !important;
                
                /* Sizing & Position */
                width: auto !important;
                min-width: 350px !important;
                max-width: 90vw !important;
                margin: 0.5rem auto !important;
                overflow: hidden !important;
            }
            
            /* Clean White Style */
            div.swal2-popup.swal2-toast.clean-toast-popup.toast-success {
                border-left: none !important;
            }
            div.swal2-popup.swal2-toast.clean-toast-popup.toast-error {
                border-left: none !important;
            }

            div.swal2-popup.swal2-toast.clean-toast-popup .swal2-title {
                color: #111827 !important;
                font-family: inherit !important;
                font-size: 0.95rem !important;
                font-weight: 600 !important;
                margin: 0 !important;
                text-align: left !important;
                white-space: nowrap !important;
                letter-spacing: -0.01em !important;
                padding-right: 0.5rem !important;
                
                /* Layout Fixes */
                flex: 0 0 auto !important;
                width: auto !important;
            /* Modern Clean Toast Override */
            div.swal2-container.swal2-top.swal2-backdrop-show {
                background: transparent !important;
                overflow: visible !important;
                padding: 2rem !important;
                display: flex !important;
                justify-content: center !important;
                z-index: 9999 !important;
            }
            div.swal2-popup.swal2-toast.clean-toast-popup {
                background-color: #ffffff !important;
                border: 1px solid #e5e7eb !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                border-radius: 1rem !important;
                padding: 0.75rem 1.25rem !important;
                
                /* GRID LAYOUT - The Ultimate Wrapper */
                display: grid !important;
                grid-template-columns: min-content 1fr !important;
                align-items: center !important;
                gap: 0.75rem !important;
                
                /* Sizing & Position */
                width: auto !important;
                min-width: 350px !important;
                max-width: 90vw !important;
                margin: 0.5rem auto !important;
                overflow: hidden !important;
            }
            
            /* Clean White Style */
            div.swal2-popup.swal2-toast.clean-toast-popup.toast-success {
                border-left: none !important;
            }
            div.swal2-popup.swal2-toast.clean-toast-popup.toast-error {
                border-left: none !important;
            }

            div.swal2-popup.swal2-toast.clean-toast-popup .swal2-title {
                grid-column: 2 !important; /* Force to second column */
                color: #111827 !important;
                font-family: inherit !important;
                font-size: 0.95rem !important;
                font-weight: 600 !important;
                margin: 0 !important;
                text-align: left !important;
                
                /* Wrapping Fixes */
                white-space: normal !important;
                word-wrap: break-word !important;
                word-break: break-word !important;
                line-height: 1.5 !important;
                letter-spacing: -0.01em !important;
                padding-right: 0 !important;
                
                width: 100% !important;
                min-width: 0 !important;
                overflow: visible !important;
            }
            
            /* Icon Styling - SINGLE CLEAN CIRCLE */
            div.swal2-popup.swal2-toast.clean-toast-popup .swal2-icon {
                grid-column: 1 !important; /* Force to first column */
                /* Set base font size for EM calculations */
                font-size: 12px !important; 
                margin: 0 !important;
                width: 3.5em !important; /* ~42px */
                height: 3.5em !important;
                min-width: 3.5em !important;
                border-width: 0.25em !important; /* Proper stroke thickness */
                border-style: solid !important;
                border-radius: 50% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                flex-shrink: 0 !important;
                transform: none !important;
                background: transparent !important;
            }

            /* Fix disconnected lines by forcing geometry */
            div.swal2-popup.swal2-toast.clean-toast-popup .swal2-icon .swal2-success-line-tip,
            div.swal2-popup.swal2-toast.clean-toast-popup .swal2-icon .swal2-success-line-long {
                background-color: #10b981 !important; /* Force Green */
                display: block !important;
                z-index: 2 !important;
            }
            
            /* Hide artifacts */
            div.swal2-popup.swal2-toast.clean-toast-popup .swal2-success-ring { display: none !important; }
            div.swal2-popup.swal2-toast.clean-toast-popup .swal2-success-fix { display: none !important; }
            div.swal2-popup.swal2-toast.clean-toast-popup .swal2-success-circular-line-left { display: none !important; }
            div.swal2-popup.swal2-toast.clean-toast-popup .swal2-success-circular-line-right { display: none !important; }
            
            /* Color overrides */
            div.swal2-popup.swal2-toast.clean-toast-popup .swal2-icon.swal2-success {
                border-color: #10b981 !important;
                color: #10b981 !important;
            }
             div.swal2-popup.swal2-toast.clean-toast-popup .swal2-icon.swal2-error {
                border-color: #ef4444 !important;
                color: #ef4444 !important;
            }
            
            /* Inner Lines Size Adjustment */
            div.swal2-popup.swal2-toast.clean-toast-popup .swal2-icon .swal2-success-line-tip,
            div.swal2-popup.swal2-toast.clean-toast-popup .swal2-icon .swal2-success-line-long {
                height: 0.35em !important; /* Scales with font-size */
            }
            
            div.swal2-popup.swal2-toast.clean-toast-popup .swal2-timer-progress-bar {
                grid-column: 1 / -1 !important;
                position: absolute !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                height: 4px !important;
                z-index: 10 !important;
            }
            div.swal2-popup.swal2-toast.clean-toast-popup.toast-success .swal2-timer-progress-bar {
                background: #10b981 !important;
                opacity: 0.2 !important;
            }
            div.swal2-popup.swal2-toast.clean-toast-popup.toast-error .swal2-timer-progress-bar {
               background: #ef4444 !important;
               opacity: 0.2 !important;
            }
        </style>
        <script>
            // Premium Modern Toast Config
            const Toast = Swal.mixin({
                toast: true,
                position: 'top', // Center Top
                showConfirmButton: false,
                timer: 4000, // Back to 4s as center is more noticeable
                timerProgressBar: true,
                customClass: {
                    popup: 'clean-toast-popup', // Use our custom CSS
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Handle Flash Messages
            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}",
                    customClass: {
                        popup: 'clean-toast-popup toast-success', // Added variation class
                    }
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}",
                    customClass: {
                        popup: 'clean-toast-popup toast-error', // Added variation class
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
