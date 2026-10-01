<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('ui.approvals_title') }}
                    </h1>
                    <p class="mt-1 text-sm text-secondary-500">{{ __('ui.approvals_desc') }}</p>
                </div>
            </div>

            <!-- Tab Navigation for Status -->
            <div class="mb-4 border-b border-secondary-200">
                <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                    <a href="{{ route('inventory.stock-approvals.index', ['status' => 'pending', 'filter_type' => request('filter_type'), 'search' => request('search')]) }}" 
                       class="{{ request('status', 'pending') === 'pending' ? 'border-primary-500 text-primary-600' : 'border-transparent text-secondary-500 hover:text-secondary-700 hover:border-secondary-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-black text-sm transition-colors flex items-center gap-2">
                        {{ __('ui.status_pending_label') }}
                    </a>
                    <a href="{{ route('inventory.stock-approvals.index', ['status' => 'approved', 'filter_type' => request('filter_type'), 'search' => request('search')]) }}" 
                       class="{{ request('status') === 'approved' ? 'border-success-500 text-success-600' : 'border-transparent text-secondary-500 hover:text-secondary-700 hover:border-secondary-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-black text-sm transition-colors flex items-center gap-2">
                        {{ __('ui.status_approved_label') }}
                    </a>
                    <a href="{{ route('inventory.stock-approvals.index', ['status' => 'rejected', 'filter_type' => request('filter_type'), 'search' => request('search')]) }}" 
                       class="{{ request('status') === 'rejected' ? 'border-danger-500 text-danger-600' : 'border-transparent text-secondary-500 hover:text-secondary-700 hover:border-secondary-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-black text-sm transition-colors flex items-center gap-2">
                        {{ __('ui.status_rejected_label') }}
                    </a>
                </nav>
            </div>

            <!-- Modern Search & Filter Bar -->
            <div class="mb-6 card p-4 overflow-visible">
                <form method="GET" action="{{ route('inventory.stock-approvals.index') }}" id="approval-filter-form" novalidate>
                    <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
                    <div class="flex flex-col lg:flex-row gap-4 items-center">
                        <div class="relative flex-1 w-full"
                             x-data="{ searchQuery: '{{ request('search') ? addslashes(request('search')) : '' }}' }"
                             @keydown.window="
                                if ($event.key === '/' && $event.target.tagName !== 'INPUT' && $event.target.tagName !== 'TEXTAREA') {
                                    $event.preventDefault();
                                    $refs.searchInput.focus();
                                }
                             "
                        >
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-icon.search class="w-5 h-5 text-secondary-400" />
                            </div>
                            <input
                                type="text"
                                x-ref="searchInput"
                                name="search"
                                x-model="searchQuery"
                                @keydown.escape="$refs.searchInput.blur()"
                                placeholder="{{ __('ui.approvals_search_placeholder') }}"
                                class="input-field pl-10 pr-20 w-full"
                                onchange="this.form.submit()"
                            >
                            
                            <!-- Search Shortcut Hint (Hidden on mobile or when typing) -->
                            <div x-show="searchQuery.length === 0" class="absolute inset-y-0 right-0 pr-3 hidden sm:flex items-center pointer-events-none">
                                <kbd class="px-2 py-1 text-[10px] font-semibold text-secondary-500 bg-secondary-100 border border-secondary-200 rounded-md shadow-sm">/</kbd>
                            </div>

                            <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''; $nextTick(() => { document.getElementById('approval-filter-form').submit(); })" class="absolute inset-y-0 right-0 pr-3 flex items-center text-secondary-400 hover:text-danger-500 transition-colors cursor-pointer" title="{{ __('ui.clear_search') }}" x-cloak>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                            <div class="w-full sm:w-64">
                                @php
                                    $typeOptions = [
                                        'all' => __('ui.type_all'),
                                        'masuk' => __('ui.type_in_label'),
                                        'keluar' => __('ui.type_out_label'),
                                    ];
                                @endphp
                                <label for="type-filter" class="sr-only">{{ __('ui.type_all') }}</label>
                                <x-select 
                                    name="filter_type" 
                                    id="type-filter"
                                    :options="$typeOptions" 
                                    :selected="request('filter_type', 'all')" 
                                    placeholder="{{ __('ui.type_all') }}" 
                                    :submitOnChange="true" 
                                    width="w-full" 
                                    :allowClear="false"
                                />
                            </div>

                            @if(request('search') || request('filter_type') || request('status'))
                                <a href="{{ route('inventory.stock-approvals.index') }}" class="btn btn-secondary flex items-center justify-center p-2.5 h-[42px] w-[42px] flex-shrink-0" title="{{ __('ui.reset_filter') }}">
                                    <x-icon.restore class="h-5 w-5" />
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Session Alerts -->
            @if(session('errors_list'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-r-xl shadow-sm animate-shake">
                    <p class="font-bold flex items-center gap-2 mb-2">
                        <x-icon.warning class="w-5 h-5 text-red-500" />
                        Beberapa pengajuan gagal diproses:
                    </p>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-6">
                        @foreach(session('errors_list') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-r-xl shadow-sm animate-shake">
                    <p class="font-bold flex items-center gap-2 mb-2">
                        <x-icon.warning class="w-5 h-5 text-red-500" />
                        Validasi Gagal:
                    </p>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-6">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div id="approvals-list-container">
                <!-- Mobile Card View -->
                @if(request('status', 'pending') === 'pending' && $pendingApprovals->count() > 0)
                <div class="md:hidden mb-4 flex items-center justify-between px-1">
                    <label class="flex items-center gap-2.5 cursor-pointer group bg-white border border-secondary-100 rounded-xl px-4 py-2.5 shadow-sm active:scale-95 transition-all">
                        <input type="checkbox" id="select-all-mobile" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:ring-primary-500 transition-all">
                        <span class="text-sm font-bold text-secondary-600 group-hover:text-primary-600 transition-colors">Pilih Semua</span>
                    </label>
                </div>
                @endif
                <div class="md:hidden space-y-4" id="mobile-approvals-list">
                    @forelse ($pendingApprovals as $approval)
                        <x-approval.card :approval="$approval" />
                    @empty
                        <div class="card p-12 text-center text-secondary-500 rounded-xl" id="empty-state-mobile">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-secondary-50 rounded-full flex items-center justify-center mb-4">
                                     <svg class="w-8 h-8 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-sm font-bold text-secondary-900 mb-1">
                                    @php
                                        $status = request('status', 'pending');
                                        $type = request('filter_type', 'all');
                                        
                                        $titleKey = 'ui.no_data_found';
                                        $descKey = 'ui.no_data_criteria';

                                        if ($status === 'pending') {
                                            if ($type === 'masuk') { $titleKey = 'ui.no_pending_in'; $descKey = 'ui.all_processed_in'; }
                                            elseif ($type === 'keluar') { $titleKey = 'ui.no_pending_out'; $descKey = 'ui.all_processed_out'; }
                                            else { $titleKey = 'ui.no_pending'; $descKey = 'ui.all_processed'; }
                                        } elseif ($status === 'approved') {
                                            if ($type === 'masuk') { $titleKey = 'ui.no_data_in_approved'; }
                                            elseif ($type === 'keluar') { $titleKey = 'ui.no_data_out_approved'; }
                                        } elseif ($status === 'rejected') {
                                            if ($type === 'masuk') { $titleKey = 'ui.no_data_in_rejected'; }
                                            elseif ($type === 'keluar') { $titleKey = 'ui.no_data_out_rejected'; }
                                        }
                                    @endphp
                                    {{ __($titleKey) }}
                                </p>
                                <p class="text-xs text-secondary-500">
                                    {{ __($descKey) }}
                                </p>
                                @if(request('search') || (request('filter_type') && request('filter_type') !== 'all') || (request('status') && request('status') !== 'pending'))
                                    <div class="mt-4">
                                        <a href="{{ route('inventory.stock-approvals.index') }}" class="btn btn-secondary px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 mx-auto w-fit">
                                            <x-icon.restore class="w-4 h-4" />
                                            Hapus Filter & Pencarian
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Standalone Bulk Form (Outside table to avoid nesting) --}}
                <form id="bulk-approval-form" action="{{ route('inventory.stock-approvals.bulk-approve') }}" method="POST" class="hidden" novalidate>
                    @csrf
                    <input type="hidden" name="status" id="bulk-status" value="approved">
                    <input type="hidden" name="rejection_reason" id="bulk-rejection-reason" value="">
                    <div id="bulk-ids-container"></div>
                </form>

                <div class="hidden md:block card overflow-hidden border border-secondary-100 shadow-sm rounded-xl">
                    <div class="overflow-x-auto">
                        <table class="table-modern w-full">
                            <thead>
                                <tr class="bg-secondary-50/50">
                                    <th class="w-10 px-2 py-4">
                                        @if(request('status', 'pending') === 'pending' && $pendingApprovals->count() > 0)
                                            <input type="checkbox" id="select-all" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:ring-primary-500 transition-all">
                                        @endif
                                    </th>
                                    <th class="px-2 py-4 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.item_column') }}</th>
                                    <th class="px-2 py-4 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.applicant_column') }}</th>
                                    <th class="px-2 py-4 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.type_column') }}</th>
                                    <th class="px-2 py-4 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.amount_column') }}</th>
                                    <th class="px-2 py-4 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.reason_column') }}</th>
                                    <th class="px-2 py-4 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.date_column') }}</th>
                                    <th class="px-2 py-4 text-right text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.action_column') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-100" id="desktop-approvals-list">
                                @forelse ($pendingApprovals as $approval)
                                    <x-approval.table-row :approval="$approval" />
                                @empty
                                    <tr id="empty-state-desktop">
                                        <td colspan="8" class="p-16 text-center text-secondary-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-20 h-20 bg-secondary-50 rounded-full flex items-center justify-center mb-4">
                                                    <svg class="w-10 h-10 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                                @php
                                                    $status = request('status', 'pending');
                                                    $type = request('filter_type', 'all');
                                                    
                                                    $titleKey = 'ui.no_data_found';
                                                    $descKey = 'ui.no_data_criteria';

                                                    if ($status === 'pending') {
                                                        if ($type === 'masuk') { $titleKey = 'ui.no_pending_in'; $descKey = 'ui.all_processed_in'; }
                                                        elseif ($type === 'keluar') { $titleKey = 'ui.no_pending_out'; $descKey = 'ui.all_processed_out'; }
                                                        else { $titleKey = 'ui.no_pending'; $descKey = 'ui.all_processed'; }
                                                    } elseif ($status === 'approved') {
                                                        if ($type === 'masuk') { $titleKey = 'ui.no_data_in_approved'; }
                                                        elseif ($type === 'keluar') { $titleKey = 'ui.no_data_out_approved'; }
                                                    } elseif ($status === 'rejected') {
                                                        if ($type === 'masuk') { $titleKey = 'ui.no_data_in_rejected'; }
                                                        elseif ($type === 'keluar') { $titleKey = 'ui.no_data_out_rejected'; }
                                                    }
                                                @endphp
                                                <p class="text-base font-bold text-secondary-900 mb-1">{{ __($titleKey) }}</p>
                                                <p class="text-sm text-secondary-500">{{ __($descKey) }}</p>
                                                @if(request('search') || (request('filter_type') && request('filter_type') !== 'all') || (request('status') && request('status') !== 'pending'))
                                                    <div class="mt-4">
                                                        <a href="{{ route('inventory.stock-approvals.index') }}" class="btn btn-secondary px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 mx-auto w-fit">
                                                            <x-icon.restore class="w-4 h-4" />
                                                            {{ __('ui.clear_filter_search') }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6" id="approvals-pagination">
                    {{ $pendingApprovals->links() }}
                </div>
            </div>

        </div>
    </div>

    <!-- Bulk Actions - Sticky bottom bar -->
    @if($pendingApprovals->isNotEmpty() && request('status', 'pending') === 'pending')
    <div id="bulk-actions-container" class="hidden fixed bottom-4 sm:bottom-6 left-1/2 transform -translate-x-1/2 z-50 bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-secondary-200 p-2 sm:p-3 animate-fade-in-up items-center justify-center gap-2 sm:gap-4 w-auto max-w-[95vw]">
        <!-- Left Side: Selection Count & Clear -->
        <div class="flex items-center gap-2 sm:gap-3 pl-1 sm:pl-3 border-r border-secondary-200 pr-2 sm:pr-4">
            <button type="button" onclick="clearBulkSelection()" class="flex items-center justify-center w-8 h-8 rounded-full bg-secondary-100 text-secondary-500 hover:text-danger-600 hover:bg-danger-50 transition-colors" title="{{ __('ui.cancel_selection') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="flex items-baseline gap-1.5">
                <span class="font-bold text-lg sm:text-xl text-primary-600 tabular-nums" id="selected-count">0</span>
                <span class="text-[10px] sm:text-xs text-secondary-500 font-bold uppercase tracking-wider hidden sm:inline">{{ __('ui.selected_label') ?? 'TERPILIH' }}</span>
            </div>
        </div>
        
        <!-- Right Side: Actions -->
        <div class="flex items-center gap-2">
            <button type="button" onclick="submitBulk('approved')" class="btn btn-success flex items-center justify-center gap-1.5 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all" id="bulk-approve-btn">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-bold text-[11px] sm:text-sm whitespace-nowrap">{{ __('ui.btn_approve') ?? 'Setujui' }}</span>
            </button>
            <button type="button" onclick="submitBulk('rejected')" class="btn btn-danger flex items-center justify-center gap-1.5 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all" id="bulk-reject-btn">
                <x-icon.close class="w-4 h-4 text-white" />
                <span class="font-bold text-[11px] sm:text-sm whitespace-nowrap">{{ __('ui.btn_reject') ?? 'Tolak' }}</span>
            </button>
        </div>
    </div>
    @endif

    @include('inventory.approvals.partials.scripts')
</x-app-layout>


