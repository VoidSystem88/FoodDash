<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FoodDash') }}</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ff6b35">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="FoodDash">
    <link rel="apple-touch-icon" href="/icon-192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icon-192.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen pb-20 md:pb-0">

    {{-- ============================================ --}}
    {{-- TOP NAVIGATION (ALL SCREENS) --}}
    {{-- ============================================ --}}
    @auth
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center h-14">

                {{-- LEFT: LOGO + DESKTOP LINKS --}}
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="font-semibold text-lg text-gray-900">
                        FoodDash
                    </a>

                    {{-- CUSTOMER DESKTOP --}}
                    @if (auth()->user()->isCustomer())
                        <div class="hidden md:flex items-center gap-1">
                            <a href="{{ route('customer.restaurants') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('customer.restaurants*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Restaurants
                            </a>
                            <a href="{{ route('customer.favorites') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('customer.favorites*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Favorites
                            </a>
                            <a href="{{ route('customer.orders') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('customer.orders*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Orders
                            </a>
                        </div>
                    @endif

                    {{-- RIDER DESKTOP --}}
                    @if (auth()->user()->isRider())
                        <div class="hidden md:flex items-center gap-1">
                            <a href="{{ route('rider.dashboard') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('rider.dashboard') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('rider.history') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('rider.history') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                History
                            </a>
                            <a href="{{ route('rider.earnings') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('rider.earnings*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Earnings
                            </a>
                        </div>
                    @endif

                    {{-- RESTAURANT DESKTOP --}}
                    @if (auth()->user()->isRestaurant())
                        <div class="hidden md:flex items-center gap-1">
                            <a href="{{ route('restaurant.dashboard') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('restaurant.dashboard') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('restaurant.orders') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('restaurant.orders') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Orders
                            </a>
                            <a href="{{ route('menu-items.index') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('menu-items*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Menu
                            </a>
                            <a href="{{ route('restaurant.hours') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('restaurant.hours*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Hours
                            </a>
                            <a href="{{ route('restaurant.analytics') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('restaurant.analytics') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Analytics
                            </a>
                        </div>
                    @endif

                    {{-- ADMIN DESKTOP --}}
                    @if (auth()->user()->isAdmin())
                        <div class="hidden md:flex items-center gap-1">
                            <a href="{{ route('admin.dashboard') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.accounts') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('admin.accounts') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Accounts
                            </a>
                            <a href="{{ route('admin.orders') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('admin.orders') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Orders
                            </a>
                            <a href="{{ route('admin.reports') }}"
                               class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('admin.reports') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                Reports
                            </a>
                        </div>
                    @endif
                </div>

                {{-- RIGHT: BELL + AVATAR --}}
                <div class="flex items-center gap-2">
                    {{-- NOTIFICATION BELL --}}
                    <div x-data="notificationBell()" x-init="init()" class="relative" x-cloak>
                        <button @click="toggle()"
                                class="relative p-2 text-gray-600 hover:text-orange-600 transition rounded-full hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>

                            <template x-if="unreadCount > 0">
                                <span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1"
                                      x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                            </template>
                        </button>

                        <div x-show="isOpen"
                             x-cloak
                             @click.outside="isOpen = false"
                             x-transition.opacity
                             class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-xl z-50 overflow-hidden">

                            <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                <span class="text-sm font-semibold text-gray-900">Notifications</span>
                                <template x-if="unreadCount > 0">
                                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-orange-600 hover:underline">
                                            Mark all as read
                                        </button>
                                    </form>
                                </template>
                            </div>

                            <div class="max-h-96 overflow-y-auto">
                                <template x-if="notifications.length === 0">
                                    <div class="py-12 text-center">
                                        <p class="text-4xl mb-2">🔕</p>
                                        <p class="text-sm text-gray-500">No notifications yet</p>
                                    </div>
                                </template>

                                <template x-for="n in notifications" :key="n.id">
                                    <a :href="'/notifications/' + n.id + '/read'"
                                       :class="n.read ? 'bg-white' : 'bg-orange-50'"
                                       class="flex gap-3 px-4 py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-lg"
                                             x-text="n.icon || '🔔'"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 line-clamp-1" x-text="n.title"></p>
                                            <p class="text-xs text-gray-600 line-clamp-2 mt-0.5" x-text="n.body"></p>
                                            <p class="text-xs text-gray-400 mt-1" x-text="n.created_at"></p>
                                        </div>
                                        <template x-if="!n.read">
                                            <span class="flex-shrink-0 w-2 h-2 rounded-full bg-orange-500 mt-2"></span>
                                        </template>
                                    </a>
                                </template>
                            </div>

                            <div class="px-4 py-2 border-t border-gray-100 bg-gray-50">
                                <a href="{{ route('notifications.index') }}"
                                   class="block text-center text-xs text-orange-600 hover:underline py-1">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- AVATAR --}}
                    <a href="{{ route('restaurant.profile') }}"
  <a href="{{ route('restaurant.profile') }}"
   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('restaurant.profile*') ? 'text-orange-600' : 'text-gray-500' }}">
    {{-- AVATAR ICON --}}
    <div class="relative">
        @if (auth()->user()->avatar_url)
            <img src="{{ auth()->user()->avatar_url }}"
                 alt="{{ auth()->user()->name }}"
                 class="w-6 h-6 rounded-full object-cover {{ request()->routeIs('restaurant.profile*') ? 'ring-2 ring-orange-600' : 'ring-2 ring-transparent' }} transition">
        @else
            <div class="w-6 h-6 rounded-full {{ auth()->user()->avatar_color }} flex items-center justify-center text-white text-[10px] font-bold {{ request()->routeIs('restaurant.profile*') ? 'ring-2 ring-orange-600' : 'ring-2 ring-transparent' }} transition">
                {{ auth()->user()->initials }}
            </div>
        @endif

        {{-- Online dot --}}
        @if (auth()->user()->isOnline())
            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
        @endif
    </div>

    <span class="text-[10px] mt-0.5 font-medium">Profile</span>
