@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-500 to-orange-600 rounded-2xl shadow-xl text-white">

        {{-- Decorative --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-yellow-300 rounded-full blur-3xl opacity-20 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            {{-- TITLE --}}
            <div class="flex justify-between items-start mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center border-2 border-white/30">
                        <span class="text-2xl">📋</span>
                    </div>
                    <div>
                        <p class="text-xs text-white/70 uppercase tracking-wider font-medium">Activity</p>
                        <h1 class="text-xl font-bold leading-tight">My Orders</h1>
                    </div>
                </div>

                <a href="{{ route('customer.restaurants') }}"
                   class="bg-white/20 hover:bg-white/30 backdrop-blur rounded-full px-4 py-2 text-sm font-semibold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New
                </a>
            </div>

            <p class="text-sm text-white/90 max-w-md">
                Track all your orders in one place
            </p>

            {{-- MINI STATS --}}
            @if ($orders->count() > 0)
                <div class="grid grid-cols-3 gap-3 pt-5 mt-5 border-t border-white/20">
                    <div>
                        <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Total Orders</p>
                        <p class="text-xl font-bold mt-1">{{ $orders->count() }}</p>
                    </div>
                    <div class="border-l border-white/20 pl-3">
                        <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Delivered</p>
                        <p class="text-xl font-bold mt-1">{{ $orders->where('status', 'delivered')->count() }}</p>
                    </div>
                    <div class="border-l border-white/20 pl-3">
                        <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Active</p>
                        <p class="text-xl font-bold mt-1">
                            {{ $orders->whereNotIn('status', ['delivered', 'cancelled', 'rejected', 'no_rider'])->count() }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ORDERS LIST --}}
    {{-- ============================================ --}}
    @if ($orders->isEmpty())
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-12 text-center transition-colors">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-orange-50 dark:bg-orange-950/40 mb-4">
                <span class="text-4xl">🍽️</span>
            </div>
            <h3 class="font-semibold text-gray-900 dark:text-neutral-100 mb-1">No orders yet</h3>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">
                Place an order and start your food journey
            </p>
            <a href="{{ route('customer.restaurants') }}"
               class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-95 transition transform">
                Browse Restaurants
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($orders as $order)
                @php
                    $statusStyles = [
    'received' => ['bg' => 'bg-blue-50 dark:bg-blue-950/30', 'border' => 'border-blue-200 dark:border-blue-800', 'text' => 'text-blue-700 dark:text-blue-300', 'icon' => '🔔'],
    'confirmed' => ['bg' => 'bg-green-50 dark:bg-green-950/30', 'border' => 'border-green-200 dark:border-green-800', 'text' => 'text-green-700 dark:text-green-300', 'icon' => '✅'],
    'preparing' => ['bg' => 'bg-amber-50 dark:bg-amber-950/30', 'border' => 'border-amber-200 dark:border-amber-800', 'text' => 'text-amber-700 dark:text-amber-300', 'icon' => '👨🍳'],
    'finding_rider' => ['bg' => 'bg-purple-50 dark:bg-purple-950/30', 'border' => 'border-purple-200 dark:border-purple-800', 'text' => 'text-purple-700 dark:text-purple-300', 'icon' => '🔍'],
    'rider_assigned' => ['bg' => 'bg-cyan-50 dark:bg-cyan-950/30', 'border' => 'border-cyan-200 dark:border-cyan-800', 'text' => 'text-cyan-700 dark:text-cyan-300', 'icon' => '🛵'],
    'picked_up' => ['bg' => 'bg-indigo-50 dark:bg-indigo-950/30', 'border' => 'border-indigo-200 dark:border-indigo-800', 'text' => 'text-indigo-700 dark:text-indigo-300', 'icon' => '📦'],
    'out_for_delivery' => ['bg' => 'bg-violet-50 dark:bg-violet-950/30', 'border' => 'border-violet-200 dark:border-violet-800', 'text' => 'text-violet-700 dark:text-violet-300', 'icon' => '🚀'],
    'delivered' => ['bg' => 'bg-green-50 dark:bg-green-950/30', 'border' => 'border-green-200 dark:border-green-800', 'text' => 'text-green-700 dark:text-green-300', 'icon' => '🎉'],
    'rejected' => ['bg' => 'bg-red-50 dark:bg-red-950/30', 'border' => 'border-red-200 dark:border-red-800', 'text' => 'text-red-700 dark:text-red-300', 'icon' => '⚠️'],
    'cancelled' => ['bg' => 'bg-gray-50 dark:bg-dark-850', 'border' => 'border-gray-200 dark:border-dark-600', 'text' => 'text-gray-700 dark:text-neutral-300', 'icon' => '❌'],
    'no_rider' => ['bg' => 'bg-red-50 dark:bg-red-950/30', 'border' => 'border-red-200 dark:border-red-800', 'text' => 'text-red-700 dark:text-red-300', 'icon' => '😔'],
];

                    $labels = [
                        'received' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'preparing' => 'Preparing',
                        'finding_rider' => 'Finding Rider',
                        'rider_assigned' => 'Rider Assigned',
                        'picked_up' => 'Picked Up',
                        'out_for_delivery' => 'On the Way',
                        'delivered' => 'Delivered',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                        'no_rider' => 'No Rider',
                    ];

                    $sc = $statusStyles[$order->status] ?? ['bg' => 'bg-gray-50 dark:bg-dark-850', 'border' => 'border-gray-200 dark:border-dark-600', 'text' => 'text-gray-700 dark:text-neutral-300', 'icon' => '📋'];
                    $label = $labels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));

                    $isActive = !in_array($order->status, ['delivered', 'cancelled', 'rejected', 'no_rider']);
                @endphp

                <a href="{{ route('customer.orders.show', $order) }}"
                   class="block bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden hover:shadow-lg hover:border-gray-300 dark:hover:border-gray-700 transition-all duration-200 group">

                    {{-- HEADER --}}
                    <div class="px-5 py-3 {{ $sc['bg'] }} {{ $sc['border'] }} border-b flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">{{ $sc['icon'] }}</span>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-neutral-100 text-sm">{{ $order->restaurant->name }}</p>
                                <p class="text-xs {{ $sc['text'] }} font-semibold uppercase tracking-wide">
                                    {{ $label }}
                                </p>
                            </div>
                        </div>

                        @if ($isActive)
                            <span class="flex items-center gap-1.5 bg-white/70 dark:bg-dark-800/70 backdrop-blur rounded-full px-2.5 py-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                <span class="text-[10px] font-bold text-orange-700 dark:text-orange-300 uppercase tracking-wider">
                                    Active
                                </span>
                            </span>
                        @endif
                    </div>

                    {{-- BODY --}}
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-4">

                            {{-- LEFT: ORDER INFO --}}
                            <div class="flex-1 min-w-0">
                                {{-- ORDER # + TIME --}}
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-xs bg-gray-100 dark:bg-dark-850 text-gray-700 dark:text-neutral-300 px-2 py-1 rounded-full font-semibold">
                                        #{{ $order->id }}
                                    </span>
                                    <span class="text-xs text-gray-400 dark:text-neutral-500">{{ $order->created_at->diffForHumans() }}</span>
                                </div>

                                {{-- ITEMS SUMMARY --}}
                                <p class="text-sm text-gray-600 dark:text-neutral-400 mb-2">
                                    <span class="font-bold text-gray-900 dark:text-neutral-100">{{ $order->items->count() }}</span>
                                    {{ Str::plural('item', $order->items->count()) }}
                                    @if ($order->items->count() > 0)
                                        · 
                                        <span class="text-gray-500 dark:text-neutral-400">{{ $order->items->first()->name }}</span>
                                        @if ($order->items->count() > 1)
                                            <span class="text-gray-400 dark:text-neutral-500">+{{ $order->items->count() - 1 }} more</span>
                                        @endif
                                    @endif
                                </p>

                                {{-- DELIVERY ADDRESS --}}
                                <div class="flex items-start gap-1.5 text-xs text-gray-500 dark:text-neutral-400">
                                    <svg class="w-3.5 h-3.5 text-gray-400 dark:text-neutral-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="line-clamp-1">{{ $order->delivery_address }}</span>
                                </div>

                                {{-- RIDER INFO --}}
                                @if ($order->rider && $isActive)
                                    <div class="flex items-center gap-2 mt-2 text-xs">
                                        <div class="w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <span class="text-green-700 dark:text-green-400 font-medium">Rider: {{ $order->rider->user->name }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- RIGHT: TOTAL --}}
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs text-gray-500 dark:text-neutral-400 mb-0.5">Total</p>
                                <p class="text-lg font-bold text-orange-600 dark:text-orange-400">₱{{ number_format($order->total_amount, 2) }}</p>

                                <span class="text-xs text-gray-500 dark:text-neutral-400 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition inline-flex items-center gap-1 mt-2">
                                    View
                                    <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection