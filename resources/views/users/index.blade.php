<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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



             <!-- Search & Filters -->
             <div class="mb-4 card p-4 overflow-visible" x-data="{ showFilters: false }">
                 <form method="GET" action="{{ route('users.index') }}">
                     <!-- Top: Search Bar & Filter Toggle -->
                     <div class="mb-4 flex flex-col md:flex-row gap-4 items-center justify-between transition-all duration-300">
                        <div class="flex w-full gap-2 md:block">
                             <div class="relative w-full md:flex-1">
                                 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                     <svg class="w-5 h-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                 </div>
                                 <input type="text" name="search" value="{{ request('search') }}" class="input-field pl-10 w-full" placeholder="{{ __('ui.search_user_placeholder') }}" onchange="this.form.submit()">
                             </div>
                            <button type="button" @click="showFilters = !showFilters" class="btn btn-secondary md:hidden flex items-center justify-center w-12 flex-shrink-0" title="{{ __('ui.show_filter') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            </button>
                        </div>

                         <!-- Filters Container -->
                         <div class="flex-col md:flex-row gap-3 w-full md:w-auto items-center" :class="showFilters ? 'flex' : 'hidden md:flex'">
                             <div class="w-full md:w-auto min-w-[150px]">
                                 @php
                                     $roleOptions = [
                                         \App\Enums\UserRole::SUPERADMIN->value => \App\Enums\UserRole::SUPERADMIN->label(),
                                         \App\Enums\UserRole::ADMIN->value => \App\Enums\UserRole::ADMIN->label(),
                                         \App\Enums\UserRole::OPERATOR->value => \App\Enums\UserRole::OPERATOR->label(),
                                     ];
                                 @endphp
                                 <x-select name="role" :options="$roleOptions" :selected="request('role')" placeholder="{{ __('ui.all_roles') }}" :submitOnChange="true" width="w-full md:w-auto" />
                             </div>
                             <div class="w-full md:w-auto min-w-[150px]">
                                 @php
                                     $statusOptions = [
                                         'active' => __('ui.active'),
                                         'inactive' => __('ui.inactive'),
                                     ];
                                 @endphp
                                 <x-select name="status" :options="$statusOptions" :selected="request('status')" placeholder="{{ __('ui.all_statuses') }}" :submitOnChange="true" width="w-full md:w-auto" />
                             </div>
         
                             <a href="{{ route('users.index') }}" id="reset-filters" class="btn btn-secondary flex items-center justify-center gap-2 w-full md:w-auto" title="{{ __('ui.reset_filter') }}">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                 </svg>
                             </a>
                         </div>
                     </div>
                 </form>
             </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-4">
                @forelse($users as $user)
                    <div class="card p-4">
                        <!-- Header: Avatar, Name, Role -->
                        <div class="flex items-start gap-4 mb-4">
                            <!-- Avatar -->
                            @if(request('trash'))
                                <div class="flex items-center self-center mr-2">
                                    <input type="checkbox" name="ids[]" value="{{ $user->id }}" class="user-checkbox rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 w-5 h-5">
                                </div>
                            @endif
                            <div class="h-14 w-14 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 border border-secondary-200 overflow-hidden">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" loading="lazy" class="h-full w-full object-cover">
                                @else
                                    <span class="font-bold text-lg">{{ substr($user->name, 0, 1) }}</span>
                                @endif
                            </div>
                            
                            <!-- Identity -->
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-base font-bold text-secondary-900 line-clamp-1">
                                            {{ $user->name }}
                                        </h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            @php
                                                $roleColor = match($user->role) {
                                                    \App\Enums\UserRole::SUPERADMIN => 'bg-purple-100 text-purple-700 border-purple-200',
                                                    \App\Enums\UserRole::ADMIN => 'bg-blue-100 text-blue-700 border-blue-200',
                                                    \App\Enums\UserRole::OPERATOR => 'bg-gray-100 text-gray-700 border-gray-200',
                                                };
                                            @endphp
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium border {{ $roleColor }}">
                                                {{ $user->role->label() }}
                                            </span>
                                            <span class="w-1 h-1 rounded-full bg-secondary-300"></span>
                                            <span class="text-xs text-secondary-500">{{ $user->created_at->format('M Y') }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Status Badge (Top Right) -->
                                     @if($user->status === 'active')
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-success-100 text-success-700 tracking-wide uppercase">{{ __('ui.active') }}</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-secondary-100 text-secondary-600 tracking-wide uppercase">{{ __('ui.inactive') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 gap-y-2 text-sm mb-4 border-t border-b border-secondary-100 py-3">
                             <div class="flex items-center gap-2 text-secondary-600">
                                <svg class="w-4 h-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="truncate">{{ $user->email }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-secondary-600">
                                <svg class="w-4 h-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="truncate">{{ $user->job_title ?? '-' }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-2">
                            @if(request('trash'))
                                @can('restore', $user)
                                <form action="{{ route('users.restore', $user->id) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success w-full justify-center flex items-center gap-2" onclick="confirmUserRestore(event)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        {{ __('ui.restore') }}
                                    </button>
                                </form>
                                @endcan
                            @else
                                @can('update', $user)
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-secondary flex-1 justify-center flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        {{ __('ui.edit') }}
                                    </a>
                                @endcan
                                @can('delete', $user)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger w-full justify-center flex items-center gap-2" onclick="confirmDelete(event)">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            {{ __('ui.delete') }}
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </div>
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
                        <tbody>
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

                                            <p class="text-sm mt-1 max-w-xs mx-auto leading-relaxed">
                                                @if(request('trash'))
                                                    {{ __('ui.trash_empty_desc') }}
                                                @elseif($isFiltered)
                                                    {{ __('ui.no_results_desc') }}
                                                @else
                                                    {{ __('ui.users_empty_desc') }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        <!-- High-Quality Skeleton Body -->
                        <tbody id="skeleton-body" class="hidden divide-y divide-secondary-100 bg-white">
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
            <div class="flex items-center gap-2 border-r border-secondary-200 pr-6">
                <span class="font-bold text-lg text-primary-600" id="selected-count">0</span>
                <span class="text-sm text-secondary-500 font-medium">{{ __('ui.selected') }}</span>
            </div>
            
            <div class="flex items-center gap-3">
                <form action="{{ route('users.bulk-restore') }}" method="POST" id="bulk-restore-form">
                    @csrf
                    <button type="button" onclick="submitBulkRestore()" class="btn btn-white text-secondary-700 hover:text-primary-600 flex items-center gap-2 border-0 bg-transparent hover:bg-secondary-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span class="font-medium">{{ __('ui.restore') }}</span>
                    </button>
                </form>

                <form action="{{ route('users.bulk-force-delete') }}" method="POST" id="bulk-delete-form">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Bulk Actions Logic ---
            const selectAll = document.getElementById('selectAll');
            const floatingBar = document.getElementById('bulk-action-bar');
            const countLabel = document.getElementById('selected-count');
            
            // Function to attach checkbox listeners (needed for initial load AND after AJAX)
            window.attachCheckboxListeners = function() {
                const checkboxes = document.querySelectorAll('.user-checkbox');
                checkboxes.forEach(cb => {
                    // Remove old listener to avoid duplicates if any (though replacement helps)
                    cb.removeEventListener('change', updateFloatingBar);
                    cb.addEventListener('change', updateFloatingBar);
                });
            };

            window.updateFloatingBar = function() {
                if(!floatingBar) return;
                
                const selected = document.querySelectorAll('.user-checkbox:checked');
                const count = selected.length;
                
                if(countLabel) countLabel.textContent = count;
                
                if(count > 0) {
                    floatingBar.classList.remove('translate-y-24', 'opacity-0');
                    floatingBar.classList.add('translate-y-0', 'opacity-100');
                } else {
                    floatingBar.classList.add('translate-y-24', 'opacity-0');
                    floatingBar.classList.remove('translate-y-0', 'opacity-100');
                }
            };

            // Initial Attach
            attachCheckboxListeners();

            if(selectAll) {
                selectAll.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.user-checkbox');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateFloatingBar();
                });
            }

            // Global Submit Functions
            window.submitBulkRestore = function() {
                const selected = document.querySelectorAll('.user-checkbox:checked');
                if(selected.length === 0) return;

                Swal.fire({
                    title: '{{ __('ui.restore_user_title') }}',
                    text: `${selected.length} {{ __('ui.restore_user_confirm') }}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('ui.yes_restore') }}',
                    cancelButtonText: '{{ __('ui.cancel') }}',
                    reverseButtons: true,
                     customClass: {
                        popup: '!rounded-2xl !font-sans',
                         title: '!text-secondary-900 !text-xl !font-bold',
                        htmlContainer: '!text-secondary-500 !text-sm',
                        confirmButton: 'btn btn-success px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-success-500',
                        cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                    },
                    buttonsStyling: false,
                    width: '24em',
                    iconColor: '#10b981', 
                    padding: '2em',
                    backdrop: `rgba(0,0,0,0.4)`
                }).then((result) => {
                     if (result.isConfirmed) {
                        const form = document.getElementById('bulk-restore-form');
                        // Clear previous hidden inputs
                        form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                        
                        selected.forEach(cb => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = cb.value;
                            form.appendChild(input);
                        });
                        form.submit();
                     }
                });
            };

            window.submitBulkDelete = function() {
                const selected = document.querySelectorAll('.user-checkbox:checked');
                if(selected.length === 0) return;

                Swal.fire({
                    title: '{{ __('ui.delete_user_title') }}',
                    text: "{{ __('ui.delete_permanent_confirm') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('ui.yes_delete') }}',
                    cancelButtonText: '{{ __('ui.cancel') }}',
                    reverseButtons: true,
                    customClass: {
                        popup: '!rounded-2xl !font-sans',
                        title: '!text-secondary-900 !text-xl !font-bold',
                        htmlContainer: '!text-secondary-500 !text-sm',
                        confirmButton: 'btn btn-danger px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-danger-500',
                        cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                    },
                    buttonsStyling: false,
                    width: '24em',
                    iconColor: '#ef4444',
                    padding: '2em',
                    backdrop: `rgba(0,0,0,0.4)`
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('bulk-delete-form');
                        // Clear previous hidden inputs
                        form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                        
                        selected.forEach(cb => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = cb.value;
                            form.appendChild(input);
                        });
                        form.submit();
                    }
                });
            };

            // Single Row Action Handlers
            window.confirmUserRestore = function(event) {
                event.preventDefault();
                const form = event.target.closest('form');
                Swal.fire({
                    title: '{{ __('ui.restore_user_title') }}',
                    text: "{{ __('ui.restore_user_confirm') }}",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('ui.yes_restore') }}',
                    cancelButtonText: '{{ __('ui.cancel') }}',
                    reverseButtons: true,
                    customClass: {
                        popup: '!rounded-2xl !font-sans',
                        title: '!text-secondary-900 !text-xl !font-bold',
                        htmlContainer: '!text-secondary-500 !text-sm',
                        confirmButton: 'btn btn-success px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-success-500',
                        cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                    },
                    buttonsStyling: false,
                    width: '24em',
                    iconColor: '#10b981', 
                    padding: '2em',
                    backdrop: `rgba(0,0,0,0.4)`
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            };

            window.confirmUserForceDelete = function(event) {
                event.preventDefault();
                const form = event.target.closest('form');
                Swal.fire({
                    title: '{{ __('ui.delete_user_title') }}',
                    text: "{{ __('ui.delete_permanent_confirm') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('ui.yes_delete') }}',
                    cancelButtonText: '{{ __('ui.cancel') }}',
                    reverseButtons: true,
                    customClass: {
                        popup: '!rounded-2xl !font-sans',
                        title: '!text-secondary-900 !text-xl !font-bold',
                        htmlContainer: '!text-secondary-500 !text-sm',
                        confirmButton: 'btn btn-danger px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-danger-500',
                        cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                    },
                    buttonsStyling: false,
                    width: '24em',
                    iconColor: '#ef4444',
                    padding: '2em',
                    backdrop: `rgba(0,0,0,0.4)`
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            };


            // --- Filter & Pagination Logic ---
            const filterForm = document.querySelector('form[action="{{ route('users.index') }}"]');
            
            const realBody = document.querySelector('tbody:not(#skeleton-body)');
            const skeletonBody = document.getElementById('skeleton-body');
            const paginationContainer = document.querySelector('.mt-6');
            const tableContainer = document.querySelector('.table-modern')?.parentNode;
            const resetBtn = document.getElementById('reset-filters');

            if (filterForm) {
                // Prevent default form submission and use AJAX
                filterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    fetchData(new FormData(filterForm));
                });

                // Handle Input Changes
                const inputs = filterForm.querySelectorAll('input, select');
                let debounceTimer;
                inputs.forEach(input => {
                    input.addEventListener('change', function() {
                        if(input.name === 'search') return; // Search input handled by 'input' event
                        fetchData(new FormData(filterForm));
                    });
                    
                    if(input.name === 'search') {
                        input.addEventListener('input', function() {
                            clearTimeout(debounceTimer);
                            debounceTimer = setTimeout(() => {
                                fetchData(new FormData(filterForm));
                            }, 500);
                        });
                    }
                });

                // Handle Reset Button
                if (resetBtn) {
                    resetBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.getAttribute('href'); // This is the base index url
                        
                        // Reset form visually
                        filterForm.reset();
                        
                        // Manually clear values for inputs that might not reset with form.reset()
                         inputs.forEach(input => {
                            if(input.type === 'text' || input.type === 'search') input.value = '';
                            if(input.tagName === 'SELECT') {
                                // For x-select, it might have a hidden input or a custom way to reset
                                // For now, assume default select behavior or rely on fetchData to rebuild URL
                                input.value = ''; // Clear selected value
                                // Trigger change event for x-select to update its display if needed
                                const event = new Event('change');
                                input.dispatchEvent(event);
                            }
                        });
                        
                        fetchData(new FormData(filterForm));
                    });
                }
            }

            // AJAX Fetch Function
            function fetchData(formData) {
                if (realBody && skeletonBody) {
                    realBody.classList.add('hidden');
                    skeletonBody.classList.remove('hidden');
                }

                const params = new URLSearchParams(formData);
                const url = `{{ route('users.index') }}?${params.toString()}`;
                
                window.history.pushState({}, '', url);

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Replace Table Body
                    const newBody = doc.querySelector('tbody:not(#skeleton-body)');
                    if (newBody && realBody) {
                        realBody.innerHTML = newBody.innerHTML;
                        
                        // Re-attach checkbox listeners!
                        if(typeof attachCheckboxListeners === 'function') {
                            attachCheckboxListeners();
                        }
                        // Uncheck selectAll
                        if(selectAll) selectAll.checked = false;
                        if(typeof updateFloatingBar === 'function') updateFloatingBar();
                    }

                    // Replace Pagination
                    const newPagination = doc.querySelector('.mt-6');
                    if (newPagination && paginationContainer) {
                        paginationContainer.innerHTML = newPagination.innerHTML;
                        attachPaginationListeners(); // Re-attach
                    } else if (newPagination && !paginationContainer) {
                          if (tableContainer) {
                             tableContainer.insertAdjacentHTML('afterend', newPagination.outerHTML);
                          }
                    } else if (!newPagination && paginationContainer) {
                        paginationContainer.innerHTML = '';
                    }

                })
                .finally(() => {
                    setTimeout(() => {
                        if (realBody && skeletonBody) {
                            skeletonBody.classList.add('hidden');
                            realBody.classList.remove('hidden');
                        }
                    }, 300);
                });
            }

            function attachPaginationListeners() {
                const links = document.querySelectorAll('.mt-6 a'); 
                links.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = new URL(this.href);
                        const page = url.searchParams.get('page');
                        if (page) {
                            const currentFormData = new FormData(filterForm);
                            currentFormData.set('page', page);
                            fetchData(currentFormData);
                        }
                    });
                });
            }
            
            // Initial Attach for Pagination
            attachPaginationListeners();
        });
    </script>
    @endpush


            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
