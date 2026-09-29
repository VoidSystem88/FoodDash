@extends('layouts.app')

@section('content')
<div x-data="riderDash({{ $rider->id }})" x-init="init()" class="max-w-3xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-500 to-orange-600 rounded-2xl shadow-xl text-white">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-yellow-300 rounded-full blur-3xl opacity-20 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        @if (auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}"
                                 class="w-14 h-14 rounded-full object-cover border-2 border-white/40 shadow-md">
                        @else
                            <div class="w-14 h-14 rounded-full bg-white/20 backdrop-blur flex items-center justify-center text-white text-xl font-bold border-2 border-white/40 shadow-md">
                                {{ auth()->user()->initials }}
                            </div>
                        @endif

                        <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full border-2 border-white shadow-sm"
                              :class="online ? 'bg-green-400' : 'bg-neutral-400'"></span>
                    </div>

                    <div>
                        <p class="text-xs text-white/80">Welcome back,</p>
                        <h1 class="text-xl font-bold">{{ auth()->user()->name }}</h1>
                    </div>
                </div>

                @if ($currentOrder)
                    <button type="button" disabled
                            class="px-5 py-2.5 rounded-full font-semibold text-sm shadow-md opacity-70 cursor-not-allowed bg-white text-orange-600 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span>Online</span>
                    </button>
                @else
                    <button @click="toggleOnline()"
                            :disabled="toggling"
                            :class="online ? 'bg-white text-orange-600' : 'bg-white/20 text-white border border-white/30'"
                            class="px-5 py-2.5 rounded-full font-semibold text-sm shadow-md hover:scale-105 active:scale-95 transition transform disabled:opacity-50 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" :class="online ? 'bg-green-500 animate-pulse' : 'bg-neutral-400'"></span>
                        <span x-text="online ? 'Online' : 'Offline'"></span>
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-white/20">
                <div>
                    <p class="text-xs text-white/80">Today's Deliveries</p>
                    <p class="text-2xl font-bold">{{ $completedToday }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-xs text-white/80">Today's Earnings</p>
                    <p class="text-2xl font-bold">₱{{ number_format($earningsToday, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- STAT CARDS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-4 hover:shadow-md transition">
            <div class="w-9 h-9 rounded-lg bg-neutral-100 dark:bg-[#262626] flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-neutral-700 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs text-neutral-500 dark:text-neutral-400">Completed</p>
            <p class="text-xl font-bold text-neutral-900 dark:text-white">{{ $completedToday }}</p>
        </div>

        <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-4 hover:shadow-md transition">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center mb-2 shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs text-neutral-500 dark:text-neutral-400">Earnings</p>
            <p class="text-xl font-bold bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent">₱{{ number_format($earningsToday, 0) }}</p>
        </div>

        <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-4 hover:shadow-md transition">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center mb-2"
                 :class="online ? 'bg-gradient-to-br from-orange-500 to-orange-600 shadow-sm' : 'bg-neutral-100 dark:bg-[#262626]'">
                <svg class="w-5 h-5" :class="online ? 'text-white' : 'text-neutral-500 dark:text-neutral-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <p class="text-xs text-neutral-500 dark:text-neutral-400">Status</p>
            <p class="text-sm font-bold mt-1"
               :class="online ? 'text-orange-600 dark:text-orange-400' : 'text-neutral-500 dark:text-neutral-400'"
               x-text="online ? 'Ready' : 'Offline'"></p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- AVAILABLE OFFERS --}}
    {{-- ============================================ --}}
    <template x-if="offers.length > 0">
        <div>
            <h2 class="font-bold text-neutral-900 dark:text-white flex items-center gap-2 mb-3">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                </span>
                Available Offers
                <span class="text-xs bg-gradient-to-r from-orange-500 to-orange-600 text-white px-2.5 py-0.5 rounded-full font-bold shadow-sm"
                      x-text="offers.length"></span>
            </h2>

            <div class="space-y-3">
                <template x-for="offer in offers" :key="offer.order_id">
                    <div class="bg-white dark:bg-[#141414] rounded-2xl shadow-md border-2 border-orange-300 dark:border-orange-800/70 overflow-hidden hover:shadow-lg transition-all">

                        <div class="h-1.5 bg-neutral-100 dark:bg-[#0a0a0a] overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-orange-500 to-orange-400 transition-all"
                                 :style="`width: ${(offer.secondsLeft / offer.expires_in) * 100}%`"></div>
                        </div>

                        <div class="px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white flex items-center justify-between">
                            <div>
                                <p class="font-bold text-sm" x-text="offer.restaurant"></p>
                                <p class="text-[10px] text-white/80 uppercase tracking-wide font-medium">Delivery Offer</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-white/80">Expires in</p>
                                <p class="text-base font-bold" x-text="offer.secondsLeft + 's'"></p>
                            </div>
                        </div>

                        <div class="p-4 space-y-3">
                            <div class="flex items-start gap-2">
                                <div class="w-7 h-7 rounded-lg bg-neutral-900 dark:bg-neutral-800 flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">Pickup From</p>
                                    <p class="text-sm font-semibold text-neutral-900 dark:text-white line-clamp-1" x-text="offer.restaurant"></p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 line-clamp-1" x-text="offer.restaurant_address"></p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2">
                                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">Deliver To</p>
                                    <p class="text-sm text-neutral-700 dark:text-neutral-300 line-clamp-2" x-text="offer.delivery_address"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2 bg-neutral-50 dark:bg-[#0a0a0a] rounded-xl p-3 text-center border border-neutral-100 dark:border-[#262626]">
                                <div>
                                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">Food</p>
                                    <p class="text-sm font-bold text-neutral-900 dark:text-white" x-text="'₱' + offer.food_cost"></p>
                                </div>
                                <div class="border-x border-neutral-200 dark:border-[#262626]">
                                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">Fee</p>
                                    <p class="text-sm font-bold bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent" x-text="'₱' + offer.delivery_fee"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">Items</p>
                                    <p class="text-sm font-bold text-orange-600 dark:text-orange-400" x-text="offer.items_count"></p>
                                </div>
                            </div>

                            <div class="flex gap-2 pt-1">
                                <button @click="declineOffer(offer.order_id)" type="button"
                                        class="flex-1 border border-neutral-300 dark:border-[#262626] text-neutral-700 dark:text-neutral-300 py-2.5 rounded-xl text-sm font-semibold hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] transition">
                                    Decline
                                </button>
                                <button @click="acceptOffer(offer.order_id)" type="button"
                                        class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white py-2.5 rounded-xl text-sm font-bold shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform">
                                    Accept
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
        <div class="bg-white dark:bg-[#141414] rounded-2xl shadow-lg overflow-hidden border border-neutral-200 dark:border-[#262626]">

            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-white/80 uppercase tracking-wide font-medium">Active Delivery</p>
                        <h2 class="text-lg font-bold">Order #{{ $currentOrder->id }}</h2>
                    </div>
                    <span class="text-xs px-3 py-1.5 rounded-full bg-white/20 backdrop-blur font-semibold uppercase tracking-wide border border-white/30">
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

            <div class="p-6 space-y-5">

                {{-- PICKUP --}}
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-xl bg-neutral-900 dark:bg-neutral-800 flex items-center justify-center flex-shrink-0 shadow-md">
                        <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold mb-0.5">Pickup From</p>
                        <p class="font-bold text-neutral-900 dark:text-white">{{ $currentOrder->restaurant->name ?? '—' }}</p>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-0.5">{{ $currentOrder->restaurant->address ?? '' }}</p>

                        <div class="flex items-center gap-2 mt-2 bg-neutral-900 dark:bg-neutral-800 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="text-xs font-medium text-neutral-300">Pay restaurant:</span>
                            <span class="text-sm font-bold text-orange-500">₱{{ number_format($currentOrder->restaurant_earnings ?? $currentOrder->food_cost, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- DROPOFF --}}
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold mb-0.5">Deliver To</p>
                        <p class="font-bold text-neutral-900 dark:text-white">{{ $currentOrder->customer->name ?? '—' }}</p>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-0.5">{{ $currentOrder->delivery_address }}</p>

                        <div class="flex items-center gap-2 mt-2 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="text-xs font-medium text-white/90">Collect from customer:</span>
                            <span class="text-sm font-bold text-white">₱{{ number_format($currentOrder->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex flex-wrap gap-2 pt-2">
                    @if ($currentOrder->status === 'rider_assigned')
                        <form method="POST" action="{{ route('rider.orders.status', $currentOrder) }}" class="flex-1">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="picked_up">
                            <button class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-3 rounded-xl font-bold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Mark as Picked Up
                            </button>
                        </form>
                    @elseif ($currentOrder->status === 'picked_up')
                        <form method="POST" action="{{ route('rider.orders.status', $currentOrder) }}" class="flex-1">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="out_for_delivery">
                            <button class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-3 rounded-xl font-bold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Out for Delivery
                            </button>
                        </form>
                    @elseif ($currentOrder->status === 'out_for_delivery')
                        <form method="POST" action="{{ route('rider.orders.status', $currentOrder) }}" class="flex-1">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="delivered">
                            <button class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-3 rounded-xl font-bold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Mark as Delivered
                            </button>
                        </form>
                    @endif
                </div>

                {{-- PAYMENT RECORDING --}}
                @if ($currentOrder->status === 'delivered' && !$currentOrder->payment)
                    <div class="mt-2 p-5 bg-gradient-to-br from-orange-50 to-orange-100/50 dark:from-orange-950/30 dark:to-orange-900/10 rounded-xl border border-orange-200 dark:border-orange-800">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-orange-900 dark:text-orange-300">Record Payment</p>
                                <p class="text-xs text-orange-700 dark:text-orange-400">Complete this to finish the order</p>
                            </div>
                        </div>

                        @if ($currentOrder->commission_amount > 0)
                            <div class="mb-3 p-2.5 bg-black dark:bg-neutral-900 rounded-lg flex items-start gap-2">
                                <svg class="w-3.5 h-3.5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-[11px] text-neutral-300 leading-relaxed">
                                    <strong class="text-orange-500">Platform fee:</strong> ₱{{ number_format($currentOrder->commission_amount, 2) }}
                                    ({{ $currentOrder->commission_rate }}%) is deducted.
                                    Bayaran mo sa restaurant ay <strong class="text-white">₱{{ number_format($currentOrder->restaurant_earnings, 2) }}</strong> lamang.
                                </p>
                            </div>
                        @endif

                        <div class="bg-white/60 dark:bg-[#0a0a0a]/60 rounded-lg p-3 mb-3 text-xs space-y-1 border border-orange-200/50 dark:border-orange-800/50">
                            <div class="flex justify-between">
                                <span class="text-neutral-600 dark:text-neutral-400">Paid to restaurant</span>
                                <span class="font-bold text-neutral-900 dark:text-white">₱{{ number_format($currentOrder->restaurant_earnings ?? $currentOrder->food_cost, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-neutral-600 dark:text-neutral-400">Collected from customer</span>
                                <span class="font-bold text-neutral-900 dark:text-white">₱{{ number_format($currentOrder->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-orange-200 dark:border-orange-800">
                                <span class="font-bold text-orange-900 dark:text-orange-300">Your earnings</span>
                                <span class="font-bold bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent">₱{{ number_format($currentOrder->delivery_fee, 2) }}</span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('rider.orders.payment', $currentOrder) }}">
                            @csrf
                            <button class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-3 rounded-xl font-bold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform">
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
        <div x-show="offers.length === 0" class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-10 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-950/40 dark:to-orange-900/20 mb-4">
                <span class="w-3 h-3 rounded-full bg-orange-500 animate-ping"></span>
            </div>
            <h3 class="font-bold text-neutral-900 dark:text-white mb-1">You're Online</h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Waiting for delivery offers...</p>
            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-4">Offers will appear on this page automatically</p>
        </div>
    @elseif (!$rider->is_online)
        <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-10 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-neutral-100 dark:bg-[#262626] mb-4">
                <svg class="w-8 h-8 text-neutral-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <h3 class="font-bold text-neutral-900 dark:text-white mb-1">You're Offline</h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Toggle online to start receiving offers</p>
            <button @click="toggleOnline()"
                    class="mt-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-95 transition transform">
                Go Online
            </button>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- FLOATING MAP (Draggable, Rider Only) --}}
    {{-- ============================================ --}}
    @if ($currentOrder)
        <div x-data="floatingMap({{ $currentOrder->id }})"
             x-init="init()"
             @keydown.escape.window="isMinimized = true"
             class="fixed z-[90] pointer-events-none"
             :style="`left: ${posX}px; top: ${posY}px;`">

            {{-- MAP PANEL --}}
            <div x-show="!isMinimized"
                 x-transition.opacity
                 class="pointer-events-auto bg-white dark:bg-[#141414] rounded-2xl shadow-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden"
                 style="width: 380px; height: 480px;">

                {{-- MAP HEADER (Draggable) --}}
                <div @mousedown="startDrag($event)"
                     @touchstart.passive="startDrag($event)"
                     class="bg-gradient-to-r from-orange-500 to-orange-600 px-3 py-2.5 flex items-center gap-2 select-none"
                     :class="isDragging ? 'cursor-grabbing' : 'cursor-grab'"
                     style="touch-action: none;">

                    {{-- Drag handle --}}
                    <svg class="w-4 h-4 text-white/70 flex-shrink-0 pointer-events-none" fill="currentColor" viewBox="0 0 20 20">
                        <circle cx="7" cy="5" r="1.5"/><circle cx="13" cy="5" r="1.5"/>
                        <circle cx="7" cy="10" r="1.5"/><circle cx="13" cy="10" r="1.5"/>
                        <circle cx="7" cy="15" r="1.5"/><circle cx="13" cy="15" r="1.5"/>
                    </svg>

                    {{-- Title --}}
                    <div class="flex items-center gap-2 flex-1 min-w-0 pointer-events-none">
                        <div class="w-6 h-6 rounded-md bg-white/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-white truncate">Live Navigation</p>
                            <p class="text-[10px] text-white/80" x-text="lastUpdated || 'Updating...'"></p>
                        </div>
                    </div>

                    {{-- Reset button --}}
                    <button @pointerdown.stop.prevent="resetPosition()"
                            @click.stop.prevent="resetPosition()"
                            type="button"
                            title="Center on screen"
                            class="w-7 h-7 rounded-md hover:bg-white/20 active:bg-white/30 flex items-center justify-center text-white transition flex-shrink-0 cursor-pointer"
                            style="touch-action: manipulation; -webkit-tap-highlight-color: transparent;">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>

                    {{-- Minimize button --}}
                    <button @pointerdown.stop.prevent="minimizePanel()"
                            @click.stop.prevent="minimizePanel()"
                            type="button"
                            title="Minimize"
                            class="w-7 h-7 rounded-md hover:bg-white/20 active:bg-white/30 flex items-center justify-center text-white transition flex-shrink-0 cursor-pointer"
                            style="touch-action: manipulation; -webkit-tap-highlight-color: transparent;">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                        </svg>
                    </button>
                </div>

                {{-- MAP CONTAINER --}}
                <div class="relative" style="height: 250px;">
                    <div id="riderMap" class="w-full h-full"></div>

                    {{-- Distance/ETA Badge --}}
                    <div class="absolute bottom-2 left-2 right-2 bg-white/95 dark:bg-[#0a0a0a]/95 backdrop-blur rounded-xl p-2.5 shadow-lg border border-neutral-200 dark:border-[#262626]">
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <p class="text-[9px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">Distance</p>
                                <p class="text-sm font-bold text-neutral-900 dark:text-white" x-text="distanceText || '—'"></p>
                            </div>
                            <div class="border-x border-neutral-200 dark:border-[#262626]">
                                <p class="text-[9px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">ETA</p>
                                <p class="text-sm font-bold text-orange-600 dark:text-orange-400" x-text="etaText || '—'"></p>
                            </div>
                            <div>
                                <p class="text-[9px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">Status</p>
                                <p class="text-sm font-bold text-neutral-900 dark:text-white" x-text="stageText"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ACTION BAR --}}
                <div class="p-2.5 border-t border-neutral-100 dark:border-[#262626] flex gap-2">
                    <button type="button"
                            @click="centerOnRider()"
                            title="Center on me"
                            class="flex-1 bg-neutral-100 dark:bg-[#262626] hover:bg-orange-100 dark:hover:bg-orange-950/40 text-neutral-700 dark:text-neutral-300 hover:text-orange-600 dark:hover:text-orange-400 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Center
                    </button>

                    <a :href="googleMapsUrl"
                       target="_blank"
                       title="Open in Google Maps"
                       class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white py-2 rounded-lg text-xs font-bold hover:from-orange-600 hover:to-orange-700 shadow-md transition flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Navigate
                    </a>
                </div>
            </div>

            {{-- MINIMIZED FAB (Draggable) --}}
            <button x-show="isMinimized"
                    x-transition
                    @mousedown.stop="startFabDrag($event)"
                    @touchstart.stop.passive="startFabDrag($event)"
                    @click="handleFabClick()"
                    type="button"
                    class="pointer-events-auto w-14 h-14 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 shadow-2xl flex items-center justify-center text-white hover:scale-105 active:scale-95 transition-transform border-2 border-white dark:border-[#141414]"
                    :class="fabDragging ? 'cursor-grabbing scale-110' : 'cursor-grab'"
                    style="touch-action: none; -webkit-tap-highlight-color: transparent;">
                <svg class="w-6 h-6 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                <span class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full bg-green-500 border-2 border-white dark:border-[#141414] animate-pulse pointer-events-none"></span>
            </button>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    .active\:scale-98:active { transform: scale(0.98); }
    .active\:scale-95:active { transform: scale(0.95); }
    #riderMap { background: #f3f4f6; }
    .dark #riderMap { background: #0a0a0a; }
</style>
<script>
// ============================================
// RIDER DASHBOARD FUNCTION
// ============================================
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
                        secondsLeft: Math.floor(parseInt(o.secondsLeft) || 0),
                        expires_in: Math.floor(parseInt(o.expires_in) || 120),
                    }));
                }
            } catch (err) { /* silent */ }
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
                    expires_in: expiresIn,
                    secondsLeft: expiresIn,
                    restaurant: data.offer.restaurant || '',
                    restaurant_address: data.offer.restaurant_address || '',
                    delivery_address: data.offer.delivery_address || '',
                    food_cost: parseFloat(data.offer.food_cost || 0).toFixed(2),
                    delivery_fee: parseFloat(data.offer.delivery_fee || 0).toFixed(2),
                    items_count: parseInt(data.offer.items_count) || 0,
                });

                this.playBeep();
            } catch (err) {
                console.error('Failed to load offer:', err);
            }
        },

        tickCountdowns() {
            if (this.offers.length === 0) return;
            this.offers = this.offers.map(o => {
                o.secondsLeft = Math.max(0, Math.floor(o.secondsLeft) - 1);
                return o;
            }).filter(o => o.secondsLeft > 0);
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
                }, () => {});
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
                    this.error = data.message || 'Could not accept.';
                    this.processing = false;
                    this.removeOffer(orderId);
                }
            } catch (err) {
                console.error(err);
                this.error = 'Network error.';
                this.processing = false;
            }
        },

        declineOffer(orderId) { this.removeOffer(orderId); },
        removeOffer(orderId) { this.offers = this.offers.filter(o => o.order_id !== orderId); }
    }
}

