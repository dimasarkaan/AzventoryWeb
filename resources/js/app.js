import './bootstrap';
import './custom.js';
import './realtime-inventory.js'; // Real-time inventory updates

import Alpine from 'alpinejs';

window.Alpine = Alpine;


import Swal from 'sweetalert2';
window.Swal = Swal;

Alpine.start();

// PWA Service Worker Registration
if ('serviceWorker' in navigator && import.meta.env.PROD) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/build/sw.js', { scope: '/' })
            .then(reg => {
                console.log('SW registered:', reg);
            })
            .catch(err => {
                console.log('SW registration failed:', err);
            });
    });
}
