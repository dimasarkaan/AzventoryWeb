<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('Persetujuan Perubahan Stok') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">Tinjau dan setujui permintaan perubahan stok manual.</p>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>Sparepart</th>
                                <th>Pengaju</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Alasan</th>
                                <th>Tanggal</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingApprovals as $approval)
                                <tr class="group hover:bg-secondary-50 transition-colors">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                                @if($approval->sparepart->image)
                                                    <img src="{{ asset('storage/' . $approval->sparepart->image) }}" alt="" class="h-full w-full object-cover rounded-lg">
                                                @else
                                                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-medium text-secondary-900">{{ $approval->sparepart->name }}</div>
                                                <div class="text-xs text-secondary-500 font-mono">{{ $approval->sparepart->part_number ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-medium text-secondary-900">{{ $approval->user->name }}</div>
                                    </td>
                                    <td>
                                        @if($approval->type === 'masuk')
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-success-100 text-success-700">Masuk</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-warning-100 text-warning-700">Keluar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-bold text-secondary-900">{{ $approval->quantity }} <span class="text-xs font-normal text-secondary-500">{{ $approval->sparepart->unit ?? 'Pcs' }}</span></div>
                                    </td>
                                    <td class="text-sm text-secondary-600 max-w-xs truncate" title="{{ $approval->reason }}">
                                        {{ $approval->reason }}
                                    </td>
                                    <td class="text-sm text-secondary-500 whitespace-nowrap">
                                        {{ $approval->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('superadmin.stock-approvals.update', $approval) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-success flex items-center gap-1 text-xs py-1.5 px-3">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Setujui
                                                </button>
                                            </form>
                                            <form action="{{ route('superadmin.stock-approvals.update', $approval) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-danger flex items-center gap-1 text-xs py-1.5 px-3" onclick="return confirm('Tolak pengajuan ini?')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-secondary-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-success-100 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p>Tidak ada pengajuan pending saat ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-4">
                @forelse ($pendingApprovals as $approval)
                    <div class="card p-4 flex flex-col gap-3">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                   @if($approval->sparepart->image)
                                        <img src="{{ asset('storage/' . $approval->sparepart->image) }}" alt="" class="h-full w-full object-cover rounded-lg">
                                    @else
                                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-secondary-900">{{ $approval->sparepart->name }}</div>
                                    <div class="text-xs text-secondary-500">oleh {{ $approval->user->name }}</div>
                                </div>
                            </div>
                           
                             @if($approval->type === 'masuk')
                                <span class="badge badge-success text-[10px]">Masuk</span>
                            @else
                                <span class="badge badge-warning text-[10px]">Keluar</span>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm border-t border-b border-secondary-50 py-3">
                            <div>
                                <span class="text-xs text-secondary-400 block">Jumlah</span>
                                <span class="font-bold text-secondary-900">{{ $approval->quantity }} <span class="text-xs font-normal text-secondary-500">{{ $approval->sparepart->unit ?? 'Pcs' }}</span></span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-secondary-400 block">Tanggal</span>
                                <span class="text-secondary-700">{{ $approval->created_at->format('d/m/y H:i') }}</span>
                            </div>
                            <div class="col-span-2 mt-1">
                                <span class="text-xs text-secondary-400 block">Alasan</span>
                                <span class="text-secondary-700 bg-secondary-50 p-2 rounded block w-full text-xs border border-secondary-100">{{ $approval->reason }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <form action="{{ route('superadmin.stock-approvals.update', $approval) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-outline-danger w-full text-xs justify-center" onclick="return confirm('Tolak pengajuan ini?')">
                                    Tolak
                                </button>
                            </form>
                            <form action="{{ route('superadmin.stock-approvals.update', $approval) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-success w-full text-xs justify-center">
                                    Setujui
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="card p-8 text-center text-secondary-500">
                        <p class="text-sm">Tidak ada pengajuan pending.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $pendingApprovals->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
