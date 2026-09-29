@extends('layouts.app')

@section('content')
<div x-data="externalOrderForm()"
     x-init="initDashboard()"
     class="max-w-4xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- NEW ORDER BANNER (Sticky Top) --}}
    {{-- ============================================ --}}
    <template x-if="newOrderIds.length > 0">
        <div class="fixed top-20 left-0 right-0 z-[60] px-4 pointer-events-none">
            <div class="max-w-4xl mx-auto pointer-events-auto">
                <div class="bg-gradient-to-r from-orange-500 via-orange-500 to-orange-600 rounded-xl shadow-2xl p-4 flex items-center gap-3 animate-pulse-slow">
                    <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-white text-sm">
                            <span x-text="newOrderIds.length"></span>
                            New Order<span x-show="newOrderIds.length > 1">s</span>!
                        </p>
                        <p class="text-xs text-white/80">
                            Click to view
                        </p>
                    </div>

                    <button type="button"
                            @click="scrollToOrder(newOrderIds[0])"
                            class="bg-white text-orange-600 px-4 py-2 rounded-lg font-bold text-sm hover:bg-orange-50 transition flex-shrink-0 shadow-md">
                        View
                    </button>

                    <button type="button"
                            @click="newOrderIds = []"
                            class="w-8 h-8 rounded-full hover:bg-white/20 flex items-center justify-center text-white transition flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] shadow-xl">

        {{-- COVER PHOTO --}}
        <div class="relative h-32 overflow-hidden">
            @if ($restaurant->cover_image_url)
                <img src="{{ $restaurant->cover_image_url }}"
                     alt="{{ $restaurant->name }}"
                     class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-black/20"></div>
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500 via-orange-600 to-neutral-900"></div>
                <div class="absolute inset-0 opacity-10"
                     style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 80%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
            @endif

            <div class="absolute top-4 right-4 flex items-center gap-2 bg-white/95 dark:bg-[#0a0a0a]/95 backdrop-blur rounded-full px-3 py-1.5 shadow-lg border border-white/20 dark:border-[#262626]">
                <span class="w-2 h-2 rounded-full {{ $restaurant->is_open ? 'bg-orange-500 animate-pulse' : 'bg-neutral-400' }}"></span>
                <span class="text-xs font-bold uppercase tracking-wide {{ $restaurant->is_open ? 'text-orange-600 dark:text-orange-400' : 'text-neutral-700 dark:text-neutral-300' }}">
                    {{ $restaurant->is_open ? 'Open' : 'Closed' }}
                </span>
            </div>
        </div>

        {{-- PROFILE IMAGE + NAME --}}
        <div class="relative px-6 -mt-12">
            <div class="flex items-end gap-4 mb-5">
                <div class="w-24 h-24 rounded-2xl bg-white dark:bg-[#141414] border-4 border-white dark:border-[#141414] shadow-xl flex items-center justify-center flex-shrink-0 overflow-hidden ring-2 ring-orange-500/20">
                    @if ($restaurant->profile_image_url)
                        <img src="{{ $restaurant->profile_image_url }}"
                             alt="{{ $restaurant->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="flex-1 pb-1 min-w-0">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Restaurant</p>
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-white leading-tight truncate">
                        {{ $restaurant->name }}
                    </h1>
                    @if ($restaurant->display_badge)
                        <span class="inline-block mt-1 text-[10px] px-2.5 py-1 rounded-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold uppercase tracking-wider shadow-sm">
                            {{ $restaurant->display_badge }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pb-6 border-t border-neutral-100 dark:border-[#262626] pt-5">
                <div>
                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Pending</p>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-xl font-bold text-neutral-900 dark:text-white">{{ $ratingStats['pending'] ?? 0 }}</p>
                        @if (($ratingStats['pending'] ?? 0) > 0)
                            <span class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                <span class="text-[10px] text-orange-600 dark:text-orange-400 font-bold uppercase">Action needed</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="md:border-l border-neutral-100 dark:border-[#262626] md:pl-3">
                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Today's Orders</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-white mt-1">{{ $ratingStats['today_orders'] ?? 0 }}</p>
                </div>

                <div class="md:border-l border-neutral-100 dark:border-[#262626] md:pl-3">
                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Today's Sales</p>
                    <p class="text-xl font-bold bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent mt-1">₱{{ number_format($ratingStats['today_sales'] ?? 0, 0) }}</p>
                </div>

                <div class="md:border-l border-neutral-100 dark:border-[#262626] md:pl-3">
                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Rating</p>
                    <div class="flex items-center gap-1 mt-1">
                        @if ($ratingStats['avg'])
                            <span class="text-xl font-bold text-neutral-900 dark:text-white">{{ $ratingStats['avg'] }}</span>
                            <svg class="w-4 h-4 fill-orange-500 text-orange-500" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="text-xs text-neutral-400 dark:text-neutral-500">({{ $ratingStats['total'] }})</span>
                        @else
                            <span class="text-sm font-medium text-neutral-400 dark:text-neutral-500">No ratings</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
    @if (session('success'))
        <div class="p-4 bg-gradient-to-r from-orange-50 to-orange-100/50 dark:from-orange-950/30 dark:to-orange-900/10 border-l-4 border-orange-500 rounded-xl text-sm flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0 shadow-md">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="text-neutral-800 dark:text-neutral-200 font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 bg-black dark:bg-neutral-900 border-l-4 border-orange-500 rounded-xl text-sm flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span class="text-white font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- QUICK ACTIONS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

        <form method="POST" action="{{ route('restaurant.toggle-open') }}" class="contents">
            @csrf
            <button class="group bg-white dark:bg-[#141414] border border-neutral-200 dark:border-[#262626] rounded-xl p-4 hover:shadow-lg hover:border-orange-300 dark:hover:border-orange-800 transition-all duration-200 text-left">
                <div class="w-10 h-10 rounded-xl {{ $restaurant->is_open ? 'bg-gradient-to-br from-neutral-800 to-neutral-900' : 'bg-gradient-to-br from-orange-500 to-orange-600' }} flex items-center justify-center mb-3 group-hover:scale-110 transition shadow-md">
                    @if ($restaurant->is_open)
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">
                    {{ $restaurant->is_open ? 'Close' : 'Open' }}
                </p>
                <p class="text-sm font-bold text-neutral-900 dark:text-white">
                    {{ $restaurant->is_open ? 'Close Restaurant' : 'Open Restaurant' }}
                </p>
            </button>
        </form>

        <a href="{{ route('restaurant.orders') }}"
           class="group bg-white dark:bg-[#141414] border border-neutral-200 dark:border-[#262626] rounded-xl p-4 hover:shadow-lg hover:border-orange-300 dark:hover:border-orange-800 transition-all duration-200">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-neutral-800 to-neutral-900 dark:from-neutral-700 dark:to-neutral-800 flex items-center justify-center mb-3 group-hover:scale-110 transition shadow-md">
                <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">View</p>
            <p class="text-sm font-bold text-neutral-900 dark:text-white">Order History</p>
        </a>

        <a href="{{ route('menu-items.index') }}"
           class="group bg-white dark:bg-[#141414] border border-neutral-200 dark:border-[#262626] rounded-xl p-4 hover:shadow-lg hover:border-orange-300 dark:hover:border-orange-800 transition-all duration-200">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-neutral-800 to-neutral-900 dark:from-neutral-700 dark:to-neutral-800 flex items-center justify-center mb-3 group-hover:scale-110 transition shadow-md">
                <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Manage</p>
            <p class="text-sm font-bold text-neutral-900 dark:text-white">Menu Items</p>
        </a>

        <a href="{{ route('restaurant.analytics') }}"
           class="group bg-white dark:bg-[#141414] border border-neutral-200 dark:border-[#262626] rounded-xl p-4 hover:shadow-lg hover:border-orange-300 dark:hover:border-orange-800 transition-all duration-200">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center mb-3 group-hover:scale-110 transition shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">View</p>
            <p class="text-sm font-bold text-neutral-900 dark:text-white">Analytics</p>
        </a>
    </div>

    {{-- ============================================ --}}
    {{-- ACTIVE ORDERS --}}
    {{-- ============================================ --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                Active Orders
                @if ($orders->count() > 0)
                    <span class="bg-gradient-to-r from-orange-500 to-orange-600 text-white text-xs font-bold px-2.5 py-0.5 rounded-full shadow-sm">
                        {{ $orders->count() }}
                    </span>
                @endif
            </h2>

            <a href="{{ route('restaurant.orders') }}"
               class="text-xs text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-semibold inline-flex items-center gap-1 transition">
                View History
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        @if ($orders->isEmpty())
            <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-950/40 dark:to-orange-900/20 mb-4">
                    <svg class="w-8 h-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-white mb-1">No active orders</h3>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">
                    New customer orders will appear here. Tap below to view your order history.
                </p>
                <a href="{{ route('restaurant.orders') }}"
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-95 transition transform">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    View Order History
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($orders as $order)
                    @php
                        $statusColors = [
                            'received'         => ['bg' => 'bg-gradient-to-r from-orange-500 to-orange-600', 'text' => 'text-white'],
                            'confirmed'        => ['bg' => 'bg-gradient-to-r from-orange-500 to-orange-600', 'text' => 'text-white'],
                            'preparing'        => ['bg' => 'bg-gradient-to-r from-orange-500 to-orange-600', 'text' => 'text-white'],
                            'ready_for_pickup' => ['bg' => 'bg-gradient-to-r from-green-500 to-green-600', 'text' => 'text-white'],
                            'finding_rider'    => ['bg' => 'bg-black dark:bg-neutral-900', 'text' => 'text-orange-500'],
                            'rider_assigned'   => ['bg' => 'bg-black dark:bg-neutral-900', 'text' => 'text-orange-500'],
                            'picked_up'        => ['bg' => 'bg-gradient-to-r from-orange-500 to-orange-600', 'text' => 'text-white'],
                            'out_for_delivery' => ['bg' => 'bg-gradient-to-r from-orange-500 to-orange-600', 'text' => 'text-white'],
                            'delivered'        => ['bg' => 'bg-black dark:bg-neutral-900', 'text' => 'text-white'],
                            'rejected'         => ['bg' => 'bg-black dark:bg-neutral-900', 'text' => 'text-orange-500'],
                            'cancelled'        => ['bg' => 'bg-neutral-800 dark:bg-neutral-900', 'text' => 'text-neutral-300'],
                            'no_rider'         => ['bg' => 'bg-black dark:bg-neutral-900', 'text' => 'text-orange-500'],
                        ];
                        $sc = $statusColors[$order->status] ?? ['bg' => 'bg-black dark:bg-neutral-900', 'text' => 'text-white'];

                        $statusIconPath = match ($order->status) {
                            'received'         => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                            'confirmed'        => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                            'preparing'        => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                            'ready_for_pickup' => 'M5 13l4 4L19 7',
                            'finding_rider'    => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                            'rider_assigned'   => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1',
                            'picked_up'        => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                            'out_for_delivery' => 'M13 10V3L4 14h7v7l9-11h-7z',
                            'delivered'        => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                            'rejected'         => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                            'cancelled'        => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                            'no_rider'         => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',
                            default            => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                        };
                    @endphp

                    <div :id="'order-{{ $order->id }}'"
                         :class="isHighlighted({{ $order->id }}) ? 'ring-4 ring-orange-500 ring-offset-2 dark:ring-offset-[#0a0a0a]' : ''"
                         class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden hover:shadow-lg transition-all duration-300 relative">

                        {{-- NEW ORDER BADGE --}}
                        <template x-if="isHighlighted({{ $order->id }})">
                            <div class="absolute top-3 right-3 z-10">
                                <span class="inline-flex items-center gap-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full shadow-md">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    New
                                </span>
                            </div>
                        </template>

                        {{-- CARD HEADER --}}
                        <div class="px-5 py-3 {{ $sc['bg'] }} border-b border-neutral-200 dark:border-[#262626] flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 {{ $sc['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $statusIconPath }}" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold {{ $sc['text'] }} text-sm">Order #{{ $order->id }}</p>
                                    <p class="text-xs {{ $sc['text'] }} opacity-80 font-semibold uppercase tracking-wide">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </p>
                                </div>
                            </div>
                            <p class="text-xs {{ $sc['text'] }} opacity-70">{{ $order->created_at->diffForHumans() }}</p>
                        </div>

                        {{-- BODY --}}
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                        <span class="font-bold text-neutral-900 dark:text-white">{{ $order->items->count() }}</span>
                                        {{ Str::plural('item', $order->items->count()) }}
                                    </p>
                                    <p class="text-lg font-bold bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent mt-0.5">
                                        ₱{{ number_format($order->total_amount, 2) }}
                                    </p>
                                </div>

                                @if ($order->is_external_order)
                                    <span class="text-[10px] bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 px-2.5 py-1 rounded-full font-semibold uppercase tracking-wide">
                                        External
                                    </span>
                                @endif
                            </div>

                            @if ($order->customer)
                                <div class="flex items-center gap-2 mb-3 text-sm">
                                    <div class="w-6 h-6 rounded-full bg-neutral-100 dark:bg-[#262626] flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3 h-3 text-neutral-600 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <p class="text-neutral-700 dark:text-neutral-300">{{ $order->customer->name }}</p>
                                </div>
                            @endif

                            <div class="flex items-start gap-2 mb-3 text-sm">
                                <svg class="w-4 h-4 text-neutral-400 dark:text-neutral-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="text-neutral-600 dark:text-neutral-400 line-clamp-2">{{ $order->delivery_address }}</p>
                            </div>

                            {{-- ⭐ RIDER ASSIGNMENT INFO — ISA LANG DAPAT --}}
                            @if ($order->rider)
                                <div class="flex items-center gap-2 mb-3 p-3 bg-orange-50 dark:bg-orange-950/30 rounded-xl border border-orange-200 dark:border-orange-800">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-orange-900 dark:text-orange-300">
                                            Rider: {{ $order->rider->user->name }}
                                        </p>
                                        <p class="text-xs text-orange-700 dark:text-orange-400">
                                            @if ($order->restaurant_marked_ready_at)
                                                ✅ Ready for pickup — {{ $order->restaurant_marked_ready_at->diffForHumans() }}
                                            @else
                                                ⏳ Naghihintay na ready — hindi pa pwedeng pumunta
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif

                            {{-- ⭐ VERIFICATION BADGE --}}
                            @if ($order->verified_pickup_at)
                                <div class="flex items-center gap-2 mb-3 p-3 bg-green-50 dark:bg-green-950/30 rounded-xl border border-green-200 dark:border-green-800">
                                    <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-green-700 dark:text-green-300 font-semibold">
                                            Picked up by rider
                                        </p>
                                        <p class="text-xs text-green-600 dark:text-green-400">
                                            {{ $order->verified_pickup_at->format('M d, Y · g:i A') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            {{-- ACTION BUTTONS --}}
                            @if ($order->status === 'received')
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('restaurant.orders.confirm', $order) }}" class="flex-1 sm:flex-none">
                                        @csrf
                                        <button class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md">
                                            Confirm Order
                                        </button>
                                    </form>
                                    <button type="button"
                                            onclick="document.getElementById('reject-{{ $order->id }}').classList.toggle('hidden')"
                                            class="flex-1 sm:flex-none bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 px-5 py-2.5 rounded-xl font-semibold text-sm">
                                        Reject
                                    </button>
                                </div>

                            @elseif ($order->status === 'confirmed')
                                <div class="mt-4">
                                    <form method="POST" action="{{ route('restaurant.orders.ready', $order) }}">
                                        @csrf
                                        <button class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm">
                                            Start Preparing
                                        </button>
                                    </form>
                                </div>

                            @elseif ($order->status === 'preparing')
                                <div class="mt-4">
                                    <form method="POST" action="{{ route('restaurant.orders.mark-ready', $order) }}">
                                        @csrf
                                        <button class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md">
                                            Mark as Ready — Notify Riders
                                        </button>
                                    </form>
                                </div>

                            @elseif ($order->status === 'rider_assigned' && !$order->restaurant_marked_ready_at)
                                {{-- ⭐ May naka-assign na rider, hintay lang na ready --}}
                                <div class="mt-4">
                                    <form method="POST" action="{{ route('restaurant.orders.mark-ready', $order) }}">
                                        @csrf
                                        <button class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-green-600 hover:to-green-700">
                                            Ready na — Notify Rider
                                        </button>
                                    </form>
                                </div>

                            @elseif ($order->status === 'rider_assigned' && $order->restaurant_marked_ready_at)
                                {{-- ⭐ BAGO: Naka-mark na as ready, hintay lang rider mag-pickup --}}
                                <div class="mt-4 p-3 bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 rounded-xl">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-xs text-green-800 dark:text-green-300 leading-relaxed font-medium">
                                            <strong>Ready na!</strong> Hintayin ang rider na mag-pickup.
                                        </p>
                                    </div>
                                </div>
                            @endif

                            {{-- REJECTION FORM --}}
                            @if ($order->status === 'received')
                                <div id="reject-{{ $order->id }}" class="hidden mt-3 p-4 bg-black dark:bg-neutral-900 rounded-xl border-l-4 border-orange-500">
                                    <form method="POST" action="{{ route('restaurant.orders.reject', $order) }}" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-semibold text-orange-500 mb-1.5">Reason for rejection</label>
                                            <select name="rejection_reason" required
                                                    class="w-full border border-neutral-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-neutral-900 text-white">
                                                <option value="">Select a reason...</option>
                                                <option value="Out of stock">Out of stock</option>
                                                <option value="Too busy at the moment">Too busy at the moment</option>
                                                <option value="Closing soon">Closing soon</option>
                                                <option value="Cannot deliver to this area">Cannot deliver to this area</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <button class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-5 py-2 rounded-lg font-semibold text-sm hover:from-orange-600 hover:to-orange-700 transition shadow-md">
                                            Confirm Rejection
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- EXTERNAL ORDER (BOTTOM) --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-neutral-900 dark:text-white">Add External Order</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">For phone or walk-in customers</p>
                </div>
            </div>

            <button type="button"
                    @click="showForm = !showForm"
                    class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform">
                <span x-show="!showForm">+ New Order</span>
                <span x-show="showForm" x-cloak>Cancel</span>
            </button>
        </div>

        <div x-show="showForm" x-cloak x-transition class="mt-5 pt-5 border-t border-neutral-100 dark:border-[#262626]">
            <form method="POST" action="{{ route('restaurant.orders.external') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Customer Name</label>
                    <input type="text" name="customer_name" placeholder="Juan Dela Cruz" required
                           class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Food Cost (₱)</label>
                    <input type="number" step="0.01" name="food_cost" placeholder="0.00" required
                           class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Delivery Address</label>
                    <textarea x-model="address" @input.debounce.800ms="geocodeAddress()"
                              required rows="2"
                              class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                              placeholder="Street, barangay, city"></textarea>
                </div>

                <input type="hidden" name="delivery_address" :value="address">
                <input type="hidden" name="delivery_lat" :value="lat">
                <input type="hidden" name="delivery_lng" :value="lng">

                <div class="md:col-span-2">
                    <button type="button" @click="useCurrentLocation()" :disabled="locating"
                            class="inline-flex items-center gap-2 text-sm text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-medium disabled:opacity-50 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span x-show="!locating">Use my current location</span>
                        <span x-show="locating">Getting location...</span>
                    </button>
                </div>

                <div class="md:col-span-2 flex items-center gap-2">
                    <template x-if="geocoding">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 flex items-center gap-2">
                            <svg class="animate-spin h-3 w-3 text-orange-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Looking up address...
                        </p>
                    </template>
                    <template x-if="!geocoding && lat && lng && !locating">
                        <p class="text-xs text-orange-600 dark:text-orange-400 font-medium flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Location set
                        </p>
                    </template>
                    <template x-if="!geocoding && (!lat || !lng) && !locating">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">
                            Please enter an address or use location
                        </p>
                    </template>
                </div>

                <div class="md:col-span-2 pt-2">
                    <button type="submit" :disabled="!lat || !lng"
                            :class="(!lat || !lng) ? 'opacity-40 cursor-not-allowed' : 'hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98'"
                            class="w-full md:w-auto bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-3 rounded-xl font-semibold text-sm shadow-md transition transform flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Create & Find Rider
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<style>
    .active\:scale-98:active { transform: scale(0.98); }
    .active\:scale-95:active { transform: scale(0.95); }

    @keyframes pulse-slow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.9; }
    }
    .animate-pulse-slow {
        animation: pulse-slow 2s ease-in-out infinite;
    }

    @keyframes highlight-flash {
        0%, 100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.5); }
        50% { box-shadow: 0 0 0 12px rgba(249, 115, 22, 0); }
    }
    .ring-orange-500 {
        animation: highlight-flash 2s ease-in-out infinite;
    }
