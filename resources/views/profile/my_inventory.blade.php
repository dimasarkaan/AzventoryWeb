<x-app-layout>
    @php
        /** 
         * @var \Illuminate\Database\Eloquent\Collection|\App\Models\Borrowing[] $activeBorrowings 
         * @var \Illuminate\Database\Eloquent\Collection|\App\Models\Borrowing[] $historyBorrowings 
         */
    @endphp
    <div class="py-6" 
         x-data="{ 
            activeTab: 'active',
            returnModalOpen: false,
            selectedBorrowingId: null,
            maxReturnQty: 1,
            returnQty: 1,
            returnCondition: '',
            returnNotes: '',
            errors: {},
            successMessage: '',
            isSubmitting: false,
            
            // Dropdown Logic
            dropdownOpen: false,
            conditionLabel: 'Pilih Kondisi',
            conditionOptions: [
                { value: 'good', label: 'Baik (Layak Pakai)' },
                { value: 'bad', label: 'Rusak (Perlu Perbaikan/Ganti)' },
                { value: 'lost', label: 'Hilang' }
            ],

            selectCondition(option) {
                this.conditionLabel = option.label;
                this.returnCondition = option.value;
                this.dropdownOpen = false;
            },

            get isValid() {
                return this.returnQty > 0 && 
                       this.returnQty <= this.maxReturnQty && 
                       this.returnCondition !== ''; 
            },
            
            openReturnModal(borrowing) {
                this.selectedBorrowingId = borrowing.id;
                this.maxReturnQty = borrowing.quantity; 
                this.returnQty = 1;
                this.returnCondition = '';
                this.conditionLabel = 'Pilih Kondisi';
                this.returnNotes = '';
                this.errors = {};
                this.successMessage = '';
                this.returnModalOpen = true;
            },
            
            getItemColor(condition) {
                if (condition === 'good') return 'bg-success-500';
                if (condition === 'bad') return 'bg-warning-500';
                if (condition === 'lost') return 'bg-danger-500';
                return 'bg-secondary-400';
            },

            getBadgeColor(condition) {
                if (condition === 'good') return 'bg-success-100 text-success-800';
                if (condition === 'bad') return 'bg-warning-100 text-warning-800';
                if (condition === 'lost') return 'bg-danger-100 text-danger-800';
                return 'bg-secondary-100 text-secondary-800';
            },
            
            async submitReturn(e) {
                if (!this.isValid) return;
                this.isSubmitting = true;
                this.errors = {};
                this.successMessage = '';

                const formData = new FormData(e.target);

                try {
                    const response = await fetch(e.target.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok) {
                        this.successMessage = data.message || 'Berhasil dikembalikan!';
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        if (response.status === 422) {
                            this.errors = data.errors;
                        } else {
                            window.showAlert('Error', data.message || 'Terjadi kesalahan sistem.', 'error');
                        }
                    }
                } catch (error) {
                    console.error('Submission error:', error);
                    window.showAlert('Error', 'Gagal menghubungi server.', 'error');
                } finally {
                    this.isSubmitting = false;
                }
            }
         }">
         
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('ui.my_inventory_title') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">
                        {{ __('ui.my_inventory_desc') }}
                    </p>
                </div>
                <a href="{{ route('profile.edit') }}" class="btn btn-secondary w-full sm:w-auto text-center">
                    {{ __('ui.back_to_profile') }}
                </a>
            </div>

            <div>
                <!-- Mobile Tabs -->
                <div class="sm:hidden mb-4" x-data="{ tabDropdownOpen: false }">
                    <div class="relative" @click.outside="tabDropdownOpen = false">
                        <button type="button" 
                                @click="tabDropdownOpen = !tabDropdownOpen"
                                class="w-full flex justify-between items-center bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-sm font-medium text-secondary-700 hover:border-primary-400 focus:ring-2 ring-primary-500 transition-all shadow-sm">
                            <span x-text="activeTab === 'active' ? '{{ __('ui.tab_active_borrowings') }}' : '{{ __('ui.tab_history') }}'"></span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{'rotate-180': tabDropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="tabDropdownOpen" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             x-cloak
                             class="absolute z-50 mt-1 w-full bg-white rounded-xl shadow-xl border border-secondary-100 overflow-hidden">
                            <div class="p-1 space-y-0.5">
                                <div @click="activeTab = 'active'; tabDropdownOpen = false" 
                                     class="px-3 py-2 rounded-lg cursor-pointer text-sm transition-colors"
                                     :class="activeTab === 'active' ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-secondary-700 hover:bg-gray-50'">
                                    {{ __('ui.tab_active_borrowings') }}
                                </div>
                                <div @click="activeTab = 'history'; tabDropdownOpen = false" 
                                     class="px-3 py-2 rounded-lg cursor-pointer text-sm transition-colors"
                                     :class="activeTab === 'history' ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-secondary-700 hover:bg-gray-50'">
                                    {{ __('ui.tab_history') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Tabs -->
                <div class="hidden sm:block border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button 
                            @click="activeTab = 'active'"
                            :class="activeTab === 'active' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ __('ui.tab_active_borrowings') }}
                            @if($activeBorrowings->count() > 0)
                                <span class="bg-primary-100 text-primary-600 py-0.5 px-2.5 rounded-full text-xs font-semibold">{{ $activeBorrowings->count() }}</span>
                            @endif
                        </button>
                        <button 
                            @click="activeTab = 'history'"
                            :class="activeTab === 'history' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            {{ __('ui.tab_history') }}
                        </button>
                    </nav>
                </div>

                <!-- Active Borrowings Content -->
                <div x-show="activeTab === 'active'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="card overflow-hidden">
                        @if($activeBorrowings->isEmpty())
                            <div class="p-12 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('ui.no_active_borrowings') }}</h3>
                                <p class="text-gray-500 mt-1">{{ __('ui.no_active_borrowings_desc') }}</p>
                                <div class="mt-6">
                                    <a href="{{ route('inventory.index') }}" class="btn btn-primary inline-flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        Jelajahi Inventaris
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 hidden sm:table-header-group">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.item') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.quantity') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.borrow_date') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.due_date') }}</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($activeBorrowings as $borrowing)
                                            <tr class="flex flex-col sm:table-row hover:bg-gray-50 transition-colors cursor-pointer group relative" onclick="if(!event.target.closest('.no-click')) window.location='{{ route('inventory.show', $borrowing->sparepart->uuid) }}'">
                                                <td class="px-6 py-4 whitespace-nowrap sm:w-1/3">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 overflow-hidden">
                                                            @if($borrowing->sparepart->image)
                                                                <img src="{{ asset('storage/' . $borrowing->sparepart->image) }}" alt="" class="h-10 w-10 object-cover">
                                                            @elseif($borrowing->sparepart->image_path)
                                                                <img src="{{ asset('storage/' . $borrowing->sparepart->image_path) }}" alt="" class="h-10 w-10 object-cover">
                                                            @else
                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                            @endif
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900 group-hover:text-primary-600 transition-colors">{{ $borrowing->sparepart->name }}</div>
                                                            <div class="text-xs text-gray-500">{{ $borrowing->sparepart->part_number }}</div>
                                                            <!-- Mobile only meta -->
                                                            <div class="sm:hidden mt-2 flex items-center gap-2 text-xs text-gray-500">
                                                                <span>{{ $borrowing->quantity }} {{ $borrowing->sparepart->unit }}</span>
                                                                <span>&bull;</span>
                                                                @if($borrowing->expected_return_at)
                                                                    @if($borrowing->expected_return_at->isPast())
                                                                        <span class="text-danger-600 font-bold">{{ $borrowing->expected_return_at->format('d M Y') }}</span>
                                                                    @else
                                                                        <span>{{ $borrowing->expected_return_at->format('d M Y') }}</span>
                                                                    @endif
                                                                @else
                                                                    <span class="text-gray-400">-</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $borrowing->quantity }} {{ $borrowing->sparepart->unit }}</td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $borrowing->borrowed_at->format('d M Y') }}</td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm">
                                                 @if($borrowing->expected_return_at)
                                                    @if($borrowing->expected_return_at->isPast())
                                                        <span class="text-danger-600 font-bold flex items-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            {{ $borrowing->expected_return_at->format('d M Y') }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-500">{{ $borrowing->expected_return_at->format('d M Y') }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium sm:w-1/6">
                                                     <div class="flex items-center justify-end gap-2">
                                                        <button 
                                                            type="button" 
                                                            class="btn btn-sm btn-primary no-click z-10 relative"
                                                            @click.stop="openReturnModal({ id: {{ $borrowing->id }}, quantity: {{ $borrowing->remaining_quantity }} })"
                                                        >
                                                            {{ __('ui.return_action') }}
                                                        </button>
                                                     </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- History Content -->
                <div x-show="activeTab === 'history'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="card overflow-hidden">
                        @if($historyBorrowings->isEmpty())
                            <div class="p-12 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('ui.no_history_borrowings') }}</h3>
                                <p class="text-gray-500 mt-1">{{ __('ui.no_history_borrowings_desc') }}</p>
                                <div class="mt-6">
                                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary inline-flex items-center">
                                        Pinjam Barang Baru
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 hidden sm:table-header-group">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.item') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.quantity') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.borrow_date') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.return_date') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi (Kembali)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($historyBorrowings as $borrowing)
                                            <tr class="flex flex-col sm:table-row hover:bg-gray-50 transition-colors cursor-pointer" @click="window.location='{{ route('inventory.borrow.show', $borrowing->id) }}'">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div x-data="{ showLightbox: false }" class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 overflow-hidden relative cursor-zoom-in" @click.stop="if('{{ $borrowing->sparepart->image ?? $borrowing->sparepart->image_path }}') showLightbox = true">
                                                            @if($borrowing->sparepart->image)
                                                                <img src="{{ asset('storage/' . $borrowing->sparepart->image) }}" alt="" class="h-10 w-10 object-cover hover:scale-110 transition-transform">
                                                            @elseif($borrowing->sparepart->image_path)
                                                                <img src="{{ asset('storage/' . $borrowing->sparepart->image_path) }}" alt="" class="h-10 w-10 object-cover hover:scale-110 transition-transform">
                                                            @else
                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                            @endif
                                                            
                                                            @if($borrowing->sparepart->image || $borrowing->sparepart->image_path)
                                                            <template x-teleport="body">
                                                                <div x-show="showLightbox" 
                                                                     @keydown.window.escape="showLightbox = false"
                                                                     x-transition.opacity.duration.300ms
                                                                     class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-90" 
                                                                     x-cloak>
                                                                    <button @click.stop="showLightbox = false" class="absolute top-4 right-4 text-white hover:text-gray-300 p-2 focus:outline-none">
                                                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                    </button>
                                                                    <img @click.away="showLightbox = false" src="{{ asset('storage/' . ($borrowing->sparepart->image ?? $borrowing->sparepart->image_path)) }}" alt="" class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg shadow-2xl">
                                                                </div>
                                                            </template>
                                                            @endif
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $borrowing->sparepart->name }}</div>
                                                            <div class="text-xs text-gray-500">{{ $borrowing->sparepart->part_number }}</div>
                                                             <!-- Mobile only meta -->
                                                             <div class="sm:hidden mt-2 flex flex-col gap-1 text-xs text-gray-500">
                                                                <div>{{ $borrowing->returned_at->format('d M Y') }}</div>
                                                                <div>
                                                                    @php
                                                                        $returnsSummaryMobile = $borrowing->returns->groupBy(function($r) {
                                                                            return strtolower($r->condition) . '|' . ($r->return_date ? $r->return_date->format('d M Y') : '-');
                                                                        })->map(function($group) {
                                                                            return [
                                                                                'condition' => strtolower($group->first()->condition),
                                                                                'date' => $group->first()->return_date ? $group->first()->return_date->format('d M Y') : '-',
                                                                                'quantity' => $group->sum('quantity')
                                                                            ];
                                                                        });
                                                                    @endphp
                                                                    @if($returnsSummaryMobile->isEmpty())
                                                                        <span class="text-xs text-gray-500">-</span>
                                                                    @else
                                                                        <ul class="space-y-1">
                                                                            @foreach($returnsSummaryMobile as $item)
                                                                                @php
                                                                                    $badgeClass = match($item['condition']) {
                                                                                        'good' => 'bg-success-100 text-success-800 border-success-200',
                                                                                        'bad' => 'bg-warning-100 text-warning-800 border-warning-200',
                                                                                        'lost' => 'bg-danger-100 text-danger-800 border-danger-200',
                                                                                        default => 'bg-gray-100 text-gray-800 border-gray-200',
                                                                                    };
                                                                                    $condText = match($item['condition']) {
                                                                                        'good' => 'Baik',
                                                                                        'bad' => 'Rusak',
                                                                                        'lost' => 'Hilang',
                                                                                        default => ucfirst($item['condition']),
                                                                                    };
                                                                                @endphp
                                                                                <li>
                                                                                    <span class="px-2 py-0.5 inline-flex items-center gap-1 text-[10px] leading-5 font-semibold rounded-full border {{ $badgeClass }}">
                                                                                        <span>{{ $item['quantity'] }} {{ $borrowing->sparepart->unit }}</span> <span class="font-normal opacity-75">|</span> <span>{{ $condText }}</span> <span class="font-normal opacity-50">({{ $item['date'] }})</span>
                                                                                    </span>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $borrowing->quantity }} {{ $borrowing->sparepart->unit }}</td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $borrowing->borrowed_at->format('d M Y') }}</td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @php
                                                        $returnsSummaryDesktop = $borrowing->returns->sortByDesc('return_date')->groupBy(function($r) {
                                                            return strtolower($r->condition) . '|' . ($r->return_date ? $r->return_date->format('d M Y') : '-');
                                                        })->map(function($group) {
                                                            return [
                                                                'condition' => strtolower($group->first()->condition),
                                                                'date' => $group->first()->return_date ? $group->first()->return_date->format('d M Y') : '-',
                                                                'quantity' => $group->sum('quantity')
                                                            ];
                                                        });
                                                    @endphp
                                                    @if($returnsSummaryDesktop->isEmpty())
                                                        {{ $borrowing->returned_at->format('d M Y') }}
                                                    @else
                                                        <ul class="space-y-2">
                                                            @foreach($returnsSummaryDesktop as $item)
                                                                <li class="h-6 flex items-center justify-end font-medium">{{ $item['date'] }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap">
                                                    @if($returnsSummaryDesktop->isEmpty())
                                                        <span class="text-sm text-gray-500">-</span>
                                                    @else
                                                        <ul class="space-y-2">
                                                            @foreach($returnsSummaryDesktop as $item)
                                                                @php
                                                                    $badgeClass = match($item['condition']) {
                                                                        'good' => 'bg-success-100 text-success-800 border-success-200',
                                                                        'bad' => 'bg-warning-100 text-warning-800 border-warning-200',
                                                                        'lost' => 'bg-danger-100 text-danger-800 border-danger-200',
                                                                        default => 'bg-gray-100 text-gray-800 border-gray-200',
                                                                    };
                                                                    $condText = match($item['condition']) {
                                                                        'good' => 'Baik',
                                                                        'bad' => 'Rusak',
                                                                        'lost' => 'Hilang',
                                                                        default => ucfirst($item['condition']),
                                                                    };
                                                                @endphp
                                                                <li class="h-6 flex items-center">
                                                                    <span class="px-2 py-0.5 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full border {{ $badgeClass }}">
                                                                        <span>{{ $item['quantity'] }} {{ $borrowing->sparepart->unit }}</span> <span class="font-normal opacity-75">|</span> <span>{{ $condText }}</span>
                                                                    </span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                                @include('profile.partials.return-modal')


            </div>
        </div>
    </div>
</x-app-layout>


