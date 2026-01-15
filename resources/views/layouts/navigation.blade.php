<nav x-data="{ open: false }" class="bg-white border-b border-secondary-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @if(Auth::user()->role === 'superadmin')
                        <a href="{{ route('superadmin.dashboard') }}" class="text-2xl font-extrabold text-primary-600">Azventory</a>
                    @elseif(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="text-2xl font-extrabold text-primary-600">Azventory</a>
                    @else
                        <a href="{{ route('operator.dashboard') }}" class="text-2xl font-extrabold text-primary-600">Azventory</a>
                    @endif
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if (Auth::user()->role === 'superadmin')
                        <x-nav-link :href="route('superadmin.dashboard')" :active="request()->routeIs('superadmin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('superadmin.inventory.index')" :active="request()->routeIs('superadmin.inventory.*')">
                            {{ __('Inventaris') }}
                        </x-nav-link>
                        <x-nav-link href="#" :active="false">
                            {{ __('Pengguna') }}
                        </x-nav-link>
                    @endif
                    {{-- Add other roles navigation here if needed --}}
                </div>
            </div>

            <!-- Notifications Dropdown -->
            <div x-data="{
                open: false,
                unreadCount: {{ auth()->user()->unreadNotifications()->count() }},
                notifications: [],
                init() {
                    this.fetchNotifications();
                    window.Echo.private('App.Models.User.{{ auth()->id() }}')
                        .notification((notification) => {
                            this.unreadCount++;
                            this.notifications.unshift(notification);
                            console.log(notification);
                        });
                },
                fetchNotifications() {
                    axios.get('{{ route('notifications.index') }}')
                        .then(response => {
                            this.notifications = response.data;
                        });
                },
                markAsRead(notification, event) {
                    event.preventDefault();
                    axios.patch(`/notifications/${notification.id}/read`).then(() => {
                        window.location.href = notification.data.url;
                    });
                }
            }" class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative">
                    <button @click="open = !open" class="relative p-2 text-secondary-500 hover:text-secondary-700 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute top-1 right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"></span>
                    </button>

                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-80 rounded-md shadow-lg origin-top-right" style="display: none;">
                        <div class="rounded-md ring-1 ring-black ring-opacity-5 bg-white">
                            <div class="p-4 font-semibold border-b">Notifikasi</div>
                            <div class="py-1 max-h-96 overflow-y-auto">
                                <template x-if="notifications.length > 0">
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <a :href="notification.data.url" @click="markAsRead(notification, $event)" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                            <p x-text="notification.data.message"></p>
                                            <p class="text-xs text-gray-500 mt-1" x-text="new Date(notification.created_at).toLocaleString('id-ID')"></p>
                                        </a>
                                    </template>
                                </template>
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-3 text-sm text-gray-500">Tidak ada notifikasi baru.</div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-secondary-500 bg-white hover:text-secondary-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Keluar') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-secondary-400 hover:text-secondary-500 hover:bg-secondary-100 focus:outline-none focus:bg-secondary-100 focus:text-secondary-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if (Auth::user()->role === 'superadmin')
                <x-responsive-nav-link :href="route('superadmin.dashboard')" :active="request()->routeIs('superadmin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('superadmin.inventory.index')" :active="request()->routeIs('superadmin.inventory.*')">
                    {{ __('Inventaris') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="#" :active="false">
                    {{ __('Pengguna') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-secondary-200">
            <div class="px-4">
                <div class="font-medium text-base text-secondary-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-secondary-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Keluar') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