</style>
<script>
function externalOrderForm() {
    return {
        showForm: false,
        address: '',
        lat: '',
        lng: '',
        locating: false,
        geocoding: false,
        restaurantId: {{ auth()->user()?->restaurant?->id ?? 'null' }},

        newOrderIds: [],
        highlightTimeout: {},

        initDashboard() {
            if (this.restaurantId && typeof window.Echo !== 'undefined') {
                window.Echo.private(`restaurant.${this.restaurantId}`)
                    .listen('.new.order', (e) => {
                        this.handleNewOrder(e);
                    });
            }
        },

        handleNewOrder(e) {
            const orderId = parseInt(e.order_id);
            if (this.newOrderIds.includes(orderId)) return;

            this.newOrderIds.push(orderId);
            this.playBeep();
            this.showBrowserNotification(e);

            this.highlightTimeout[orderId] = setTimeout(() => {
                this.removeHighlight(orderId);
            }, 60000);

            setTimeout(() => {
                window.location.reload();
            }, 1500);
        },

        isHighlighted(orderId) {
            return this.newOrderIds.includes(parseInt(orderId));
        },

        removeHighlight(orderId) {
            this.newOrderIds = this.newOrderIds.filter(id => id !== parseInt(orderId));
            if (this.highlightTimeout[orderId]) {
                clearTimeout(this.highlightTimeout[orderId]);
                delete this.highlightTimeout[orderId];
            }
        },

        scrollToOrder(orderId) {
            const el = document.getElementById(`order-${orderId}`);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                el.classList.add('ring-4', 'ring-orange-500', 'ring-offset-2');
                setTimeout(() => {
                    el.classList.remove('ring-4', 'ring-orange-500', 'ring-offset-2');
                }, 3000);
            }
        },

        playBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.value = 800;
                gain.gain.value = 0.3;
                osc.start();
                setTimeout(() => { osc.stop(); ctx.close(); }, 200);
            } catch (e) { /* silent */ }
        },

        showBrowserNotification(e) {
            if (!('Notification' in window)) return;
            if (Notification.permission !== 'granted') return;

            new Notification('New Order Received!', {
                body: `${e.customer_name || 'Customer'} — ₱${e.total_amount || 0}`,
                icon: '/icon-192.png',
                tag: `order-${e.order_id}`,
                requireInteraction: true,
            });
        },

        async useCurrentLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation not supported.');
                return;
            }

            this.locating = true;

            navigator.geolocation.getCurrentPosition(async (pos) => {
                this.lat = pos.coords.latitude;
                this.lng = pos.coords.longitude;

                try {
                    const res = await fetch(
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${this.lat}&lon=${this.lng}&zoom=18&addressdetails=1`,
                        { headers: { 'Accept-Language': 'en', 'User-Agent': 'FoodDash/1.0' } }
                    );
                    const data = await res.json();
                    if (data && data.display_name) {
                        this.address = data.display_name;
                    }
                } catch (err) {
                    console.warn('Reverse geocoding failed:', err);
                }

                this.locating = false;
            }, () => {
                alert('Could not get your location.');
                this.locating = false;
            });
        },

        async geocodeAddress() {
            if (!this.address || this.address.length < 5) {
                this.lat = '';
                this.lng = '';
                return;
            }
            if (this.locating) return;

            this.geocoding = true;

            try {
                const town = '{{ \App\Models\SystemConfig::current()->town_address ?? "Cagayan de Oro" }}';
                const query = `${this.address}, ${town}, Philippines`;

                const res = await fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=ph`,
                    { headers: { 'Accept-Language': 'en', 'User-Agent': 'FoodDash/1.0' } }
                );
                const data = await res.json();

                if (data && data.length > 0) {
                    this.lat = data[0].lat;
                    this.lng = data[0].lon;
                } else {
                    this.lat = '';
                    this.lng = '';
                }
            } catch (err) {
                console.warn('Geocoding failed:', err);
                this.lat = '';
                this.lng = '';
            }

            this.geocoding = false;
        }
    }
}
</script>
@endpush