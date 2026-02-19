import './bootstrap';
import './custom.js';
import './realtime-inventory.js'; // Real-time inventory updates

import Alpine from 'alpinejs';

window.Alpine = Alpine;


import Swal from 'sweetalert2';
window.Swal = Swal;

Alpine.start();
