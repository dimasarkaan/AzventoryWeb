<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showFilters: false, isFiltering: false }">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        Manajemen Pengguna
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">{{ __('ui.user_management_desc') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    @if(request('trash'))
                        <a href="{{ route('users.index') }}" class="btn btn-danger p-2.5 rounded-lg flex items-center justify-center" title="{{ __('ui.back') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('users.index', ['trash' => 'true']) }}" class="btn btn-white bg-white p-2.5 shadow-sm border border-secondary-200 text-secondary-600 hover:bg-secondary-50 rounded-lg" title="{{ __('ui.view_trash') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </a>
                        <a href="{{ route('users.create') }}" class="btn btn-primary flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            {{ __('ui.add_user') }}
                        </a>
                    @endif
                </div>
            </div>

            @if(request('trash'))
                <div class="rounded-lg border border-danger-200 bg-danger-50 p-4 mb-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <div>
                            <h3 class="font-medium text-danger-900">{{ __('ui.trash_mode') }}</h3>
                            <p class="text-sm text-danger-700 mt-1">{{ __('ui.trash_mode_desc') }}</p>
                        </div>
                    </div>
                </div>
            @endif



             <!-- Tab Navigation for Status -->
             <div class="mb-4 border-b border-secondary-200">
                <nav class="-mb-px flex space-x-6 overflow-x-auto scrollbar-hide" aria-label="Tabs">
                    <a href="{{ route('users.index', ['status' => '', 'role' => request('role'), 'search' => request('search'), 'trash' => request('trash')]) }}" 
                       class="{{ request('status') === null || request('status') === '' ? 'border-primary-500 text-primary-600' : 'border-transparent text-secondary-500 hover:text-secondary-700 hover:border-secondary-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-black text-sm transition-colors flex items-center gap-2">
                        Semua Status
                    </a>
                    <a href="{{ route('users.index', ['status' => 'active', 'role' => request('role'), 'search' => request('search'), 'trash' => request('trash')]) }}" 
                       class="{{ request('status') === 'active' ? 'border-success-500 text-success-600' : 'border-transparent text-secondary-500 hover:text-secondary-700 hover:border-secondary-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-black text-sm transition-colors flex items-center gap-2">
                        Aktif
                    </a>
                    <a href="{{ route('users.index', ['status' => 'inactive', 'role' => request('role'), 'search' => request('search'), 'trash' => request('trash')]) }}" 
                       class="{{ request('status') === 'inactive' ? 'border-secondary-500 text-secondary-600' : 'border-transparent text-secondary-400 hover:text-secondary-600 hover:border-secondary-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-black text-sm transition-colors flex items-center gap-2">
                        Nonaktif
                    </a>
                </nav>
            </div>

             <!-- Search & Filters -->
             <div class="mb-4 card p-4 overflow-visible">
                 <form method="GET" action="{{ route('users.index') }}" @submit="isFiltering = true" novalidate id="userFilterForm">
                     @if(request('trash'))
                         <input type="hidden" name="trash" value="true">
                     @endif
                     <input type="hidden" name="status" value="{{ request('status') }}">
                     
                     <!-- Top: Search Bar & Filter Toggle -->
                     <div class="flex flex-col md:flex-row gap-4 items-center justify-between transition-all duration-300">
                        <div class="flex w-full gap-2 md:block">
                             <div class="relative w-full md:flex-1"
                                  x-data="{ searchQuery: '{{ request('search') ? addslashes(request('search')) : '' }}' }"
                                  @keydown.window="
                                    if ($event.key === '/' && $event.target.tagName !== 'INPUT' && $event.target.tagName !== 'TEXTAREA') {
                                        $event.preventDefault();
                                        $refs.searchInput.focus();
                                    }
                                  "
                             >
                                 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                     <svg x-show="!isFiltering" class="w-5 h-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                     <svg x-show="isFiltering" x-cloak class="animate-spin w-5 h-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                 </div>
                                 <input type="text" x-ref="searchInput" name="search" x-model="searchQuery" 
                                        data-testid="search-users"
                                        @keydown.escape="$refs.searchInput.blur()"
                                        class="input-field pl-10 pr-20 w-full" 
                                        placeholder="{{ __('ui.search_user_placeholder') }}" 
                                        onchange="this.form.submit()" maxlength="255">
                                 
                                 <!-- Search Shortcut Hint (Hidden on mobile or when typing) -->
                                 <div x-show="searchQuery.length === 0" class="absolute inset-y-0 right-0 pr-3 hidden sm:flex items-center pointer-events-none">
                                     <kbd class="px-2 py-1 text-[10px] font-semibold text-secondary-500 bg-secondary-100 border border-secondary-200 rounded-md shadow-sm">/</kbd>
                                 </div>

                                 <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''; isFiltering = true; $nextTick(() => { document.querySelector('form[action=\'{{ route('users.index') }}\']').submit(); })" class="absolute inset-y-0 right-0 pr-3 flex items-center text-secondary-400 hover:text-danger-500 transition-colors cursor-pointer" title="Hapus Pencarian" x-cloak>
                                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                 </button>
                             </div>
                             
                            <!-- Mobile Filter Button -->
                            <button type="button" @click="showFilters = true" class="btn btn-secondary md:hidden flex items-center justify-center w-12 flex-shrink-0 relative" title="{{ __('ui.show_filter') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                @if(request('role'))
                                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-primary-500 rounded-full border-2 border-white"></span>
                                @endif
                            </button>
                        </div>

                         <!-- Desktop Filters Container -->
                         <div class="hidden md:flex flex-row gap-3 items-center">
                             <div class="w-auto min-w-[150px]">
                                 @php
                                     $roleOptions = [
                                         \App\Enums\UserRole::SUPERADMIN->value => \App\Enums\UserRole::SUPERADMIN->label(),
                                         \App\Enums\UserRole::ADMIN->value => \App\Enums\UserRole::ADMIN->label(),
                                         \App\Enums\UserRole::OPERATOR->value => \App\Enums\UserRole::OPERATOR->label(),
                                     ];
                                 @endphp
                                 <x-select name="role" :options="$roleOptions" :selected="request('role')" placeholder="{{ __('ui.all_roles') }}" :submitOnChange="true" width="w-full" />
                             </div>
         
                             <a href="{{ request('trash') ? route('users.index', ['trash' => 'true']) : route('users.index') }}" id="reset-filters" class="btn btn-secondary flex items-center justify-center gap-2 p-2.5 h-[42px] w-[42px] flex-shrink-0" title="{{ __('ui.reset_filter') }}">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                 </svg>
                             </a>
                         </div>
                     </div>
                     
                     <!-- Mobile Filter Drawer (Off-Canvas) -->
                     <template x-teleport="body">
                         <div x-show="showFilters" class="fixed inset-0 z-[100] md:hidden" style="display: none;">
                             <!-- Backdrop -->
                             <div x-show="showFilters" 
                                  x-transition:enter="transition-opacity ease-linear duration-300" 
                                  x-transition:enter-start="opacity-0" 
                                  x-transition:enter-end="opacity-100" 
                                  x-transition:leave="transition-opacity ease-linear duration-300" 
                                  x-transition:leave-start="opacity-100" 
                                  x-transition:leave-end="opacity-0" 
                                  class="fixed inset-0 bg-secondary-900/60 backdrop-blur-sm" 
                                  @click="showFilters = false"></div>
                             
                             <!-- Bottom Sheet -->
                             <div x-show="showFilters" 
                                  x-transition:enter="transition ease-in-out duration-300 transform" 
                                  x-transition:enter-start="translate-y-full" 
                                  x-transition:enter-end="translate-y-0" 
                                  x-transition:leave="transition ease-in-out duration-300 transform" 
                                  x-transition:leave-start="translate-y-0" 
                                  x-transition:leave-end="translate-y-full" 
                                  class="fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl flex flex-col max-h-[85vh]">
                                 
                                 <!-- Handle -->
                                 <div class="flex justify-center pt-3 pb-2" @click="showFilters = false">
                                     <div class="w-12 h-1.5 bg-secondary-200 rounded-full"></div>
                                 </div>
                                 
                                 <div class="px-6 py-4 border-b border-secondary-100 flex justify-between items-center bg-white sticky top-0 z-10">
                                     <h3 class="text-lg font-bold text-secondary-900">{{ __('ui.filter_configuration') }}</h3>
                                     <button type="button" @click="showFilters = false" class="text-secondary-400 hover:text-secondary-600 bg-secondary-50 p-2 rounded-full">
                                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                     </button>
                                 </div>
                                 
                                 <div class="p-6 overflow-y-auto pb-24 space-y-6">
                                     <div>
                                         <label class="block text-sm font-bold text-secondary-700 mb-2">{{ __('ui.role') }}</label>
                                         <x-select name="role_mobile" :options="$roleOptions" :selected="request('role')" placeholder="{{ __('ui.all_roles') }}" :submitOnChange="false" width="w-full" />
                                     </div>
                                 </div>
                                 
                                 <div class="p-4 border-t border-secondary-100 bg-white fixed bottom-0 left-0 right-0 flex gap-3 shadow-[0_-10px_20px_-10px_rgba(0,0,0,0.1)] pb-[calc(1rem+env(safe-area-inset-bottom))]">
                                     <a href="{{ request('trash') ? route('users.index', ['trash' => 'true']) : route('users.index') }}" class="btn btn-secondary flex-1 justify-center">{{ __('ui.reset_filter') }}</a>
                                     <button type="button" @click="
                                         document.querySelector('input[name=\'role\']').value = document.querySelector('input[name=\'role_mobile\']').value;
                                         document.getElementById('userFilterForm').submit();
                                         isFiltering = true;
                                     " class="btn btn-primary flex-1 justify-center">{{ __('ui.apply_filter') }}</button>
                                 </div>
                             </div>
                         </div>
                     </template>
                 </form>
             </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-4 transition-opacity duration-300" :class="isFiltering ? 'opacity-50 pointer-events-none' : ''">
                @forelse($users as $user)
                    @include('users.partials.mobile-card', ['user' => $user])
                @empty
                    <!-- Mobile Empty State -->
                    <div class="card p-8 flex flex-col items-center justify-center text-center">
                        @php
                            $isFiltered = request('search') || request('role') || request('status');
                        @endphp

                        <div class="h-16 w-16 bg-secondary-100 text-secondary-400 rounded-full flex items-center justify-center mb-4 shadow-sm border border-secondary-200">
                             @if(request('trash'))
                                {{-- Trash Icon --}}
                                <svg class="w-8 h-8 text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            @elseif($isFiltered)
                                {{-- Search/Filter Icon --}}
                                <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            @else
                                {{-- Default User Icon --}}
                                <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            @endif
                        </div>
                        
                        <h3 class="text-lg font-medium text-secondary-900">
                            @if(request('trash'))
                                {{ __('ui.trash_empty') }}
                            @elseif($isFiltered)
                                {{ __('ui.no_results') }}
                            @else
                                {{ __('ui.users_empty') }}
                            @endif
                        </h3>

                        <p class="text-secondary-500 text-sm mt-1 max-w-xs mx-auto">
                            @if(request('trash'))
                                {{ __('ui.trash_empty_desc') }}
                            @elseif($isFiltered)
                                {{ __('ui.no_results_desc') }}
                            @else
                                {{ __('ui.users_empty_desc') }}
                            @endif
                        </p>

                        @if($isFiltered)
                            <div class="mt-4">
                                <a href="{{ route('users.index', request()->only(['trash'])) }}" class="btn btn-secondary px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 mx-auto w-fit">
                                    <x-icon.restore class="w-4 h-4" />
                                    Hapus Filter & Pencarian
                                </a>
                            </div>
                        @endif
                    </div>
                @endforelse
                
                 <!-- Mobile Pagination -->
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>

            <!-- Desktop Table View (Hidden on Mobile) -->
            <div class="hidden md:block card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                @if(request('trash'))
                                    <th class="w-10 text-center">
                                        <input type="checkbox" id="selectAll" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                    </th>
                                @endif
                                <th>{{ __('ui.profile') }}</th>
                                <th>{{ __('ui.email_contact') }}</th>
                                <th>{{ __('ui.job_title') }}</th>
                                <th>{{ __('ui.role') }}</th>
                                <th>{{ __('ui.status') }}</th>
                                <th class="text-right">{{ __('ui.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody x-show="!isFiltering">
                            @forelse($users as $user)
                                <x-user.table-row :user="$user" :trash="request('trash')" />
                            @empty
                                <tr>
                                    <td colspan="{{ request('trash') ? '7' : '6' }}" class="px-6 py-12 text-center text-secondary-500">
                                        <div class="flex flex-col items-center justify-center">
                                            @php
                                                $isFiltered = request('search') || request('role') || request('status');
                                            @endphp

                                            <div class="h-16 w-16 bg-secondary-100 text-secondary-400 rounded-full flex items-center justify-center mb-4">
                                                @if(request('trash'))
                                                    {{-- Trash Icon --}}
                                                    <svg class="w-8 h-8 text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                @elseif($isFiltered)
                                                    {{-- Search/Filter Icon --}}
                                                    <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                                @else
                                                    {{-- Default User Icon --}}
                                                    <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                @endif
                                            </div>

                                            <p class="text-lg font-medium text-secondary-900">
                                                @if(request('trash'))
                                                    {{ __('ui.trash_empty') }}
                                                @elseif($isFiltered)
                                                    {{ __('ui.no_results') }}
                                                @else
                                                    {{ __('ui.users_empty') }}
                                                @endif
                                            </p>

                                            <p class="text-sm mt-1 max-w-xs mx-auto leading-relaxed text-secondary-500">
                                                @if(request('trash'))
                                                    {{ __('ui.trash_empty_desc') }}
                                                @elseif($isFiltered)
                                                    {{ __('ui.no_results_desc') }}
                                                @else
                                                    {{ __('ui.users_empty_desc') }}
                                                @endif
                                            </p>

                                            @if($isFiltered)
                                                <div class="mt-4">
                                                    <a href="{{ route('users.index', request()->only(['trash'])) }}" class="btn btn-secondary px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 mx-auto w-fit">
                                                        <x-icon.restore class="w-4 h-4" />
                                                        Hapus Filter & Pencarian
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        <!-- High-Quality Skeleton Body -->
                        <tbody id="skeleton-body" class="divide-y divide-secondary-100 bg-white" x-show="isFiltering" x-cloak>
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    @if(request('trash'))
                                        <td class="px-4 py-4 text-center">
                                            <div class="h-4 w-4 bg-secondary-100 rounded animate-pulse mx-auto"></div>
                                        </td>
                                    @endif
                                    <!-- Profil -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-secondary-100 animate-pulse flex-shrink-0"></div>
                                            <div class="space-y-2">
                                                <div class="h-4 w-32 bg-secondary-100 rounded animate-pulse"></div>
                                                <div class="h-3 w-20 bg-secondary-50 rounded animate-pulse"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Email / Kontak -->
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                             <div class="h-4 w-40 bg-secondary-50 rounded animate-pulse"></div>
                                             <div class="h-3 w-24 bg-secondary-50 rounded animate-pulse hidden sm:block"></div>
                                        </div>
                                    </td>
                                    <!-- Jabatan -->
                                    <td class="px-6 py-4">
                                         <div class="h-4 w-24 bg-secondary-50 rounded animate-pulse"></div>
                                    </td>
                                    <!-- Role -->
                                    <td class="px-6 py-4">
                                        <div class="h-5 w-20 bg-secondary-100 rounded-full animate-pulse"></div>
                                    </td>
                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        <div class="h-5 w-16 bg-secondary-100 rounded-full animate-pulse"></div>
                                    </td>
                                    <!-- Aksi -->
                                    <td class="px-6 py-4 text-right">
                                         <div class="flex justify-end gap-2">
                                            <div class="h-8 w-8 bg-secondary-50 rounded-lg animate-pulse"></div>
                                            <div class="h-8 w-8 bg-secondary-50 rounded-lg animate-pulse"></div>
                                            <div class="h-8 w-8 bg-secondary-50 rounded-lg animate-pulse"></div>
                                         </div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

    <!-- Floating Bulk Action Bar -->
    @if(request('trash'))
        <div id="bulk-action-bar" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-xl shadow-xl border border-secondary-200 px-6 py-3 flex items-center gap-6 z-50 transition-all duration-300 translate-y-24 opacity-0">
            <div class="flex items-center gap-3 border-r border-secondary-200 pr-6">
                <button type="button" onclick="clearBulkSelection()" class="p-1.5 rounded-full text-secondary-400 hover:text-danger-500 hover:bg-danger-50 transition-colors" title="Batalkan Pilihan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <div class="flex items-center gap-1.5">
                    <span class="font-bold text-lg text-primary-600" id="selected-count">0</span>
                    <span class="text-sm text-secondary-500 font-medium">{{ __('ui.selected') }}</span>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <form action="{{ route('users.bulk-restore') }}" method="POST" id="bulk-restore-form" novalidate>
                    @csrf
                    <button type="button" onclick="submitBulkRestore()" class="btn btn-white text-secondary-700 hover:text-primary-600 flex items-center gap-2 border-0 bg-transparent hover:bg-secondary-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span class="font-medium">{{ __('ui.restore') }}</span>
                    </button>
                </form>

                <form action="{{ route('users.bulk-force-delete') }}" method="POST" id="bulk-delete-form" novalidate>
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="submitBulkDelete()" class="btn btn-danger flex items-center gap-2 px-4 py-2 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span>{{ __('ui.force_delete') }}</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    @push('scripts')
    @include('users.partials._index_scripts')
    @endpush


            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>




