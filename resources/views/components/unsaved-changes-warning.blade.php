@once
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let isDirty = false;
        let targetUrl = null;
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            // Kecualikan form logout atau pencarian (GET)
            if (form.getAttribute('action') && form.getAttribute('action').includes('logout')) return;
            if (form.getAttribute('method') && form.getAttribute('method').toUpperCase() === 'GET') return;
            if (form.classList.contains('no-warn')) return; // Tambahan fleksibilitas

            form.addEventListener('input', () => {
                isDirty = true;
            });

            form.addEventListener('change', () => {
                isDirty = true;
            });

            form.addEventListener('submit', () => {
                isDirty = false;
            });
        });

        // Intercept klik link internal
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            // Cek apakah link internal dan bukan download/void
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:void') || link.hasAttribute('download')) return;
            
            // Pastikan link menuju domain yang sama
            if (link.hostname !== window.location.hostname) return;

            if (isDirty) {
                e.preventDefault();
                targetUrl = href;
                // Dispatch event Alpine untuk membuka modal
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'confirm-leave-page' }));
            }
        });

        // Handle konfirmasi di dalam modal
        const confirmBtn = document.getElementById('confirm-leave-button');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                isDirty = false;
                if (targetUrl) window.location.href = targetUrl;
            });
        }

        // Fallback untuk penutupan tab/browser (tetap dialog browser)
        window.addEventListener('beforeunload', (e) => {
            if (isDirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    });
</script>
@endpush

<!-- Modal Konfirmasi Unsaved Changes -->
<x-modal name="confirm-leave-page" focusable>
    <div class="p-6">
        <div class="mb-4 flex items-center gap-3 text-warning-600">
            <div class="p-2 bg-warning-50 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-secondary-900">
                {{ __('Perubahan Belum Disimpan') }}
            </h2>
        </div>

        <p class="text-secondary-600 leading-relaxed">
            {{ __('Anda memiliki perubahan yang belum disimpan di halaman ini. Jika Anda pergi sekarang, data yang Anda ketik akan hilang.') }}
            <br><br>
            {{ __('Apakah Anda tetap ingin meninggalkan halaman ini?') }}
        </p>

        <div class="mt-8 flex justify-end gap-3">
            <button type="button" class="btn btn-secondary px-6" x-on:click="$dispatch('close')">
                {{ __('Tetap di Sini') }}
            </button>
            <button type="button" id="confirm-leave-button" class="btn btn-danger px-6">
                {{ __('Tinggalkan Halaman') }}
            </button>
        </div>
    </div>
</x-modal>

@endonce
