import './bootstrap';
import './custom.js';
import './realtime-inventory.js'; // Real-time inventory updates

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;


import Swal from 'sweetalert2';
window.Swal = Swal;

// Global Tooltip System (Vanilla JS Event Delegation)
document.addEventListener('DOMContentLoaded', () => {
    let tooltipEl = null;

    const createTooltip = (text, targetEl) => {
        if (!text) return;
        
        // Remove native title to prevent double tooltip
        targetEl.setAttribute('data-original-title', text);
        targetEl.removeAttribute('title');

        tooltipEl = document.createElement('div');
        tooltipEl.className = 'fixed z-[99999] px-2.5 py-1.5 text-xs font-semibold text-white bg-secondary-900 rounded-lg shadow-xl pointer-events-none transform scale-95 opacity-0 transition-all duration-150 ease-out whitespace-nowrap border border-secondary-700';
        tooltipEl.innerText = text;
        document.body.appendChild(tooltipEl);
        
        const rect = targetEl.getBoundingClientRect();
        tooltipEl.style.left = `${rect.left + (rect.width / 2) - (tooltipEl.offsetWidth / 2)}px`;
        tooltipEl.style.top = `${rect.top - tooltipEl.offsetHeight - 8}px`;
        
        const currentTooltip = tooltipEl;
        requestAnimationFrame(() => {
            if (currentTooltip) {
                currentTooltip.classList.remove('scale-95', 'opacity-0');
                currentTooltip.classList.add('scale-100', 'opacity-100');
            }
        });
    };

    const removeTooltip = () => {
        if (tooltipEl) {
            tooltipEl.classList.remove('scale-100', 'opacity-100');
            tooltipEl.classList.add('scale-95', 'opacity-0');
            const toRemove = tooltipEl;
            setTimeout(() => { if (toRemove.parentNode) toRemove.remove(); }, 150);
            tooltipEl = null;
        }
    };

    document.body.addEventListener('mouseover', (e) => {
        const target = e.target.closest('[title], [data-original-title]');
        if (!target) return;
        
        const title = target.getAttribute('title') || target.getAttribute('data-original-title');
        if (title) {
            createTooltip(title, target);
        }
    });

    document.body.addEventListener('mouseout', (e) => {
        const target = e.target.closest('[data-original-title]');
        if (target) removeTooltip();
    });

    document.body.addEventListener('click', () => {
        removeTooltip();
    });
});

Alpine.start();

// PWA Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', { scope: '/' });
    });
}
