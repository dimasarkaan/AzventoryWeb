<x-app-layout>
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
                            alert(data.message || 'Terjadi kesalahan sistem.');
                        }
                    }
                } catch (error) {
                    console.error('Submission error:', error);
                    alert('Gagal menghubungi server.');
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
                <div class="sm:hidden mb-4">
                    <select x-model="activeTab" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-primary-500 focus:outline-none focus:ring-primary-500 sm:text-sm">
                        <option value="active">{{ __('ui.tab_active_borrowings') }}</option>
                        <option value="history">{{ __('ui.tab_history') }}</option>
                    </select>
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
                                            <tr class="flex flex-col sm:table-row hover:bg-gray-50 transition-colors cursor-pointer group relative" onclick="if(!event.target.closest('.no-click')) window.location='{{ route('inventory.show', $borrowing->sparepart->id) }}'">
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
                                                                @if($borrowing->expected_return_at->isPast())
                                                                    <span class="text-danger-600 font-bold">{{ $borrowing->expected_return_at->format('d M Y') }}</span>
                                                                @else
                                                                    <span>{{ $borrowing->expected_return_at->format('d M Y') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $borrowing->quantity }} {{ $borrowing->sparepart->unit }}</td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $borrowing->borrowed_at->format('d M Y') }}</td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm">
                                                    @if($borrowing->expected_return_at->isPast())
                                                        <span class="text-danger-600 font-bold flex items-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            {{ $borrowing->expected_return_at->format('d M Y') }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-500">{{ $borrowing->expected_return_at->format('d M Y') }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium sm:w-1/6">
                                                     <div class="flex items-center justify-end gap-2">
                                                        <button 
                                                            type="button" 
                                                            class="btn btn-sm btn-primary no-click z-10 relative"
                                                            @click.stop="openReturnModal({ id: {{ $borrowing->id }}, quantity: {{ $borrowing->quantity }} })"
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
                <div x-show="activeTab === 'history'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="card overflow-hidden">
                        @if($historyBorrowings->isEmpty())
                            <div class="p-12 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('ui.no_history_borrowings') }}</h3>
                                <p class="text-gray-500 mt-1">{{ __('ui.no_history_borrowings_desc') }}</p>
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
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($historyBorrowings as $borrowing)
                                            <tr class="flex flex-col sm:table-row hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('inventory.show', $borrowing->sparepart->id) }}'">
                                                <td class="px-6 py-4 whitespace-nowrap">
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
                                                            <div class="text-sm font-medium text-gray-900">{{ $borrowing->sparepart->name }}</div>
                                                            <div class="text-xs text-gray-500">{{ $borrowing->sparepart->part_number }}</div>
                                                             <!-- Mobile only meta -->
                                                             <div class="sm:hidden mt-2 flex items-center gap-2 text-xs text-gray-500">
                                                                <span>{{ $borrowing->returned_at->format('d M Y') }}</span>
                                                                <span>&bull;</span>
                                                                <span class="text-success-600 font-medium">{{ __('ui.status_returned_badge') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $borrowing->quantity }} {{ $borrowing->sparepart->unit }}</td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $borrowing->borrowed_at->format('d M Y') }}</td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $borrowing->returned_at->format('d M Y') }}</td>
                                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-success-100 text-success-800">
                                                        {{ __('ui.status_returned_badge') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Return Modal -->
                <template x-teleport="body">
                    <div x-show="returnModalOpen" 
                         style="display: none;"
                         class="fixed inset-0 z-[99]" 
                         aria-labelledby="modal-title" 
                         role="dialog" 
                         aria-modal="true">
                        
                        <div class="flex min-h-screen items-center justify-center py-12 px-4 sm:px-6">
                            <!-- Backdrop -->
                            <div x-show="returnModalOpen"
                                 x-transition:enter="ease-out duration-300" 
                                 x-transition:enter-start="opacity-0" 
                                 x-transition:enter-end="opacity-100" 
                                 x-transition:leave="ease-in duration-200" 
                                 x-transition:leave-start="opacity-100" 
                                 x-transition:leave-end="opacity-0" 
                                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                                 @click="returnModalOpen = false"
                                 aria-hidden="true"></div>
                    
                            <!-- Modal Panel -->
                            <div x-show="returnModalOpen" 
                                 x-transition:enter="ease-out duration-300" 
                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                                 x-transition:leave="ease-in duration-200" 
                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                 class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full flex flex-col max-h-[85vh]">
                                
                                <!-- Header (Fixed) -->
                                <div class="bg-white px-4 py-4 sm:px-6 border-b border-gray-200 flex-none z-10 shadow-sm">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-success-100 mx-0">
                                            <svg class="h-6 w-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div class="ml-4 text-left">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                {{ __('ui.return_item_title') }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
            
                                <!-- Form -->
                                <form :action="`/my-inventory/return/${selectedBorrowingId}`" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0"
                                      @submit.prevent="submitReturn">
                                    @csrf
                                    
                                    <!-- Scrollable Content -->
                                    <div class="flex-1 overflow-y-auto px-4 pt-2 pb-4 sm:px-6 space-y-4 custom-scrollbar">
                                        
                                        <!-- Error Display -->
                                        <div x-show="Object.keys(errors).length > 0" class="mb-4 bg-danger-50 border border-danger-200 text-danger-700 px-4 py-3 rounded relative">
                                            <strong class="font-bold">{{ __('ui.save_failed') }}</strong>
                                            <ul class="list-disc list-inside text-sm mt-1">
                                                <template x-for="(fieldErrors, field) in errors" :key="field">
                                                    <template x-for="error in fieldErrors">
                                                        <li x-text="error"></li>
                                                    </template>
                                                </template>
                                            </ul>
                                        </div>
                                        <div x-show="successMessage" class="mb-4 bg-success-50 border border-success-200 text-success-700 px-4 py-3 rounded relative">
                                            <strong class="font-bold" x-text="successMessage"></strong>
                                        </div>
                                        <style>
                                            /* Custom Scrollbar for better UX hint */
                                            .custom-scrollbar::-webkit-scrollbar { width: 6px; }
                                            .custom-scrollbar::-webkit-scrollbar-track { bg-gray-100; }
                                            .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #CBD5E0; border-radius: 3px; }
                                        </style>
            
                                        <!-- Quantity -->
                                        <div>
                                            <label for="return_qty" class="block text-sm font-medium text-gray-700">{{ __('ui.return_quantity') }} <span class="text-danger-500">*</span></label>
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <input type="number" 
                                                       name="return_quantity" 
                                                       id="return_qty" 
                                                       x-model="returnQty"
                                                       :max="maxReturnQty"
                                                       min="1"
                                                       required
                                                       @keydown="if(['-','+','e','E','.'].includes($event.key)) $event.preventDefault()"
                                                       @input="returnQty = returnQty.replace(/[^0-9]/g, '')"
                                                       class="focus:ring-success-500 focus:border-success-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                       placeholder="Jumlah">
                                            </div>
                                            <p class="mt-1 text-xs" 
                                               :class="{'text-danger-600': !isValid, 'text-gray-500': isValid}">
                                                Maks: <span x-text="maxReturnQty"></span>
                                            </p>
                                        </div>
            
                                        <!-- Condition (Custom Dropdown) -->
                                        <div @click.outside="dropdownOpen = false" class="relative z-50">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.condition') }} <span class="text-danger-500">*</span></label>
                                            <div class="relative">
                                                <button type="button" @click="dropdownOpen = !dropdownOpen"
                                                        class="input-field w-full text-left flex justify-between items-center rounded-xl py-2.5 px-4 text-sm cursor-pointer border border-gray-300 hover:border-primary-400 focus:ring-2 ring-primary-500 bg-white transition-all shadow-sm">
                                                    <span x-text="conditionLabel" :class="{'text-gray-900': returnCondition, 'text-gray-500': !returnCondition}" class="truncate mr-2"></span>
                                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': dropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                </button>
        
                                                <div x-show="dropdownOpen" 
                                                     style="display: none;"
                                                     x-transition:enter="transition ease-out duration-100"
                                                     x-transition:enter-start="transform opacity-0 scale-95"
                                                     x-transition:enter-end="transform opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-75"
                                                     x-transition:leave-start="transform opacity-100 scale-100"
                                                     x-transition:leave-end="transform opacity-0 scale-95"
                                                     class="absolute z-50 mt-1 w-full min-w-[100%] bg-white rounded-xl shadow-xl border border-secondary-100 overflow-hidden">
                                                    <div class="max-h-60 overflow-y-auto p-1 space-y-0.5">
                                                        <template x-for="option in conditionOptions" :key="option.value">
                                                            <div @click="selectCondition(option)" 
                                                                 class="px-3 py-2 rounded-lg cursor-pointer text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors text-sm"
                                                                 :class="{'bg-primary-50 text-primary-700 font-medium': returnCondition === option.value}">
                                                                <span x-text="option.label"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="return_condition" x-model="returnCondition">
                                        </div>
            
                                        <!-- Multiple Evidence Images & Camera (Hidden if Lost) -->
                                        <div class="mt-4" x-show="returnCondition !== 'lost'" x-data="{ 
                                            files: [], 
                                            previews: [],
                                            cameraOpen: false,
                                            stream: null,
                                            
                                            addFiles(e) {
                                                const newFiles = Array.from(e.target.files);
                                                this.processFiles(newFiles);
                                            },
            
                                            processFiles(newFiles) {
                                                if (this.files.length + newFiles.length > 5) {
                                                    alert('Maksimal 5 foto');
                                                    return;
                                                }
                                                this.files = this.files.concat(newFiles);
                                                this.updateInput();
                                                newFiles.forEach(file => {
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => { this.previews.push(e.target.result); };
                                                    reader.readAsDataURL(file);
                                                });
                                                $dispatch('file-change', this.files.length);
                                            },
            
                                            removeFile(index) {
                                                this.files.splice(index, 1);
                                                this.previews.splice(index, 1);
                                                this.updateInput();
                                                $dispatch('file-change', this.files.length);
                                            },
            
                                            updateInput() {
                                                const dt = new DataTransfer();
                                                this.files.forEach(file => dt.items.add(file));
                                                $refs.fileInput.files = dt.files;
                                                // Trigger change event manually if needed
                                            },
            
                                            // Camera Logic
                                            async startCamera() {
                                                this.cameraOpen = true;
                                                this.$nextTick(async () => {
                                                    try {
                                                        this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                                                        this.$refs.video.srcObject = this.stream;
                                                    } catch (err) {
                                                        let msg = "{{ __('ui.camera_error_default') }}";
                                                        const errStr = err.toString();
                                                        if (errStr.includes('NotAllowedError') || errStr.includes('PermissionDeniedError')) msg = "{{ __('ui.camera_error_not_allowed') }}";
                                                        else if (errStr.includes('NotFoundError')) msg = "{{ __('ui.camera_error_not_found') }}";
                                                        else if (errStr.includes('NotReadableError')) msg = "{{ __('ui.camera_error_not_readable') }}";
                                                        
                                                        alert(msg);
                                                        this.cameraOpen = false;
                                                    }
                                                });
                                            },
            
                                            stopCamera() {
                                                if (this.stream) {
                                                    this.stream.getTracks().forEach(track => track.stop());
                                                    this.stream = null;
                                                }
                                                this.cameraOpen = false;
                                            },
            
                                            capturePhoto() {
                                                const video = this.$refs.video;
                                                const canvas = this.$refs.canvas;
                                                canvas.width = video.videoWidth;
                                                canvas.height = video.videoHeight;
                                                const ctx = canvas.getContext('2d');
                                                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                                
                                                canvas.toBlob(blob => {
                                                    const file = new File([blob], 'camera_' + Date.now() + '.jpg', { type: 'image/jpeg' });
                                                    this.processFiles([file]);
                                                    this.stopCamera();
                                                }, 'image/jpeg', 0.8);
                                            },
            
                                            triggerGallery() {
                                                $refs.galleryInput.click();
                                            }
                                        }">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('ui.upload_photo') }} <span class="text-danger-500">*</span></label>
                                            
                                            <!-- Split Buttons (Pill/Chip Style - Full Width) -->
                                            <div class="grid grid-cols-2 gap-3 mb-4">
                                                <button type="button" @click="startCamera" 
                                                        class="w-full inline-flex justify-center items-center px-4 py-2 rounded-full bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-800 transition-colors duration-200 border border-transparent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span class="text-sm font-semibold">Buka Kamera</span>
                                                </button>
            
                                                <button type="button" @click="triggerGallery" 
                                                        class="w-full inline-flex justify-center items-center px-4 py-2 rounded-full bg-purple-50 text-purple-700 hover:bg-purple-100 hover:text-purple-800 transition-colors duration-200 border border-transparent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span class="text-sm font-semibold">Pilih Galeri</span>
                                                </button>
                                            </div>
            
                                            <!-- Camera Overlay -->
                                            <div x-show="cameraOpen" 
                                                    style="display: none;"
                                                    class="fixed inset-0 z-[100] bg-black bg-opacity-90 flex flex-col items-center justify-center p-4">
                                                <div class="relative w-full max-w-lg bg-black rounded-lg overflow-hidden shadow-2xl">
                                                    <video x-ref="video" autoplay playsinline class="w-full h-auto object-cover"></video>
                                                    <canvas x-ref="canvas" class="hidden"></canvas>
                                                    
                                                    <div class="absolute bottom-6 left-0 right-0 flex justify-center gap-6 items-center">
                                                        <button type="button" @click="stopCamera" class="p-3 bg-white/20 hover:bg-white/30 rounded-full text-white backdrop-blur-sm transition-all">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                        <button type="button" @click="capturePhoto" class="p-4 bg-white rounded-full text-primary-600 shadow-lg hover:scale-105 transition-transform">
                                                            <div class="w-12 h-12 rounded-full border-4 border-primary-600 flex items-center justify-center">
                                                                <div class="w-10 h-10 bg-primary-600 rounded-full"></div>
                                                            </div>
                                                        </button>
                                                    </div>
                                                </div>
                                                <p class="text-white mt-4 text-sm font-medium">Pose dan ambil foto</p>
                                            </div>
            
                                            <!-- Hidden Inputs -->
                                            <input type="file" x-ref="galleryInput" accept="image/*" multiple class="hidden" @change="addFiles($event)">
                                            <input type="file" name="return_photos[]" multiple class="hidden" x-ref="fileInput">
            
                                            <!-- Previews Grid -->
                                            <div class="grid grid-cols-3 gap-3" x-show="previews.length > 0">
                                                <template x-for="(preview, index) in previews" :key="index">
                                                    <div class="relative aspect-square rounded-lg overflow-hidden border border-gray-200 group bg-gray-50">
                                                        <img :src="preview" class="w-full h-full object-cover">
                                                        <button type="button" @click="removeFile(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition-colors z-10 w-6 h-6 flex items-center justify-center">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                            <p class="mt-2 text-xs text-gray-500" x-show="files.length > 0">Maksimal 5 foto (Wajib)</p>
                                            <input type="hidden" id="file_count" :value="files.length">
                                        </div>
            
                                        <!-- Notes -->
                                        <div>
                                            <label for="return_notes" class="block text-sm font-medium text-gray-700">{{ __('ui.notes') }} <span class="text-secondary-400 font-normal">(Opsional)</span></label>
                                            <textarea name="return_notes" id="return_notes" x-model="returnNotes" rows="2" class="form-textarea mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Catatan tambahan..."></textarea>
                                        </div>
                                    </div>
            
                                    <!-- Footer Actions (Fixed) -->
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-col sm:flex-row-reverse gap-3 border-t border-gray-200 flex-none z-10 shadow-[0_-2px_4px_rgba(0,0,0,0.05)]"
                                         x-data="{ hasFiles: false }" 
                                         @file-change.window="hasFiles = $event.detail > 0">
                                        <button type="submit" 
                                                :disabled="!isValid || isSubmitting || (returnCondition !== 'lost' && !hasFiles)"
                                                :class="{ 'opacity-50 cursor-not-allowed': !isValid || isSubmitting || (returnCondition !== 'lost' && !hasFiles), 'hover:bg-primary-700': isValid && !isSubmitting && (returnCondition === 'lost' || hasFiles) }"
                                                class="w-full sm:w-auto inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm transition-all">
                                            <span x-show="!isSubmitting">Konfirmasi</span>
                                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                Proses...
                                            </span>
                                        </button>
                                        <button type="button" @click="returnModalOpen = false" class="mt-0 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm">
                                            {{ __('ui.cancel') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-app-layout>
