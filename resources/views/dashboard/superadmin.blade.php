<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" crossorigin="anonymous">
        <style>
            /* --- Flatpickr Premium Theme (Figma Auto Layout) --- */
            .flatpickr-calendar { 
                background: #ffffff; 
                border: 1px solid #f1f5f9; 
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02); 
                border-radius: 28px; 
                z-index: 99999 !important;
                padding: 24px; 
                font-family: inherit;
                width: 380px !important; 
                max-width: 95vw !important; /* Mobile Fix */
                overflow: visible !important; 
                margin-top: 12px !important; /* Proper spacing from input */
                position: absolute; /* Auto fallback bila CDN lambat */
            }

            /* Mencegah kalender ngablak / bocor jika CDN JSDelivr diblokir atau telat me-load */
            .flatpickr-calendar:not(.open):not(.inline) {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
            }

            @media (max-width: 480px) {
                .flatpickr-calendar {
                    padding: 16px 12px !important;
                    width: 320px !important;
                    border-radius: 20px;
                }
                .custom-month-selector {
                    min-width: 65px !important;
                    padding: 0 8px !important;
                    font-size: 0.8rem !important;
                    justify-content: center !important;
                }
                .numInputWrapper {
                    width: 70px !important;
                    grid-column: span 1 / span 1 !important;
                }
                .stat-card {
                    padding: 16px !important;
                }
                .flatpickr-months {
                    gap: 8px !important;
                }
            }
            
            /* --- Export Mode Styles (PDF & PNG) --- */
            body.is-exporting, .is-exporting .min-h-screen {
                background-color: #ffffff !important;
            }
            .is-exporting .export-hide {
                display: none !important;
            }
            .is-exporting .export-show {
                display: block !important;
                margin-bottom: 24px;
            }
            .is-exporting .max-w-7xl {
                max-width: 100% !important;
                padding: 20px !important;
            }
            @media print {
                body, .min-h-screen, .bg-gray-100 { 
                    background-color: #ffffff !important; 
                    -webkit-print-color-adjust: exact !important; 
                    print-color-adjust: exact !important; 
                }
                
                /* Sembunyikan elemen skeleton, navigasi, dan elemen non-cetak */
                nav, header, form, button, .btn, .no-print, .animate-pulse { display: none !important; }
                .export-hide { display: none !important; }

                /* Aturan Print Table Header agar berulang di setiap halaman */
                thead.export-show { display: table-header-group !important; }
                tfoot.export-show { display: table-footer-group !important; }
                .export-show { display: block !important; }
                .print-container { display: table !important; width: 100% !important; }

                /* Mencegah grid collapse */
                .max-w-7xl { max-width: none !important; margin: 0 !important; padding: 0 !important; }

                /* Mencegah grafik mencetak terlalu besar */
                canvas { max-height: 280px !important; width: auto !important; margin: 0 auto !important; }
                .card-body.min-h-\[300px\] { min-height: 280px !important; }
                
                @page { 
                    margin: 12mm; 
                    size: auto; /* Mencegah browser print dialog menambahkan header/footer bawaan (URL, tgl) */
                }
            }
            
            /* Normalisasi tabel menjadi block di layar monitor agar layout CSS Grid Tailwind tidak rusak */
            @media screen {
                table.print-container, 
                table.print-container > tbody, 
                table.print-container > tbody > tr, 
                table.print-container > tbody > tr > td {
                    display: block; 
                    width: 100%;
                }
                /* Pastikan header dan footer cetak benar-benar tersembunyi di layar reguler */
                table.print-container > thead, 
                table.print-container > tfoot {
                    display: none;
                }
            }
            
            /* FORCE ALL INTERNAL CONTAINERS TO ALLOW DROPDOWN OVERLAP & CENTERING */
            .flatpickr-innerContainer {
                display: flex !important;
                justify-content: center !important;
            }
            .flatpickr-rContainer {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                max-width: 100% !important;
                overflow: visible !important;
            }
            .flatpickr-days, 
            .flatpickr-weeks,
            .flatpickr-month, 
            .flatpickr-current-month { 
                overflow: visible !important; 
            }
            .dayContainer, .flatpickr-weekdaycontainer {
                margin: 0 auto !important;
                justify-content: center !important;
                display: flex !important;
                flex-wrap: wrap !important;
            }
            .flatpickr-weekdaycontainer { width: 100% !important; }

            /* Header: True Auto Layout & Stacking Context */
            .flatpickr-months { 
                display: flex !important;
                align-items: center !important;
                justify-content: center !important; /* CENTER EVERYTHING */
                padding: 4px 0 !important;
                margin-bottom: 20px;
                position: relative !important;
                z-index: 100 !important; /* Higher than days */
                gap: 16px !important; /* Slightly more gap for balance */
                overflow: visible !important;
                width: 100% !important;
            }
            .flatpickr-month { 
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                height: 44px !important;
                width: 100% !important;
                margin: 0 !important;
            }
            .flatpickr-current-month { 
                position: static !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 100% !important;
                padding: 0 !important;
                gap: 12px !important;
                font-size: 1rem;
                font-weight: 700;
            }
            
            /* Custom Dropdown Trigger (Replaces Static/Native) */
            .custom-month-selector {
                position: relative;
                display: flex;
                align-items: center;
                gap: 8px;
                background: #ffffff;
                border: 2px solid #e2e8f0;
                border-radius: 12px;
                padding: 0 16px;
                height: 44px;
                min-width: 155px; 
                cursor: pointer;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                z-index: 101 !important;
            }
            .custom-month-selector:hover { border-color: #3b82f6; background: #f8fafc; }
            .custom-month-selector.active { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
            .custom-month-selector .month-name { font-weight: 800; color: #1e293b; font-size: 0.9rem; flex: 1; text-align: center; }
            .custom-month-selector svg { color: #3b82f6; width: 14px; height: 14px; }
            
            /* Custom Month List Panel */
            .custom-month-list {
                position: absolute;
                top: calc(100% + 8px);
                left: 0;
                width: 100%;
                background: #ffffff !important;
                border: 1px solid #e2e8f0;
                border-radius: 16px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                z-index: 9999999 !important; /* Ensure it stays above days */
                display: none;
                padding: 6px;
                max-height: 280px;
                overflow-y: auto;
            }
            .custom-month-list div {
                padding: 10px 14px;
                border-radius: 10px;
                font-size: 0.85rem;
                color: #475569;
                cursor: pointer;
                transition: all 0.15s;
                font-weight: 600;
            }
            .custom-month-list div:hover { background: #eff6ff; color: #3b82f6; }
            .custom-month-list div.active { background: #3b82f6; color: #ffffff; font-weight: 700; }
            
            /* Year Input - Figma Precision */
            .numInputWrapper { 
                width: 85px !important;
                height: 44px !important;
                background: #ffffff;
                border: 2px solid #e2e8f0;
                border-radius: 12px;
                padding: 0 !important;
                transition: all 0.2s ease;
                display: flex !important;
                align-items: center;
                justify-content: center;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                overflow: visible !important;
            }
            .numInputWrapper:hover, .numInputWrapper:focus-within { border-color: #3b82f6; }
            .numInputWrapper:focus-within { box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
            .numInputWrapper input { 
                font-weight: 800 !important; 
                color: #1e293b !important; 
                font-size: 0.95rem !important;
                padding: 0 !important;
                width: 100% !important;
                height: 100% !important;
                text-align: center !important;
                background: transparent !important;
                border: none !important;
                outline: none !important;
            }
            .numInputWrapper span { display: none !important; }

            /* Navigation Buttons - Locked 44px */
            .flatpickr-prev-month, .flatpickr-next-month {
                position: static !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                height: 44px !important;
                width: 44px !important;
                border-radius: 12px !important;
                background: #ffffff !important;
                border: 2px solid #f1f5f9 !important;
                transition: all 0.2s ease;
                z-index: 10;
            }
            .flatpickr-prev-month:hover, .flatpickr-next-month:hover { 
                background: #eff6ff !important; 
                border-color: #3b82f6 !important;
                transform: translateY(-1px);
            }
            .flatpickr-prev-month svg, .flatpickr-next-month svg { width: 14px !important; height: 14px !important; fill: #3b82f6 !important; }
            
            /* Weekdays & Days Polishing */
            .flatpickr-weekday { color: #94a3b8; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; padding: 16px 0; }
            
            /* Default: Current Month Days (Deep & Circular) */
            .flatpickr-day { 
                border-radius: 9999px !important; /* PERFECT CIRCLE */
                color: #0f172a !important; /* Deepest Slate */
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
                border: 2px solid transparent !important; 
                height: 40px !important; /* Adjusted slightly for perfect circle in 44px cell */
                line-height: 36px !important; 
                font-weight: 800 !important; 
                width: 40px !important;
                margin: 2px auto !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
            }

            .flatpickr-day.today { 
                background: #eff6ff !important; 
                color: #3b82f6 !important; 
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            }
            
            /* Non-current Month Days - Extreme Transition */
            .flatpickr-day.prevMonthDay, 
            .flatpickr-day.nextMonthDay,
            .flatpickr-day.prevMonthDay.inRange,
            .flatpickr-day.nextMonthDay.inRange {
                color: #94a3b8 !important; /* Neutral Gray */
                opacity: 0.45 !important; /* Slightly more visible */
                font-weight: 400 !important;
                background: transparent !important;
                border-color: transparent !important;
                pointer-events: all; /* Re-enable selection as requested */
            }
            .flatpickr-day.prevMonthDay:hover, .flatpickr-day.nextMonthDay:hover {
                background: #f1f5f9 !important;
                opacity: 0.8 !important;
                color: #94a3b8 !important;
            }

            .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange { 
                background: #3b82f6 !important; 
                color: #ffffff !important;
                box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
                opacity: 1 !important;
                border-color: #3b82f6 !important;
            }
            .flatpickr-day.inRange { 
                background: #f1f7ff !important; 
                color: #3b82f6 !important; 
                border-radius: 0 !important; /* Keep range segments square-ish for continuity */
                opacity: 1 !important;
            }
            .flatpickr-day.startRange { border-radius: 9999px 0 0 9999px !important; }
            .flatpickr-day.endRange { border-radius: 0 9999px 9999px 0 !important; }
            .flatpickr-day.selected.startRange.endRange { border-radius: 9999px !important; }

            .flatpickr-day:not(.selected):not(.prevMonthDay):not(.nextMonthDay):hover { 
                background: #f1f5f9; 
                border-color: #e2e8f0;
                transform: scale(1.1); 
            }
        </style>
    @endpush
<script>
    (function () {
        const STORAGE_KEY = 'dashboard_period';
        const params = new URLSearchParams(window.location.search);

        // Jika URL tidak punya ?period=, coba restore dari sessionStorage
        if (!params.has('period')) {
            const saved = sessionStorage.getItem(STORAGE_KEY);
            if (saved && saved !== 'today') {
                // Redirect ke URL yang sama + ?period=saved
                params.set('period', saved);
                window.location.replace(window.location.pathname + '?' + params.toString());
            }
        } else {
            // Simpan periode saat ini ke sessionStorage
            sessionStorage.setItem(STORAGE_KEY, params.get('period'));
        }

        // Fungsi global untuk dipanggil saat klik tab
        window.savePeriod = function (key) {
            sessionStorage.setItem(STORAGE_KEY, key);
        };

        // Fungsi global untuk dipakai tombol logout
        window.clearDashboardPeriod = function () {
            sessionStorage.removeItem(STORAGE_KEY);
        };
    })();
</script>

<script>
    function dashboardData() {
        const userSettings = @json(auth()->user()->settings ?? []);

        return {
            showStats: userSettings.showStats ?? (localStorage.getItem('dashboard_{{ auth()->id() }}_showStats') !== 'false'),
            showCharts: userSettings.showCharts ?? (localStorage.getItem('dashboard_{{ auth()->id() }}_showCharts') !== 'false'),
            showLowStock: userSettings.showLowStock ?? (localStorage.getItem('dashboard_{{ auth()->id() }}_showLowStock') !== 'false'),
            showBorrowings: userSettings.showBorrowings ?? (localStorage.getItem('dashboard_{{ auth()->id() }}_showBorrowings') !== 'false'),
            showOverdue: userSettings.showOverdue ?? (localStorage.getItem('dashboard_{{ auth()->id() }}_showOverdue') !== 'false'),
            showNoPriceItems: userSettings.showNoPriceItems ?? (localStorage.getItem('dashboard_{{ auth()->id() }}_showNoPriceItems') !== 'false'),
            showMovement: userSettings.showMovement ?? (localStorage.getItem('dashboard_{{ auth()->id() }}_showMovement') !== 'false'),
            showRecent: userSettings.showRecent ?? (localStorage.getItem('dashboard_{{ auth()->id() }}_showRecent') !== 'false'),
            showTopItems: userSettings.showTopItems ?? (localStorage.getItem('dashboard_{{ auth()->id() }}_showTopItems') === 'true'),
            showDeadStock: userSettings.showDeadStock ?? (localStorage.getItem('dashboard_{{ auth()->id() }}_showDeadStock') === 'true'),
            showLeaderboard: userSettings.showLeaderboard ?? (localStorage.getItem('dashboard_{{ auth()->id() }}_showLeaderboard') === 'true'),
            
            isLoading: true,
            showActivityModal: false,
            selectedActivity: null,

            // Toast Helper
            showToast(type, message) {
                if (window.Toast) {
                    window.Toast.fire({ icon: type, title: message });
                } else if (window.Swal) {
                    window.Swal.fire({ toast: true, position: 'top-end', icon: type, title: message, showConfirmButton: false, timer: 3000 });
                } else {
                    alert(message);
                }
            },

            // Location Management
            showLocationModal: false,
            locationsList: [],
            isLoadingLocations: false,
            editingId: null,
            editingName: '',
            confirmDeleteId: null,
            confirmDeleteName: '',
            isDeleting: false,

            // Category Management
            showCategoryModal: false,
            categoriesList: [],
            isLoadingCategories: false,
            catEditingId: null,
            catEditingName: '',
            catConfirmDeleteId: null,
            catConfirmDeleteName: '',
            isDeletingCat: false,

            // Brand Management
            showBrandModal: false,
            brandsList: [],
            isLoadingBrands: false,
            brandEditingId: null,
            brandEditingName: '',
            brandConfirmDeleteId: null,
            brandConfirmDeleteName: '',
            isDeletingBrand: false,
            newBrandName: '',
            isAddingBrand: false,

            // Add Location
            newLocationName: '',
            isAddingLocation: false,

            async addLocation() {
                if (!this.newLocationName.trim()) return;
                this.isAddingLocation = true;
                try {
                    const response = await fetch('{{ route("locations.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: this.newLocationName })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.showToast('success', data.message);
                        this.newLocationName = '';
                        this.fetchLocations();
                        this.refreshData();
                    } else {
                        this.showToast('warning', data.message);
                    }
                } catch (e) {
                    console.error('Add failed:', e);
                    this.showToast('error', 'Terjadi kesalahan.');
                } finally {
                    this.isAddingLocation = false;
                }
            },

            async openLocationModal() {
                this.showLocationModal = true;
                this.editingId = null;
                this.confirmDeleteId = null;
                this.fetchLocations();
            },

            startEdit(loc) {
                this.editingId = loc.id;
                this.editingName = loc.name;
                this.$nextTick(() => {
                    const input = document.getElementById('edit-input-' + loc.id);
                    if (input) input.focus();
                });
            },

            cancelEdit() {
                this.editingId = null;
                this.editingName = '';
            },

            async saveEdit(id) {
                if (!this.editingName || this.editingName.trim() === '') {
                    this.cancelEdit();
                    return;
                }
                await this.updateLocationName(id, this.editingName);
                this.cancelEdit();
            },

            askDelete(loc) {
                this.confirmDeleteId = loc.id;
                this.confirmDeleteName = loc.name;
            },

            cancelDelete() {
                this.confirmDeleteId = null;
                this.confirmDeleteName = '';
                this.isDeleting = false;
            },

            // Category Methods
            // Add Category
            newCategoryName: '',
            isAddingCategory: false,

            async addCategory() {
                if (!this.newCategoryName.trim()) return;
                this.isAddingCategory = true;
                try {
                    const response = await fetch('{{ route("categories.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: this.newCategoryName })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.showToast('success', data.message);
                        this.newCategoryName = '';
                        this.fetchCategories();
                        this.refreshData();
                    } else {
                        this.showToast('warning', data.message);
                    }
                } catch (e) {
                    console.error('Add failed:', e);
                    this.showToast('error', 'Terjadi kesalahan.');
                } finally {
                    this.isAddingCategory = false;
                }
            },

            async openCategoryModal() {
                console.log('📂 [Alpine] openCategoryModal called');
                this.showCategoryModal = true;
                console.log('📂 [Alpine] showCategoryModal set to true');
                await this.fetchCategories();
            },

            async fetchCategories() {
                this.isLoadingCategories = true;
                try {
                    const response = await fetch('{{ route("categories.index") }}');
                    this.categoriesList = await response.json();
                } catch (e) {
                    console.error('Failed to fetch categories:', e);
                    this.showToast('error', 'Gagal memuat data kategori.');
                } finally {
                    this.isLoadingCategories = false;
                }
            },

            startCatEdit(cat) {
                this.catEditingId = cat.id;
                this.catEditingName = cat.name;
                this.$nextTick(() => {
                    const input = document.getElementById('cat-edit-input-' + cat.id);
                    if (input) input.focus();
                });
            },

            cancelCatEdit() {
                this.catEditingId = null;
                this.catEditingName = '';
            },

            async saveCatEdit(id) {
                if (!this.catEditingName.trim()) return;
                try {
                    const response = await fetch(`/categories/${id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: this.catEditingName })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.showToast('success', data.message);
                        this.fetchCategories();
                        this.cancelCatEdit();
                    } else {
                        this.showToast('warning', data.message);
                    }
                } catch (e) {
                    console.error('Update failed:', e);
                    this.showToast('error', 'Terjadi kesalahan saat memperbarui kategori.');
                }
            },

            askCatDelete(cat) {
                this.catConfirmDeleteId = cat.id;
                this.catConfirmDeleteName = cat.name;
            },

            cancelCatDelete() {
                this.catConfirmDeleteId = null;
                this.catConfirmDeleteName = '';
                this.isDeletingCat = false;
            },

            // Brand Methods
            async addBrand() {
                if (!this.newBrandName.trim()) return;
                this.isAddingBrand = true;
                try {
                    const response = await fetch('{{ route("brands.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: this.newBrandName })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        if (window.showToast) window.showToast('success', data.message);
                        this.newBrandName = '';
                        this.fetchBrands();
                        this.refreshData();
                    } else {
                        if (window.showToast) window.showToast('warning', data.message);
                    }
                } catch (e) {
                    console.error('Add failed:', e);
                    if (window.showToast) window.showToast('error', 'Terjadi kesalahan.');
                } finally {
                    this.isAddingBrand = false;
                }
            },

            async openBrandModal() {
                this.showBrandModal = true;
                await this.fetchBrands();
            },

            async fetchBrands() {
                this.isLoadingBrands = true;
                try {
                    const response = await fetch('{{ route("brands.index") }}');
                    this.brandsList = await response.json();
                } catch (e) {
                    console.error('Failed to fetch brands:', e);
                    if (window.showToast) window.showToast('error', 'Gagal memuat data merk.');
                } finally {
                    this.isLoadingBrands = false;
                }
            },

            startBrandEdit(brand) {
                this.brandEditingId = brand.id;
                this.brandEditingName = brand.name;
                this.$nextTick(() => {
                    const input = document.getElementById('brand-edit-input-' + brand.id);
                    if (input) input.focus();
                });
            },

            cancelBrandEdit() {
                this.brandEditingId = null;
                this.brandEditingName = '';
            },

            async saveBrandEdit(id) {
                if (!this.brandEditingName.trim()) return;
                try {
                    const response = await fetch(`/brands/${id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: this.brandEditingName })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        if (window.showToast) window.showToast('success', data.message);
                        this.fetchBrands();
                        this.cancelBrandEdit();
                    } else {
                        if (window.showToast) window.showToast('warning', data.message);
                    }
                } catch (e) {
                    console.error('Update failed:', e);
                    if (window.showToast) window.showToast('error', 'Terjadi kesalahan saat memperbarui merk.');
                }
            },

            askBrandDelete(brand) {
                this.brandConfirmDeleteId = brand.id;
                this.brandConfirmDeleteName = brand.name;
            },

            cancelBrandDelete() {
                this.brandConfirmDeleteId = null;
                this.brandConfirmDeleteName = '';
                this.isDeletingBrand = false;
            },

            async deleteBrand(id) {
                if (this.isDeletingBrand) return;
                this.isDeletingBrand = true;
                try {
                    const response = await fetch(`/brands/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (response.ok) {
                        if (window.showToast) window.showToast('success', data.message);
                        this.fetchBrands();
                        this.cancelBrandDelete();
                    } else {
                        if (window.showToast) window.showToast('warning', data.message);
                    }
                } catch (e) {
                    console.error('Delete failed:', e);
                    if (window.showToast) window.showToast('error', 'Terjadi kesalahan saat menghapus merk.');
                } finally {
                    this.isDeletingBrand = false;
                }
            },

            resetWidgets() {
                const defaults = {
                    showStats: true,
                    showCharts: true,
                    showMovement: false,
                    showTopItems: false,
                    showLowStock: true,
                    showRecent: true,
                    showDeadStock: false,
                    showLeaderboard: false,
                    showBorrowings: true,
                    showOverdue: true,
                    showNoPriceItems: true
                };
                Object.keys(defaults).forEach(key => {
                    this[key] = defaults[key];
                    localStorage.setItem('dashboard_{{ auth()->id() }}_' + key, defaults[key]);
                });
                this.showToast('success', 'Tampilan dashboard telah direset.');
            },

            async deleteCategory(id) {
                if (this.isDeletingCat) return;
                this.isDeletingCat = true;
                try {
                    const response = await fetch(`/categories/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.showToast('success', data.message);
                        this.fetchCategories();
                        this.cancelCatDelete();
                    } else {
                        this.showToast('warning', data.message);
                    }
                } catch (e) {
                    console.error('Delete failed:', e);
                    this.showToast('error', 'Terjadi kesalahan saat menghapus kategori.');
                } finally {
                    this.isDeletingCat = false;
                }
            },

            async fetchLocations() {
                this.isLoadingLocations = true; // Fixed typo
                try {
                    const response = await fetch('{{ route("locations.index") }}');
                    this.locationsList = await response.json();
                } catch (e) {
                    console.error('Failed to fetch locations:', e);
                } finally {
                    this.isLoadingLocations = false; // Fixed typo
                }
            },

            async updateLocationName(id, newName) {
                if (!newName || newName.trim() === '') return;
                try {
                    const response = await fetch(`/locations/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: newName })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        if (window.showToast) window.showToast('success', data.message);
                        this.fetchLocations();
                        this.refreshData(); // Refresh counts on dashboard
                    } else {
                        if (window.showToast) window.showToast('error', data.message);
                    }
                } catch (e) {
                    console.error('Update failed:', e);
                }
            },

            async deleteLocation(id) {
                if (this.isDeleting) return;
                this.isDeleting = true;
                try {
                    const response = await fetch(`/locations/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.showToast('success', data.message);
                        this.fetchLocations();
                        this.totalLocations--;
                        this.cancelDelete();
                    } else {
                        this.showToast('warning', data.message);
                    }
                } catch (e) {
                    console.error('Delete failed:', e);
                    this.showToast('error', 'Terjadi kesalahan saat menghapus lokasi.');
                } finally {
                    this.isDeleting = false;
                }
            },
            
            // Data Statis & List
            totalSpareparts: {{ $totalSpareparts }},
            totalStock: {{ $totalStock }},
            totalCategories: {{ $totalCategories }},
            totalBrands: {{ $totalBrands }},
            totalLocations: {{ $totalLocations }},
            pendingApprovalsCount: {{ $pendingApprovalsCount }},
            activeBorrowingsCount: {{ $activeBorrowingsCount }},

            // Arrays (untuk x-for)
            recentActivities: @json($recentActivities),
            topExited: @json($topExited),
            topEntered: @json($topEntered),
            deadStockItems: @json($deadStockItems),
            activeUsers: @json($activeUsers),
            activeBorrowingsList: @json($activeBorrowingsList),
            overdueBorrowingsList: @json($overdueBorrowingsList),
            lowStockItems: @json($lowStockItems),
            noPriceItems: @json($noPriceItems ?? []),

            init() {
                // Cek jika ada parameter untuk buka modal lokasi otomatis
                const params = new URLSearchParams(window.location.search);
                if (params.get('manage_locations') === 'true') {
                    this.openLocationModal();
                    window.history.replaceState({}, document.title, window.location.pathname);
                }

                if (params.get('manage_categories') === 'true') {
                    this.openCategoryModal();
                    window.history.replaceState({}, document.title, window.location.pathname);
                }

                // Tampilkan konten setelah loading selesai
                setTimeout(() => {
                    this.isLoading = false;
                    if (this.showMovement && window.fetchMovementData) {
                        window.fetchMovementData(30);
                    }
                }, 300);

                // Listener untuk event real-time global (dari realtime-inventory.js)
                window.addEventListener('dashboard-refresh', (e) => {
                    this.updateState(e.detail);
                });

                // Offline/Online Listeners
                window.addEventListener('online', () => {
                    this.refreshData();
                    if (window.showToast) window.showToast('success', 'Koneksi kembali online.');
                });

                window.addEventListener('offline', () => {
                    if (window.showToast) window.showToast('error', 'Koneksi terputus.');
                });
            },

            // Charts Data
            movementData: @json($movementData),
            stockByCategory: @json($stockByCategory),
            stockByLocation: @json($stockByLocation),
            
            updateState(data) {
                if (!data) return;
                this.totalSpareparts = data.totalSpareparts ?? this.totalSpareparts;
                this.totalStock = data.totalStock ?? this.totalStock;
                this.totalCategories = data.totalCategories ?? this.totalCategories;
                this.totalBrands = data.totalBrands ?? this.totalBrands;
                this.totalLocations = data.totalLocations ?? this.totalLocations;
                this.pendingApprovalsCount = data.pendingApprovalsCount ?? this.pendingApprovalsCount;
                this.activeBorrowingsCount = data.activeBorrowingsCount ?? this.activeBorrowingsCount;

                if (data.recentActivities) this.recentActivities = data.recentActivities;
                if (data.activeBorrowingsList) this.activeBorrowingsList = data.activeBorrowingsList;
                if (data.overdueBorrowingsList) this.overdueBorrowingsList = data.overdueBorrowingsList;

                if (window.updateDashboardCharts) {
                    window.updateDashboardCharts(data.movementData, data.stockByCategory, data.stockByLocation, data);
                }
            },

            async refreshData() {
               try {
                   const url = new URL('{{ route("dashboard.superadmin") }}');
                   const currentParams = new URLSearchParams(window.location.search);
                   currentParams.forEach((val, key) => url.searchParams.set(key, val));

                   const response = await fetch(url.toString(), {
                       headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                   });
                   
                   if (!response.ok) throw new Error('Refresh failed');
                   const data = await response.json();
                   this.updateState(data);
               } catch (error) {
                   console.error('Failed to refresh dashboard data:', error);
               }
            },

            viewActivityDetails(activity) {
                this.selectedActivity = activity;
                this.showActivityModal = true;
            },

            async toggle(key) {
                const widgetKeys = ['showStats', 'showCharts', 'showMovement', 'showTopItems', 'showLowStock', 'showRecent', 'showDeadStock', 'showLeaderboard', 'showBorrowings', 'showOverdue', 'showNoPriceItems'];
                const activeCount = widgetKeys.filter(k => this[k]).length;
                if (this[key] && activeCount <= 1) {
                     if (window.showToast) window.showToast('warning', 'Minimal satu widget harus tetap aktif.');
                     return;
                }

                this[key] = !this[key];
                localStorage.setItem('dashboard_{{ auth()->id() }}_' + key, this[key]);
                if (key === 'showMovement' && this[key] && window.fetchMovementData) {
                    window.fetchMovementData(30);
                }

                try {
                    await fetch('{{ route("profile.settings.update") }}', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ settings: { [key]: this[key] } })
                    });
                } catch (e) { console.error('Settings sync failed:', e); }
            },

            async resetWidgets() {
                const defaults = {
                    showStats: true,
                    showCharts: true,
                    showLowStock: true,
                    showBorrowings: true,
                    showOverdue: true,
                    showNoPriceItems: true,
                    showMovement: true,
                    showRecent: true,
                    showTopItems: false,
                    showDeadStock: false,
                    showLeaderboard: false
                };

                for (const [key, value] of Object.entries(defaults)) {
                    this[key] = value;
                    localStorage.setItem('dashboard_{{ auth()->id() }}_' + key, value);
                }

                if (window.showToast) window.showToast('success', 'Tampilan kembali ke default.');
                if (this.showMovement && window.fetchMovementData) window.fetchMovementData(30);

                try {
                    await fetch('{{ route("profile.settings.update") }}', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ settings: defaults })
                    });
                } catch (e) { console.error('Settings sync failed:', e); }
            }
        };
    }
</script>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" 
             x-data="dashboardData()">
             
            @include('dashboard._location_modal')
            @include('dashboard._category_modal')
            @include('dashboard._brand_modal')
            @include('dashboard._activity_modal')
             
            {{-- ================================================================
                 HEADER KHUSUS CETAK & EXPORT (MUNCUL DI SETIAP HALAMAN)
                 ================================================================ --}}
            <table class="w-full print-container">
                <thead class="hidden export-show pb-4 border-b-2 border-primary-900 mb-6">
                    <tr><td>
                        <div class="flex items-start justify-between w-full">
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('images/logo/logo_azzahracomputer.png') }}" class="h-12 w-auto" alt="Logo Azzahra">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900 uppercase">AZZAHRA COMPUTER</h1>
                                    <p class="text-sm text-gray-500">Solusi Teknologi Terpercaya &bull; Laporan Resmi Inventaris</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <h2 class="text-xl font-bold text-primary-900">LAPORAN DASHBOARD</h2>
                                <p class="text-sm text-gray-600 mt-1">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</p>
                                <p class="text-sm text-gray-600">Oleh: {{ auth()->user()->name }}</p>
                            </div>
                        </div>
                    </td></tr>
                </thead>

                <tbody>
                    <tr><td class="print:pt-8">

            {{-- ================================================================
                 HEADER DASHBOARD
                 Baris 1: Judul + Tombol Pengaturan & Approvals
                 Baris 2: Tab Filter Periode Global (Opsi F)
                 ================================================================ --}}
            <div class="mb-6 export-hide print:hidden">
                {{-- Baris 1 --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-secondary-900 tracking-tight">{{ __('ui.dashboard') }}</h1>
                        <p class="mt-1 text-sm text-secondary-500">{{ __('ui.dashboard_desc') }}</p>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{-- Tombol Pengaturan Widget --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                    class="btn btn-secondary flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="hidden sm:inline">{{ __('ui.display_settings') }}</span>
                            </button>
                            <div x-show="open" x-transition
                                 class="absolute left-0 sm:left-auto sm:right-0 mt-2 w-56 bg-white rounded-xl shadow-xl py-1 z-50 border border-secondary-100 max-h-[80vh] overflow-y-auto">
                                <div class="px-4 py-2 text-xs font-semibold text-secondary-400 uppercase tracking-wider">{{ __('ui.active_widgets') }}</div>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showStats" @change="toggle('showStats')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_main_stats') }}</span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showCharts" @change="toggle('showCharts')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_distribution_location') }}</span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showLowStock" @change="toggle('showLowStock')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_stock_alerts') }}</span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showBorrowings" @change="toggle('showBorrowings')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_active_borrowings') }}</span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showOverdue" @change="toggle('showOverdue')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_overdue') }}</span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showNoPriceItems" @change="toggle('showNoPriceItems')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_missing_price') }}</span>
                                </label>
                                <div class="border-t border-secondary-100 my-1"></div>
                                <div class="px-4 py-2 text-xs font-semibold text-secondary-400 uppercase tracking-wider">{{ __('ui.widget_analytics') }}</div>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showMovement" @change="toggle('showMovement')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_stock_movement') }}</span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showTopItems" @change="toggle('showTopItems')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_popular_items') }}</span>
                                </label>

                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showDeadStock" @change="toggle('showDeadStock')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_dead_stock') }}</span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showLeaderboard" @change="toggle('showLeaderboard')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_top_contributors') }}</span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showRecent" @change="toggle('showRecent')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700">{{ __('ui.widget_recent_activity') }}</span>
                                </label>
                                {{-- Tombol Reset ke Default --}}
                                <div class="border-t border-secondary-100 my-1"></div>
                                <div class="px-4 py-2">
                                    <button @click="resetWidgets()" class="w-full text-xs text-center text-secondary-500 hover:text-danger-600 transition-colors py-1 rounded hover:bg-danger-50">
                                        ↺ Reset ke Tampilan Default
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Ekspor --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                    class="btn btn-secondary flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span class="hidden sm:inline">Ekspor</span>
                            </button>
                            <div x-show="open" x-transition
                                 class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-xl py-1 z-50 border border-secondary-100">
                                <button onclick="exportDashboardPDF()" class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Cetak / PDF
                                </button>
                                <button onclick="exportDashboardPNG()" class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Simpan sebagai PNG
                                </button>
                            </div>
                        </div>

                        {{-- Tombol Approvals --}}
                        <a href="{{ route('inventory.stock-approvals.index') }}" class="btn btn-primary flex items-center gap-2 relative">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span class="hidden sm:inline">{{ __('ui.approvals') }}</span>
                            @if($pendingApprovalsCount > 0)
                                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-danger-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-danger-500"></span>
                                </span>
                            @endif
                        </a>
                    </div>
                </div>

                {{-- ========================================================
                     Baris 2: Tab Filter Periode Global (Opsi F)
                     Tab ini mengirim GET request ke URL yang sama + ?period=...
                     ======================================================== --}}
                @php
                    $activePeriod = $period ?? 'today';
                    $tabDefs = [
                        'today'      => 'Hari Ini',
                        'this_week'  => 'Minggu Ini',
                        'this_month' => 'Bulan Ini',
                        'this_year'  => 'Tahun Ini',
                    ];
                @endphp
                {{-- Sticky wrapper: tab period menempel di atas saat scroll mobile --}}
                {{-- Sticky wrapper removed as requested --}}
                <div x-data="globalPeriodFilter()" class="flex flex-col gap-2">
                    {{-- Tab Row --}}
                    <div class="flex flex-wrap items-center gap-1 bg-secondary-100/60 rounded-xl p-1.5">
                        @foreach($tabDefs as $key => $label)
                            <a href="{{ route('dashboard.superadmin', ['period' => $key]) }}"
                               onclick="savePeriod('{{ $key }}')"
                               class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-150 whitespace-nowrap
                                      {{ $activePeriod === $key
                                          ? 'bg-white text-primary-700 shadow-sm font-semibold ring-1 ring-secondary-200'
                                          : 'text-secondary-600 hover:text-secondary-900 hover:bg-white/60' }}">
                                {{ $label }}
                            </a>
                        @endforeach

                        {{-- Custom tab dengan SVG icon 1 warna --}}
                        <button @click="showCustom = !showCustom"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-150 whitespace-nowrap
                                       {{ in_array($activePeriod, ['custom','custom_year'])
                                           ? 'bg-white text-primary-700 shadow-sm font-semibold ring-1 ring-secondary-200'
                                           : 'text-secondary-600 hover:text-secondary-900 hover:bg-white/60' }}">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Custom</span>
                            @if(in_array($activePeriod, ['custom','custom_year']) && $year)
                                <span class="text-xs text-secondary-500">
                                    ({{ $year }}{{ isset($month) && $month !== 'all' ? '/' . str_pad($month,2,'0',STR_PAD_LEFT) : '' }})
                                </span>
                            @endif
                        </button>

                        {{-- Indikator rentang tanggal aktif --}}
                        <span class="ml-auto text-xs text-secondary-400 hidden sm:block">
                            Data: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} — {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
                        </span>
                    </div>

                    {{-- Panel Custom dengan dropdown Alpine kustom --}}
                    <div x-show="showCustom"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-cloak
                         class="mt-4">
                        <form method="GET" action="{{ route('dashboard.superadmin') }}"
                              class="bg-white border border-secondary-100 rounded-[24px] p-5 shadow-xl shadow-secondary-900/5 flex flex-col md:flex-row items-stretch md:items-end gap-6 transition-all">
                            <input type="hidden" name="period" value="custom">

                            {{-- Form Group: Date Range --}}
                            <div class="flex-grow space-y-3">
                                <div class="flex items-center justify-between px-1">
                                    <label class="text-[11px] font-extrabold text-secondary-400 uppercase tracking-[0.1em]">Rentang Tanggal</label>
                                    <div class="flex items-center gap-3">
                                        <button type="button" onclick="setPickerRange(0)" class="text-[10px] font-bold text-secondary-500 hover:text-primary-600 transition-colors bg-secondary-50 px-2 py-0.5 rounded-md hover:bg-primary-50">HARI INI</button>
                                        <button type="button" onclick="setPickerRange(7)" class="text-[10px] font-bold text-secondary-500 hover:text-primary-600 transition-colors bg-secondary-50 px-2 py-0.5 rounded-md hover:bg-primary-50">7 HARI TERAKHIR</button>
                                        <button type="button" onclick="setPickerRange(30)" class="text-[10px] font-bold text-secondary-500 hover:text-primary-600 transition-colors bg-secondary-50 px-2 py-0.5 rounded-md hover:bg-primary-50">30 HARI TERAKHIR</button>
                                    </div>
                                </div>
                                
                                <div class="relative group">
                                    <input type="text" id="date_range_picker" 
                                           class="w-full pl-12 pr-4 py-3 text-sm bg-secondary-50/50 border-secondary-200 rounded-2xl text-secondary-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all cursor-pointer font-semibold placeholder:text-secondary-400"
                                           placeholder="Pilih rentang tanggal...">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-secondary-400 group-focus-within:text-primary-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                </div>

                                <input type="hidden" name="start_date" id="start_date" value="{{ $start->format('Y-m-d') }}">
                                <input type="hidden" name="end_date" id="end_date" value="{{ $end->format('Y-m-d') }}">
                            </div>

                            {{-- Form Group: Actions --}}
                            <div class="flex items-center gap-3 pt-2 md:pt-0">
                                <button type="submit" class="flex-grow md:flex-none btn btn-primary px-8 h-[44px] rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-primary-500/20 active:scale-95 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <span class="font-bold tracking-wide">Terapkan</span>
                                </button>
                                <button type="button" 
                                        onclick="resetCustomPicker()"
                                        class="btn btn-secondary h-[44px] px-6 rounded-xl border-secondary-200 hover:bg-secondary-50 font-bold active:scale-95 transition-transform flex items-center justify-center">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ====================================================================
                 Mobile Quick Summary Bar (hanya tampil di layar < md)
                 Ringkasan 1 baris di atas stat cards — above the fold
                 ==================================================================== --}}
            {{-- Mobile Quick Summary removed as requested --}}

            <!-- Bagian Ikhtisar Statistik -->
            <!-- Loading Skeleton -->
            <div x-show="showStats && isLoading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-6 animate-pulse print:hidden">
                @for($i = 0; $i < 5; $i++)
                    <div class="card p-6 flex flex-col justify-between h-40">
                        <div class="flex justify-between items-start">
                            <div class="h-4 bg-gray-200 rounded w-24"></div>
                            <div class="h-10 w-10 bg-gray-200 rounded-bl-full -mr-6 -mt-6"></div>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-200">000</div>
                        <div class="mt-4 flex items-center">
                            <div class="p-2 bg-gray-100 rounded-lg w-9 h-9"></div>
                            <div class="ml-2 h-4 bg-gray-100 rounded w-16"></div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Konten Asli -->
            <div x-show="!isLoading" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 print:grid-cols-6 gap-6 mb-6 print:break-inside-avoid">
                
                <!-- 1. Total Sparepart (Interactive) -->
                <div @click="window.location.href='{{ route('inventory.index') }}'" 
                     class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-all duration-300 cursor-pointer shadow-md hover:shadow-lg border-2 border-transparent hover:border-primary-100">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-primary-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-primary-200"></div>
                    <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative leading-tight">Total<br>Barang</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative" x-text="totalSpareparts">{{ $totalSpareparts }}</h3>
                    </div>
                    <div class="mt-4 flex items-center text-primary-600 z-10 relative">
                        <div class="p-2 bg-primary-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <x-icon.inventory class="w-5 h-5" />
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-primary-50 text-primary-700 px-2 py-0.5 rounded-full">{{ __('ui.sku_items') }}</span>
                    </div>
                </div>

                <!-- 2. Total Stok (Interactive) -->
                <div @click="document.getElementById('stockByCategoryChart').scrollIntoView({behavior: 'smooth', block: 'center'})" 
                     class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-all duration-300 cursor-pointer shadow-md hover:shadow-lg border-2 border-transparent hover:border-success-100">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-success-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-success-200"></div>
                    <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative leading-tight">Total Stok<br>Fisik</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative" x-text="totalStock">{{ $totalStock }}</h3>
                    </div>
                    <div class="mt-4 flex items-center text-success-600 z-10 relative">
                        <div class="p-2 bg-success-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <x-icon.package class="w-5 h-5" />
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-success-50 text-success-700 px-2 py-0.5 rounded-full">{{ __('ui.units') }}</span>
                    </div>
                </div>

                <!-- 3. Widget Peminjaman Aktif (Interactive) -->
                <div @click="window.location.href='{{ route('inventory.index', ['filter' => 'borrowed']) }}'" 
                     x-show="showBorrowings" 
                     class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-all duration-300 cursor-pointer shadow-md hover:shadow-lg border-2 border-transparent hover:border-fuchsia-100">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-fuchsia-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-fuchsia-200"></div>
                    <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative leading-tight">Sedang<br>Dipinjam</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative">{{ $activeBorrowingsCount }}</h3>
                    </div>
                    <div class="mt-4 flex items-center text-fuchsia-600 z-10 relative">
                        <div class="p-2 bg-fuchsia-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <x-icon.borrow-user class="w-5 h-5" />
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-fuchsia-50 text-fuchsia-700 px-2 py-0.5 rounded-full">{{ __('ui.units_out') }}</span>
                    </div>
                </div>

                <!-- 4. Total Kategori (Interactive - Trigger Modal) -->
                <div @click="openCategoryModal()" 
                     class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-all duration-300 cursor-pointer shadow-md hover:shadow-lg border-2 border-transparent hover:border-warning-100">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-warning-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-warning-200"></div>
                     <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative leading-tight">Kategori<br>Barang</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative">{{ $totalCategories }}</h3>
                    </div>
                    <div class="mt-4 flex items-center text-warning-600 z-10 relative">
                        <div class="p-2 bg-warning-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <x-icon.category class="w-5 h-5" />
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-warning-50 text-warning-700 px-2 py-0.5 rounded-full">{{ __('ui.item_types') }}</span>
                    </div>
                </div>

                <!-- 5. Total Merk (Interactive - Trigger Modal) -->
                <div @click="openBrandModal()" 
                     class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-all duration-300 cursor-pointer shadow-md hover:shadow-lg border-2 border-transparent hover:border-pink-100">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-pink-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-pink-200"></div>
                     <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative leading-tight">Total<br>Merk</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative" x-text="totalBrands">{{ $totalBrands }}</h3>
                    </div>
                    <div class="mt-4 flex items-center text-pink-600 z-10 relative">
                        <div class="p-2 bg-pink-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <x-icon.tag class="w-5 h-5" />
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-pink-50 text-pink-700 px-2 py-0.5 rounded-full">Daftar Merk</span>
                    </div>
                </div>

                <!-- 6. Total Lokasi (Interactive - Trigger Modal) -->
                <div @click="openLocationModal()" 
                     class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-all duration-300 cursor-pointer shadow-md hover:shadow-lg border-2 border-transparent hover:border-cyan-100">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-cyan-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-cyan-200"></div>
                     <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative leading-tight">Lokasi<br>Penyimpanan</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative" x-text="totalLocations">{{ $totalLocations }}</h3>
                    </div>
                    <div class="mt-4 flex items-center text-cyan-600 z-10 relative">
                        <div class="p-2 bg-cyan-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <x-icon.location class="w-5 h-5" />
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-cyan-50 text-cyan-700 px-2 py-0.5 rounded-full">{{ __('ui.warehouse_racks') }}</span>
                    </div>
                </div>
            </div>


            {{-- ============================================================
                 GRID 2 KOLOM: Overdue Terlambat + Harga Belum Diisi
                 Side-by-side di desktop, stacked di mobile.
                 Overdue otomatis full-width jika kolom harga disembunyikan.
                 ============================================================ --}}
            <div class="mb-6"
                 x-show="(showOverdue && overdueBorrowingsList.length > 0) || (showNoPriceItems && noPriceItems.length > 0)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak>

                <div class="grid gap-4 items-stretch print:break-inside-avoid"
                     :class="(showOverdue && overdueBorrowingsList.length > 0) && (showNoPriceItems && noPriceItems.length > 0) ? 'grid-cols-1 lg:grid-cols-2 print:grid-cols-2' : 'grid-cols-1'">

                    {{-- Skeleton Kiri: Barang Terlambat Kembali --}}
                    <div x-show="showOverdue && overdueBorrowingsList.length > 0 && isLoading" class="h-full animate-pulse print:hidden">
                        <div class="card border-l-4 border-danger-200 h-full">
                            <div class="card-header p-4 border-b border-gray-100 flex justify-between">
                                 <div class="h-6 bg-gray-200 rounded w-64"></div>
                            </div>
                            <div class="p-4 space-y-3">
                                @for($i=0; $i<3; $i++)
                                    <div class="flex justify-between">
                                        <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                                        <div class="h-4 bg-gray-200 rounded w-20"></div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kiri: Barang Terlambat Kembali --}}
                    <div x-show="showOverdue && overdueBorrowingsList.length > 0 && !isLoading" class="h-full">
                        <div class="card bg-white shadow-lg border-none overflow-hidden h-full">
                            {{-- Header merah --}}
                            <div class="p-4 bg-gradient-to-r from-red-500 to-orange-600 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <h3 class="font-bold text-white text-sm">{{ __('ui.attention_overdue') }} ({{ $totalOverdueCount }})</h3>
                                </div>
                                @if($totalOverdueCount > 0)
                                    <a href="{{ route('inventory.index', ['filter' => 'overdue']) }}" class="text-xs text-white hover:text-red-100 font-bold underline decoration-white/50">{{ __('ui.view_all') }}</a>
                                @endif
                            </div>
                            {{-- List tanpa scroll --}}
                            <div class="divide-y divide-secondary-100">
                                <template x-for="(borrow, index) in overdueBorrowingsList.slice(0, 5)" :key="borrow.id">
                                    <div class="px-4 py-3 hover:bg-red-50 transition-colors cursor-pointer flex justify-between items-center gap-3"
                                         @click="window.location.href = '/inventory/borrow/' + borrow.id">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-secondary-900 text-sm truncate" x-text="borrow.user_name || borrow.borrower_name"></p>
                                            <p class="text-xs text-secondary-500 truncate" x-text="borrow.sparepart_name + ' (' + borrow.quantity + 'x)'"></p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <p class="text-xs font-bold text-danger-600" x-text="borrow.due_date_formatted"></p>
                                            <p class="text-xs text-danger-400" x-text="borrow.due_date_rel"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Harga Belum Diisi --}}
                    <div x-show="showNoPriceItems && noPriceItems.length > 0 && !isLoading" class="h-full">
                        <div class="card bg-white shadow-lg border-none overflow-hidden h-full">
                            {{-- Header amber — konsisten dengan overdue --}}
                            <div class="p-4 bg-gradient-to-r from-amber-500 to-orange-500 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <h3 class="font-bold text-white text-sm">
                                        {{ __('ui.widget_missing_price') }} (<span x-text="noPriceItems.length"></span>)
                                    </h3>
                                </div>
                                <a href="{{ route('inventory.index', ['type' => 'sale', 'filter' => 'no_price']) }}" class="text-xs text-white hover:text-amber-100 font-bold underline decoration-white/50">{{ __('ui.view_all') }}</a>
                            </div>
                            {{-- List tanpa scroll --}}
                            <div class="divide-y divide-secondary-100">
                                <template x-for="item in noPriceItems.slice(0, 5)" :key="item.id">
                                    <div class="px-4 py-3 hover:bg-amber-50 transition-colors group flex justify-between items-center gap-3">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-secondary-900 text-sm truncate" x-text="item.name"></p>
                                            <p class="text-xs text-secondary-500 truncate" x-text="item.part_number"></p>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <span class="text-xs bg-amber-100 text-amber-700 font-semibold px-2 py-0.5 rounded-full">Rp 0</span>
                                            <a :href="'/inventory/' + item.id + '/edit'"
                                               class="opacity-0 group-hover:opacity-100 inline-flex items-center gap-1 text-xs font-semibold text-primary-600 bg-primary-50 hover:bg-primary-100 px-2 py-1 rounded-lg transition-all">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Isi
                                            </a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>{{-- end inner grid --}}
            </div>{{-- end outer wrapper --}}

            <div x-show="showMovement && isLoading" class="card mb-4 animate-pulse print:hidden">
                <div class="card-header border-b border-gray-100 p-5 flex justify-between items-center">
                    <div class="space-y-2">
                        <div class="h-5 bg-gray-200 rounded w-40"></div>
                        <div class="h-3 bg-gray-200 rounded w-56"></div>
                    </div>
                    <div class="flex gap-2">
                        <div class="h-7 bg-gray-200 rounded-lg w-14"></div>
                        <div class="h-7 bg-gray-200 rounded-lg w-16"></div>
                        <div class="h-7 bg-gray-200 rounded-lg w-16"></div>
                    </div>
                </div>
                <div class="p-5">
                    {{-- Skeleton chart: sumbu Y kiri --}}
                    <div class="flex gap-3 h-[250px]">
                        <div class="flex flex-col justify-between py-1">
                            @for($i=0; $i<6; $i++)
                                <div class="h-2 bg-gray-200 rounded w-6"></div>
                            @endfor
                        </div>
                        <div class="flex-1 flex flex-col justify-between">
                            @for($i=0; $i<6; $i++)
                                <div class="h-px bg-gray-100 w-full"></div>
                            @endfor
                        </div>
                    </div>
                    {{-- Label sumbu X --}}
                    <div class="flex justify-between mt-2 pl-9">
                        @for($i=0; $i<7; $i++)
                            <div class="h-2 bg-gray-200 rounded w-8"></div>
                        @endfor
                    </div>
                </div>
            </div>

            <div x-show="showMovement && !isLoading" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="card mb-4 print:break-inside-avoid">
                <div class="card-header border-b border-secondary-100 p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-secondary-900">{{ __('ui.stock_movement') }}</h3>
                            <p class="text-xs text-secondary-500">{{ __('ui.stock_movement_desc') }}</p>
                        </div>
                    {{-- KPI Summary Badges --}}
                        <div class="flex flex-wrap gap-2" id="movement-kpi-badges">
                            <div class="flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                <span class="text-xs text-emerald-700 font-medium whitespace-nowrap">Masuk: <span id="kpi-masuk" class="font-bold">0</span> unit <span id="kpi-masuk-pct" class="text-[10px] ml-1 opacity-80"></span></span>
                            </div>
                            <div class="flex items-center gap-1.5 bg-red-50 border border-red-200 rounded-lg px-3 py-1.5">
                                <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                                <span class="text-xs text-red-700 font-medium whitespace-nowrap">Keluar: <span id="kpi-keluar" class="font-bold">0</span> unit <span id="kpi-keluar-pct" class="text-[10px] ml-1 opacity-80"></span></span>
                            </div>
                            <div class="flex items-center gap-1.5 bg-blue-50 border border-blue-200 rounded-lg px-3 py-1.5" id="kpi-net-badge">
                                <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0" id="kpi-net-dot"></span>
                                <span class="text-xs font-medium whitespace-nowrap" id="kpi-net-label">Net: <span id="kpi-net" class="font-bold">0</span> <span id="kpi-net-pct" class="text-[10px] ml-1 opacity-80"></span></span>
                            </div>
                        </div>
                        {{-- Quick-filter Periode Widget (Opsi C) --}}
                        <div class="flex items-center gap-1 bg-secondary-100 rounded-lg p-0.5" id="movement-range-btns">
                            <button onclick="fetchMovementData(7)" id="mov-btn-7"
                                    class="mov-range-btn px-2.5 py-1 rounded-md text-xs font-medium transition-all text-secondary-600 hover:bg-white/70">7 Hari</button>
                            <button onclick="fetchMovementData(30)" id="mov-btn-30"
                                    class="mov-range-btn px-2.5 py-1 rounded-md text-xs font-medium transition-all bg-white shadow-sm text-primary-700">30 Hari</button>
                            <button onclick="fetchMovementData(90)" id="mov-btn-90"
                                    class="mov-range-btn px-2.5 py-1 rounded-md text-xs font-medium transition-all text-secondary-600 hover:bg-white/70">3 Bulan</button>
                        </div>
                    </div>
                    {{-- Label periode aktif --}}
                    <p class="text-xs text-secondary-400 mt-1" id="movement-period-label">Memuat data...</p>
                </div>
                <div class="card-body p-4 md:p-6">
                    {{-- Error state --}}
                    <div id="movement-error" class="hidden flex-col items-center justify-center h-[280px] gap-3">
                        <svg class="w-10 h-10 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        <p class="text-sm font-medium text-secondary-600">Gagal memuat data chart</p>
                        <button onclick="fetchMovementData(movementActiveRange)" class="text-xs text-primary-600 hover:text-primary-700 font-semibold border border-primary-200 rounded-lg px-3 py-1.5 hover:bg-primary-50 transition-colors">↻ Coba Lagi</button>
                    </div>
                    <div class="min-h-[200px] md:h-[280px] w-full" id="movement-chart-wrap">
                        <canvas id="stockMovementChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Baru: Bagian Item Teratas -->
            <div x-show="showTopItems && isLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4 animate-pulse print:hidden">
                 <!-- Skeleton Teratas Keluar -->
                 <div class="card">
                     <div class="card-header border-b border-gray-100 p-4">
                         <div class="h-4 bg-gray-200 rounded w-32"></div>
                     </div>
                     <div class="p-4 space-y-3">
                        @for($i=0; $i<5; $i++)
                            <div class="flex justify-between">
                                <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                <div class="h-4 bg-gray-200 rounded w-8"></div>
                            </div>
                        @endfor
                     </div>
                 </div>
                 <!-- Skeleton Teratas Masuk -->
                 <div class="card">
                    <div class="card-header border-b border-gray-100 p-4">
                        <div class="h-4 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="p-4 space-y-3">
                       @for($i=0; $i<5; $i++)
                           <div class="flex justify-between">
                               <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                               <div class="h-4 bg-gray-200 rounded w-8"></div>
                           </div>
                       @endfor
                    </div>
                </div>
            </div>

            <div x-show="showTopItems && !isLoading" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                 {{-- Barang Sering Keluar --}}
                 <div class="card flex flex-col overflow-hidden">
                     <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                         <div class="w-8 h-8 rounded-lg bg-danger-50 flex items-center justify-center flex-shrink-0">
                             <svg class="w-4 h-4 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                         </div>
                         <div>
                             <h3 class="font-bold text-secondary-900">{{ __('ui.top_exiting_items') }}</h3>
                             <p class="text-xs text-secondary-400">Berdasarkan periode yang dipilih</p>
                         </div>
                     </div>
                     <div class="flex-grow divide-y divide-secondary-100">
                         <template x-for="(item, index) in topExited" :key="item.sparepart_id">
                             <div class="group flex items-center gap-4 px-6 py-3.5 hover:bg-danger-50/40 transition-all duration-150 cursor-pointer"
                                  @click="window.location.href = '/inventory/' + item.sparepart_id">
                                 <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold transition-transform duration-200 group-hover:scale-110"
                                      :class="{
                                         'bg-amber-100 text-amber-700 shadow shadow-amber-200':  index === 0,
                                         'bg-slate-100  text-slate-600  shadow shadow-slate-200': index === 1,
                                         'bg-orange-100 text-orange-700 shadow shadow-orange-200': index === 2,
                                         'bg-secondary-100 text-secondary-500': index > 2
                                      }"
                                      x-text="index + 1"></div>
                                 <span class="flex-grow font-semibold text-secondary-800 text-sm group-hover:text-primary-700 transition-colors truncate" x-text="item.sparepart_name || 'Unknown'"></span>
                                 <span class="flex-shrink-0 font-bold text-sm text-danger-600 tabular-nums" x-text="'− ' + parseInt(item.total_qty).toLocaleString('id-ID')"></span>
                             </div>
                         </template>
                         <div x-show="topExited.length === 0" class="px-6 py-10 text-center text-secondary-400">
                             <svg class="w-8 h-8 mx-auto text-secondary-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                             <p class="text-sm italic">{{ __('ui.no_data') }}</p>
                         </div>
                     </div>
                 </div>

                 {{-- Barang Sering Masuk — style: emerald border + pill qty badge --}}
                 <div class="card flex flex-col overflow-hidden border-l-4 border-emerald-400">
                     <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                         <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                             <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                         </div>
                         <div>
                             <h3 class="font-bold text-secondary-900">{{ __('ui.top_entering_items') }}</h3>
                             <p class="text-xs text-secondary-400">Berdasarkan periode yang dipilih</p>
                         </div>
                     </div>
                     <div class="flex-grow divide-y divide-secondary-100">
                         @forelse($topEntered as $item)
                             @php
                                  $rank = $loop->iteration;
                                  $badgeClass = match(true) {
                                      $rank === 1 => 'bg-amber-100 text-amber-700 shadow shadow-amber-200',
                                      $rank === 2 => 'bg-slate-100  text-slate-600 shadow shadow-slate-200',
                                      $rank === 3 => 'bg-orange-100 text-orange-700 shadow shadow-orange-200',
                                      default     => 'bg-secondary-100 text-secondary-500',
                                  };
                             @endphp
                             <div class="group flex items-center gap-4 px-6 py-3.5 hover:bg-emerald-50/50 transition-all duration-150 cursor-pointer"
                                  onclick="window.location.href='{{ route('inventory.show', $item->sparepart_id) }}'">
                                 <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold transition-transform duration-200 group-hover:scale-110 {{ $badgeClass }}">
                                     {{ $rank }}
                                 </div>
                                 <span class="flex-grow font-semibold text-secondary-800 text-sm group-hover:text-primary-700 transition-colors truncate">{{ $item->sparepart_name ?? 'Unknown' }}</span>
                                 <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs tabular-nums">
                                     <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                     {{ number_format($item->total_qty, 0, ',', '.') }}
                                 </span>
                             </div>
                         @empty
                              <div class="px-6 py-10 text-center text-secondary-400">
                                  <svg class="w-8 h-8 mx-auto text-secondary-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                  <p class="text-sm italic">{{ __('ui.no_data') }}</p>
                              </div>
                         @endforelse
                     </div>
                 </div>

             </div>

            <!-- Bagian Grafik -->
            <div x-show="showCharts && isLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4 animate-pulse print:hidden">
                <!-- Skeleton Donut -->
                <div class="card flex flex-col h-[400px]">
                    <div class="card-header border-b border-gray-100 p-5">
                        <div class="h-5 bg-gray-200 rounded w-48"></div>
                    </div>
                    <div class="card-body p-6 flex-grow flex items-center justify-center">
                        <div class="w-48 h-48 rounded-full border-8 border-gray-200"></div>
                    </div>
                </div>
                <!-- Skeleton Bar -->
                <div class="card flex flex-col h-[400px]">
                    <div class="card-header border-b border-gray-100 p-5">
                        <div class="h-5 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="card-body p-6 flex-grow flex items-end justify-around gap-2 px-10">
                        @for($i=0; $i<6; $i++)
                            <div class="w-12 bg-gray-200 rounded-t" style="height: {{ rand(30, 90) }}%"></div>
                        @endfor
                    </div>
                </div>
            </div>

            <div x-show="showCharts && !isLoading" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="grid grid-cols-1 lg:grid-cols-2 print:grid-cols-1 print:gap-y-8 gap-4 mb-4 print:break-inside-avoid">
                <!-- Grafik Donut -->
                <div class="card flex flex-col">
                    <div class="card-header border-b border-secondary-100 p-5 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900">{{ __('ui.stock_distribution_category') }}</h3>
 
                    </div>
                    <div class="card-body p-6 flex-grow flex items-center justify-center bg-white min-h-[300px]">
                        <div class="w-full h-full max-h-[300px]">
                            <canvas id="stockByCategoryChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Grafik Batang -->
                <div class="card flex flex-col">
                     <div class="card-header border-b border-secondary-100 p-5 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900">{{ __('ui.stock_location') }}</h3>

                    </div>
                    <div class="card-body p-6 flex-grow flex items-center justify-center bg-white min-h-[300px]">
                        <div class="w-full h-full max-h-[300px]">
                            <canvas id="stockByLocationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Bawah: Stok Rendah & Aktivitas -->
            <div class="grid grid-cols-1 lg:grid-cols-3 print:grid-cols-1 gap-4 print:gap-y-8">
                <!-- Skeleton Stok Rendah -->
                <div x-show="showLowStock && isLoading" 
                     class="card animate-pulse h-[400px] print:hidden"
                     :class="{ 'lg:col-span-3 print:col-span-1': !showRecent, 'lg:col-span-2 print:col-span-1': showRecent }">
                    <div class="card-header p-5 border-b border-gray-100 flex justify-between">
                        <div class="h-5 bg-gray-200 rounded w-48"></div>
                        <div class="h-4 bg-gray-200 rounded w-20"></div>
                    </div>
                    <div class="p-6 space-y-4">
                         <div class="flex gap-4 mb-4">
                             <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                             <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                             <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                             <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                         </div>
                         @for($i=0; $i<5; $i++)
                             <div class="h-10 bg-gray-100 rounded w-full"></div>
                         @endfor
                    </div>
                </div>

                <!-- Item Stok Rendah (2 kolom) -->
                <div x-show="showLowStock && !isLoading" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-4"
                     class="card bg-white shadow-lg transform hover:scale-[1.01] transition-all duration-300 border-none overflow-hidden print:break-inside-avoid" :class="{ 'lg:col-span-3 print:col-span-1': !showRecent, 'lg:col-span-2 print:col-span-1': showRecent }">
                    <div class="card-header p-5 bg-gradient-to-r from-amber-500 to-orange-500 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                             <div class="p-1.5 bg-white/20 text-white rounded-lg backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                             </div>
                            <h3 class="font-bold text-white">{{ __('ui.warning_low_stock') }}</h3>
                        </div>
                        <a href="{{ route('inventory.index', ['filter' => 'low_stock']) }}" class="text-sm text-white hover:text-amber-100 font-medium underline decoration-white/50">{{ __('ui.view_all') }}</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-secondary-500">
                            <thead class="text-xs text-secondary-700 uppercase bg-secondary-50 border-b border-secondary-200">
                                <tr>
                                    <th class="px-6 py-3 font-semibold tracking-wider">{{ __('ui.item') }}</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider hidden md:table-cell">{{ __('ui.categories') }}</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center">{{ __('ui.stock') }}</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center hidden md:table-cell">{{ __('ui.min_stock') }}</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center">{{ __('ui.status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-100">
                                <template x-for="item in lowStockItems" :key="item.id">
                                    <tr class="bg-white hover:bg-secondary-50 transition-colors cursor-pointer" @click="window.location.href = '/inventory/' + item.id">
                                        <td class="px-4 py-3 font-medium text-secondary-800" x-text="item.name || 'Unknown'"></td>
                                        <td class="px-6 py-4 hidden md:table-cell" x-text="item.category || '-'"></td>
                                        <td class="px-6 py-4 text-center font-bold text-danger-600" x-text="item.stock"></td>
                                        <td class="px-6 py-4 text-center text-secondary-600 hidden md:table-cell" x-text="item.minimum_stock"></td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="badge badge-danger" x-show="item.stock == 0">{{ __('ui.status_out_of_stock') }}</span>
                                            <span class="badge badge-warning" x-show="item.stock > 0">{{ __('ui.status_critical') }}</span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="lowStockItems.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-secondary-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-success-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p>{{ __('ui.all_stock_safe') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Skeleton Terkini -->
                <div x-show="showRecent && isLoading" class="card lg:col-span-1 animate-pulse h-[400px]">
                    <div class="card-header p-5 border-b border-gray-100 flex justify-between">
                        <div class="h-5 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="p-5 space-y-4">
                        @for($i=0; $i<5; $i++)
                            <div class="flex gap-4">
                                <div class="h-8 w-8 bg-gray-200 rounded-full flex-shrink-0"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-3 bg-gray-200 rounded w-full"></div>
                                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Aktivitas Terkini (1 kolom di web, penuh di cetak jika diperlukan) -->

                <div x-show="showRecent && !isLoading" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-4"
                     class="card p-0 flex flex-col h-full print-safe" :class="{ 'lg:col-span-3 print:col-span-1': !showLowStock, 'lg:col-span-1 print:col-span-1': showLowStock }">
                     <div class="card-header p-5 border-b border-secondary-100 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900">{{ __('ui.recent_activities') }}</h3>
                        <a href="{{ route('reports.activity-logs.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">{{ __('ui.view_all') }}</a>
                     </div>
                    <div class="card-body p-0 overflow-y-auto max-h-[500px] custom-scrollbar">
                        <div class="flex flex-col">
                            <!-- Alpine Loop -->
                            <template x-for="log in recentActivities" :key="log.id">
                                <div class="px-5 py-2.5 hover:bg-secondary-50 transition-colors group">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 group-hover:bg-primary-600 group-hover:text-white transition-all ring-2 ring-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0 cursor-pointer hover:bg-secondary-100 p-2 rounded-lg transition-colors"
                                             @click="viewActivityDetails(log)">
                                            <p class="text-sm font-medium text-secondary-900 line-clamp-2" x-text="log.description"></p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <p class="text-xs text-secondary-500 font-semibold" x-text="log.user_name || log.user?.name || 'Sistem'"></p>
                                                <span class="text-secondary-300">&bull;</span>
                                                <p class="text-xs text-secondary-400" x-text="log.created_at_diff"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Empty State -->
                            <div x-show="recentActivities.length === 0" class="px-5 py-12 text-center min-h-[300px] flex flex-col items-center justify-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-secondary-100 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-secondary-700">Belum ada aktivitas</p>
                                        <p class="text-xs text-secondary-400 mt-1">{{ __('ui.no_recent_activities') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Baru: Analitik & Perkiraan -->
            <!-- Skeleton Analitik -->
            <div x-show="showStats && isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4 animate-pulse">
                 <!-- Skeleton Stok Mati -->
                <div class="card h-[300px]">
                    <div class="card-header p-4 border-b border-gray-100">
                        <div class="h-4 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="p-4 space-y-3">
                        @for($i=0; $i<5; $i++)
                            <div class="flex justify-between">
                                <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                <div class="h-4 bg-gray-200 rounded w-10"></div>
                            </div>
                        @endfor
                    </div>
                </div>
                <!-- Skeleton Papan Peringkat -->
                <div class="card h-[300px]">
                    <div class="card-header p-4 border-b border-gray-100">
                        <div class="h-4 bg-gray-200 rounded w-40"></div>
                    </div>
                    <div class="p-4 space-y-3">
                        @for($i=0; $i<5; $i++)
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2 w-2/3">
                                    <div class="h-6 w-6 rounded-full bg-gray-200"></div>
                                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                                </div>
                                <div class="h-4 bg-gray-200 rounded w-16"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <div x-show="showStats && !isLoading"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4 print-grid-3">

                {{-- Widget Stok Mati — style: amber border, alternating rows, no rank badges, warning pill qty --}}
                <div x-show="showDeadStock" x-transition class="card flex flex-col overflow-hidden border-l-4 border-amber-400">
                    <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-secondary-900">{{ __('ui.dead_stock_title') }}</h3>
                            <p class="text-xs text-secondary-400">Barang tidak bergerak dalam periode ini</p>
                        </div>
                    </div>
                    <div class="flex-grow">
                        <template x-for="(item, index) in deadStockItems" :key="item.id">
                            <div class="group flex items-center gap-4 px-6 py-3.5 transition-all duration-150 cursor-pointer"
                                 :class="index % 2 === 0 ? 'bg-white hover:bg-amber-50/50' : 'bg-secondary-50/50 hover:bg-amber-50/50'"
                                 @click="window.location.href = '/inventory/' + item.id">
                                <div class="flex-shrink-0 w-2 h-2 rounded-full bg-amber-400"></div>
                                <span class="flex-grow font-semibold text-secondary-800 text-sm group-hover:text-primary-700 transition-colors truncate" x-text="item.name"></span>
                                <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-bold text-xs tabular-nums">
                                    <span x-text="item.stock"></span>&nbsp;{{ __('ui.units') }}
                                </span>
                            </div>
                        </template>
                        <div x-show="deadStockItems.length === 0" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-success-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-sm font-medium text-secondary-700">Semua barang bergerak aktif</p>
                                <p class="text-xs text-secondary-400">Tidak ada barang yang stagnan dalam periode ini</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Papan Peringkat Pengguna — style: user initial avatar, green pill action count --}}
                <div x-show="showLeaderboard" x-transition class="card flex flex-col overflow-hidden border-l-4 border-success-400">
                    <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-success-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-secondary-900">{{ __('ui.top_contributors_title') }}</h3>
                            <p class="text-xs text-secondary-400">Pengguna paling aktif dalam periode ini</p>
                        </div>
                    </div>
                    <div class="flex-grow divide-y divide-secondary-100">
                        <template x-for="(userLog, index) in activeUsers" :key="userLog.user_id || Math.random()">
                            <div class="group flex items-center gap-4 px-6 py-3.5 hover:bg-success-50/40 transition-all duration-150">
                                {{-- User initial avatar with rank-tinted colors --}}
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-transform duration-200 group-hover:scale-110"
                                     :class="{
                                        'bg-amber-100 text-amber-700 ring-2 ring-amber-300':  index === 0,
                                        'bg-slate-100  text-slate-600  ring-2 ring-slate-300': index === 1,
                                        'bg-orange-100 text-orange-700 ring-2 ring-orange-300': index === 2,
                                        'bg-success-100 text-success-700': index > 2
                                     }"
                                     x-text="userLog.user ? userLog.user.name.charAt(0).toUpperCase() : '?'"></div>
                                <span class="flex-grow font-semibold text-secondary-800 text-sm group-hover:text-primary-700 transition-colors truncate" x-text="userLog.user ? userLog.user.name : 'Unknown'"></span>
                                <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-success-100 text-success-700 font-bold text-xs tabular-nums">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span x-text="userLog.total_actions"></span> {{ __('ui.actions_count') }}
                                </span>
                            </div>
                        </template>
                        {{-- Empty State --}}
                        <div x-show="activeUsers.length === 0" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-secondary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm font-medium text-secondary-600">Belum ada aktivitas</p>
                                <p class="text-xs text-secondary-400">Data akan muncul setelah ada transaksi stok</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inline modal removed, using partial at the bottom --}}
            


                    </td></tr>
                </tbody>

                {{-- Footer statis di bawah setiap halaman PDF (Dikurangi kontennya agar tidak ganda dengan header) --}}
                <tfoot class="hidden export-show mt-8 pt-4 border-t border-secondary-200 w-full">
                    <tr><td>
                        <div class="text-center text-xs text-secondary-500">
                            Azventory Management System - Laporan Stok & Inventaris
                        </div>
                    </td></tr>
                </tfoot>
            </table> {{-- End print-container --}}

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Default Chart untuk konsistensi
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';
        Chart.defaults.scale.grid.color = '#f1f5f9';

        // =====================================================================
        // Helper: Update KPI Summary Badges dari data movement
        // =====================================================================
        function updateMovementKPI(movementData) {
            const totalMasuk = (movementData.masuk || []).reduce((a, b) => a + b, 0);
            const totalKeluar = (movementData.keluar || []).reduce((a, b) => a + b, 0);
            const net = totalMasuk - totalKeluar;

            const elMasuk = document.getElementById('kpi-masuk');
            const elKeluar = document.getElementById('kpi-keluar');
            const elNet = document.getElementById('kpi-net');
            const elNetDot = document.getElementById('kpi-net-dot');
            const elNetLabel = document.getElementById('kpi-net-label');
            const elNetBadge = document.getElementById('kpi-net-badge');

            // Trend elements
            const elMasukPct = document.getElementById('kpi-masuk-pct');
            const elKeluarPct = document.getElementById('kpi-keluar-pct');
            const elNetPct = document.getElementById('kpi-net-pct');

            if (elMasuk) elMasuk.textContent = totalMasuk.toLocaleString('id-ID');
            if (elKeluar) elKeluar.textContent = totalKeluar.toLocaleString('id-ID');
            if (elNet) elNet.textContent = (net >= 0 ? '+' : '') + net.toLocaleString('id-ID');

            const comp = movementData.comparison || {};
            
            const updateTrend = (el, val) => {
                if (!el) return;
                if (val === undefined || val === null) { el.textContent = ''; return; }
                const prefix = val > 0 ? '↑' : (val < 0 ? '↓' : '');
                el.textContent = `${prefix} ${Math.abs(val)}%`;
                el.className = `text-[10px] ml-1 font-bold ${val >= 0 ? 'text-emerald-600' : 'text-red-600'}`;
            };

            updateTrend(elMasukPct, comp.masuk_pct);
            updateTrend(elKeluarPct, comp.keluar_pct);
            updateTrend(elNetPct, comp.net_pct);

            if (elNetBadge && elNetDot && elNetLabel) {
                if (net > 0) {
                    elNetBadge.className = 'flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-1.5';
                    elNetDot.className = 'w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0';
                    elNetLabel.className = 'text-xs text-emerald-700 font-medium whitespace-nowrap';
                } else if (net < 0) {
                    elNetBadge.className = 'flex items-center gap-1.5 bg-red-50 border border-red-200 rounded-lg px-3 py-1.5';
                    elNetDot.className = 'w-2 h-2 rounded-full bg-red-500 flex-shrink-0';
                    elNetLabel.className = 'text-xs text-red-700 font-medium whitespace-nowrap';
                } else {
                    elNetBadge.className = 'flex items-center gap-1.5 bg-blue-50 border border-blue-200 rounded-lg px-3 py-1.5';
                    elNetDot.className = 'w-2 h-2 rounded-full bg-blue-400 flex-shrink-0';
                    elNetLabel.className = 'text-xs text-blue-700 font-medium whitespace-nowrap';
                }
            }
        }

        // =====================================================================
        // Grafik Pergerakan Stok — Grouped Bar Chart
        // =====================================================================

        // =====================================================================
        // Export Dashboard — PDF (Print) and PNG (html2canvas)
        // =====================================================================
        function exportDashboardPDF() {
            document.title = 'Dashboard Azventory - ' + new Date().toLocaleDateString('id-ID');
            window.print();
        }

        function exportDashboardPNG() {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-6 right-6 z-[9999] bg-secondary-900 text-white text-sm px-4 py-3 rounded-xl shadow-xl flex items-center gap-2';
            toast.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Menyiapkan gambar...';
            document.body.appendChild(toast);

            // Load html2canvas jika belum ada
            if (typeof html2canvas === 'undefined') {
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
                s.onload = () => doCapture(toast);
                document.head.appendChild(s);
            } else {
                doCapture(toast);
            }
        }

        function doCapture(toastEl) {
            document.body.classList.add('is-exporting'); // Add export class to trigger pure white mode

            // Tunggu sebentar agar CSS apply sebelum direkam
            setTimeout(() => {
                const target = document.querySelector('[x-data]') || document.body;
                html2canvas(target, {
                    scale: 1.5,
                    useCORS: true,
                    backgroundColor: '#ffffff',
                    windowWidth: 1280, // force desktop width so it doesn't squish
                    logging: false,
                }).then(canvas => {
                    document.body.classList.remove('is-exporting'); // Remove immediately
                    const link = document.createElement('a');
                    const d = new Date();
                    const dateStr = d.toISOString().slice(0, 10);
                    link.download = `dashboard-azventory-${dateStr}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                    if (toastEl) toastEl.remove();
                }).catch(() => {
                    document.body.classList.remove('is-exporting');
                    if (toastEl) toastEl.remove();
                    alert('Gagal mengambil screenshot. Gunakan opsi Cetak/PDF.');
                });
            }, 300);
        }

        // Muat data 7 hari via AJAX saat pertama load — ini adalah default tampilan chart
        const movementDataKey = { labels: [], masuk: [], keluar: [] };

        // Inisialisasi KPI Badge saat load
        updateMovementKPI(@json($movementData));

        // Fungsi pembuatan gradient (dipakai ulang)
        function makeGradient(ctx, color1, color2) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, color1);
            gradient.addColorStop(1, color2);
            return gradient;
        }

        const movCtx = document.getElementById('stockMovementChart').getContext('2d');
        const gradMasuk = makeGradient(movCtx, 'rgba(16,185,129,0.85)', 'rgba(16,185,129,0.15)');
        const gradKeluar = makeGradient(movCtx, 'rgba(239,68,68,0.85)', 'rgba(239,68,68,0.15)');

        // Jika tidak ada label (periode kosong), tampilkan placeholder
        const movLabels = movementDataKey.labels.length > 0 ? movementDataKey.labels : ['Tidak ada data'];
        const movMasuk  = movementDataKey.masuk.length > 0  ? movementDataKey.masuk  : [0];
        const movKeluar = movementDataKey.keluar.length > 0 ? movementDataKey.keluar : [0];

        // Plugin Custom untuk Garis Vertikal (Crosshair)
        const verticalLine = {
            id: 'verticalLine',
            beforeDraw(chart) {
                if (chart.tooltip?._active?.length) {
                    const ctx = chart.ctx;
                    const x = chart.tooltip._active[0].element.x;
                    const topY = chart.scales.y.top;
                    const bottomY = chart.scales.y.bottom;

                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(x, topY);
                    ctx.lineTo(x, bottomY);
                    ctx.lineWidth = 1;
                    ctx.strokeStyle = 'rgba(100, 116, 139, 0.4)'; // Gray-400
                    ctx.setLineDash([4, 4]); // Dashed
                    ctx.stroke();
                    ctx.restore();
                }
            }
        };

        let movementChart = new Chart(movCtx, {
            type: 'line',
            plugins: [verticalLine], // Register plugin
            data: {
                labels: movLabels,
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: movMasuk,
                        backgroundColor: gradMasuk,
                        borderColor: '#10b981',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    },
                    {
                        label: 'Barang Keluar',
                        data: movKeluar,
                        backgroundColor: gradKeluar,
                        borderColor: '#ef4444',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'rectRounded',
                            padding: 16,
                            font: { size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleColor: '#e2e8f0',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            title(ctx) {
                                return ctx[0].label;
                            },
                            label(ctx) {
                                const val = ctx.parsed.y.toLocaleString('id-ID');
                                return `  ${ctx.dataset.label}: ${val} unit`;
                            },
                            afterBody(ctx) {
                                if (ctx.length < 2) return '';
                                const masuk  = ctx.find(c => c.datasetIndex === 0)?.parsed.y ?? 0;
                                const keluar = ctx.find(c => c.datasetIndex === 1)?.parsed.y ?? 0;
                                const net = masuk - keluar;
                                const prefix = net >= 0 ? '+' : '';
                                return [`  ─────────────────`, `  Net Stok: ${prefix}${net.toLocaleString('id-ID')} unit`];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 10, // Batasi jumlah label agar tidak sesak
                            padding: 10
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148,163,184,0.15)',
                            borderDash: [4, 4]
                        },
                        ticks: {
                            callback: val => val.toLocaleString('id-ID')
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 45,
                            autoSkipPadding: 8
                        }
                    }
                }
            }
        });


        // =====================================================================
        // Grafik Donut: Stok berdasarkan Kategori
        // =====================================================================
        const stockByCategoryData = @json($stockByCategory);
        const catCtx = document.getElementById('stockByCategoryChart').getContext('2d');
        // Cool-tone Gradient Colors (Blue -> Violet -> Pink -> Cyan)
        const baseColors = [
            '#3b82f6', // Blue
            '#8b5cf6', // Violet
            '#ec4899', // Pink
            '#06b6d4', // Cyan
            '#6366f1', // Indigo
            '#14b8a6', // Teal
        ];
        const chartColors = baseColors.map(c => {
            const grd = catCtx.createLinearGradient(0, 0, 0, 300);
            grd.addColorStop(0, c);
            grd.addColorStop(1, c + '90'); // Less transparency for richer color
            return grd;
        });
        const chartColorsBorder = baseColors;

        // Total untuk persentase tooltip
        const catTotal = Object.values(stockByCategoryData).reduce((a, b) => a + b, 0);

        // Responsive legend position
        const isSmallScreen = window.innerWidth < 640;

        // Custom Plugin untuk menggambar dashed ring yang presisi di tengah chart
        const outerDashedRing = {
            id: 'outerDashedRing',
            beforeDraw(chart) {
                const {ctx, chartArea: {top, bottom, left, right, width, height}} = chart;
                const centerX = (left + right) / 2;
                const centerY = (top + bottom) / 2;
                
                // Pastikan radius ring dihitung dari radius chart sebenarnya
                const meta = chart.getDatasetMeta(0);
                if (meta.data.length > 0) {
                    const outerRadius = meta.data[0].outerRadius;
                    const ringRadius = outerRadius + 15; // Jarak ring dari chart

                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, ringRadius, 0, 2 * Math.PI);
                    ctx.lineWidth = 2;
                    ctx.strokeStyle = '#e0e7ff'; // Indigo-100
                    ctx.setLineDash([6, 6]); // Garis putus-putus
                    ctx.stroke();
                    ctx.restore();
                }
            }
        };

        let stockCategoryChart = new Chart(document.getElementById('stockByCategoryChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(stockByCategoryData),
                datasets: [{
                    label: '{{ __('ui.total_stock') }}',
                    data: Object.values(stockByCategoryData),
                    backgroundColor: chartColors,
                    borderColor: '#ffffff',
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            plugins: [outerDashedRing, {
                id: 'noData',
                afterDraw: (chart) => {
                    const dataCount = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    if (dataCount === 0) {
                        const { ctx, chartArea: { top, bottom, left, right, width, height } } = chart;
                        ctx.save();
                        ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
                        ctx.font = '14px sans-serif'; ctx.fillStyle = '#94a3b8';
                        ctx.fillText('Tidak ada data distribusi', left + width / 2, top + height / 2);
                        ctx.restore();
                    }
                }
            }],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%', // Balanced & Modern
                layout: {
                    padding: 40 // Extra padding for ring
                },
                elements: {
                    arc: {
                        borderWidth: 0,
                        borderColor: '#ffffff',
                        borderRadius: 5,
                        hoverOffset: 10
                    }
                },
                plugins: {
                    legend: {
                        position: isSmallScreen ? 'bottom' : 'right',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20,
                            font: { size: 12 },
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleColor: '#e2e8f0',
                        bodyColor: '#cbd5e1',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label(ctx) {
                                const val = ctx.parsed;
                                const pct = catTotal > 0 ? ((val / catTotal) * 100).toFixed(1) : 0;
                                return `  ${ctx.label}: ${val.toLocaleString('id-ID')} unit (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });

        // =====================================================================
        // Grafik Batang: Stok berdasarkan Lokasi
        // =====================================================================
        const stockByLocationData = @json($stockByLocation);
        const locCtx = document.getElementById('stockByLocationChart').getContext('2d');
        const gradLoc = locCtx.createLinearGradient(0, 0, 0, 280);
        gradLoc.addColorStop(0, 'rgba(59,130,246,0.9)');
        gradLoc.addColorStop(1, 'rgba(59,130,246,0.2)');

        let stockLocationChart = new Chart(locCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(stockByLocationData),
                datasets: [{
                    label: '{{ __('ui.total_stock') }}',
                    data: Object.values(stockByLocationData),
                    backgroundColor: gradLoc,
                    borderColor: '#3b82f6',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.65,
                    maxBarThickness: 60
                }]
            },
            plugins: [{
                id: 'noData',
                afterDraw: (chart) => {
                    const dataCount = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    if (dataCount === 0) {
                        const { ctx, chartArea: { top, bottom, left, right, width, height } } = chart;
                        ctx.save();
                        ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
                        ctx.font = '14px sans-serif'; ctx.fillStyle = '#94a3b8';
                        ctx.fillText('Tidak ada data lokasi', left + width / 2, top + height / 2);
                        ctx.restore();
                    }
                }
            }],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleColor: '#e2e8f0',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label(ctx) {
                                return `  Stok: ${ctx.parsed.y.toLocaleString('id-ID')} unit`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148,163,184,0.15)',
                            borderDash: [4, 4]
                        },
                        ticks: {
                            callback: val => val.toLocaleString('id-ID')
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 35,
                            autoSkipPadding: 6
                        }
                    }
                }
            }
        });

        // =====================================================================
        // Global Function untuk Update Chart (Real-time Safe)
        // Dipanggil oleh real-time listener Alpine.js saat ada event baru
        // =====================================================================
        window.updateDashboardCharts = function(movementData, stockByCategory, stockByLocation) {
            // Update Movement Chart + KPI Badges
            if (movementData && movementChart) {
                const newLabels  = movementData.labels.length > 0 ? movementData.labels : ['Tidak ada data'];
                const newMasuk   = movementData.masuk.length  > 0 ? movementData.masuk  : [0];
                const newKeluar  = movementData.keluar.length > 0 ? movementData.keluar : [0];
                movementChart.data.labels = newLabels;
                movementChart.data.datasets[0].data = newMasuk;
                movementChart.data.datasets[1].data = newKeluar;
                movementChart.update();
                // Sinkronkan KPI badges dengan data terbaru
                updateMovementKPI(movementData);
            }

            // Update Category Chart
            if (stockByCategory && stockCategoryChart) {
                stockCategoryChart.data.labels = Object.keys(stockByCategory);
                stockCategoryChart.data.datasets[0].data = Object.values(stockByCategory);
                stockCategoryChart.update();
            }

            // Update Location Chart
            if (stockByLocation && stockLocationChart) {
                stockLocationChart.data.labels = Object.keys(stockByLocation);
                stockLocationChart.data.datasets[0].data = Object.values(stockByLocation);
                stockLocationChart.update();
            }
        };

        // =====================================================================
        // Alpine Component: Tab Period Global
        // Mengelola state panel "Custom" (Opsi F)
        // =====================================================================
        function globalPeriodFilter() {
            return {
                // Buka panel custom secara otomatis jika periode aktif = custom
                showCustom: {{ in_array($period ?? 'today', ['custom','custom_year']) ? 'true' : 'false' }},
            };
        }

        // =====================================================================
        // Opsi C: Quick-filter per-widget Pergerakan Stok
        // Fetch data movement dari endpoint ringan tanpa reload halaman
        // =====================================================================
        let movementActiveRange = 30; // default aktif

        async function fetchMovementData(range) {
            movementActiveRange = range;

            // Update tampilan state tombol aktif
            document.querySelectorAll('.mov-range-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'shadow-sm', 'text-primary-700');
                btn.classList.add('text-secondary-600');
            });
            const activeBtn = document.getElementById('mov-btn-' + range);
            if (activeBtn) {
                activeBtn.classList.add('bg-white', 'shadow-sm', 'text-primary-700');
                activeBtn.classList.remove('text-secondary-600');
            }

            // Tampilkan loading state pada canvas
            const canvas = document.getElementById('stockMovementChart');
            if (canvas) canvas.style.opacity = '0.5';

            // Update label periode dan reset error state
            const periodLabel = document.getElementById('movement-period-label');
            const rangeLabel = range === 7 ? '7 hari terakhir' : range === 30 ? '30 hari terakhir' : '3 bulan terakhir';
            if (periodLabel) periodLabel.textContent = 'Data: ' + rangeLabel;
            const errEl = document.getElementById('movement-error');
            const wrapEl = document.getElementById('movement-chart-wrap');
            if (errEl)  errEl.classList.replace('flex', 'hidden');
            if (wrapEl) wrapEl.classList.remove('hidden');

            try {
                const response = await fetch('{{ route("dashboard.movement-data") }}?range=' + range, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                if (!response.ok) throw new Error('Gagal memuat data movement');

                const data = await response.json();

                // Update chart dengan data baru
                if (movementChart) {
                    const newLabels  = (data.labels  || []).length > 0 ? data.labels  : ['Tidak ada data'];
                    const newMasuk   = (data.masuk   || []).length > 0 ? data.masuk   : [0];
                    const newKeluar  = (data.keluar  || []).length > 0 ? data.keluar  : [0];

                    movementChart.data.labels = newLabels;
                    movementChart.data.datasets[0].data = newMasuk;
                    movementChart.data.datasets[1].data = newKeluar;
                    movementChart.update('active');
                }

                // Update KPI badges
                updateMovementKPI(data);

            } catch (err) {
                console.error('fetchMovementData error:', err);
                // Tampilkan error state di chart
                const errEl2 = document.getElementById('movement-error');
                const wrapEl2 = document.getElementById('movement-chart-wrap');
                if (errEl2)  errEl2.classList.replace('hidden', 'flex');
                if (wrapEl2) wrapEl2.classList.add('hidden');
            } finally {
                // Hapus loading state
                if (canvas) canvas.style.opacity = '1';
            }
        }
    </script>
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const startInput = document.getElementById('start_date');
                const endInput = document.getElementById('end_date');
                const pickerInput = document.getElementById('date_range_picker');
                
                if (pickerInput) {
                    const picker = flatpickr("#date_range_picker", {
                        locale: {
                            rangeSeparator: " - ",
                            weekdays: {
                                shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                                longhand: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
                            },
                            months: {
                                shorthand: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
                                longhand: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
                            }
                        },
                        mode: "range",
                        position: "below center", // Precise alignment
                        monthSelectorType: 'static', // MUST be static to replace with custom
                        conjunction: " - ",
                        altInput: true,
                        altFormat: "j F Y",
                        dateFormat: "Y-m-d",
                        altInputClass: "w-full pl-12 pr-4 py-3 text-sm bg-secondary-50/50 border-secondary-200 rounded-2xl text-secondary-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all cursor-pointer font-semibold placeholder:text-secondary-400",
                        allowInput: false,
                        animate: true,
                        minDate: "2010-01-01",
                        maxDate: new Date().getFullYear() + 20 + "-12-31",
                        defaultDate: ["{{ \Carbon\Carbon::parse($start)->format('Y-m-d') }}", "{{ \Carbon\Carbon::parse($end)->format('Y-m-d') }}"],
                        onOpen: function(selectedDates, dateStr, instance) {
                            // Selalu lompat ke hari ini saat dibuka agar tidak "nyasar" ke Januari
                            instance.jumpToDate(new Date());
                        },
                        onReady: function(selectedDates, dateStr, instance) {
                            const injectCustomUI = () => {
                                const container = instance.calendarContainer.querySelector('.flatpickr-current-month');
                                if (!container) return;

                                container.innerHTML = `
                                    <div class="custom-month-selector" id="custom-month-btn">
                                        <span class="month-name">
                                            <span class="hidden sm:inline">${instance.l10n.months.longhand[instance.currentMonth]}</span>
                                            <span class="sm:hidden font-extrabold text-lg">${instance.currentMonth + 1}</span>
                                        </span>
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin='round' stroke-width='2.5' d='M19 9l-7 7-7-7'></path></svg>
                                        <div class="custom-month-list" id="custom-month-panel">
                                            ${instance.l10n.months.longhand.map((m, i) => `
                                                <div class="${i === instance.currentMonth ? 'active' : ''}" data-index="${i}">${m}</div>
                                            `).join('')}
                                        </div>
                                    </div>
                                    <div class="numInputWrapper">
                                        <input class="numInput cur-year" type="text" inputmode="numeric" value="${instance.currentYear}">
                                    </div>
                                `;

                                const trigger = container.querySelector('#custom-month-btn');
                                const panel = container.querySelector('#custom-month-panel');
                                const yearInput = container.querySelector('.numInput');

                                trigger.addEventListener('click', (e) => {
                                    e.stopPropagation();
                                    const isVisible = panel.style.display === 'block';
                                    panel.style.display = isVisible ? 'none' : 'block';
                                    trigger.classList.toggle('active', !isVisible);
                                });

                                panel.querySelectorAll('div').forEach(item => {
                                    item.addEventListener('click', (e) => {
                                        const index = parseInt(item.getAttribute('data-index'));
                                        instance.changeMonth(index);
                                        panel.style.display = 'none';
                                        trigger.classList.remove('active');
                                    });
                                });

                                // Year Input Logic - Block 'e' and non-digits
                                if (yearInput) {
                                    yearInput.addEventListener('keydown', (e) => {
                                        // Allow navigation keys, backspace, delete, etc.
                                        if (['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter'].includes(e.key)) return;
                                        if (!/[0-9]/.test(e.key)) e.preventDefault();
                                    });

                                    yearInput.addEventListener('input', function() {
                                        this.value = this.value.replace(/[^0-9]/g, ''); // Double safety
                                        if (this.value.length > 4) this.value = this.value.slice(0, 4);
                                        if (this.value.length === 4) instance.changeYear(parseInt(this.value));
                                    });

                                    yearInput.addEventListener('blur', function() {
                                        const val = parseInt(this.value);
                                        if (isNaN(val) || val < 2010) {
                                            this.value = new Date().getFullYear();
                                            instance.changeYear(parseInt(this.value));
                                        }
                                    });
                                }
                            };

                            injectCustomUI();

                            // Ensure logic persists on navigation
                            instance.currentInject = injectCustomUI;
                            document.addEventListener('click', () => {
                                const panels = document.querySelectorAll('.custom-month-list');
                                panels.forEach(p => p.style.display = 'none');
                                document.querySelectorAll('.custom-month-selector').forEach(s => s.classList.remove('active'));
                            });
                        },
                        onMonthChange: function(selectedDates, dateStr, instance) {
                            if (instance.currentInject) instance.currentInject();
                        },
                        onYearChange: function(selectedDates, dateStr, instance) {
                            if (instance.currentInject) instance.currentInject();
                        },
                        onChange: function(selectedDates, dateStr, instance) {
                            if (selectedDates.length === 2) {
                                // Validasi: max range 365 hari
                                const diffDays = Math.round((selectedDates[1] - selectedDates[0]) / (1000 * 60 * 60 * 24));
                                if (diffDays > 365) {
                                    instance.clear();
                                    if (startInput) startInput.value = '';
                                    if (endInput) endInput.value = '';
                                    if (window.showToast) window.showToast('warning', 'Rentang tanggal maksimal 365 hari. Silakan pilih ulang.');
                                    return;
                                }
                                if (startInput) startInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
                                if (endInput) endInput.value = instance.formatDate(selectedDates[1], "Y-m-d");
                            }
                        }
                    });

                    window.setPickerRange = function(days) {
                        const end = new Date();
                        const start = new Date();
                        start.setDate(end.getDate() - (days > 0 ? days - 1 : 0)); // Adjust to include today if 7/30
                        picker.setDate([start, end], true);
                    };

                    window.resetCustomPicker = function() {
                        // Reset ke default "Hari Ini" (tanpa query string)
                        window.location.href = '{{ route("dashboard.superadmin") }}';
                    };

                    // Responsive altFormat: Gunakan angka jika layar sempit agar tidak terpotong
                    const updateResponsiveFormat = () => {
                        const isMobile = window.innerWidth < 480;
                        picker.set('altFormat', isMobile ? "d/m/y" : "j F Y");
                    };
                    window.addEventListener('resize', updateResponsiveFormat);
                    updateResponsiveFormat();
                }
            });
        </script>
    @endpush
    </div>
</div>
</x-app-layout>
