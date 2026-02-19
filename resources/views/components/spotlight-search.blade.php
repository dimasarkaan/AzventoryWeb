<div 
    x-data="{ 
        open: false, 
        query: '', 
        results: { menus: [], spareparts: [], users: [] },
        isLoading: false,
        selectedIndex: 0,
        totalItems: 0,
        init() {
            this.$watch('query', (value) => {
                if (value.length < 2) {
                    this.results = { menus: [], spareparts: [], users: [] };
                    this.totalItems = 0;
                    return;
                }
                this.search();
            });
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => this.$refs.searchInput.focus());
                this.query = '';
                this.results = { menus: [], spareparts: [], users: [] };
            }
        },
        search() {
            this.isLoading = true;
            fetch('{{ route('global-search') }}?query=' + encodeURIComponent(this.query))
                .then(response => response.json())
                .then(data => {
                    this.results = data;
                    this.calculateTotalItems();
                    this.selectedIndex = 0;
                    this.isLoading = false;
                })
                .catch(() => {
                    this.isLoading = false;
                });
        },
        calculateTotalItems() {
            this.totalItems = this.results.menus.length + this.results.spareparts.length + this.results.users.length;
        },
        goDown() {
            if (this.selectedIndex < this.totalItems - 1) {
                this.selectedIndex++;
                this.scrollToSelected();
            }
        },
        goUp() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
                this.scrollToSelected();
            }
        },
        scrollToSelected() {
            const el = document.getElementById('result-item-' + this.selectedIndex);
            if (el) el.scrollIntoView({ block: 'nearest' });
        },
        selectResult() {
            const el = document.getElementById('result-item-' + this.selectedIndex);
            if (el) el.click();
        }
    }"
    @keydown.window.ctrl.k.prevent="toggle()"
    @keydown.window.cmd.k.prevent="toggle()"
    @keydown.escape.window="open = false"
    class="relative z-50"
    style="display: none;"
    x-show="open"
    x-cloak