// ============================================
// FLOATING MAP FUNCTION
// ============================================
function floatingMap(orderId) {
    return {
        // Panel state
        isMinimized: false,
        isDragging: false,

        // Panel drag
        posX: 0,
        posY: 0,
        dragStartX: 0,
        dragStartY: 0,
        dragStartPosX: 0,
        dragStartPosY: 0,

        // FAB drag
        fabDragging: false,
        fabDragStartX: 0,
        fabDragStartY: 0,
        fabDragStartPosX: 0,
        fabDragStartPosY: 0,
        fabMoved: false,
        fabDragThreshold: 8,

        // Map refs
        map: null,
        riderMarker: null,
        restaurantMarker: null,
        customerMarker: null,
        routeLine: null,

        // Tracking data
        distanceText: '',
        etaText: '',
        lastUpdated: '',
        stageText: '',

        // Order coords (from Blade)
        orderId: {{ $currentOrder->id }},
        restaurantLat: {{ $currentOrder->restaurant->latitude }},
        restaurantLng: {{ $currentOrder->restaurant->longitude }},
        customerLat: {{ $currentOrder->delivery_lat }},
        customerLng: {{ $currentOrder->delivery_lng }},
        riderLat: {{ $currentOrder->rider->latitude ?? $currentOrder->restaurant->latitude }},
        riderLng: {{ $currentOrder->rider->longitude ?? $currentOrder->restaurant->longitude }},

        restaurantName: @json($currentOrder->restaurant->name),
        restaurantProfileUrl: @json($currentOrder->restaurant->profile_image_url),
        riderName: @json(auth()->user()->name),

        routeDebounce: null,
        storageKey: 'fooddash_rider_map_pos',
        fabStorageKey: 'fooddash_rider_fab_pos',

        // ============================================
        // INIT
        // ============================================
        init() {
            // Center panel sa screen agad
            this.$nextTick(() => {
                this.centerOnScreen();
            });

            // Set stage text
            this.updateStageText();

            // Init map after DOM ready
            this.$nextTick(() => {
                this.initMap();
            });

            // Listen for rider location updates
            if (typeof window.Echo !== 'undefined') {
                window.Echo.private(`order.${this.orderId}`)
                    .listen('.rider.location', (e) => {
                        this.updateRiderLocation(e.latitude, e.longitude);
                    });
            }

            // Resize handler
            window.addEventListener('resize', () => {
                if (this.isMinimized) {
                    this.clampFabPosition();
                } else {
                    this.clampPosition();
                }
            });
        },

        // ============================================
        // POSITIONING
        // ============================================
        centerOnScreen() {
            const winW = window.innerWidth;
            const winH = window.innerHeight;
            const panelW = this.isMinimized ? 56 : 380;
            const panelH = this.isMinimized ? 56 : 480;

            this.posX = Math.max(8, (winW - panelW) / 2);
            this.posY = Math.max(8, (winH - panelH) / 2);
        },

        resetPosition() {
            localStorage.removeItem(this.storageKey);
            localStorage.removeItem(this.fabStorageKey);
            this.centerOnScreen();
        },

        clampPosition() {
            const winW = window.innerWidth;
            const winH = window.innerHeight;
            const margin = 8;
            const panelW = this.isMinimized ? 56 : 380;
            const panelH = this.isMinimized ? 56 : 480;

            const bottomNavOffset = winW < 768 ? 80 : 0;
            const topNavOffset = 64;

            if (this.posX < margin) this.posX = margin;
            if (this.posX > winW - panelW - margin) {
                this.posX = winW - panelW - margin;
            }

            if (this.posY < topNavOffset + margin) {
                this.posY = topNavOffset + margin;
            }
            if (this.posY > winH - panelH - bottomNavOffset - margin) {
                this.posY = winH - panelH - bottomNavOffset - margin;
            }
        },

        clampFabPosition() {
            const winW = window.innerWidth;
            const winH = window.innerHeight;
            const fabSize = 56;
            const margin = 8;
            const bottomNavOffset = winW < 768 ? 80 : 0;
            const topNavOffset = 64;

            if (this.posX < margin) this.posX = margin;
            if (this.posX > winW - fabSize - margin) {
                this.posX = winW - fabSize - margin;
            }

            if (this.posY < topNavOffset + margin) {
                this.posY = topNavOffset + margin;
            }
            if (this.posY > winH - fabSize - bottomNavOffset - margin) {
                this.posY = winH - fabSize - bottomNavOffset - margin;
            }
        },

        // ============================================
        // PANEL DRAG
        // ============================================
        startDrag(e) {
            if (e.type === 'mousedown' && e.button !== 0) return;
            this.isDragging = true;

            const point = e.touches ? e.touches[0] : e;
            this.dragStartX = point.clientX;
            this.dragStartY = point.clientY;
            this.dragStartPosX = this.posX;
            this.dragStartPosY = this.posY;

            if (e.type === 'mousedown') {
                document.addEventListener('mousemove', this.onDragMove);
                document.addEventListener('mouseup', this.onDragEnd);
            } else {
                document.addEventListener('touchmove', this.onDragMove, { passive: false });
                document.addEventListener('touchend', this.onDragEnd);
            }

            e.preventDefault();
        },

        onDragMove(e) {
            if (!this.isDragging) return;

            const point = e.touches ? e.touches[0] : e;
            const dx = point.clientX - this.dragStartX;
            const dy = point.clientY - this.dragStartY;

            this.posX = this.dragStartPosX + dx;
            this.posY = this.dragStartPosY + dy;

            this.clampPosition();

            if (e.cancelable) e.preventDefault();
        },

        onDragEnd() {
            if (!this.isDragging) return;
            this.isDragging = false;

            document.removeEventListener('mousemove', this.onDragMove);
            document.removeEventListener('mouseup', this.onDragEnd);
            document.removeEventListener('touchmove', this.onDragMove);
            document.removeEventListener('touchend', this.onDragEnd);

            // Smart snap sa edge
            const winW = window.innerWidth;
            const panelW = this.isMinimized ? 56 : 380;
            const snapThreshold = 40;

            if (this.posX < snapThreshold) {
                this.posX = 8;
            } else if (this.posX > winW - panelW - snapThreshold) {
                this.posX = winW - panelW - 8;
            }

            localStorage.setItem(this.storageKey, JSON.stringify({
                x: this.posX,
                y: this.posY,
            }));
        },

        // ============================================
        // FAB DRAG
        // ============================================
        startFabDrag(e) {
            if (e.type === 'mousedown' && e.button !== 0) return;

            this.fabDragging = true;
            this.fabMoved = false;

            const point = e.touches ? e.touches[0] : e;
            this.fabDragStartX = point.clientX;
            this.fabDragStartY = point.clientY;
            this.fabDragStartPosX = this.posX;
            this.fabDragStartPosY = this.posY;

            if (e.type === 'mousedown') {
                document.addEventListener('mousemove', this.onFabDragMove);
                document.addEventListener('mouseup', this.onFabDragEnd);
            } else {
                document.addEventListener('touchmove', this.onFabDragMove, { passive: false });
                document.addEventListener('touchend', this.onFabDragEnd);
            }

            e.preventDefault();
        },

        onFabDragMove(e) {
            if (!this.fabDragging) return;

            const point = e.touches ? e.touches[0] : e;
            const dx = point.clientX - this.fabDragStartX;
            const dy = point.clientY - this.fabDragStartY;

            if (Math.abs(dx) > this.fabDragThreshold || Math.abs(dy) > this.fabDragThreshold) {
                this.fabMoved = true;
            }

            this.posX = this.fabDragStartPosX + dx;
            this.posY = this.fabDragStartPosY + dy;

            this.clampFabPosition();

            if (e.cancelable) e.preventDefault();
        },

        onFabDragEnd() {
            if (!this.fabDragging) return;
            this.fabDragging = false;

            document.removeEventListener('mousemove', this.onFabDragMove);
            document.removeEventListener('mouseup', this.onFabDragEnd);
            document.removeEventListener('touchmove', this.onFabDragMove);
            document.removeEventListener('touchend', this.onFabDragEnd);

            // Auto-snap sa nearest horizontal edge
            if (this.fabMoved) {
                const winW = window.innerWidth;
                const fabSize = 56;
                const margin = 16;

                const centerX = this.posX + fabSize / 2;
                if (centerX < winW / 2) {
                    this.posX = margin;
                } else {
                    this.posX = winW - fabSize - margin;
                }

                localStorage.setItem(this.fabStorageKey, JSON.stringify({
                    x: this.posX,
                    y: this.posY,
                }));
            }
        },

        handleFabClick() {
            if (this.fabMoved) {
                this.fabMoved = false;
                return;
            }
            this.openPanel();
        },

        // ============================================
        // PANEL MINIMIZE / OPEN
        // ============================================
        minimizePanel() {
            this.isMinimized = true;

            this.$nextTick(() => {
                // I-restore ang FAB position kung meron
                const saved = localStorage.getItem(this.fabStorageKey);
                if (saved) {
                    try {
                        const pos = JSON.parse(saved);
                        this.posX = pos.x;
                        this.posY = pos.y;
                        this.clampFabPosition();
                        return;
                    } catch (e) { /* fall through */ }
                }

                // Default: bottom-right
                const winW = window.innerWidth;
                const winH = window.innerHeight;
                const fabSize = 56;
                const margin = 16;
                const bottomOffset = winW < 768 ? 80 : 16;

                this.posX = winW - fabSize - margin;
                this.posY = winH - fabSize - bottomOffset - margin;
            });
        },

        openPanel() {
            this.isMinimized = false;
            this.$nextTick(() => {
                this.centerOnScreen();
            });
        },

        // ============================================
        // MAP INITIALIZATION
        // ============================================
        initMap() {
            const mapContainer = document.getElementById('riderMap');
            if (!mapContainer) return;

            if (mapContainer._leaflet_id) {
                mapContainer._leaflet_id = null;
            }

            this.map = L.map('riderMap', {
                zoomControl: false,
                attributionControl: false,
            }).setView([this.riderLat, this.riderLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(this.map);

            L.control.zoom({ position: 'bottomright' }).addTo(this.map);

            // Restaurant icon
            const restaurantIcon = L.divIcon({
                html: `
                    <div style="position:relative;width:50px;height:50px;">
                        <div style="width:50px;height:50px;border-radius:50%;overflow:hidden;border:4px solid #ea580c;box-shadow:0 4px 10px rgba(234,88,12,0.5), 0 2px 6px rgba(0,0,0,0.3);background:white;">
                            ${this.restaurantProfileUrl
                                ? `<img src="${this.restaurantProfileUrl}" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none';this.parentElement.innerHTML='<div style=&quot;width:100%;height:100%;background:#ea580c;color:white;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:bold;&quot;>R</div>';" alt="R">`
                                : `<div style="width:100%;height:100%;background:#ea580c;color:white;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:bold;">R</div>`
                            }
                        </div>
                        <div style="position:absolute;bottom:-2px;right:-2px;background:#ea580c;color:white;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;border:2px solid white;box-shadow:0 2px 4px rgba(0,0,0,0.3);">🏪</div>
                    </div>
                `,
                className: '',
                iconSize: [50, 50],
                iconAnchor: [25, 25],
            });

            // Customer icon
            const customerIcon = L.divIcon({
                html: `
                    <div style="width:44px;height:44px;">
                        <img src="/images/cusicon.png" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" style="width:44px;height:44px;object-fit:contain;filter:drop-shadow(0 3px 6px rgba(0,0,0,0.4));" alt="C">
                        <div style="display:none;width:44px;height:44px;background:#10b981;color:white;border-radius:50%;align-items:center;justify-content:center;font-size:20px;border:3px solid white;box-shadow:0 3px 6px rgba(0,0,0,0.4);">🏠</div>
                    </div>
                `,
                className: '',
                iconSize: [44, 44],
                iconAnchor: [22, 22],
            });

            // Rider icon (with pulse)
            const riderIcon = L.divIcon({
                html: `
                    <div style="position:relative;width:56px;height:56px;">
                        <div style="position:absolute;inset:0;border-radius:50%;background:rgba(234,88,12,0.3);animation:pulseRider 2s ease-out infinite;"></div>
                        <img src="/images/ridicon.png" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" style="position:relative;width:56px;height:56px;object-fit:contain;filter:drop-shadow(0 4px 8px rgba(234,88,12,0.6));" alt="R">
                        <div style="display:none;position:relative;width:56px;height:56px;background:#ea580c;color:white;border-radius:50%;align-items:center;justify-content:center;font-size:26px;border:3px solid white;box-shadow:0 4px 8px rgba(0,0,0,0.4);">🛵</div>
                    </div>
                    <style>
                        @keyframes pulseRider {
                            0% { transform: scale(0.9); opacity: 0.7; }
                            100% { transform: scale(1.5); opacity: 0; }
                        }
                    </style>
                `,
                className: '',
                iconSize: [56, 56],
                iconAnchor: [28, 28],
            });

            // Add markers
            this.restaurantMarker = L.marker([this.restaurantLat, this.restaurantLng], { icon: restaurantIcon })
                .addTo(this.map)
                .bindPopup(`🏪 ${this.restaurantName}`);

            this.customerMarker = L.marker([this.customerLat, this.customerLng], { icon: customerIcon })
                .addTo(this.map)
                .bindPopup('🏠 Customer');

            this.riderMarker = L.marker([this.riderLat, this.riderLng], { icon: riderIcon })
                .addTo(this.map)
                .bindPopup(`🛵 ${this.riderName}`);

            // Draw initial route
            this.drawRoute(
                this.restaurantLat, this.restaurantLng,
                this.customerLat, this.customerLng
            );

            this.lastUpdated = 'Updated just now';
        },

        async drawRoute(fromLat, fromLng, toLat, toLng) {
            try {
                const url = `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${toLng},${toLat}?overview=full&geometries=geojson`;
                const res = await fetch(url);
                const data = await res.json();

                if (!data.routes || data.routes.length === 0) {
                    this.drawStraightLine(fromLat, fromLng, toLat, toLng);
                    return;
                }

                const route = data.routes[0];
                const coords = route.geometry.coordinates;
                const latlngs = coords.map(c => [c[1], c[0]]);

                if (this.routeLine) {
                    this.map.removeLayer(this.routeLine);
                }

                this.routeLine = L.polyline(latlngs, {
                    color: '#ea580c',
                    weight: 5,
                    opacity: 0.8,
                    lineJoin: 'round',
                    lineCap: 'round',
                }).addTo(this.map);

                const bounds = this.routeLine.getBounds();
                this.map.fitBounds(bounds, { padding: [40, 40] });

                const distanceKm = route.distance / 1000;
                const durationMin = Math.round(route.duration / 60);

                this.distanceText = distanceKm < 1
                    ? Math.round(distanceKm * 1000) + ' m'
                    : distanceKm.toFixed(1) + ' km';

                this.etaText = durationMin < 1 ? 'Arriving' : durationMin + ' min';

            } catch (err) {
                console.error('Route fetch failed:', err);
                this.drawStraightLine(fromLat, fromLng, toLat, toLng);
            }
        },

        drawStraightLine(fromLat, fromLng, toLat, toLng) {
            if (this.routeLine) this.map.removeLayer(this.routeLine);

            this.routeLine = L.polyline([
                [fromLat, fromLng],
                [toLat, toLng],
            ], {
                color: '#ea580c',
                weight: 3,
                opacity: 0.5,
                dashArray: '8, 8',
            }).addTo(this.map);
        },

        updateRiderLocation(lat, lng) {
            if (!this.riderMarker) return;

            this.riderLat = lat;
            this.riderLng = lng;
            this.riderMarker.setLatLng([lat, lng]);

            this.lastUpdated = 'Updated: ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            clearTimeout(this.routeDebounce);
            this.routeDebounce = setTimeout(() => {
                this.redrawRouteFromRider();
            }, 800);
        },

        async redrawRouteFromRider() {
            const target = this.stageText === 'Pickup' 
                ? { lat: this.restaurantLat, lng: this.restaurantLng }
                : { lat: this.customerLat, lng: this.customerLng };

            try {
                const url = `https://router.project-osrm.org/route/v1/driving/${this.riderLng},${this.riderLat};${target.lng},${target.lat}?overview=full&geometries=geojson`;
                const res = await fetch(url);
                const data = await res.json();

                if (!data.routes || data.routes.length === 0) return;

                const route = data.routes[0];
                const coords = route.geometry.coordinates;
                const latlngs = coords.map(c => [c[1], c[0]]);

                if (this.routeLine) this.map.removeLayer(this.routeLine);

                this.routeLine = L.polyline(latlngs, {
                    color: '#ea580c',
                    weight: 5,
                    opacity: 0.8,
                    lineJoin: 'round',
                    lineCap: 'round',
                }).addTo(this.map);

                const distanceKm = route.distance / 1000;
                const durationMin = Math.round(route.duration / 60);

                this.distanceText = distanceKm < 1
                    ? Math.round(distanceKm * 1000) + ' m'
                    : distanceKm.toFixed(1) + ' km';

                this.etaText = durationMin < 1 ? 'Arriving' : durationMin + ' min';
            } catch (err) {
                console.warn('Route redraw failed:', err);
            }
        },

        centerOnRider() {
            if (this.map && this.riderLat && this.riderLng) {
                this.map.setView([this.riderLat, this.riderLng], 16, { animate: true });
            }
        },

        updateStageText() {
            @php
                $stageMap = [
                    'rider_assigned'   => 'Pickup',
                    'picked_up'        => 'Pickup',
                    'out_for_delivery' => 'Deliver',
                ];
            @endphp
            this.stageText = @json($stageMap[$currentOrder->status] ?? 'Active');
        },

        get googleMapsUrl() {
            return `https://www.google.com/maps/dir/?api=1&origin=${this.restaurantLat},${this.restaurantLng}&destination=${this.customerLat},${this.customerLng}&travelmode=driving`;
        },
    }
}
</script>
@endpush