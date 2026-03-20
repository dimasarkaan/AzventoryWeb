<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-8 mb-4">
                <div>
                     <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('ui.notification_title') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">
                        {{ __('ui.notification_desc') }}
                    </p>
                </div>
                 @if(!$notifications->isEmpty() || request('filter') === 'unread')
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                    <!-- Tabs/Filter -->
                    <div class="flex p-1 bg-secondary-100 rounded-xl w-full sm:w-auto shadow-sm">
                        <a href="{{ route('notifications.index') }}" 
                           class="flex-1 sm:flex-none px-4 py-1.5 text-xs font-medium rounded-lg transition-all {{ !request('filter') ? 'bg-white text-primary-600 shadow-sm' : 'text-secondary-500 hover:text-secondary-700' }}">
                            {{ __('ui.all') }}
                        </a>
                        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
                           class="flex-1 sm:flex-none px-4 py-1.5 text-xs font-medium rounded-lg transition-all {{ request('filter') === 'unread' ? 'bg-white text-primary-600 shadow-sm' : 'text-secondary-500 hover:text-secondary-700' }}">
                            {{ __('ui.unread') }}
                        </a>
                    </div>
                
                    <form action="{{ route('notifications.markAllRead') }}" method="POST" id="mark-all-read-form" class="w-full sm:w-auto">
                        @csrf
                        @method('PATCH')
                        <button type="button" onclick="confirmMarkAllRead()" class="btn btn-secondary text-[11px] whitespace-nowrap w-full sm:w-auto justify-center shadow-sm hover:shadow-md transition-all">
                            <svg class="w-3.5 h-3.5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ __('ui.notification_mark_all_read') }}
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <div class="space-y-2">
                @php
                    $currentGroup = null;
                    $isFirstGroup = true;

                    // Helper function to get group name
                    $getGroupName = function($date) {
                        if ($date->isToday()) return __('ui.today');
                        if ($date->isYesterday()) return __('ui.yesterday');
                        return __('ui.older');
                    };
                    // Helper function for icons
                    $getIcon = function($notification) {
                        $type = $notification->type;
                        if (str_contains($type, 'LowStock') || str_contains($type, 'ApproachingStock')) {
                            return '<div class="p-2 bg-warning-50 text-warning-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>';
                        }
                        if (str_contains($type, 'StockRequest')) {
                            return '<div class="p-2 bg-primary-50 text-primary-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg></div>';
                        }
                        if (str_contains($type, 'OverdueBorrowing')) {
                            return '<div class="p-2 bg-danger-50 text-danger-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>';
                        }
                        if (str_contains($type, 'ReportReady')) {
                            return '<div class="p-2 bg-success-50 text-success-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>';
                        }
                        return '<div class="p-2 bg-secondary-50 text-secondary-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg></div>';
                    };
                @endphp

                @forelse($notifications as $notification)
                    @php
                        $group = $getGroupName($notification->created_at);
                    @endphp

                    @if($currentGroup !== $group)
                        @php 
                            $currentGroup = $group;
                            $headerPadding = $isFirstGroup ? 'pt-0' : 'pt-5';
                            $isFirstGroup = false;
                        @endphp
                        <div class="{{ $headerPadding }} mb-1">
                            <span class="text-xs font-bold text-secondary-400 uppercase tracking-widest">{{ $group }}</span>
                        </div>
                    @endif

                    <div x-data="{ 
                            read: {{ $notification->read_at ? 'true' : 'false' }},
                            type: '{{ str_replace('\\', '\\\\', $notification->type) }}',
                            url: '{{ $notification->data['url'] ?? '#' }}',
                            markRead() {
                                if (!this.read) {
                                    axios.patch('{{ route('notifications.read', $notification->id) }}')
                                        .then(() => { 
                                            this.read = true; 
                                            window.dispatchEvent(new CustomEvent('notification-read'));
                                        })
                                        .catch(err => console.error(err));
                                }
                                this.navigate();
                            },
                            navigate() {
                                if (this.url === '#') return;
                                if (this.type.indexOf('ReportReady') !== -1) {
                                    window.open(this.url, '_blank');
                                } else {
                                    window.location.href = this.url;
                                }
                            }
                        }"
                        @click="markRead()"
                        class="card group relative transition-all duration-200 overflow-hidden cursor-pointer"
                        :class="read ? 'bg-white opacity-70 border border-secondary-100 shadow-none' : 'bg-white shadow-sm border-l-4 border-l-primary-500 border-y border-r border-secondary-100'"
                    >
                        <div class="px-4 py-3 flex items-center justify-between gap-4">
                            <div class="flex gap-4 w-full items-start">
                                <div class="flex-shrink-0 mt-0.5">
                                    {!! $getIcon($notification) !!}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <h4 class="text-sm font-semibold text-secondary-900 truncate" :class="{ 'font-normal text-secondary-600': read }">
                                            {{ $notification->data['title'] ?? __('ui.notification_default_title') }}
                                        </h4>
                                        <span class="text-[10px] text-secondary-400 font-normal flex-shrink-0">
                                            &bull; {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-secondary-600 line-clamp-2 leading-relaxed" :class="{ 'text-secondary-400': read }">
                                        {{ $notification->data['message'] ?? __('ui.notification_default_message') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Action Link (Detail) -->
                            <div class="flex-shrink-0 self-center">
                                <a :href="url" 
                                   :target="type.indexOf('ReportReady') !== -1 ? '_blank' : '_self'"
                                   @click.stop="markRead()"
                                   class="inline-flex items-center px-2 py-1 text-xs font-medium text-primary-600 hover:text-primary-700 hover:underline transition-all">
                                    {{ __('ui.notification_action_detail') }}
                                    <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card p-16 flex flex-col items-center justify-center text-center relative overflow-hidden bg-gradient-to-b from-white to-secondary-50/50">
                        <!-- Decorative background glow -->
                        <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary-100/30 rounded-full blur-3xl"></div>
                        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-secondary-100/30 rounded-full blur-3xl"></div>
                        
                        <div class="relative z-10 flex items-center justify-center mb-6">
                            <!-- Single soft circle with icon -->
                            <div class="w-24 h-24 bg-primary-50 rounded-full flex items-center justify-center text-primary-600">
                                @if(request('filter') === 'unread')
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @else
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                @endif
                            </div>
                        </div>

                        <h3 class="relative z-10 text-xl font-semibold text-secondary-900 mb-2">
                            {{ request('filter') === 'unread' ? __('ui.no_unread_notifications') : __('ui.notification_empty_title') }}
                        </h3>
                        <p class="relative z-10 text-secondary-500 max-w-sm leading-relaxed">
                            {{ request('filter') === 'unread' ? 'Semua notifikasi penting sudah Anda baca.' : __('ui.notification_empty_desc') }}
                        </p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmMarkAllRead() {
            Swal.fire({
                title: '{{ __('ui.confirm_mark_all_read') }}',
                text: '{{ __('ui.confirm_mark_all_read_desc') }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '{{ __('ui.yes_mark_all') }}',
                cancelButtonText: '{{ __('ui.cancel') }}',
                reverseButtons: true,
                customClass: {
                    popup: '!rounded-2xl !font-sans',
                    title: '!text-secondary-900 !text-xl !font-bold',
                    htmlContainer: '!text-secondary-500 !text-sm',
                    confirmButton: 'btn btn-primary px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-primary-500 transition-all',
                    cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm transition-all'
                },
                buttonsStyling: false,
                width: '24em',
                iconColor: '#3b82f6',
                padding: '2em',
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('mark-all-read-form').submit();
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
