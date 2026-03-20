import Swal from 'sweetalert2';

// Solid Premium Toast Config
window.Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true,
    customClass: {
        popup: 'solid-toast-popup',
    },
    didOpen: (toast) => {
        // Pastikan toast selalu di atas modal (z-[100] = 100)
        const container = toast.closest('.swal2-container');
        if (container) container.style.zIndex = '99999';
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// Global helper agar bisa dipanggil dari mana saja (menggunakan Native Alpine Toast)
window.showToast = (type, message) => {
    window.dispatchEvent(new CustomEvent('notify', {
        detail: { type: type, message: message }
    }));
};

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

// Handle Flash Messages from Global Variable (Menggunakan Native Alpine Toast)
document.addEventListener('DOMContentLoaded', () => {
    if (window.flashMessages) {
        if (window.flashMessages.success) {
            window.showToast('success', window.flashMessages.success);
        }

        if (window.flashMessages.error) {
            window.showToast('error', window.flashMessages.error);
        }

        if (window.flashMessages.warning) {
            window.showToast('warning', window.flashMessages.warning);
        }

        if (window.flashMessages.info) {
            window.showToast('info', window.flashMessages.info);
        }
    }
});
