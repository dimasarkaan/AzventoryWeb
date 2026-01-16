<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                     <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('Notifikasi') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">
                        Pantau semua aktivitas dan peringatan sistem.
                    </p>
                </div>
                 @if(!$notifications->isEmpty())
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-secondary text-xs">
                        <svg class="w-4 h-4 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Tandai Semua Dibaca
                    </button>
                </form>
                @endif
            </div>

            <div class="space-y-4">
                @forelse($notifications as $notification)
                    <div class="card p-5 transition-all duration-200 hover:shadow-md border-l-4 {{ $notification->read_at ? 'border-l-transparent bg-white' : 'border-l-primary-500 bg-primary-50/10' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 mt-1">
                                    @if($notification->type === 'App\Notifications\LowStockNotification')
                                        <div class="h-10 w-10 rounded-full bg-warning-100 flex items-center justify-center text-warning-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-base font-semibold text-secondary-900">
                                            {{ $notification->data['title'] ?? 'Notifikasi Baru' }}
                                        </h4>
                                        @if(!$notification->read_at)
                                            <span class="badge badge-primary">Baru</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-secondary-600 mt-1 leading-relaxed">
                                        {{ $notification->data['message'] ?? 'Tidak ada pesan detail.' }}
                                    </p>
                                    <p class="text-xs text-secondary-400 mt-2 font-medium">
                                        {{ $notification->created_at->diffForHumans() }} &bull; {{ $notification->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex-shrink-0">
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-ghost text-xs group" title="Lihat Detail">
                                        <span class="group-hover:text-primary-600">Detail &rarr;</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card p-12 text-center flex flex-col items-center">
                        <div class="h-16 w-16 bg-secondary-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <h3 class="text-lg font-medium text-secondary-900">Belum ada notifikasi</h3>
                        <p class="text-secondary-500 mt-1 max-w-sm">
                            Saat ini belum ada aktivitas atau peringatan baru untuk Anda.
                        </p>
                    </div>
                @endforelse

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
