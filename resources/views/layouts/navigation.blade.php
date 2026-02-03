<nav x-data="{ mobileMenuOpen: false }" class="glass-nav border-b border-secondary-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    @php
                        $dashboardRoute = match(Auth::user()->role) {
                            'superadmin' => route('superadmin.dashboard'),
                            'admin' => route('admin.dashboard'),
                            'operator' => route('operator.dashboard'),
                            default => route('dashboard'),
                        };
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-primary-500/30">
                            A
                        </div>
                        <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-700 to-primary-500">
                            Azventory
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex items-center">
                    @php
                        $navClass = "inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md transition duration-150 ease-in-out gap-2";
                        $activeClass = "bg-primary-50 text-primary-700";
                        $inactiveClass = "text-secondary-600 hover:text-secondary-900 hover:bg-secondary-50";
                    @endphp

                    @if (Auth::user()->role === 'superadmin')
                        <a href="{{ route('superadmin.dashboard') }}" class="{{ $navClass }} {{ request()->routeIs('superadmin.dashboard') ? $activeClass : $inactiveClass }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            {{ __('Dashboard') }}
                        </a>
                        <a href="{{ route('superadmin.inventory.index') }}" class="{{ $navClass }} {{ request()->routeIs('superadmin.inventory.*') ? $activeClass : $inactiveClass }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            {{ __('Inventaris') }}
                        </a>
                        <a href="{{ route('superadmin.users.index') }}" class="{{ $navClass }} {{ request()->routeIs('superadmin.users.*') ? $activeClass : $inactiveClass }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            {{ __('Pengguna') }}
                        </a>
                        <a href="{{ route('superadmin.scan-qr') }}" class="{{ $navClass }} {{ request()->routeIs('superadmin.scan-qr') ? $activeClass : $inactiveClass }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            {{ __('Scan QR') }}
                        </a>
                        <a href="{{ route('superadmin.reports.index') }}" class="{{ $navClass }} {{ request()->routeIs('superadmin.reports.*') ? $activeClass : $inactiveClass }}">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            {{ __('Laporan') }}
                        </a>
                    @endif
                    
                     @if (Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="{{ $navClass }} {{ request()->routeIs('admin.dashboard') ? $activeClass : $inactiveClass }}">
                            Dashboard
                        </a>
                     @endif
                     
                     @if (Auth::user()->role === 'operator')
                        <a href="{{ route('operator.dashboard') }}" class="{{ $navClass }} {{ request()->routeIs('operator.dashboard') ? $activeClass : $inactiveClass }}">
                            Dashboard
                        </a>
                     @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <!-- Notifications Dropdown -->
                <div x-data="{ 
                    notificationOpen: false, 
                    unreadCount: {{ auth()->user()->unreadNotifications()->count() }},
                    notifications: [],
                    init() { this.fetchNotifications(); },
                    fetchNotifications() {
                        axios.get('/notifications?_=' + new Date().getTime())
                            .then(response => {
                                this.notifications = response.data;
                                // Since API only returns unread, count is length
                                this.unreadCount = this.notifications.length;
                            })
                            .catch(error => console.error(error));
                    },
                    markAsRead(id, url, type) {
                         axios.patch('/notifications/' + id + '/read')
                            .then(response => {
                                // If AJAX success, update local state immediately
                                this.unreadCount = Math.max(0, this.unreadCount - 1);
                                this.notifications = this.notifications.map(n => 
                                    n.id === id ? { ...n, read_at: new Date().toISOString() } : n
                                );
                                
                                const targetUrl = response.data.url || url;
                                if (targetUrl) {
                                    if (type === 'App\\Notifications\\ReportReadyNotification') {
                                        window.open(targetUrl, '_blank');
                                    } else {
                                        window.location.href = targetUrl;
                                    }
                                }
                            })
                            .catch(error => console.error(error));
                    },
                    markAllRead() {
                        // Optimistic UI update
                        this.unreadCount = 0;
                        this.notifications = this.notifications.map(n => ({ ...n, read_at: new Date().toISOString() }));

                        axios.patch('/notifications/read-all')
                            .then(response => {
                                // Optional: fetch to ensure sync, but UI is already updated
                                this.fetchNotifications();
                            })
                            .catch(error => {
                                console.error(error);
                                // Revert on error if needed, or just fetch
                                this.fetchNotifications(); 
                            });
                    }
                }" 
                @notification-read.window="unreadCount = Math.max(0, unreadCount - 1)"
                class="relative">
                    <button @click="notificationOpen = !notificationOpen" class="relative p-2 text-secondary-500 hover:text-primary-600 hover:bg-primary-50 rounded-full focus:outline-none transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span x-show="unreadCount > 0" x-text="unreadCount" style="display: none;" class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-danger-600 rounded-full border-2 border-white shadow-sm min-w-[1.25rem]">
                        </span>
                    </button>

                    <div x-show="notificationOpen" @click.away="notificationOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-80 rounded-xl shadow-floating bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden" style="display: none;">
                        <div class="py-2">
                            <div class="px-4 py-3 border-b border-secondary-100 flex justify-between items-center bg-white">
                                <span class="font-bold text-secondary-800 text-sm">Notifikasi</span>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('notifications.index') }}" class="text-xs text-secondary-400 hover:text-secondary-600 font-medium transition-colors">Lihat Semua</a>
                                </div>
                            </div>
                            <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                                <template x-if="notifications.length > 0">
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <div class="block px-4 py-3 hover:bg-secondary-50 border-b border-secondary-50 last:border-0 transition duration-150 group relative">
                                            <div @click="markAsRead(notification.id, notification.data.url, notification.type)" class="cursor-pointer flex gap-3">
                                                <div class="flex-shrink-0 mt-1">
                                                     <div class="w-2 h-2 rounded-full" :class="notification.read_at ? 'bg-transparent' : 'bg-primary-500'"></div>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-secondary-900 group-hover:text-primary-600 transition-colors" x-text="notification.data.title || 'Notifikasi Baru'"></p>
                                                    <p class="text-xs text-secondary-500 line-clamp-2 mt-0.5" x-text="notification.data.message"></p>
                                                    <p class="text-[10px] text-secondary-400 mt-1" x-text="new Date(notification.created_at).toLocaleString('id-ID')"></p>
                                                </div>
                                            </div>
                                            <!-- Manual Mark Read Button -->
                                            <button @click.stop="markAsRead(notification.id, null, null)" x-show="!notification.read_at" class="absolute top-3 right-3 text-secondary-300 hover:text-primary-600 bg-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity shadow-sm border border-secondary-100" title="Tandai Dibaca">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </template>
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-8 text-center text-sm text-secondary-500 flex flex-col items-center">
                                        <svg class="w-8 h-8 text-secondary-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                        Tidak ada notifikasi baru.
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Dropdown -->
            <div x-data="{ profileOpen: false }" class="relative">
                <button @click="profileOpen = !profileOpen" class="inline-flex items-center gap-3 px-1 py-1 border border-transparent text-sm leading-4 font-medium rounded-full text-secondary-500 hover:text-secondary-700 focus:outline-none transition ease-in-out duration-150 group">
                    <div class="flex flex-col items-end hidden md:flex text-right">
                        <span class="font-bold text-secondary-800 text-sm group-hover:text-primary-600 transition-colors">{{ Auth::user()->name }}</span>
                        <span class="text-xs text-secondary-500 font-normal">{{ ucfirst(Auth::user()->role) }}</span>
                    </div>
                    <div class="h-9 w-9 rounded-full overflow-hidden border-2 border-secondary-200 group-hover:border-primary-200 transition-colors shadow-sm relative">
                         @if(Auth::user()->avatar)
                            <img class="h-full w-full object-cover" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" />
                         @else
                            <div class="h-full w-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                         @endif
                    </div>
                </button>

                <div x-show="profileOpen" @click.away="profileOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 top-full z-50 mt-2 w-48 rounded-xl shadow-floating bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden" 
                     style="display: none;">
                    
                    <div class="px-4 py-3 border-b border-secondary-100 bg-secondary-50/50">
                        <p class="text-sm font-semibold text-secondary-900">Akun Saya</p>
                        <p class="text-xs text-secondary-500 truncate" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</p>
                    </div>
                    
                    <div class="py-1">
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2 group">
                             <svg class="w-4 h-4 text-secondary-400 group-hover:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            {{ __('Profil Saya') }}
                        </x-dropdown-link>
                    </div>

                    <div class="border-t border-secondary-100 my-1"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();" 
                                class="text-danger-600 hover:bg-danger-50 hover:text-danger-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            {{ __('Keluar') }}
                        </x-dropdown-link>
                    </form>
                </div>
            </div>
        </div>

        <!-- Hamburger -->
        <div class="-me-2 flex items-center sm:hidden absolute right-4 top-4">
            <button @click="mobileMenuOpen = ! mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-secondary-500 hover:text-secondary-900 hover:bg-secondary-100 focus:outline-none focus:bg-secondary-100 focus:text-secondary-900 transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': mobileMenuOpen, 'inline-flex': ! mobileMenuOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! mobileMenuOpen, 'inline-flex': mobileMenuOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div x-show="mobileMenuOpen" style="display: none;" class="sm:hidden bg-white border-t border-secondary-100">
        <div class="pt-2 pb-3 space-y-1">
             @php
                $resNavClass = "block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium transition duration-150 ease-in-out";
                $resActiveClass = "border-primary-400 text-primary-700 bg-primary-50 focus:outline-none focus:text-primary-800 focus:bg-primary-100 focus:border-primary-700";
                $resInactiveClass = "border-transparent text-secondary-600 hover:text-secondary-800 hover:bg-secondary-50 hover:border-secondary-300 focus:outline-none focus:text-secondary-800 focus:bg-secondary-50 focus:border-secondary-300";
            @endphp

            @if (Auth::user()->role === 'superadmin')
                <a href="{{ route('superadmin.dashboard') }}" class="{{ $resNavClass }} {{ request()->routeIs('superadmin.dashboard') ? $resActiveClass : $resInactiveClass }}">
                    {{ __('Dashboard') }}
                </a>
                <a href="{{ route('superadmin.inventory.index') }}" class="{{ $resNavClass }} {{ request()->routeIs('superadmin.inventory.*') ? $resActiveClass : $resInactiveClass }}">
                    {{ __('Inventaris') }}
                </a>
                <a href="{{ route('superadmin.users.index') }}" class="{{ $resNavClass }} {{ request()->routeIs('superadmin.users.*') ? $resActiveClass : $resInactiveClass }}">
                    {{ __('Pengguna') }}
                </a>
                 <a href="{{ route('superadmin.scan-qr') }}" class="{{ $resNavClass }} {{ request()->routeIs('superadmin.scan-qr') ? $resActiveClass : $resInactiveClass }}">
                    {{ __('Scan QR') }}
                </a>
                <a href="{{ route('superadmin.reports.index') }}" class="{{ $resNavClass }} {{ request()->routeIs('superadmin.reports.*') ? $resActiveClass : $resInactiveClass }}">
                    {{ __('Laporan') }}
                </a>
            @endif

            <a href="{{ route('notifications.index') }}" class="{{ $resNavClass }} {{ request()->routeIs('notifications.index') ? $resActiveClass : $resInactiveClass }} flex justify-between items-center">
                {{ __('Notifikasi') }}
                @if(auth()->user()->unreadNotifications()->count() > 0)
                    <span class="bg-danger-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ auth()->user()->unreadNotifications()->count() }}
                    </span>
                @endif
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-4 border-t border-secondary-100 bg-secondary-50/50">
            <div class="px-4 flex items-center gap-3">
                 <div class="h-10 w-10 rounded-full overflow-hidden border border-secondary-300">
                    <img class="h-full w-full object-cover" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" />
                </div>
                <div>
                    <div class="font-medium text-base text-secondary-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-secondary-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="{{ $resNavClass }} {{ $resInactiveClass }}">
                    {{ __('Profil') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="{{ $resNavClass }} {{ $resInactiveClass }} text-danger-600">
                        {{ __('Keluar') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
