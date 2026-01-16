<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('Log Aktivitas Sistem') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">Riwayat aktivitas pengguna dan perubahan data dalam sistem.</p>
                </div>
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
