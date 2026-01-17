<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showFilters: false }">
            <!-- Header & Actions -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('Log Aktivitas Sistem') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">Riwayat aktivitas pengguna dan perubahan data dalam sistem.</p>
                </div>
                
                <button @click="showFilters = !showFilters" 
                        class="btn btn-secondary flex items-center gap-2"
                        :class="{ 'bg-secondary-100 ring-2 ring-secondary-200': showFilters }">
                    <svg class="w-5 h-5 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <span>Filter & Pencarian</span>
                    <svg class="w-4 h-4 text-secondary-400 transition-transform duration-200" :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
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
                 
                <form action="{{ route('superadmin.activity-logs.index') }}" method="GET" class="card p-6 border border-secondary-200 shadow-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                        <div class="space-y-1">
                            <label for="start_date" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">Dari Tanggal</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </span>
                                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                                    class="form-input pl-10 block w-full rounded-lg border-secondary-300 focus:ring-primary-500 focus:border-primary-500 text-sm">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="end_date" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">Sampai Tanggal</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </span>
                                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                                    class="form-input pl-10 block w-full rounded-lg border-secondary-300 focus:ring-primary-500 focus:border-primary-500 text-sm">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="user_id" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">Pengguna</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </span>
                                <select name="user_id" id="user_id" class="form-select pl-10 block w-full rounded-lg border-secondary-300 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                    <option value="">Semua User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-2">
                             <button type="submit" class="btn btn-primary w-full justify-center flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                Terapkan
                            </button>
                            @if(request()->hasAny(['start_date', 'end_date', 'user_id']))
                                <a href="{{ route('superadmin.activity-logs.index') }}" class="btn btn-secondary w-full justify-center">
                                    Reset
                                </a>
                            @endif
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
                                <th>Pengguna</th>
                                <th>Aksi</th>
                                <th>Deskripsi</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activityLogs as $log)
                                <tr class="group hover:bg-secondary-50 transition-colors">
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
                                                <div class="font-medium text-secondary-900">{{ $log->user->name ?? 'Sistem' }}</div>
                                                <div class="text-xs text-secondary-500 font-mono">{{ $log->user->email ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-secondary-100 text-secondary-800">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-secondary-600 max-w-md truncate" title="{{ $log->description }}">
                                        {{ $log->description }}
                                    </td>
                                    <td class="text-sm text-secondary-500 whitespace-nowrap">
                                        {{ $log->created_at->format('d M Y H:i:s') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-secondary-500">
                                        Tidak ada aktivitas yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-4">
                @forelse ($activityLogs as $log)
                    <div class="card p-4 flex flex-col gap-3">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 font-bold overflow-hidden">
                                     @if($log->user && $log->user->avatar)
                                        <img src="{{ asset('storage/' . $log->user->avatar) }}" alt="" class="h-full w-full object-cover">
                                    @else
                                        {{ substr($log->user->name ?? 'S', 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-secondary-900">{{ $log->user->name ?? 'Sistem' }}</div>
                                    <div class="text-xs text-secondary-500">{{ $log->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <span class="badge badge-secondary text-[10px]">{{ $log->action }}</span>
                        </div>
                        
                        <div class="text-sm text-secondary-600 bg-secondary-50 p-3 rounded-lg border border-secondary-100">
                            {{ $log->description }}
                        </div>

                        <div class="text-xs text-secondary-400 text-right">
                            {{ $log->created_at->format('d M Y • H:i:s') }}
                        </div>
                    </div>
                @empty
                    <div class="card p-8 text-center text-secondary-500">
                        <p class="text-sm">Tidak ada aktivitas yang tercatat.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $activityLogs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
