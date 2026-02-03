<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
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

            <div class="space-y-3">
                @forelse($notifications as $notification)
                    <div x-data="{ 
                            read: {{ $notification->read_at ? 'true' : 'false' }},
                            markRead() {
                                if (this.read) return;
                                axios.patch('{{ route('notifications.read', $notification->id) }}')
                                    .then(() => { 
                                        this.read = true; 
                                        window.dispatchEvent(new CustomEvent('notification-read'));
                                    })
                                    .catch(err => console.error(err));
                            }
                        }"
                        }"
                        @click="markRead()"
                        class="card group relative transition-all duration-200 overflow-hidden cursor-pointer"
                        :class="read ? 'bg-white opacity-60 border border-secondary-100' : 'bg-white shadow-md border-l-4 border-l-primary-500 border-y border-r border-secondary-100'"
                    >
                        <div class="p-4 flex items-start justify-between gap-4">
                            <div class="flex gap-4 w-full">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="text-sm font-semibold text-secondary-900" :class="{ 'font-normal text-secondary-600': read }">
                                            {{ $notification->data['title'] ?? 'Notifikasi Baru' }}
                                        </h4>
                                    </div>
                                    <p class="text-sm text-secondary-600 mb-2 leading-relaxed" :class="{ 'text-secondary-400': read }">
                                        {{ $notification->data['message'] ?? 'Tidak ada pesan detail.' }}
                                    </p>
                                    <p class="text-xs text-secondary-400">
                                        {{ $notification->created_at->diffForHumans() }} &bull; {{ $notification->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Action Button (Detail) -->
                            <div class="flex-shrink-0 pointer-events-auto self-center" @click.stop>
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" {{ $notification->type === 'App\Notifications\ReportReadyNotification' ? 'target=_blank' : '' }}>
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-secondary-200 text-xs font-medium rounded-lg text-secondary-600 bg-white hover:bg-secondary-50 hover:text-primary-600 transition-colors shadow-sm">
                                        Detail
                                        <svg class="ml-1.5 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card p-12 text-center flex flex-col items-center">
                         <div class="h-12 w-12 bg-secondary-50 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                        <h3 class="text-base font-medium text-secondary-900">Belum ada notifikasi</h3>
                        <p class="text-sm text-secondary-500 mt-1">Semua aman terkendali.</p>
                    </div>
                @endforelse

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
