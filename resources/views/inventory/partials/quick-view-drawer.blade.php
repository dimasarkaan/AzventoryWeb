<div x-data="{ 
        showQuickView: false, 
        loading: false, 
        content: '',
        url: '',
        openDrawer(e) {
            if(this.showQuickView) return; // Prevent double trigger
            this.showQuickView = true;
            this.loading = true;
            this.content = '';
            this.url = e.detail.url;
            
            if (e.detail.id) {
                window.dispatchEvent(new CustomEvent('quick-view-opened', { detail: { id: e.detail.id } }));
            }
            
            // Fetch content via AJAX
            fetch(this.url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => response.text())
            .then(html => {
                this.content = html;
                this.loading = false;
            })
            .catch(error => {
                this.content = `<div class='p-6 text-center text-danger-500'>Gagal memuat data. Silakan coba lagi.</div>`;
                this.loading = false;
            });
        }
    }" 
    @open-quick-view.window="openDrawer($event)"
    @keydown.escape.window="showQuickView = false"
    class="relative z-[100]" 
    aria-labelledby="slide-over-title" 
    role="dialog" 
    aria-modal="true"
    x-init="$watch('showQuickView', value => { if(!value) window.dispatchEvent(new CustomEvent('quick-view-closed')) })"
    x-cloak>
    
    <!-- Background backdrop -->
    <div x-show="showQuickView" 
         x-transition:enter="ease-in-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in-out duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-secondary-900/40 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                <!-- Slide-over panel -->
                <div x-show="showQuickView" 
                     @click.away="showQuickView = false"
                     x-transition:enter="transform transition ease-in-out duration-400 sm:duration-500" 
                     x-transition:enter-start="translate-x-full" 
                     x-transition:enter-end="translate-x-0" 
                     x-transition:leave="transform transition ease-in-out duration-400 sm:duration-500" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="translate-x-full" 
                     class="pointer-events-auto w-screen max-w-md">
                     
                    <div class="flex h-full flex-col bg-white shadow-2xl">
                        <!-- Header -->
                        <div class="px-4 py-4 sm:px-6 bg-secondary-50 border-b border-secondary-100 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-secondary-900" id="slide-over-title">
                                Quick View
                            </h2>
                            <div class="ml-3 flex h-7 items-center gap-2">
                                <button type="button" @click="showQuickView = false" class="rounded-md bg-white text-secondary-400 hover:text-secondary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 shadow-sm border border-secondary-200 p-1">
                                    <span class="sr-only">Close panel</span>
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Body -->
                        <div class="relative flex-1 px-4 py-6 sm:px-6 overflow-y-auto" :class="{'flex items-center justify-center': loading}">
                            <!-- Loading State -->
                            <div x-show="loading" class="flex flex-col items-center justify-center text-secondary-400 space-y-3">
                                <svg class="animate-spin h-8 w-8 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span class="text-sm font-medium">Memuat data...</span>
                            </div>
                            
                            <!-- Dynamic Content -->
                            <div x-show="!loading" x-html="content" class="h-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>