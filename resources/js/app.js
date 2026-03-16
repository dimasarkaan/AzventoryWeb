import './bootstrap';
import './custom.js';
import './realtime-inventory.js'; // Real-time inventory updates

import Alpine from 'alpinejs';

window.Alpine = Alpine;


import Swal from 'sweetalert2';
window.Swal = Swal;

Alpine.start();

// PWA Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', { scope: '/' });
    });
}
