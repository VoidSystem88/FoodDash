@extends('layouts.app')

@section('content')
<div x-data="riderDash({{ $rider->id }})" x-init="init()" class="max-w-3xl mx-auto">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 mb-6 text-white shadow-lg">
        <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-3">
                <div class="relative">
                    @if (auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}"
                             class="w-14 h-14 rounded-full object-cover border-3 border-white/30">
                    @else
                        <div class="w-14 h-14 rounded-full bg-white/20 backdrop-blur flex items-center justify-center text-white text-xl font-bold border-3 border-white/30">
                            {{ auth()->user()->initials }}
                        </div>
                    @endif

                    <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full border-2 border-white"
                          :class="online ? 'bg-green-400' : 'bg-gray-400'"></span>
                </div>

                <div>
                    <p class="text-xs text-white/80">Welcome back,</p>
                    <h1 class="text-xl font-bold">{{ auth()->user()->name }}</h1>
                </div>
            </div>

            @if ($currentOrder)
                {{-- May active order — disabled toggle --}}
                <button type="button"
                        disabled
                        title="Complete your active delivery first"
                        class="px-5 py-2.5 rounded-full font-semibold text-sm shadow-md opacity-70 cursor-not-allowed bg-white text-orange-600 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span>Online</span>
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </button>
            @else
                {{-- Walang active order — normal toggle --}}
                <button @click="toggleOnline()"
                        :disabled="toggling"
                        :class="online ? 'bg-white text-orange-600' : 'bg-white/20 text-white'"
                        class="px-5 py-2.5 rounded-full font-semibold text-sm shadow-md hover:scale-105 active:scale-95 transition transform disabled:opacity-50 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full" :class="online ? 'bg-green-500 animate-pulse' : 'bg-gray-400'"></span>
                    <span x-text="online ? 'Online' : 'Offline'"></span>
                </button>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-white/20">
            <div>
                <p class="text-xs text-white/80">Today's Deliveries</p>
                <p class="text-2xl font-bold">{{ $completedToday }}</p>
            </div>
            <div>
                <p class="text-xs text-white/80">Today's Earnings</p>
                <p class="text-2xl font-bold">₱{{ number_format($earningsToday, 0) }}</p>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- STAT CARDS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-gray-200 dark:border-dark-700 p-4 hover:shadow-md transition">
            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-950/40 flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-neutral-400">Completed</p>
            <p class="text-xl font-bold text-gray-900 dark:text-neutral-100">{{ $completedToday }}</p>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-xl border border-gray-200 dark:border-dark-700 p-4 hover:shadow-md transition">
            <div class="w-9 h-9 rounded-lg bg-green-100 dark:bg-green-950/40 flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-neutral-400">Earnings</p>
            <p class="text-xl font-bold text-green-600 dark:text-green-400">₱{{ number_format($earningsToday, 0) }}</p>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-xl border border-gray-200 dark:border-dark-700 p-4 hover:shadow-md transition">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center mb-2"
                 :class="online ? 'bg-green-100 dark:bg-green-950/40' : 'bg-gray-100 dark:bg-dark-850'">
                <svg class="w-5 h-5" :class="online ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-neutral-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-neutral-400">Status</p>
            <p class="text-sm font-bold mt-1" :class="online ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-neutral-400'"
               x-text="online ? 'Ready' : 'Offline'"></p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- AVAILABLE OFFERS (Stacked, hindi popup) --}}
    {{-- ============================================ --}}
    <template x-if="offers.length > 0">
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-bold text-gray-900 dark:text-neutral-100 flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                    </span>
                    Available Offers
                    <span class="text-xs bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 px-2 py-0.5 rounded-full font-bold"
                          x-text="offers.length"></span>
                </h2>
                <p class="text-xs text-gray-500 dark:text-neutral-400">Pumili ng order</p>
            </div>

            <div class="space-y-3">
                <template x-for="(offer, index) in offers" :key="offer.order_id">
                    <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-md border-2 border-orange-300 dark:border-orange-800 overflow-hidden transition-all hover:shadow-lg">

                        {{-- PROGRESS BAR --}}
                        <div class="h-1.5 bg-gray-100 dark:bg-dark-850 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-orange-500 to-orange-400 transition-all duration-1000"
                                 :style="`width: ${(offer.secondsLeft / offer.expires_in) * 100}%`"></div>
                        </div>

                        {{-- HEADER --}}
                        <div class="px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm" x-text="offer.restaurant"></p>
                                        <p class="text-[10px] text-white/80">Delivery Offer</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-white/80">Expires in</p>
                                    <p class="text-base font-bold" x-text="offer.secondsLeft + 's'"></p>
                                </div>
                            </div>
                        </div>

                        {{-- BODY --}}
                        <div class="p-4">
                            {{-- PICKUP --}}
                            <div class="flex items-start gap-2 mb-3">
                                <div class="w-7 h-7 rounded-lg bg-red-50 dark:bg-red-950/40 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] text-gray-500 dark:text-neutral-400 uppercase tracking-wide font-medium">Pickup From</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100 line-clamp-1" x-text="offer.restaurant"></p>
                                    <p class="text-xs text-gray-500 dark:text-neutral-400 line-clamp-1" x-text="offer.restaurant_address"></p>
                                </div>
                            </div>

                            {{-- DROPOFF --}}
                            <div class="flex items-start gap-2 mb-3">
                                <div class="w-7 h-7 rounded-lg bg-green-50 dark:bg-green-950/40 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] text-gray-500 dark:text-neutral-400 uppercase tracking-wide font-medium">Deliver to</p>
                                    <p class="text-sm text-gray-700 dark:text-neutral-300 line-clamp-2" x-text="offer.delivery_address"></p>
                                </div>
                            </div>

                            {{-- STATS --}}
                            <div class="grid grid-cols-3 gap-2 bg-gray-50 dark:bg-dark-850 rounded-xl p-3 mb-3 text-center">
                                <div>
                                    <p class="text-[10px] text-gray-500 dark:text-neutral-400">Food Cost</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-neutral-100" x-text="'₱' + offer.food_cost"></p>
                                </div>
                                <div class="border-x border-gray-200 dark:border-dark-600">
                                    <p class="text-[10px] text-gray-500 dark:text-neutral-400">Delivery Fee</p>
                                    <p class="text-sm font-bold text-green-600 dark:text-green-400" x-text="'₱' + offer.delivery_fee"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-500 dark:text-neutral-400">Items</p>
                                    <p class="text-sm font-bold text-orange-600 dark:text-orange-400" x-text="offer.items_count"></p>
                                </div>
                            </div>

                            {{-- ERROR --}}
                            <template x-if="error">
                                <div class="mb-3 p-2 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg text-xs"
                                     x-text="error"></div>
                            </template>

                            {{-- ACTIONS --}}
                            <div class="flex gap-2">
                                <button @click="declineOffer(offer.order_id)"
                                        type="button"
                                        :disabled="processing"
                                        class="flex-1 border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 dark:hover:bg-dark-850 transition disabled:opacity-50">
                                    Decline
                                </button>
                                <button @click="acceptOffer(offer.order_id)"
                                        type="button"
                                        :disabled="processing"
                                        :class="processing
                                                ? 'opacity-50 cursor-not-allowed'
                                                : 'hover:from-orange-600 hover:to-orange-700 active:scale-98'"
                                        class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white py-2.5 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition transform flex items-center justify-center gap-2">
                                    <template x-if="!processing">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Accept
                                        </span>
                                    </template>
                                    <template x-if="processing">
                                        <span>Processing...</span>
                                    </template>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- ============================================ --}}
    {{-- CURRENT ACTIVE ORDER --}}
    {{-- ============================================ --}}
    @if ($currentOrder)
        <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg mb-6 overflow-hidden transition-colors">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-white/80">Active Delivery</p>
                        <h2 class="text-lg font-bold">Order #{{ $currentOrder->id }}</h2>
                    </div>
                    <span class="text-xs px-3 py-1.5 rounded-full bg-white/20 backdrop-blur font-semibold uppercase tracking-wide">
                        {{ ucfirst(str_replace('_', ' ', $currentOrder->status)) }}
                    </span>
                </div>

                <div class="flex items-center gap-1 mt-3">
                    @php
                        $stages = ['rider_assigned', 'picked_up', 'out_for_delivery'];
                        $currentIdx = array_search($currentOrder->status, $stages);
                    @endphp
                    @foreach ($stages as $idx => $stage)
                        <div class="flex-1 h-1 rounded-full transition-all
                            {{ $currentIdx >= $idx ? 'bg-white' : 'bg-white/30' }}"></div>
                    @endforeach
                </div>
                <div class="flex justify-between mt-1 text-[10px] text-white/80">
                    <span>Assigned</span>
                    <span>Picked Up</span>
                    <span>On the Way</span>
                </div>
            </div>

            <div class="p-6">
                {{-- PICKUP --}}
                <div class="flex gap-4 mb-5 pb-5 border-b border-gray-100 dark:border-dark-700">
                    <div class="relative flex-shrink-0">
                        @if ($currentOrder->restaurant && $currentOrder->restaurant->profile_image_url)
                            <img src="{{ $currentOrder->restaurant->profile_image_url }}"
                                 alt="{{ $currentOrder->restaurant->name }}"
                                 class="w-12 h-12 rounded-xl object-cover border-2 border-red-300 dark:border-red-800 shadow-sm">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white text-lg font-bold shadow-sm">
                                {{ $currentOrder->restaurant ? strtoupper(substr($currentOrder->restaurant->name, 0, 1)) : '?' }}
                            </div>
                        @endif
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-red-500 flex items-center justify-center border-2 border-white dark:border-dark-800 shadow-sm">
                            <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 dark:text-neutral-400 uppercase tracking-wide font-medium mb-0.5">Pickup From</p>
                        <p class="font-bold text-gray-900 dark:text-neutral-100">
                            {{ $currentOrder->restaurant->name ?? 'Restaurant not found' }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-neutral-400 mt-0.5">
                            {{ $currentOrder->restaurant->address ?? 'Address unavailable' }}
                        </p>

                        <div class="flex items-center gap-2 mt-3 bg-red-50 dark:bg-red-950/30 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-700 dark:text-neutral-300">Pay restaurant:</span>
                            <span class="text-sm font-bold text-red-600 dark:text-red-400">
                                ₱{{ number_format($currentOrder->restaurant_earnings ?? $currentOrder->food_cost, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- DROP-OFF --}}
                <div class="flex gap-4 mb-5 pb-5 border-b border-gray-100 dark:border-dark-700">
                    <div class="flex-shrink-0">
                        @php
                            $customer = $currentOrder->customer;
                        @endphp

                        @if ($customer && $customer->avatar_url)
                            <img src="{{ $customer->avatar_url }}"
                                 alt="{{ $customer->name }}"
                                 class="w-14 h-14 rounded-xl object-cover border-2 border-green-300 dark:border-green-800 shadow-sm">
                        @else
                            <div class="w-14 h-14 rounded-xl {{ $customer?->avatar_color ?? 'bg-green-500' }} flex items-center justify-center text-white text-xl font-bold shadow-sm">
                                {{ $customer?->initials ?? '?' }}
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 dark:text-neutral-400 uppercase tracking-wide font-medium mb-1">Deliver To</p>

                        @if ($customer)
                            <p class="font-bold text-gray-900 dark:text-neutral-100">{{ $customer->name }}</p>
                            @if ($customer->phone)
                                <p class="text-xs text-gray-500 dark:text-neutral-400">📞 {{ $customer->phone }}</p>
                            @endif
                        @endif

                        <p class="text-sm text-gray-600 dark:text-neutral-400 mt-0.5">{{ $currentOrder->delivery_address }}</p>

                        <div class="flex items-center gap-2 mt-3 bg-green-50 dark:bg-green-950/30 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-700 dark:text-neutral-300">Collect from customer:</span>
                            <span class="text-sm font-bold text-green-600 dark:text-green-400">₱{{ number_format($currentOrder->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- ORDER ITEMS --}}
                <div class="mb-5">
                    <p class="text-xs text-gray-500 dark:text-neutral-400 uppercase tracking-wide font-medium mb-2">Order Items</p>
                    <div class="space-y-1.5 bg-gray-50 dark:bg-dark-850 rounded-xl p-3">
                        @foreach ($currentOrder->items as $item)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-700 dark:text-neutral-300">
                                    <span class="font-semibold text-gray-900 dark:text-neutral-100">{{ $item->quantity }}x</span>
                                    {{ $item->name }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex flex-wrap gap-2">
                    @if ($currentOrder->status === 'rider_assigned')
                        <form method="POST" action="{{ route('rider.orders.status', $currentOrder) }}" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="picked_up">
                            <button class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 font-semibold text-sm shadow-md hover:shadow-lg active:scale-98 transition transform flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Mark as Picked Up
                            </button>
                        </form>
                    @elseif ($currentOrder->status === 'picked_up')
                        <form method="POST" action="{{ route('rider.orders.status', $currentOrder) }}" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="out_for_delivery">
                            <button class="w-full bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-3 rounded-xl hover:from-purple-600 hover:to-purple-700 font-semibold text-sm shadow-md hover:shadow-lg active:scale-98 transition transform flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Out for Delivery
                            </button>
                        </form>
                    @elseif ($currentOrder->status === 'out_for_delivery')
                        <form method="POST" action="{{ route('rider.orders.status', $currentOrder) }}" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="delivered">
                            <button class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-3 rounded-xl hover:from-green-600 hover:to-green-700 font-semibold text-sm shadow-md hover:shadow-lg active:scale-98 transition transform flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Mark as Delivered
                            </button>
                        </form>
                    @endif
                </div>

                {{-- PAYMENT RECORDING --}}
                @if ($currentOrder->status === 'delivered' && !$currentOrder->payment)
                    <div class="mt-4 p-5 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/30 dark:to-emerald-950/30 rounded-xl border border-green-200 dark:border-green-800">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-green-900 dark:text-green-300">Record Payment</p>
                                <p class="text-xs text-green-700 dark:text-green-400">Complete this to finish the order</p>
                            </div>
                        </div>

                        @if ($currentOrder->commission_amount > 0)
                            <div class="mb-3 p-2.5 bg-orange-100 dark:bg-orange-950/30 rounded-lg flex items-start gap-2">
                                <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-[11px] text-orange-800 dark:text-orange-300 leading-relaxed">
                                    <strong>Platform fee:</strong> ₱{{ number_format($currentOrder->commission_amount, 2) }}
                                    ({{ $currentOrder->commission_rate }}%) is deducted.
                                    Bayaran mo sa restaurant ay <strong>₱{{ number_format($currentOrder->restaurant_earnings, 2) }}</strong> lamang.
                                </p>
                            </div>
                        @endif

                        <div class="bg-white/60 dark:bg-dark-800/60 rounded-lg p-3 mb-3 text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-neutral-400">Paid to restaurant</span>
                                <span class="font-semibold text-red-600 dark:text-red-400">₱{{ number_format($currentOrder->restaurant_earnings ?? $currentOrder->food_cost, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-neutral-400">Collected from customer</span>
                                <span class="font-semibold text-gray-900 dark:text-neutral-100">₱{{ number_format($currentOrder->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-green-200 dark:border-green-800">
                                <span class="font-semibold text-green-900 dark:text-green-300">Your earnings</span>
                                <span class="font-bold text-green-600 dark:text-green-400">₱{{ number_format($currentOrder->delivery_fee, 2) }}</span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('rider.orders.payment', $currentOrder) }}">
                            @csrf
                            <button class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-3 rounded-xl hover:from-green-600 hover:to-green-700 font-semibold text-sm shadow-md hover:shadow-lg active:scale-98 transition transform">
                                Record Payment
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- WAITING / OFFLINE STATE --}}
    {{-- ============================================ --}}
    @if (!$currentOrder && $rider->is_online)
        <div x-show="offers.length === 0" class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-10 text-center transition-colors">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-950/40 mb-4">
                <span class="w-3 h-3 rounded-full bg-green-500 animate-ping"></span>
            </div>
            <h3 class="font-bold text-gray-900 dark:text-neutral-100 mb-1">You're Online</h3>
            <p class="text-sm text-gray-500 dark:text-neutral-400">Waiting for delivery offers...</p>
            <p class="text-xs text-gray-400 dark:text-neutral-500 mt-4">Offers will appear on this page automatically</p>
        </div>
    @elseif (!$rider->is_online)
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-10 text-center transition-colors">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-dark-850 mb-4">
                <svg class="w-8 h-8 text-gray-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 dark:text-neutral-100 mb-1">You're Offline</h3>
            <p class="text-sm text-gray-500 dark:text-neutral-400">Toggle online to start receiving delivery offers</p>
            <button @click="toggleOnline()"
                    class="mt-4 bg-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm hover:bg-orange-700 active:scale-98 transition transform">
                Go Online
            </button>
        </div>
    @endif

    

</div>
@endsection

@push('scripts')
<style>
    .typing-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #9ca3af;
        animation: bounceDot 1.4s infinite ease-in-out both;
    }
    @keyframes bounceDot {
        0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
        40% { transform: scale(1); opacity: 1; }
    }
    .active\:scale-98:active {
        transform: scale(0.98);
    }
    .chat-scroll::-webkit-scrollbar {
        display: none;
    }
    .chat-scroll {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .chat-message {
        animation: slideIn 0.25s ease-out;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<script>
function riderDash(riderId) {
    return {
        online: {{ $rider->is_online ? 'true' : 'false' }},
        toggling: false,
        offers: [],
        processing: false,
        error: '',
        timer: null,

        init() {
            if (typeof window.Echo !== 'undefined') {
                window.Echo.private(`rider.${riderId}`)
                    .listen('.delivery.offer', (e) => {
                        this.loadOffer(parseInt(e.order_id));
                    });
            }

            this.timer = setInterval(() => {
                this.tickCountdowns();
            }, 1000);

            this.loadExistingOffers();

            if (this.online) {
                this.startLocationSharing();
            }
        },

        async loadExistingOffers() {
            try {
                const res = await fetch('/rider/offers/active', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                if (!res.ok) return;

                const data = await res.json();
                if (data.offers) {
                    this.offers = data.offers.map(o => ({
                        ...o,
                        food_cost: parseFloat(o.food_cost || 0).toFixed(2),
                        delivery_fee: parseFloat(o.delivery_fee || 0).toFixed(2),
                        total_amount: parseFloat(o.total_amount || 0).toFixed(2),
                        secondsLeft: Math.floor(parseInt(o.secondsLeft) || 0),
                        expires_in: Math.floor(parseInt(o.expires_in) || 120),
                    }));
                }
            } catch (err) {
                // silent
            }
        },

        async loadOffer(orderId) {
            if (this.offers.find(o => o.order_id === orderId)) return;

            try {
                const res = await fetch(`/rider/offers/${orderId}/details`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                if (!res.ok) return;

                const data = await res.json();
                if (!data.ok) return;

                const expiresIn = Math.floor(parseInt(data.offer.expires_in) || 120);

                this.offers.push({
                    order_id: parseInt(data.offer.order_id),
                    radius_km: Math.floor(parseFloat(data.offer.radius_km) || 0),
                    expires_in: expiresIn,
                    secondsLeft: expiresIn,
                    restaurant: data.offer.restaurant || '',
                    restaurant_address: data.offer.restaurant_address || '',
                    delivery_address: data.offer.delivery_address || '',
                    food_cost: parseFloat(data.offer.food_cost || 0).toFixed(2),
                    delivery_fee: parseFloat(data.offer.delivery_fee || 0).toFixed(2),
                    total_amount: parseFloat(data.offer.total_amount || 0).toFixed(2),
                    items_count: parseInt(data.offer.items_count) || 0,
                    distance_km: data.offer.distance_km ? parseFloat(data.offer.distance_km).toFixed(2) : null,
                });

                this.playBeep();
            } catch (err) {
                console.error('Failed to load offer:', err);
            }
        },

        tickCountdowns() {
            if (this.offers.length === 0) return;

            this.offers = this.offers.map(offer => {
                offer.secondsLeft = Math.max(0, Math.floor(offer.secondsLeft) - 1);
                return offer;
            }).filter(offer => offer.secondsLeft > 0);
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

        async toggleOnline() {
            if (this.toggling) return;

            @if ($currentOrder)
                if (this.online) {
                    alert('⚠️ Cannot go offline — you have an active delivery (Order #{{ $currentOrder->id }}). Complete it first.');
                    return;
                }
            @endif

            this.toggling = true;

            try {
                const res = await fetch('{{ route('rider.online') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                const data = await res.json();

                if (!data.ok) {
                    alert('⚠️ ' + (data.message || 'Cannot toggle status'));
                    this.online = data.is_online;
                    this.toggling = false;
                    return;
                }

                this.online = data.is_online;
                if (this.online) this.startLocationSharing();
            } catch (err) {
                console.error(err);
            }

            this.toggling = false;
        },

        startLocationSharing() {
            const send = () => {
                navigator.geolocation.getCurrentPosition(pos => {
                    fetch('{{ route('rider.location') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            latitude: pos.coords.latitude,
                            longitude: pos.coords.longitude
                        })
                    });
                }, () => {
                    console.warn('Could not get location');
                });
            };
            send();
            setInterval(send, 10000);
        },

        async acceptOffer(orderId) {
            if (this.processing) return;
            this.processing = true;
            this.error = '';

            try {
                const res = await fetch(`/rider/orders/${orderId}/accept`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await res.json();

                if (data.ok) {
                    clearInterval(this.timer);
                    window.location.href = '{{ route('rider.dashboard') }}';
                } else {
                    this.error = data.message || 'Could not accept offer.';
                    this.processing = false;
                    this.removeOffer(orderId);
                }
            } catch (err) {
                console.error(err);
                this.error = 'Network error. Please try again.';
                this.processing = false;
            }
        },

        declineOffer(orderId) {
            this.removeOffer(orderId);
        },

        removeOffer(orderId) {
            this.offers = this.offers.filter(o => o.order_id !== orderId);
        }
    }
}

function chatBox(orderId) {
    return {
        isOpen: false,
        loading: true,
        sending: false,
        messages: [],
        newMessage: '',
        unread: 0,
        currentUserId: {{ auth()->id() }},

        posX: 0,
        posY: 0,
        isDragging: false,
        dragStartX: 0,
        dragStartY: 0,
        dragStartPosX: 0,
        dragStartPosY: 0,
        hasMoved: false,
        bubbleSize: 56,
        storageKey: 'fooddash_chat_bubble_pos',
        currentUserName: '{{ auth()->user()->name }}',
        currentUserAvatar: '{{ auth()->user()->avatar_url }}',
        currentUserInitials: '{{ auth()->user()->initials }}',
        currentUserAvatarColor: '{{ auth()->user()->avatar_color }}',
        channel: null,
        typingName: '',
        typingTimeout: null,
        isTypingSent: false,
        isAtBottom: true,
        listenerAttached: false,

                init() {
            // ⭐ Initialize drag position
            this.initPosition();

            if (window.Echo) {
                window.Echo.leave(`order.${orderId}.chat`);
            }

            this.loadMessages();

            setTimeout(() => {
                this.subscribeToChat(orderId);
            }, 300);

            // Handle window resize para hindi lumabas sa screen
            window.addEventListener('resize', () => this.clampPosition());
        },

        // ⭐ DRAG: Initialize position from storage or default
        initPosition() {
            const saved = localStorage.getItem(this.storageKey);

            if (saved) {
                try {
                    const pos = JSON.parse(saved);
                    this.posX = pos.x;
                    this.posY = pos.y;
                    this.$nextTick(() => this.clampPosition());
                    return;
                } catch (e) {
                    // Fall through to default
                }
            }

            // Default: bottom-right
            this.$nextTick(() => {
                const winW = window.innerWidth;
                const winH = window.innerHeight;
                this.posX = winW - this.bubbleSize - 16; // 16px margin
                this.posY = winH - this.bubbleSize - 80; // 80px offset for bottom nav
            });
        },

        // ⭐ DRAG: Start dragging
        startDrag(e) {
            // Wag mag-start ng drag kung naka-open ang chat panel
            if (this.isOpen) return;

            // Ignore right-click
            if (e.type === 'mousedown' && e.button !== 0) return;

            this.isDragging = true;
            this.hasMoved = false;

            const point = e.touches ? e.touches[0] : e;
            this.dragStartX = point.clientX;
            this.dragStartY = point.clientY;
            this.dragStartPosX = this.posX;
            this.dragStartPosY = this.posY;

            // Attach global listeners
            if (e.type === 'mousedown') {
                document.addEventListener('mousemove', this.onDragMove);
                document.addEventListener('mouseup', this.onDragEnd);
            } else {
                document.addEventListener('touchmove', this.onDragMove, { passive: false });
                document.addEventListener('touchend', this.onDragEnd);
            }

            e.preventDefault();
        },

        // ⭐ DRAG: Move
        onDragMove(e) {
            if (!this.isDragging) return;

            const point = e.touches ? e.touches[0] : e;
            const dx = point.clientX - this.dragStartX;
            const dy = point.clientY - this.dragStartY;

            // Threshold para ma-detect kung drag o click
            if (Math.abs(dx) > 5 || Math.abs(dy) > 5) {
                this.hasMoved = true;
            }

            this.posX = this.dragStartPosX + dx;
            this.posY = this.dragStartPosY + dy;

            this.clampPosition();

            if (e.cancelable) e.preventDefault();
        },

        // ⭐ DRAG: End
        onDragEnd(e) {
            if (!this.isDragging) return;

            this.isDragging = false;

            // Remove global listeners
            document.removeEventListener('mousemove', this.onDragMove);
            document.removeEventListener('mouseup', this.onDragEnd);
            document.removeEventListener('touchmove', this.onDragMove);
            document.removeEventListener('touchend', this.onDragEnd);

            // Save position
            if (this.hasMoved) {
                localStorage.setItem(this.storageKey, JSON.stringify({
                    x: this.posX,
                    y: this.posY,
                }));
            }
        },

        // ⭐ DRAG: Clamp position sa loob ng screen
        clampPosition() {
            const winW = window.innerWidth;
            const winH = window.innerHeight;
            const margin = 8;

            // Clamp X
            if (this.posX < margin) this.posX = margin;
            if (this.posX > winW - this.bubbleSize - margin) {
                this.posX = winW - this.bubbleSize - margin;
            }

            // Clamp Y (may bottom offset para sa mobile nav)
            const bottomOffset = winW < 768 ? 80 : 16;
            if (this.posY < margin) this.posY = margin;
            if (this.posY > winH - this.bubbleSize - bottomOffset) {
                this.posY = winH - this.bubbleSize - bottomOffset;
            }
        },

        subscribeToChat(orderId) {
            if (this.listenerAttached) return;

            if (typeof window.Echo === 'undefined') {
                console.error('Echo not available');
                return;
            }

            const chatChannel = window.Echo.private(`order.${orderId}.chat`);

            chatChannel.listen('.message.sent', (e) => {
                if (e.sender_id === this.currentUserId) return;

                e.status = 'received';
                this.messages.push(e);

                if (!this.isOpen || !this.isAtBottom) {
                    this.unread++;
                    this.playBeep();
                } else {
                    this.markAsRead();
                }

                if (this.isAtBottom) {
                    this.$nextTick(() => this.scrollToBottom());
                }
            });

            chatChannel.listen('.user.typing', (e) => {
                if (e.user_id === this.currentUserId) return;

                const wasTyping = this.typingName;
                this.typingName = e.is_typing ? e.user_name : '';

                if (e.is_typing && !wasTyping && this.isAtBottom) {
                    this.$nextTick(() => this.scrollToBottom());
                }
            });

            chatChannel.listen('.messages.read', (e) => {
                if (e.reader_id === this.currentUserId) return;
                this.messages.forEach(m => {
                    if (e.message_ids.includes(m.id) && m.sender_id === this.currentUserId) {
                        m.status = 'seen';
                        m.read_at = e.read_at;
                    }
                });
            });

            this.channel = chatChannel;
            this.listenerAttached = true;
        },

        playBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.value = 900;
                gain.gain.value = 0.15;
                osc.start();
                setTimeout(() => { osc.stop(); ctx.close(); }, 150);
            } catch (e) { /* silent */ }
        },

        async loadMessages() {
            try {
                const res = await fetch('{{ route('rider.chat.index', $currentOrder ?? 0) }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                const data = await res.json();
                this.messages = (data.messages || []).map(m => ({
                    ...m,
                    status: m.sender_id === this.currentUserId
                        ? (m.read_at ? 'seen' : (m.delivered_at ? 'delivered' : 'sent'))
                        : 'received',
                }));
                this.unread = 0;
                this.loading = false;
                this.$nextTick(() => this.scrollToBottom());
            } catch (err) {
                console.error('Failed to load messages', err);
                this.loading = false;
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.sending) return;

            this.sending = true;
            const body = this.newMessage.trim();
            this.newMessage = '';
            this.stopTyping();

            const optimisticId = 'temp-' + Date.now();
            const optimisticMsg = {
                id: optimisticId,
                sender_id: this.currentUserId,
                sender_name: this.currentUserName,
                sender_avatar_url: this.currentUserAvatar,
                sender_initials: this.currentUserInitials,
                sender_avatar_color: this.currentUserAvatarColor,
                body: body,
                created_at: new Date().toISOString(),
                created_at_human: 'just now',
                status: 'sent',
            };
            this.messages.push(optimisticMsg);
            this.scrollToBottom();

            try {
                const res = await fetch('{{ route('rider.chat.store', $currentOrder ?? 0) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ body })
                });
                const data = await res.json();
                const idx = this.messages.findIndex(m => m.id === optimisticId);
                if (idx !== -1) {
                    this.messages[idx] = { ...data.message, status: 'delivered' };
                }
            } catch (err) {
                console.error('Failed to send message', err);
                const idx = this.messages.findIndex(m => m.id === optimisticId);
                if (idx !== -1) this.messages.splice(idx, 1);
                this.newMessage = body;
            }
            this.sending = false;
        },

        onTypingInput() {
            if (!this.isTypingSent && this.newMessage.trim()) {
                this.sendTyping(true);
                this.isTypingSent = true;
            }
            clearTimeout(this.typingTimeout);
            this.typingTimeout = setTimeout(() => {
                this.stopTyping();
            }, 2000);
        },

        stopTyping() {
            if (this.isTypingSent) {
                this.sendTyping(false);
                this.isTypingSent = false;
            }
            clearTimeout(this.typingTimeout);
        },

        async sendTyping(isTyping) {
            try {
                await fetch('{{ route('rider.chat.typing', $currentOrder ?? 0) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ is_typing: isTyping })
                });
            } catch (err) { /* silent */ }
        },

        async markAsRead() {
            try {
                await fetch('{{ route('rider.chat.mark-read', $currentOrder ?? 0) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                this.unread = 0;
            } catch (err) { /* silent */ }
        },

        onScroll() {
            const el = this.$refs.messagesContainer;
            if (!el) return;
            this.isAtBottom = (el.scrollHeight - el.scrollTop - el.clientHeight) < 50;
            if (this.isAtBottom && this.unread > 0) this.markAsRead();
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.messagesContainer;
                if (el) {
                    el.scrollTop = el.scrollHeight;
                    this.isAtBottom = true;
                }
            });
        }
    }
}
</script>
@endpush