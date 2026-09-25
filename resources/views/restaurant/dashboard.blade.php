@extends('layouts.app')

@section('content')
<div x-data="externalOrderForm()" class="max-w-4xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-500 to-orange-600 rounded-2xl shadow-xl text-white">

        {{-- Decorative --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-yellow-300 rounded-full blur-3xl opacity-20 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            {{-- TOP --}}
            <div class="flex justify-between items-start mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-2xl border-2 border-white/30">
                        🍽️
                    </div>
                    <div>
                        <p class="text-xs text-white/70 uppercase tracking-wider font-medium">Restaurant</p>
                        <h1 class="text-xl font-bold leading-tight">{{ $restaurant->name }}</h1>
                        @if ($restaurant->cuisine)
                            <span class="inline-block mt-1 text-[10px] px-2 py-0.5 rounded-full bg-white/20 backdrop-blur font-semibold">
                                {{ $restaurant->cuisine }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- STATUS PILL --}}
                <div class="flex items-center gap-2 bg-white/20 backdrop-blur rounded-full px-3 py-1.5">
                    <span class="w-2 h-2 rounded-full {{ $restaurant->is_open ? 'bg-green-300 animate-pulse' : 'bg-red-300' }}"></span>
                    <span class="text-xs font-bold uppercase tracking-wide">
                        {{ $restaurant->is_open ? 'Open' : 'Closed' }}
                    </span>
                </div>
            </div>

            {{-- QUICK STATS --}}
            <div class="grid grid-cols-3 gap-3 pt-5 border-t border-white/20">
                <div>
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Rating</p>
                    <div class="flex items-center gap-1 mt-1">
                        @if ($ratingStats['avg'])
                            <span class="text-xl font-bold">{{ $ratingStats['avg'] }}</span>
                            <svg class="w-4 h-4 fill-yellow-300 text-yellow-300" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @else
                            <span class="text-sm font-medium text-white/70">No ratings</span>
                        @endif
                    </div>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Reviews</p>
                    <p class="text-xl font-bold mt-1">{{ $ratingStats['total'] }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Pending</p>
                    <p class="text-xl font-bold mt-1">{{ $orders->where('status', 'received')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- QUICK ACTIONS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

        {{-- TOGGLE OPEN --}}
        <form method="POST" action="{{ route('restaurant.toggle-open') }}" class="contents">
            @csrf
            <button class="group bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md hover:border-gray-300 transition text-left">
                <div class="w-10 h-10 rounded-xl {{ $restaurant->is_open ? 'bg-red-100' : 'bg-green-100' }} flex items-center justify-center mb-3 group-hover:scale-110 transition">
                    @if ($restaurant->is_open)
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </div>
                <p class="text-xs text-gray-500 font-medium">
                    {{ $restaurant->is_open ? 'Close' : 'Open' }}
                </p>
                <p class="text-sm font-bold text-gray-900">
                    {{ $restaurant->is_open ? 'Close Restaurant' : 'Open Restaurant' }}
                </p>
            </button>
        </form>

        {{-- ORDER HISTORY --}}
        <a href="{{ route('restaurant.orders') }}"
           class="group bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md hover:border-gray-300 transition">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <p class="text-xs text-gray-500 font-medium">View</p>
            <p class="text-sm font-bold text-gray-900">Order History</p>
        </a>

        {{-- MANAGE MENU --}}
        <a href="{{ route('menu-items.index') }}"
           class="group bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md hover:border-gray-300 transition">
            <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <p class="text-xs text-gray-500 font-medium">Manage</p>
            <p class="text-sm font-bold text-gray-900">Menu Items</p>
        </a>

        {{-- ANALYTICS --}}
        <a href="{{ route('restaurant.analytics') }}"
           class="group bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md hover:border-gray-300 transition">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <p class="text-xs text-gray-500 font-medium">View</p>
            <p class="text-sm font-bold text-gray-900">Analytics</p>
        </a>
    </div>

    {{-- ============================================ --}}
    {{-- EXTERNAL ORDER BUTTON --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-gray-900">Add External Order</p>
                    <p class="text-xs text-gray-500">Para sa phone o walk-in customers</p>
                </div>
            </div>

            <button type="button"
                    @click="showForm = !showForm"
                    class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform">
                <span x-show="!showForm">+ New Order</span>
                <span x-show="showForm" x-cloak>Cancel</span>
            </button>
        </div>

        {{-- EXTERNAL ORDER FORM --}}
        <div x-show="showForm" x-cloak x-transition class="mt-5 pt-5 border-t border-gray-100">
            <form method="POST" action="{{ route('restaurant.orders.external') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Customer Name</label>
                    <input type="text" name="customer_name" placeholder="Juan Dela Cruz" required
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Food Cost (₱)</label>
                    <input type="number" step="0.01" name="food_cost" placeholder="0.00" required
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Delivery Address</label>
                    <textarea x-model="address" @input.debounce.800ms="geocodeAddress()"
                              required rows="2"
                              class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                              placeholder="Street, barangay, city"></textarea>
                </div>

                <input type="hidden" name="delivery_address" :value="address">
                <input type="hidden" name="delivery_lat" :value="lat">
                <input type="hidden" name="delivery_lng" :value="lng">

                <div class="md:col-span-2">
                    <button type="button" @click="useCurrentLocation()" :disabled="locating"
                            class="inline-flex items-center gap-2 text-sm text-orange-600 hover:text-orange-700 font-medium disabled:opacity-50">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span x-show="!locating">Use my current location</span>
                        <span x-show="locating">Getting location...</span>
                    </button>
                </div>

                <div class="md:col-span-2 flex items-center gap-2">
                    <template x-if="geocoding">
                        <p class="text-xs text-gray-500 flex items-center gap-2">
                            <svg class="animate-spin h-3 w-3 text-orange-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Looking up address...
                        </p>
                    </template>
                    <template x-if="!geocoding && lat && lng && !locating">
                        <p class="text-xs text-green-600 font-medium flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Location set
                        </p>
                    </template>
                    <template x-if="!geocoding && (!lat || !lng) && !locating">
                        <p class="text-xs text-amber-600 font-medium">
                            Please enter an address or use location
                        </p>
                    </template>
                </div>

                <div class="md:col-span-2 pt-2">
                    <button type="submit" :disabled="!lat || !lng"
                            :class="(!lat || !lng) ? 'opacity-40 cursor-not-allowed' : 'hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98'"
                            class="w-full md:w-auto bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-3 rounded-xl font-semibold text-sm shadow-md transition transform flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create & Find Rider
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACTIVE ORDERS --}}
    {{-- ============================================ --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Active Orders
            </h2>
            @if ($orders->count() > 0)
                <span class="text-xs text-gray-500 font-medium">{{ $orders->count() }} total</span>
            @endif
        </div>

        @if ($orders->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">No orders yet</h3>
                <p class="text-sm text-gray-500">Orders will appear here when customers place them</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($orders as $order)
                    @php
                        $statusColors = [
                            'received' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700', 'icon' => '🔔'],
                            'confirmed' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-700', 'icon' => '✅'],
                            'preparing' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-700', 'icon' => '👨‍🍳'],
                            'finding_rider' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'text' => 'text-purple-700', 'icon' => '🔍'],
                            'rider_assigned' => ['bg' => 'bg-cyan-50', 'border' => 'border-cyan-200', 'text' => 'text-cyan-700', 'icon' => '🛵'],
                            'picked_up' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'text' => 'text-indigo-700', 'icon' => '📦'],
                            'out_for_delivery' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'text' => 'text-purple-700', 'icon' => '🚀'],
                            'delivered' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-700', 'icon' => '🎉'],
                            'rejected' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-700', 'icon' => '⚠️'],
                            'cancelled' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-700', 'icon' => '❌'],
                            'no_rider' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-700', 'icon' => '😔'],
                        ];
                        $sc = $statusColors[$order->status] ?? ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-700', 'icon' => '📋'];
                    @endphp

                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-md transition">

                        {{-- CARD HEADER --}}
                        <div class="px-5 py-3 {{ $sc['bg'] }} {{ $sc['border'] }} border-b flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">{{ $sc['icon'] }}</span>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">Order #{{ $order->id }}</p>
                                    <p class="text-xs {{ $sc['text'] }} font-semibold uppercase tracking-wide">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                        </div>

                        {{-- BODY --}}
                        <div class="p-5">
                            {{-- SUMMARY --}}
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-bold text-gray-900">{{ $order->items->count() }}</span>
                                        {{ Str::plural('item', $order->items->count()) }}
                                    </p>
                                    <p class="text-lg font-bold text-orange-600 mt-0.5">₱{{ number_format($order->total_amount, 2) }}</p>
                                </div>

                                @if ($order->is_external_order)
                                    <span class="text-[10px] bg-gray-100 text-gray-700 px-2 py-1 rounded-full font-semibold uppercase tracking-wide">
                                        External
                                    </span>
                                @endif
                            </div>

                            {{-- DELIVERY --}}
                            <div class="flex items-start gap-2 mb-3 text-sm">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="text-gray-600 line-clamp-2">{{ $order->delivery_address }}</p>
                            </div>

                            {{-- RIDER --}}
                            @if ($order->rider)
                                <div class="flex items-center gap-2 mb-3 text-sm">
                                    <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3 h-3 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <p class="text-green-700 font-medium">
                                        Rider: {{ $order->rider->user->name }}
                                    </p>
                                </div>
                            @endif

                            {{-- PREP TIME COUNTDOWN --}}
                            @if (in_array($order->status, ['confirmed', 'preparing']))
                                @php
                                    $prepTime = $restaurant->prep_time_minutes ?? 20;
                                    $elapsed = (int) $order->created_at->diffInMinutes(now());
                                    $remaining = $prepTime - $elapsed;
                                @endphp

                                <div class="mt-3 p-3 rounded-xl {{ $remaining > 0 ? 'bg-gray-50' : 'bg-red-50 border border-red-200' }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 {{ $remaining > 0 ? 'text-gray-500' : 'text-red-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-xs font-medium {{ $remaining > 0 ? 'text-gray-600' : 'text-red-700' }}">
                                                Prep time
                                            </span>
                                        </div>
                                        <p class="text-sm font-bold {{ $remaining > 0 ? 'text-gray-900' : 'text-red-600' }}">
                                            @if ($remaining > 0)
                                                {{ $remaining }} min left
                                            @else
                                                {{ abs($remaining) }} min overdue
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full {{ $remaining > 0 ? 'bg-orange-500' : 'bg-red-500' }} rounded-full transition-all"
                                             style="width: {{ min(100, ($elapsed / $prepTime) * 100) }}%"></div>
                                    </div>
                                </div>
                            @endif

                            {{-- ACTIONS --}}
                            @if (in_array($order->status, ['received', 'confirmed']))
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @if ($order->status === 'received')
                                        <form method="POST" action="{{ route('restaurant.orders.confirm', $order) }}" class="flex-1 sm:flex-none">
                                            @csrf
                                            <button class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-green-600 hover:to-green-700 hover:shadow-lg active:scale-95 transition transform flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Confirm Order
                                            </button>
                                        </form>
                                        <button type="button"
                                                onclick="document.getElementById('reject-{{ $order->id }}').classList.toggle('hidden')"
                                                class="flex-1 sm:flex-none bg-red-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:bg-red-700 hover:shadow-lg active:scale-95 transition transform">
                                            Reject
                                        </button>
                                    @elseif ($order->status === 'confirmed')
                                        <form method="POST" action="{{ route('restaurant.orders.ready', $order) }}" class="flex-1 sm:flex-none">
                                            @csrf
                                            <button class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-blue-600 hover:to-blue-700 hover:shadow-lg active:scale-95 transition transform flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Mark as Preparing
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif

                            {{-- REJECT FORM --}}
                            @if ($order->status === 'received')
                                <div id="reject-{{ $order->id }}" class="hidden mt-3 p-4 bg-red-50 border border-red-200 rounded-xl">
                                    <form method="POST" action="{{ route('restaurant.orders.reject', $order) }}" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-semibold text-red-800 mb-1.5">Reason for rejection</label>
                                            <select name="rejection_reason" required
                                                    class="w-full border border-red-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white">
                                                <option value="">Select a reason...</option>
                                                <option value="Out of stock">Out of stock</option>
                                                <option value="Too busy at the moment">Too busy at the moment</option>
                                                <option value="Closing soon">Closing soon</option>
                                                <option value="Cannot deliver to this area">Cannot deliver to this area</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <button class="bg-red-600 text-white px-5 py-2 rounded-lg font-semibold text-sm hover:bg-red-700 transition">
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

</div>
@endsection

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<style>
    .active\:scale-98:active { transform: scale(0.98); }
    .active\:scale-95:active { transform: scale(0.95); }
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