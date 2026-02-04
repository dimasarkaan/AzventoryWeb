import Swal from 'sweetalert2';

// Solid Premium Toast Config
window.Toast = Swal.mixin({
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

// Global Delete Confirmation
window.confirmDelete = function (event) {
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
};

// Handle Flash Messages from Global Variable
document.addEventListener('DOMContentLoaded', () => {
    if (window.flashMessages) {
        if (window.flashMessages.success) {
            window.Toast.fire({
                icon: 'success',
                iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                title: window.flashMessages.success,
                customClass: {
                    popup: 'solid-toast-popup toast-success',
                }
            });
        }

        if (window.flashMessages.error) {
            window.Toast.fire({
                icon: 'error',
                iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 15.75h.008v.008H12v-.008z" /></svg>',
                title: window.flashMessages.error,
                customClass: {
                    popup: 'solid-toast-popup toast-error',
                }
            });
        }

        if (window.flashMessages.warning) {
            window.Toast.fire({
                icon: 'warning',
                iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>',
                title: window.flashMessages.warning,
                customClass: {
                    popup: 'solid-toast-popup toast-warning',
                }
            });
        }
    }
});
