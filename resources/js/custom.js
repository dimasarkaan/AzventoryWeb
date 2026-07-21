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

// Global Alert Helper (Consistent Styling)
window.showAlert = function(title, text, icon = 'info') {
    let btnClass = 'btn-primary ring-primary-500';
    if(icon === 'error') btnClass = 'btn-danger ring-danger-500';
    if(icon === 'warning') btnClass = 'btn-warning ring-warning-500';
    if(icon === 'success') btnClass = 'btn-success ring-success-500';
    
    return Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonText: 'Tutup',
        customClass: {
            popup: '!rounded-2xl !font-sans',
            title: '!text-secondary-900 !text-xl !font-bold',
            htmlContainer: '!text-secondary-500 !text-sm',
            confirmButton: `btn ${btnClass} px-6 py-2.5 rounded-lg shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2`
        },
        buttonsStyling: false,
        width: '24em',
        padding: '2em',
        backdrop: `rgba(0,0,0,0.4)`
    });
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
const initFlashes = () => {
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
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => setTimeout(initFlashes, 150));
} else {
    setTimeout(initFlashes, 150);
}