>
    <!-- Latar Belakang -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity" 
         @click="open = false"></div>

    <!-- Panel Modal -->
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all">

            <!-- Input Pencarian -->
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1114 0 7 7 0 01-14 0z" clip-rule="evenodd" />
                </svg>
                <input type="text" 
                       x-ref="searchInput"
                       x-model.debounce.300ms="query"
                       @keydown.arrow-down.prevent="goDown()"
                       @keydown.arrow-up.prevent="goUp()"
                       @keydown.enter.prevent="selectResult()"
                       class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm" 
                       placeholder="{{ __('ui.search_placeholder') }}" 
                       role="combobox" 
                       aria-expanded="false" 
                       aria-controls="options">
                <div class="absolute right-3 top-3.5 flex items-center gap-1">
                    <span class="hidden sm:inline-block rounded bg-gray-100 px-1.5 py-0.5 text-xs font-medium text-gray-500 border border-gray-200">ESC</span>
                </div>
            </div>

            <!-- Hasil -->
            <ul x-show="query.length >= 2 && totalItems > 0" class="max-h-96 scroll-py-3 overflow-y-auto p-3" id="options" role="listbox">
                @php $globalIndex = 0; @endphp
                
                <!-- Menu -->
                <template x-if="results.menus.length > 0">
                    <li class="mb-2">
                        <h2 class="px-2.5 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50/50 rounded-md mb-1">{{ __('ui.search_menu') }}</h2>
                        <ul class="text-sm text-gray-700">
                            <template x-for="(item, index) in results.menus" :key="item.url">
                                <li class="group flex cursor-default select-none items-center rounded-md p-2 hover:bg-primary-50 hover:text-primary-700 transition"
                                    :id="'result-item-' + (index)"
                                    :class="{ 'bg-primary-50 text-primary-700': selectedIndex === index }"
                                    @click="window.location.href = item.url"
                                    role="option" 
                                    tabindex="-1">
                                    <div class="flex h-8 w-8 flex-none items-center justify-center rounded-lg bg-white border border-gray-200 group-hover:border-primary-200 shadow-sm">
                                        <!-- Fallback Ikon Sederhana -->
                                        <svg class="h-5 w-5 text-gray-400 group-hover:text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    </div>
                                    <span class="ml-3 flex-auto truncate" x-text="item.title"></span>
                                    <span class="ml-3 flex-none text-xs font-medium text-gray-400 group-hover:text-primary-400">{{ __('ui.search_jump_to') }}</span>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>

                <!-- Inventaris -->
                <template x-if="results.spareparts.length > 0">
                    <li class="mb-2">
                         <h2 class="px-2.5 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50/50 rounded-md mb-1">{{ __('ui.search_inventory') }}</h2>
                        <ul class="text-sm text-gray-700">
                            <template x-for="(item, index) in results.spareparts" :key="item.id">
                                <li class="group flex cursor-default select-none items-center rounded-md p-2 hover:bg-primary-50 hover:text-primary-700 transition"
                                    :id="'result-item-' + (results.menus.length + index)"
                                    :class="{ 'bg-primary-50 text-primary-700': selectedIndex === (results.menus.length + index) }"
                                    @click="window.location.href = item.url"
                                    role="option" 
                                    tabindex="-1">
                                    <img :src="item.image" x-show="item.image" class="h-10 w-10 flex-none rounded-lg object-cover bg-gray-100 border border-gray-200" alt="">
                                    <div x-show="!item.image" class="h-10 w-10 flex-none rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200">
                                         <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    </div>
                                    <div class="ml-3 flex-auto truncate">
                                         <p class="font-medium" x-text="item.title"></p>
                                         <p class="text-xs text-gray-500 group-hover:text-primary-600" x-text="item.subtitle"></p>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>

                 <!-- Pengguna -->
                 <template x-if="results.users.length > 0">
                    <li class="mb-2">
                         <h2 class="px-2.5 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50/50 rounded-md mb-1">{{ __('ui.search_users') }}</h2>
                        <ul class="text-sm text-gray-700">
                            <template x-for="(item, index) in results.users" :key="item.id">
                                <li class="group flex cursor-default select-none items-center rounded-md p-2 hover:bg-primary-50 hover:text-primary-700 transition"
                                    :id="'result-item-' + (results.menus.length + results.spareparts.length + index)"
                                    :class="{ 'bg-primary-50 text-primary-700': selectedIndex === (results.menus.length + results.spareparts.length + index) }"
                                    @click="window.location.href = item.url"
                                    role="option" 
                                    tabindex="-1">
                                    <div class="h-8 w-8 flex-none rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold border border-primary-200">
                                        <span x-text="item.title.substring(0,2).toUpperCase()"></span>
                                    </div>
                                    <div class="ml-3 flex-auto truncate">
                                         <p class="font-medium" x-text="item.title"></p>
                                         <p class="text-xs text-gray-500 group-hover:text-primary-600" x-text="item.subtitle"></p>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>
            </ul>

            <!-- State Kosong -->
            <div x-show="query.length >= 2 && totalItems === 0 && !isLoading" class="px-6 py-14 text-center text-sm sm:px-14">
                <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="mt-4 font-semibold text-gray-900">{{ __('ui.search_no_results') }}</p>
                <p class="mt-2 text-gray-500">{{ __('ui.search_no_results_desc') }}</p>
            </div>
            
             <!-- State Loading -->
             <!-- Skeleton Loading State -->
            <div x-show="isLoading" class="max-h-96 scroll-py-3 overflow-y-auto p-3 space-y-4">
                <!-- Grup Skeleton 1 (Menu) -->
                <div>
                     <div class="h-4 w-16 bg-gray-100 rounded mb-2 animate-pulse"></div>
                     <div class="space-y-1">
                        <div class="flex items-center p-2 rounded-md">
                            <div class="h-8 w-8 rounded-lg bg-gray-100 animate-pulse"></div>
                            <div class="ml-3 h-4 w-32 bg-gray-100 rounded animate-pulse"></div>
                            <div class="ml-auto h-3 w-12 bg-gray-100 rounded animate-pulse"></div>
                        </div>
                        <div class="flex items-center p-2 rounded-md">
                            <div class="h-8 w-8 rounded-lg bg-gray-100 animate-pulse"></div>
                            <div class="ml-3 h-4 w-24 bg-gray-100 rounded animate-pulse"></div>
                            <div class="ml-auto h-3 w-12 bg-gray-100 rounded animate-pulse"></div>
                        </div>
                     </div>
                </div>

                <!-- Grup Skeleton 2 (Inventaris) -->
                 <div>
                     <div class="h-4 w-20 bg-gray-100 rounded mb-2 animate-pulse"></div>
                     <div class="space-y-1">
                        <div class="flex items-center p-2 rounded-md">
                            <div class="h-10 w-10 rounded-lg bg-gray-100 animate-pulse"></div>
                            <div class="ml-3 space-y-1.5 flex-1">
                                <div class="h-3.5 w-40 bg-gray-100 rounded animate-pulse"></div>
                                <div class="h-2.5 w-24 bg-gray-100 rounded animate-pulse"></div>
                            </div>
                        </div>
                        <div class="flex items-center p-2 rounded-md">
                            <div class="h-10 w-10 rounded-lg bg-gray-100 animate-pulse"></div>
                            <div class="ml-3 space-y-1.5 flex-1">
                                <div class="h-3.5 w-32 bg-gray-100 rounded animate-pulse"></div>
                                <div class="h-2.5 w-20 bg-gray-100 rounded animate-pulse"></div>
                            </div>
                        </div>
                        <div class="flex items-center p-2 rounded-md">
                            <div class="h-10 w-10 rounded-lg bg-gray-100 animate-pulse"></div>
                            <div class="ml-3 space-y-1.5 flex-1">
                                <div class="h-3.5 w-36 bg-gray-100 rounded animate-pulse"></div>
                                <div class="h-2.5 w-16 bg-gray-100 rounded animate-pulse"></div>
                            </div>
                        </div>
                     </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex flex-wrap items-center bg-gray-50 px-4 py-2.5 text-xs text-gray-500">
               <span class="mx-1 font-medium text-gray-900">{{ __('ui.search_enter') }}</span>
               {{ __('ui.search_select') }}
               <span class="mx-1 ml-3 font-medium text-gray-900">↑↓</span>
               {{ __('ui.search_navigate') }}
               <span class="mx-1 ml-3 font-medium text-gray-900">{{ __('ui.search_esc') }}</span>
               {{ __('ui.search_close') }}
            </div>
        </div>
    </div>
</div>