</a>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    {{-- PWA INSTALL PROMPT --}}
    <div x-data="pwaInstall()" x-init="init()" x-cloak>
        <template x-if="showInstall">
            <div class="fixed bottom-24 left-4 right-4 md:bottom-4 md:left-auto md:right-4 md:w-80 bg-white border border-gray-200 rounded-lg shadow-lg p-4 z-30">
                <div class="flex items-start gap-3">
                    <img src="/icon-192.png" alt="FoodDash" class="w-10 h-10 rounded">
                    <div class="flex-1">
                        <p class="font-semibold text-sm">Install FoodDash</p>
                        <p class="text-xs text-gray-500 mt-1">Add to your home screen for quick access.</p>
                    </div>
                    <button @click="dismiss()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-3 flex gap-2">
                    <button @click="install()" class="flex-1 bg-orange-600 text-white text-sm py-2 rounded hover:bg-orange-700">
                        Install
                    </button>
                    <button @click="dismiss()" class="flex-1 border text-sm py-2 rounded hover:bg-gray-50">
                        Not now
                    </button>
                </div>
            </div>
        </template>
    </div>

    <main class="max-w-6xl mx-auto px-4 py-6 md:py-8">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ============================================ --}}
    {{-- BOTTOM NAVIGATION (MOBILE ONLY) --}}
    {{-- ============================================ --}}
    @auth
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-40">
        <div class="flex justify-around items-center h-16">

            {{-- CUSTOMER MOBILE --}}
            @if (auth()->user()->isCustomer())
                <a href="{{ route('customer.restaurants') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('customer.restaurants*') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Home</span>
                </a>

                <a href="{{ route('customer.favorites') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('customer.favorites*') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Favorites</span>
                </a>

                <a href="{{ route('customer.orders') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('customer.orders*') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Orders</span>
                </a>

                <a href="{{ route('notifications.index') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition relative {{ request()->routeIs('notifications*') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Alerts</span>

                    @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                    @if ($unread > 0)
                        <span class="absolute top-1 right-1/4 bg-red-500 text-white text-[9px] font-bold rounded-full min-w-[16px] h-[16px] flex items-center justify-center px-1">
                            {{ $unread > 99 ? '99+' : $unread }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('profile.index') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('profile*') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Profile</span>
                </a>
            @endif

            {{-- RIDER MOBILE --}}
            @if (auth()->user()->isRider())
                <a href="{{ route('rider.dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('rider.dashboard') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Home</span>
                </a>

                <a href="{{ route('rider.history') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('rider.history') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">History</span>
                </a>

                <a href="{{ route('rider.earnings') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('rider.earnings*') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Earnings</span>
                </a>

                <a href="{{ route('notifications.index') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition relative {{ request()->routeIs('notifications*') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Alerts</span>

                    @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                    @if ($unread > 0)
                        <span class="absolute top-1 right-1/4 bg-red-500 text-white text-[9px] font-bold rounded-full min-w-[16px] h-[16px] flex items-center justify-center px-1">
                            {{ $unread > 99 ? '99+' : $unread }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('profile.index') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('profile*') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Profile</span>
                </a>
            @endif

            {{-- RESTAURANT MOBILE --}}
            @if (auth()->user()->isRestaurant())
                <a href="{{ route('restaurant.dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('restaurant.dashboard') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Home</span>
                </a>

                <a href="{{ route('restaurant.orders') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('restaurant.orders') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Orders</span>
                </a>

                <a href="{{ route('menu-items.index') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('menu-items*') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Menu</span>
                </a>

                <a href="{{ route('restaurant.hours') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('restaurant.hours*') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Hours</span>
                </a>

                <a href="{{ route('restaurant.analytics') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('restaurant.analytics') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Analytics</span>
                </a>

                <a href="{{ route('profile.index') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('profile*') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Profile</span>
                </a>
            @endif

            {{-- ADMIN MOBILE --}}
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('admin.dashboard') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Home</span>
                </a>

                <a href="{{ route('admin.accounts') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('admin.accounts') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Accounts</span>
                </a>

                <a href="{{ route('admin.orders') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('admin.orders') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Orders</span>
                </a>

                <a href="{{ route('admin.reports') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('admin.reports') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Reports</span>
                </a>

                <a href="{{ route('profile.index') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('profile*') ? 'text-orange-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Profile</span>
                </a>
            @endif

        </div>
    </nav>
    @endauth

    @stack('scripts')

    {{-- SERVICE WORKER --}}
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }
    </script>

    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
    function pwaInstall() {
        return {
            showInstall: false,
            deferredPrompt: null,
            dismissed: false,
            init() {
                if (window.matchMedia('(display-mode: standalone)').matches) return;
                if (localStorage.getItem('pwa-dismissed') === 'yes') return;
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    this.deferredPrompt = e;
                    this.showInstall = true;
                });
                window.addEventListener('appinstalled', () => {
                    this.showInstall = false;
                    this.deferredPrompt = null;
                });
            },
            async install() {
                if (!this.deferredPrompt) return;
                this.deferredPrompt.prompt();
                const { outcome } = await this.deferredPrompt.userChoice;
                if (outcome === 'accepted') this.showInstall = false;
                this.deferredPrompt = null;
            },
            dismiss() {
                this.showInstall = false;
                localStorage.setItem('pwa-dismissed', 'yes');
            }
        }
    }

    function notificationBell() {
        return {
            isOpen: false,
            notifications: [],
            unreadCount: 0,
            currentUserId: {{ auth()->id() ?? 'null' }},
            init() {
                this.loadNotifications();
                if (this.currentUserId && typeof window.Echo !== 'undefined') {
                    window.Echo.private(`user.${this.currentUserId}`)
                        .listen('.notification.new', (e) => {
                            this.notifications.unshift({
                                id: e.id,
                                title: e.title,
                                body: e.body,
                                url: e.url,
                                icon: e.icon,
                                read: false,
                                created_at: 'just now',
                            });
                            this.unreadCount++;
                            this.playBeep();
                            if (this.notifications.length > 10) this.notifications.pop();
                        });
                }
                setInterval(() => this.loadNotifications(), 60000);
            },
            async loadNotifications() {
                try {
                    const res = await fetch('{{ route('notifications.recent') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });
                    const data = await res.json();
                    this.notifications = data.notifications || [];
                    this.unreadCount = data.unread_count || 0;
                } catch (err) { /* silent */ }
            },
            toggle() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) this.loadNotifications();
            },
            playBeep() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.frequency.value = 600;
                    gain.gain.value = 0.15;
                    osc.start();
                    setTimeout(() => { osc.stop(); ctx.close(); }, 150);
                } catch (e) { /* silent */ }
            }
        }
    }
    </script>
</body>
</html>