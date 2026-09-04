<nav x-data="{ mobileMenuOpen: false }" @keydown.escape.window="mobileMenuOpen = false" class="glass-nav border-b border-secondary-200">
    <!-- Menu Navigasi Utama -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    @php
                        $dashboardRoute = match(Auth::user()->role) {
                            \App\Enums\UserRole::SUPERADMIN => route('dashboard.superadmin'),
                            \App\Enums\UserRole::ADMIN => route('dashboard.admin'),
                            \App\Enums\UserRole::OPERATOR => route('dashboard.operator'),
                            default => route('dashboard'),
                        };
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="flex items-center">
                        <x-application-logo class="h-5 lg:h-6 w-auto" />
                    </a>
                </div>

                <!-- Link Navigasi -->
                <div class="hidden space-x-1 lg:-my-px lg:ms-10 lg:flex items-center">
                    @php
                        $navClass = "inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md transition duration-150 ease-in-out gap-2";
                        $activeClass = "bg-primary-50 text-primary-700";
                        $inactiveClass = "text-secondary-600 hover:text-secondary-900 hover:bg-secondary-50";
                    @endphp

                    @can('viewAny', \App\Models\User::class)
                        <a href="{{ route('dashboard.superadmin') }}" class="{{ $navClass }} {{ request()->routeIs('dashboard.superadmin') ? $activeClass : $inactiveClass }}">
                            <x-icon.dashboard class="w-4 h-4" />
                            {{ __('ui.dashboard') }}
                        </a>
                    @endcan

                     @if (Auth::user()->role === \App\Enums\UserRole::ADMIN)
                        <a href="{{ route('dashboard.admin') }}" class="{{ $navClass }} {{ request()->routeIs('dashboard.admin') ? $activeClass : $inactiveClass }}">
                            <x-icon.dashboard class="w-4 h-4" />
                            {{ __('ui.dashboard') }}
                        </a>
                     @endif
                     
                     @if (Auth::user()->role === \App\Enums\UserRole::OPERATOR)
                        <a href="{{ route('dashboard.operator') }}" class="{{ $navClass }} {{ request()->routeIs('dashboard.operator') ? $activeClass : $inactiveClass }}">
                            <x-icon.dashboard class="w-4 h-4" />
                            {{ __('ui.dashboard') }}
                        </a>
                     @endif

                    {{-- Menu Bersama --}}
                    <a href="{{ route('inventory.index') }}" class="{{ $navClass }} {{ (request()->routeIs('inventory.*') && !request()->routeIs('inventory.scan-qr') && !request()->routeIs('inventory.stock-approvals.*')) ? $activeClass : $inactiveClass }}">
                        <x-icon.inventory class="w-4 h-4" />
                        {{ __('ui.inventory_list') }}
                    </a>

                    @can('viewAny', \App\Models\User::class)
                        <a href="{{ route('users.index') }}" class="{{ $navClass }} {{ request()->routeIs('users.*') ? $activeClass : $inactiveClass }}">
                            <x-icon.users class="w-4 h-4" />
                            {{ __('ui.user_management') }}
                        </a>
                    @endcan

                    <a href="{{ route('inventory.scan-qr') }}" class="{{ $navClass }} {{ request()->routeIs('inventory.scan-qr') ? $activeClass : $inactiveClass }}">
                        <x-icon.scan-qr class="w-4 h-4" />
                        {{ __('ui.scan_qr') }}
                    </a>

                    @if(Auth::user()->role === \App\Enums\UserRole::SUPERADMIN)
                        <a href="{{ route('reports.index') }}" class="{{ $navClass }} {{ request()->routeIs('reports.index') ? $activeClass : $inactiveClass }}">
                             <x-icon.reports class="w-4 h-4" />
                            {{ __('ui.reports') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex items-center ms-auto gap-2 sm:gap-4 lg:ms-6">

                <!-- Notifications Dropdown -->
                <div x-data="notificationComponent()" @keydown.escape.window="notificationOpen = false" class="relative">
                    <button @click="notificationOpen = !notificationOpen" :aria-expanded="notificationOpen.toString()" aria-label="Toggle notifications" class="relative p-2 text-secondary-500 hover:text-primary-600 hover:bg-primary-50 rounded-full focus:outline-none transition-all duration-200">
                        <svg aria-hidden="true" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span x-show="unreadCount > 0" x-text="unreadCount" x-cloak class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-danger-600 rounded-full border-2 border-white shadow-sm min-w-[1.25rem]">
                        </span>
                    </button>

                    <div x-show="notificationOpen" @click.away="notificationOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-72 sm:w-80 max-w-[calc(100vw-2rem)] rounded-xl shadow-floating bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden" x-cloak>
                        <div class="py-2">
                            <div class="px-4 py-3 border-b border-secondary-100 flex justify-between items-center bg-white">
                                <span class="font-bold text-secondary-800 text-sm">{{ __('ui.notifications') }}</span>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('notifications.index') }}" class="text-xs text-secondary-400 hover:text-secondary-600 font-medium transition-colors">{{ __('ui.view_all') }}</a>
                                </div>
                            </div>
                            <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                                <template x-if="isLoading">
                                    <div class="p-4 space-y-4">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-full bg-secondary-200 animate-pulse flex-shrink-0"></div>
                                            <div class="flex-1 space-y-2">
                                                <div class="h-3.5 bg-secondary-200 rounded w-3/4 animate-pulse"></div>
                                                <div class="h-2.5 bg-secondary-200 rounded w-full animate-pulse"></div>
                                                <div class="h-2 bg-secondary-200 rounded w-1/4 animate-pulse mt-2"></div>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-full bg-secondary-200 animate-pulse flex-shrink-0"></div>
                                            <div class="flex-1 space-y-2">
                                                <div class="h-3.5 bg-secondary-200 rounded w-1/2 animate-pulse"></div>
                                                <div class="h-2.5 bg-secondary-200 rounded w-5/6 animate-pulse"></div>
                                                <div class="h-2 bg-secondary-200 rounded w-1/3 animate-pulse mt-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!isLoading && notifications.length > 0">
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <div @click="markAsRead(notification.id, notification.data.url, notification.type)" 
                                             class="block cursor-pointer border-b border-secondary-50 last:border-0 transition duration-150 group relative"
                                             :class="notification.read_at ? 'hover:bg-secondary-50' : 'bg-primary-50/20 hover:bg-primary-50/40 border-l-4 border-l-primary-500'">
                                            <div class="px-4 py-3 flex gap-3">
                                                <div class="flex-shrink-0 mt-1">
                                                     <div x-html="getIcon(notification.type)" class="w-8 h-8 flex items-center justify-center"></div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-secondary-900 group-hover:text-primary-600 transition-colors truncate" 
                                                       :class="notification.read_at ? 'font-normal text-secondary-600' : 'font-semibold'"
                                                       x-text="notification.data.title || '{{ __('ui.new_notification') }}'"></p>
                                                    <p class="text-xs text-secondary-500 line-clamp-2 mt-0.5" x-text="notification.data.message"></p>
                                                    <p class="text-[10px] text-secondary-400 mt-1" x-text="timeAgo(notification.created_at)"></p>
                                                </div>
                                            </div>
                                            <!-- Tombol Tandai Dibaca Manual -->
                                            <button @click.stop="markAsRead(notification.id, null, null)" x-show="!notification.read_at" class="absolute top-3 right-3 text-secondary-300 hover:text-primary-600 bg-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity shadow-sm border border-secondary-100" title="{{ __('ui.mark_as_read') }}">
                                                <svg aria-hidden="true" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </template>
                                <template x-if="!isLoading && notifications.length === 0">
                                    <div class="px-4 py-8 text-center text-sm text-secondary-500 flex flex-col items-center">
                                        <svg aria-hidden="true" class="w-8 h-8 text-secondary-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                        {{ __('ui.no_new_notifications') }}
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dropdown Pengaturan -->
            <div x-data="{ profileOpen: false }" @keydown.escape.window="profileOpen = false" class="relative hidden lg:block">
                <button @click="profileOpen = !profileOpen" :aria-expanded="profileOpen.toString()" aria-label="Toggle profile menu" class="inline-flex items-center gap-3 px-1 py-1 border border-transparent text-sm leading-4 font-medium rounded-full text-secondary-500 hover:text-secondary-700 focus:outline-none transition ease-in-out duration-150 group">
                    <!-- Info Profil: 2 Baris (Nama di Atas, Peran di Bawah) -->
                    <div class="hidden md:flex flex-col items-end text-right mr-3">
                        <span class="font-bold text-secondary-800 text-sm group-hover:text-primary-600 transition-colors whitespace-nowrap">{{ Auth::user()->username }}</span>
                        <span class="text-xs text-secondary-500 font-normal">{{ Auth::user()->role->label() }}</span>
                    </div>
                    <div class="h-9 w-9 rounded-full overflow-hidden border-2 border-secondary-200 group-hover:border-primary-200 transition-colors shadow-sm relative">
                         @if(Auth::user()->avatar)
                            <img class="h-full w-full object-cover" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" />
                         @else
                            <div class="h-full w-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold">
                                {{ substr(Auth::user()->username, 0, 1) }}
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
                     x-cloak>
                    
                    <div class="px-4 py-3 border-b border-secondary-100 bg-secondary-50/50">
                        <p class="text-sm font-semibold text-secondary-900">{{ Auth::user()->username }}</p>
                        <p class="text-xs text-secondary-500 truncate" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</p>
                    </div>
                    
                    <div class="py-1">
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2 group">
                             <svg aria-hidden="true" class="w-4 h-4 text-secondary-400 group-hover:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            {{ __('ui.my_profile') }}
                        </x-dropdown-link>
                    </div>

                    <div class="border-t border-secondary-100 my-1"></div>

                    <form method="POST" action="{{ route('logout') }}" x-data="{ isLoggingOut: false }" @submit="isLoggingOut = true; clearDashboardPeriod()" novalidate>
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-danger-600 hover:bg-danger-50 transition-colors flex items-center gap-2 group" :disabled="isLoggingOut">
                            <span x-show="!isLoggingOut" class="flex items-center gap-2">
                                <svg aria-hidden="true" class="w-4 h-4 group-hover:text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                {{ __('ui.log_out') }}
                            </span>
                            <span x-show="isLoggingOut" class="flex items-center gap-2 text-danger-400">
                                <svg aria-hidden="true" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Keluar...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Hamburger (lg:hidden, statis di dalam flex) -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="mobileMenuOpen = ! mobileMenuOpen" :aria-expanded="mobileMenuOpen.toString()" aria-label="Toggle mobile menu" class="inline-flex items-center justify-center p-2 rounded-md text-secondary-500 hover:text-secondary-900 hover:bg-secondary-100 focus:outline-none focus:bg-secondary-100 focus:text-secondary-900 transition duration-150 ease-in-out">
                    <svg aria-hidden="true" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileMenuOpen, 'inline-flex': ! mobileMenuOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! mobileMenuOpen, 'inline-flex': mobileMenuOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu Navigasi Responsif (Side Drawer dengan Swipe-to-Close) -->
    <div class="lg:hidden" 
         x-data="{ touchStartX: 0, touchEndX: 0, threshold: 75 }"
         x-on:touchstart="touchStartX = $event.touches[0].clientX"
         x-on:touchend="touchEndX = $event.changedTouches[0].clientX; if(touchStartX - touchEndX > threshold && mobileMenuOpen) mobileMenuOpen = false"
         x-cloak>
         
        <!-- Backdrop -->
        <div x-show="mobileMenuOpen" 
             x-transition.opacity.duration.300ms
             @click="mobileMenuOpen = false"
             class="fixed inset-0 bg-secondary-900/50 z-40 backdrop-blur-sm"></div>

        <!-- Side Drawer -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 w-[85%] max-w-sm bg-white shadow-2xl z-50 flex flex-col overflow-hidden">
             
            <!-- Drawer Header -->
            <div class="p-4 border-b border-secondary-100 flex items-center justify-between bg-secondary-50/50">
                <div class="flex items-center gap-3">
                     <div class="h-10 w-10 rounded-full overflow-hidden border-2 border-primary-200 flex-shrink-0 relative">
                        @if(Auth::user()->avatar)
                            <img class="h-full w-full object-cover" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" />
                        @else
                            <div class="h-full w-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-lg">
                                {{ mb_substr(Auth::user()->username, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="font-bold text-secondary-900 text-sm">{{ Auth::user()->username }}</p>
                        <p class="text-xs text-secondary-500">{{ Auth::user()->role->label() }}</p>
                    </div>
                </div>
                <button @click="mobileMenuOpen = false" class="p-2 text-secondary-400 hover:text-secondary-600 rounded-full hover:bg-secondary-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Drawer Links -->
            <div class="flex-1 overflow-y-auto py-2">
                @php
                    $resNavClass = "block w-full px-5 py-3 border-l-4 text-start text-base font-medium transition duration-150 ease-in-out";
                    $resActiveClass = "border-primary-500 text-primary-700 bg-primary-50 focus:outline-none";
                    $resInactiveClass = "border-transparent text-secondary-600 hover:text-secondary-800 hover:bg-secondary-50 hover:border-secondary-300 focus:outline-none";
                @endphp

                @if (Auth::user()->role === \App\Enums\UserRole::SUPERADMIN)
                    <a href="{{ route('dashboard.superadmin') }}" class="{{ $resNavClass }} {{ request()->routeIs('dashboard.superadmin') ? $resActiveClass : $resInactiveClass }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            {{ __('ui.dashboard') }}
                        </div>
                    </a>
                @endif

                @if (Auth::user()->role === \App\Enums\UserRole::ADMIN)
                    <a href="{{ route('dashboard.admin') }}" class="{{ $resNavClass }} {{ request()->routeIs('dashboard.admin') ? $resActiveClass : $resInactiveClass }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            {{ __('ui.dashboard') }}
                        </div>
                    </a>
                @endif

                @if (Auth::user()->role === \App\Enums\UserRole::OPERATOR)
                    <a href="{{ route('dashboard.operator') }}" class="{{ $resNavClass }} {{ request()->routeIs('dashboard.operator') ? $resActiveClass : $resInactiveClass }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            {{ __('ui.dashboard') }}
                        </div>
                    </a>
                @endif
                
                {{-- Menu Bersama --}}
                <a href="{{ route('inventory.index') }}" class="{{ $resNavClass }} {{ (request()->routeIs('inventory.*') && !request()->routeIs('inventory.scan-qr') && !request()->routeIs('inventory.stock-approvals.*')) ? $resActiveClass : $resInactiveClass }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        {{ __('ui.inventory_list') }}
                    </div>
                </a>

                @can('viewAny', \App\Models\User::class)
                    <a href="{{ route('users.index') }}" class="{{ $resNavClass }} {{ request()->routeIs('users.*') ? $resActiveClass : $resInactiveClass }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            {{ __('ui.user_management') }}
                        </div>
                    </a>
                @endcan
                
                <a href="{{ route('inventory.scan-qr') }}" class="{{ $resNavClass }} {{ request()->routeIs('inventory.scan-qr') ? $resActiveClass : $resInactiveClass }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        {{ __('ui.scan_qr') }}
                    </div>
                </a>

                @if(Auth::user()->role === \App\Enums\UserRole::SUPERADMIN)
                    <a href="{{ route('reports.index') }}" class="{{ $resNavClass }} {{ request()->routeIs('reports.index') ? $resActiveClass : $resInactiveClass }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            {{ __('ui.reports') }}
                        </div>
                    </a>
                @endif

                <a href="{{ route('notifications.index') }}" class="{{ $resNavClass }} {{ request()->routeIs('notifications.index') ? $resActiveClass : $resInactiveClass }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            {{ __('ui.notifications') }}
                        </div>
                        @if(auth()->user()->unreadNotifications()->count() > 0)
                            <span class="bg-danger-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                                {{ auth()->user()->unreadNotifications()->count() }}
                            </span>
                        @endif
                    </div>
                </a>
                
                <div class="border-t border-secondary-100 my-2"></div>
                
                <a href="{{ route('profile.edit') }}" class="{{ $resNavClass }} {{ $resInactiveClass }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        {{ __('ui.profile') }}
                    </div>
                </a>
            </div>

            <!-- Drawer Footer -->
            <div class="p-4 border-t border-secondary-100 bg-secondary-50/30">
                <form method="POST" action="{{ route('logout') }}" x-data="{ isLoggingOut: false }" @submit="isLoggingOut = true; clearDashboardPeriod()" novalidate>
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 text-sm font-medium text-danger-600 bg-danger-50 hover:bg-danger-100 rounded-xl transition-colors flex justify-center items-center gap-2" :disabled="isLoggingOut">
                        <span x-show="!isLoggingOut" class="flex items-center gap-2">
                            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            {{ __('ui.logout') }}
                        </span>
                        <span x-show="isLoggingOut" class="flex items-center gap-2">
                            <svg aria-hidden="true" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Keluar...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

    </div>
</nav>

@include('layouts.partials.navigation-scripts')
