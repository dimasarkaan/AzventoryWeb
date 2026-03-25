<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="activityLogComponent()">
            <!-- Header & Actions -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('ui.activity_logs_title') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">{{ __('ui.activity_logs_desc') }}</p>
                </div>
                
                <div class="flex gap-2">
                    <!-- Export Dropdown -->
                    <div x-data="{ open: false, isExporting: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" 
                                :disabled="isExporting"
                                class="btn btn-outline-secondary flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <template x-if="!isExporting">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </template>
                            <template x-if="isExporting">
                                <svg class="animate-spin h-5 w-5 text-secondary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isExporting ? 'Memproses...' : '{{ __('ui.export_data') }}'"></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" 
                             class="absolute left-0 sm:left-auto sm:right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-secondary-100"
                             style="display: none;">
                            <a href="{{ route('reports.activity-logs.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" 
                               @click="isExporting = true; open = false; setTimeout(() => isExporting = false, 3000)"
                               class="px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                {{ __('ui.export_pdf') }}
                            </a>
                            <a href="{{ route('reports.activity-logs.export', array_merge(request()->query(), ['format' => 'csv'])) }}" 
                               @click="isExporting = true; open = false; setTimeout(() => isExporting = false, 3000)"
                               class="px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                {{ __('ui.export_excel') }}
                            </a>
                        </div>
                    </div>

                    <button @click="showFilters = !showFilters" 
                            class="btn btn-secondary flex items-center gap-2"
                            :class="{ 'bg-secondary-100 ring-2 ring-secondary-200': showFilters }">
                        <svg class="w-5 h-5 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        <span>{{ __('ui.filter_search') }}</span>
                        <svg class="w-4 h-4 text-secondary-400 transition-transform duration-200" :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Collabsible Filter Section -->
            <div x-show="showFilters" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="mb-6"
                 style="display: none;">
                 
                <form id="filter-form" action="{{ route('reports.activity-logs.index') }}" method="GET" class="card p-6 border border-secondary-200 shadow-lg overflow-visible"
                      @submit="
                        const start = document.getElementById('start_date').value;
                        const end = document.getElementById('end_date').value;
                        if (start && end && new Date(start) > new Date(end)) {
                            $event.preventDefault();
                            Swal.fire({
                                icon: 'warning',
                                title: 'Rentang Tanggal Tidak Valid',
                                text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.',
                                confirmButtonColor: '#3b82f6'
                            });
                        }
                      ">
                    <div class="grid grid-cols-1 sm:grid-cols-2 {{ auth()->user()->role === \App\Enums\UserRole::OPERATOR ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} gap-4 items-end">
                        
                        <!-- Search -->
                        <div class="space-y-1">
                            <label for="search" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">{{ __('ui.search_keyword') }}</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Deskripsi..."
                                    class="form-input pl-10 block w-full rounded-lg border-secondary-300 focus:ring-primary-500 focus:border-primary-500 text-sm h-[42px]">
                            </div>
                        </div>

                        <!-- Role Filter -->
                        @if(auth()->user()->role !== \App\Enums\UserRole::OPERATOR)
                        <div class="space-y-1">
                            <label for="role" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">{{ __('ui.role_filter') }}</label>
                            @php
                                $roleOptions = [];
                                if (auth()->user()->role === \App\Enums\UserRole::SUPERADMIN) {
                                    $roleOptions[\App\Enums\UserRole::SUPERADMIN->value] = \App\Enums\UserRole::SUPERADMIN->label();
                                }
                                $roleOptions[\App\Enums\UserRole::ADMIN->value] = \App\Enums\UserRole::ADMIN->label();
                                $roleOptions[\App\Enums\UserRole::OPERATOR->value] = \App\Enums\UserRole::OPERATOR->label();
                            @endphp
                            <x-select name="role" id="role" :options="$roleOptions" :selected="request('role')" placeholder="{{ __('ui.all_roles') }}" width="w-full" />
                        </div>
                        @endif

                        <!-- Action Filter -->
                        <div class="space-y-1">
                            <label for="action" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">{{ __('ui.action_type') }}</label>
                            @php
                                $actionOptions = $actions->mapWithKeys(fn($item) => [$item => $item])->toArray();
                            @endphp
                            <x-select name="action" id="action" :options="$actionOptions" :selected="request('action')" placeholder="{{ __('ui.all_actions') }}" width="w-full" />
                        </div>

                        <!-- User Filter -->
                        @if(auth()->user()->role !== \App\Enums\UserRole::OPERATOR)
                        <div class="space-y-1">
                            <label for="user_id" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">{{ __('ui.user_filter') }}</label>
                            @php
                                $userOptions = $users->pluck('name', 'id')->toArray();
                            @endphp
                            <x-select name="user_id" id="user_id" :options="$userOptions" :selected="request('user_id')" placeholder="{{ __('ui.all_users') }}" width="w-full" />
                        </div>
                        @endif

                        <!-- Date Start -->
                        <div class="space-y-1">
                            <label for="start_date" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">{{ __('ui.from_date') }}</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </span>
                                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                                    class="form-input pl-10 block w-full rounded-lg border-secondary-300 focus:ring-primary-500 focus:border-primary-500 text-sm h-[42px]">
                            </div>
                        </div>

                        <!-- Date End -->
                        <div class="space-y-1">
                            <label for="end_date" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">{{ __('ui.to_date') }}</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </span>
                                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                                    class="form-input pl-10 block w-full rounded-lg border-secondary-300 focus:ring-primary-500 focus:border-primary-500 text-sm h-[42px]">
                            </div>
                        </div>

                        <!-- NEW: Subject Type Filter -->
                        @if(auth()->user()->role !== \App\Enums\UserRole::OPERATOR)
                        <div class="space-y-1">
                            <label for="subject_type" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">{{ __('ui.subject_type') }}</label>
                            @php
                                $subjectOptions = [
                                    'inventory' => __('ui.subject_inventory'),
                                    'user' => __('ui.subject_user'),
                                    'auth' => __('ui.subject_auth'),
                                    'report' => __('ui.subject_report'),
                                ];
                            @endphp
                            <x-select name="subject_type" id="subject_type" :options="$subjectOptions" :selected="request('subject_type')" placeholder="{{ __('ui.all_types') }}" width="w-full" />
                        </div>
                        @endif

                        <!-- Buttons -->
                        <div class="flex gap-2 w-full">
                             <button type="submit" class="btn btn-primary flex-1 justify-center flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                {{ __('ui.apply_filter') }}
                            </button>
                            <a href="{{ route('reports.activity-logs.index') }}" class="btn btn-secondary flex items-center justify-center gap-2 px-3" title="{{ __('ui.reset_filter') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </form>
            </div>



            <!-- Desktop Table View -->
            <div class="hidden md:block card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>{{ __('ui.user_header') }}</th>
                                <th>{{ __('ui.action_header') }}</th>
                                 <th>{{ __('ui.description_header') }}</th>
                                <th>{{ __('ui.time_header') }}</th>
                                <th class="text-right">{{ __('ui.action_column') }}</th>
                            </tr>
                        </thead>
                        <tbody id="desktop-logs-body">
                            @forelse ($activityLogs as $log)
                                <tr class="group hover:bg-secondary-50 transition-colors" id="log-{{ $log->id }}">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                                @if($log->user && $log->user->avatar)
                                                    <img src="{{ asset('storage/' . $log->user->avatar) }}" alt="" class="h-full w-full object-cover rounded-full">
                                                @else
                                                    <span class="font-bold text-xs">{{ substr($log->user->name ?? 'S', 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-medium text-secondary-900">{{ $log->user->name ?? __('ui.system_user') }}</div>
                                                <div class="text-xs text-secondary-500 font-mono">{{ $log->user->email ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $badgeColor = 'bg-secondary-50 text-secondary-700 border-secondary-200';
                                            $action = strtolower($log->action);
                                            if (str_contains($action, 'buat') || str_contains($action, 'create')) {
                                                $badgeColor = 'bg-success-50 text-success-700 border-success-200';
                                            } elseif (str_contains($action, 'update') || str_contains($action, 'perbarui') || str_contains($action, 'edit')) {
                                                $badgeColor = 'bg-primary-50 text-primary-700 border-primary-200';
                                            } elseif (str_contains($action, 'hapus') || str_contains($action, 'delete') || str_contains($action, 'reject') || str_contains($action, 'tolak')) {
                                                $badgeColor = 'bg-red-50 text-red-700 border-red-200';
                                            }
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border {{ $badgeColor }}">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-secondary-600">
                                        <div class="max-w-lg whitespace-normal break-words">
                                            {{ $log->description }}
                                        </div>
                                    </td>
                                     <td class="text-sm text-secondary-500 whitespace-nowrap">
                                        {{ $log->created_at->format('d M Y H:i:s') }}
                                    </td>
                                    <td class="text-right">
                                        @if($log->properties && count($log->properties) > 0)
                                            <button @click="viewActivityDetails({{ $log->id }})" 
                                                    class="p-2 text-secondary-400 hover:text-primary-600 transition-colors rounded-full hover:bg-primary-50 text-eye-btn"
                                                    title="Lihat Detail Perubahan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-secondary-500">
                                        {{ __('ui.no_activity_logs') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div id="mobile-logs-container" class="md:hidden space-y-4">
                @forelse ($activityLogs as $log)
                    <div class="card p-4 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-10 w-10 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 font-bold overflow-hidden">
                                     @if($log->user && $log->user->avatar)
                                        <img src="{{ asset('storage/' . $log->user->avatar) }}" alt="" class="h-full w-full object-cover">
                                    @else
                                        {{ substr($log->user->name ?? 'S', 0, 1) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-secondary-900 truncate">{{ $log->user->name ?? __('ui.system_user') }}</div>
                                    <div class="text-xs text-secondary-500">{{ $log->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            @php
                                $badgeColor = 'bg-secondary-50 text-secondary-700 border-secondary-200';
                                $action = strtolower($log->action);
                                if (str_contains($action, 'buat') || str_contains($action, 'create')) {
                                    $badgeColor = 'bg-success-50 text-success-700 border-success-200';
                                } elseif (str_contains($action, 'update') || str_contains($action, 'perbarui') || str_contains($action, 'edit')) {
                                    $badgeColor = 'bg-primary-50 text-primary-700 border-primary-200';
                                } elseif (str_contains($action, 'hapus') || str_contains($action, 'delete') || str_contains($action, 'reject') || str_contains($action, 'tolak')) {
                                    $badgeColor = 'bg-red-50 text-red-700 border-red-200';
                                }
                            @endphp
                            <span class="badge {{ $badgeColor }} text-[10px] uppercase font-bold tracking-wider flex-shrink-0 whitespace-nowrap">{{ $log->action }}</span>
                        </div>
                        
                        <div class="text-sm text-secondary-600 bg-secondary-50 p-3 rounded-lg border border-secondary-100 flex justify-between items-start gap-4">
                            <span class="flex-1">{{ $log->description }}</span>
                            @if($log->properties && count($log->properties) > 0)
                                <button @click="viewActivityDetails({{ $log->id }})" 
                                        class="px-3 py-1.5 text-sm font-semibold text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors shadow-sm">
                                    Detail
                                </button>
                            @endif
                        </div>

                        <div class="text-xs text-secondary-400 text-right">
                            {{ $log->created_at->format('d M Y • H:i:s') }}
                        </div>
                    </div>
                @empty
                    <div class="card p-8 text-center text-secondary-500">
                        <p class="text-sm">{{ __('ui.no_activity_logs') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $activityLogs->links() }}
            </div>

            <!-- Detail Modal (Premium Design) -->
            <div x-show="showActivityModal" 
                 class="fixed inset-0 z-[9999] overflow-y-auto" 
                 style="display: none;"
                 x-cloak
                 @keydown.escape.window="showActivityModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-secondary-900/60 backdrop-blur-sm" @click="showActivityModal = false" aria-hidden="true"></div>

                    <div class="relative inline-block w-full max-w-lg overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:max-w-2xl border border-secondary-100"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                        
                        {{-- Header --}}
                        <div class="bg-secondary-50/50 px-6 py-4 border-b border-secondary-100 flex justify-between items-center">
                            <h3 class="text-base font-bold text-secondary-900">Detail Aktivitas</h3>
                            <button @click="showActivityModal = false" class="text-secondary-400 hover:text-secondary-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        {{-- Content --}}
                        <div class="px-6 py-6" x-show="selectedActivity">
                            {{-- Activity Summary --}}
                            <div class="flex items-start gap-4 mb-6">
                                <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 flex-shrink-0 ring-4 ring-primary-50">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-lg font-bold text-secondary-900 leading-tight mb-1" x-text="selectedActivity?.description"></p>
                                    <span class="badge badge-secondary text-[10px] uppercase font-bold tracking-widest" x-text="selectedActivity?.action"></span>
                                </div>
                            </div>

                            {{-- Properties Table (The Audit Core) --}}
                            <div class="mb-6">
                                <h4 class="text-xs font-bold text-secondary-400 uppercase tracking-widest mb-3">Detail Perubahan Data</h4>
                                <div class="overflow-hidden border border-secondary-200 rounded-xl shadow-sm bg-white">
                                    <table class="min-w-full divide-y divide-secondary-200">
                                        <thead class="bg-secondary-50/50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-widest">Kolom</th>
                                                <th class="px-4 py-2 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-widest bg-red-50/30">Sebelum</th>
                                                <th class="px-4 py-2 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-widest bg-green-50/30">Sesudah</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-secondary-100">
                                            <template x-if="selectedActivity && selectedActivity.properties">
                                                <template x-for="(values, key) in selectedActivity.properties" :key="key">
                                                    <tr class="hover:bg-secondary-50/30 transition-colors">
                                                        <td class="px-4 py-3 text-xs font-bold text-secondary-700 capitalize" x-text="formatKey(key)"></td>
                                                        <td class="px-4 py-3 text-xs text-red-600 bg-red-50/10 break-all italic" x-text="formatValue(values.old)"></td>
                                                        <td class="px-4 py-3 text-xs text-green-700 bg-green-50/10 font-bold break-all" x-text="formatValue(values.new)"></td>
                                                    </tr>
                                                </template>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Metadata Info --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-3 bg-secondary-50 rounded-xl border border-secondary-100">
                                    <p class="text-[10px] font-bold text-secondary-400 uppercase tracking-widest mb-1">Pengguna</p>
                                    <p class="text-sm font-bold text-secondary-900 truncate" x-text="selectedActivity?.user?.name || selectedActivity?.user_name || 'System'"></p>
                                    <p class="text-[10px] text-secondary-500 font-mono" x-text="selectedActivity?.user?.email || selectedActivity?.user_email || ''"></p>
                                </div>
                                <div class="p-3 bg-secondary-50 rounded-xl border border-secondary-100">
                                    <p class="text-[10px] font-bold text-secondary-400 uppercase tracking-widest mb-1">Waktu Presisi</p>
                                    <p class="text-sm font-bold text-secondary-900" 
                                       x-text="selectedActivity ? new Date(selectedActivity.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'medium' }) : '-'"></p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

    <script>
        function activityLogComponent() {
            return {
                showFilters: false,
                showActivityModal: false,
                selectedActivity: null,
                logs: @js($activityLogs->getCollection()->keyBy('id')),

                init() {
                    console.log('[Alpine] Activity Log Initialized');
                    if (window.Echo) {
                        console.log('[Alpine] Echo found, listening for activity-logs...');
                        window.Echo.channel('activity-logs')
                            .listen('ActivityLogged', (e) => {
                                console.log('[Alpine] Event received:', e);
                                const activity = e.activity;
                                this.logs[activity.id] = activity;
                                this.$nextTick(() => {
                                    this.appendLogToUI(activity);
                                });
                            });
                    }
                },                appendLogToUI(activity) {
                    const desktopBody = document.getElementById('desktop-logs-body');
                    const mobileContainer = document.getElementById('mobile-logs-container');
                    
                    const action = (activity.action || '').toLowerCase();
                    let badgeColor = 'bg-secondary-50 text-secondary-700 border-secondary-200';
                    if (action.includes('buat') || action.includes('create')) {
                        badgeColor = 'bg-success-50 text-success-700 border-success-200';
                    } else if (action.includes('update') || action.includes('perbarui') || action.includes('edit')) {
                        badgeColor = 'bg-primary-50 text-primary-700 border-primary-200';
                    } else if (action.includes('hapus') || action.includes('delete') || action.includes('reject') || action.includes('tolak')) {
                        badgeColor = 'bg-red-50 text-red-700 border-red-200';
                    }

                    if (desktopBody) {
                        const emptyRow = desktopBody.querySelector('td[colspan="5"]');
                        if (emptyRow) emptyRow.closest('tr').remove();
                        
                        const newRowHTML = `
                            <tr class="group hover:bg-secondary-50 transition-colors animate-highlight" id="log-${activity.id}">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                            <span class="font-bold text-xs">${(activity.user_name || 'S').charAt(0)}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-secondary-900">${activity.user_name || 'System'}</div>
                                            <div class="text-xs text-secondary-500 font-mono">${activity.user_email || '-'}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border ${badgeColor}">
                                        ${activity.action}
                                    </span>
                                </td>
                                <td class="text-sm text-secondary-600">
                                    <div class="max-w-lg whitespace-normal break-words">${activity.description}</div>
                                </td>
                                <td class="text-sm text-secondary-500 whitespace-nowrap">
                                    ${new Date(activity.created_at).toLocaleString('id-ID')}
                                </td>
                                <td class="text-right">
                                    ${(activity.properties && Object.keys(activity.properties).length > 0) ? `
                                        <button class="view-log-btn p-2 text-secondary-400 hover:text-primary-600 transition-colors rounded-full hover:bg-primary-50"
                                                title="Lihat Detail Perubahan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                    ` : ''}
                                </td>
                            </tr>
                        `;
                        desktopBody.insertAdjacentHTML('afterbegin', newRowHTML);
                        const newRow = desktopBody.firstElementChild;
                        const btn = newRow.querySelector('.view-log-btn');
                        if (btn) btn.onclick = () => this.viewActivityDetails(activity.id);
                    }

                    if (mobileContainer) {
                        const emptyCard = mobileContainer.querySelector('.text-center');
                        if (emptyCard) emptyCard.closest('.card').remove();

                        const newCardHTML = `
                            <div class="card p-4 flex flex-col gap-3 animate-highlight">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="h-10 w-10 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 font-bold overflow-hidden">
                                            ${(activity.user_name || 'S').charAt(0)}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-secondary-900 truncate">${activity.user_name || 'System'}</div>
                                            <div class="text-xs text-secondary-500">Baru Saja</div>
                                        </div>
                                    </div>
                                    <span class="badge ${badgeColor} text-[10px] uppercase font-bold tracking-wider flex-shrink-0 whitespace-nowrap">${activity.action}</span>
                                </div>
                                <div class="text-sm text-secondary-600 bg-secondary-50 p-3 rounded-lg border border-secondary-100 flex justify-between items-start gap-4">
                                    <span class="flex-1">${activity.description}</span>
                                    ${(activity.properties && Object.keys(activity.properties).length > 0) ? `
                                        <button class="view-log-btn px-3 py-1.5 text-sm font-semibold text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors shadow-sm">
                                            Detail
                                        </button>
                                    ` : ''}
                                </div>
                                <div class="text-xs text-secondary-400 text-right">
                                    Baru Saja
                                </div>
                            </div>
                        `;
                        mobileContainer.insertAdjacentHTML('afterbegin', newCardHTML);
                        const newCard = mobileContainer.firstElementChild;
                        const btn = newCard.querySelector('.view-log-btn');
                        if (btn) btn.onclick = () => this.viewActivityDetails(activity.id);
                    }
                },

                formatValue(val) {
                    if (val === null || val === undefined) return '-';
                    if (typeof val === 'boolean') return val ? 'Ya' : 'Tidak';
                    return val;
                },

                formatKey(key) {
                    const translations = {
                        'item_ids': 'ID Item',
                        'counts': 'Jumlah Potongan',
                        'total_labels': 'Total Label',
                        'name': 'Nama',
                        'brand': 'Merek',
                        'category': 'Kategori',
                        'stock': 'Stok',
                        'price': 'Harga',
                        'description': 'Deskripsi',
                        'condition': 'Kondisi',
                        'location': 'Lokasi',
                        'color': 'Warna',
                        'status': 'Status',
                        'type': 'Tipe',
                        'role': 'Peran',
                        'email': 'Email',
                        'password': 'Kata Sandi',
                        'part_number': 'No. Identifikasi',
                        'remarks': 'Catatan',
                        'problem_chronology': 'Kronologi Masalah'
                    };
                    const lowerKey = key.toLowerCase();
                    return translations[lowerKey] || key.replace(/_/g, ' ');
                },

                viewActivityDetails(id) {
                    console.log('[Alpine] viewActivityDetails called for ID:', id);
                    if (!this.logs) {
                        console.error('[Alpine] logs object is undefined!');
                        return;
                    }
                    this.selectedActivity = this.logs[id];
                    if (this.selectedActivity) {
                        this.showActivityModal = true;
                    } else {
                        console.warn('[Alpine] Log not found for ID:', id);
                    }
                }
            };
        }
    </script>

    <!-- Animation Helper -->
    <style>
        @keyframes highlight-fade {
            0% { background-color: rgba(253, 224, 71, 0.4); }
            100% { background-color: transparent; }
        }
        .animate-highlight {
            animation: highlight-fade 4s ease-out forwards;
        }
    </style>
        </div>
    </div>
</x-app-layout>
