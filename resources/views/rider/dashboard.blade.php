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

                    {{-- Status Dot --}}
                    <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full border-2 border-white"
                          :class="online ? 'bg-green-400' : 'bg-gray-400'"></span>
                </div>

                <div>
                    <p class="text-xs text-white/80">Welcome back,</p>
                    <h1 class="text-xl font-bold">{{ auth()->user()->name }}</h1>
                </div>
            </div>

            {{-- Online Toggle --}}
            <button @click="toggleOnline()"
                    :disabled="toggling"
                    :class="online ? 'bg-white text-orange-600' : 'bg-white/20 text-white'"
                    class="px-5 py-2.5 rounded-full font-semibold text-sm shadow-md hover:scale-105 active:scale-95 transition transform disabled:opacity-50 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" :class="online ? 'bg-green-500 animate-pulse' : 'bg-gray-400'"></span>
                <span x-text="online ? 'Online' : 'Offline'"></span>
            </button>
        </div>

        {{-- Mini Stats Row --}}
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
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4 hover:shadow-md transition">
            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-950/40 flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Completed</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $completedToday }}</p>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4 hover:shadow-md transition">
            <div class="w-9 h-9 rounded-lg bg-green-100 dark:bg-green-950/40 flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Earnings</p>
            <p class="text-xl font-bold text-green-600 dark:text-green-400">₱{{ number_format($earningsToday, 0) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4 hover:shadow-md transition">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center mb-2"
                 :class="online ? 'bg-green-100 dark:bg-green-950/40' : 'bg-gray-100 dark:bg-gray-800'">
                <svg class="w-5 h-5" :class="online ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
            <p class="text-sm font-bold mt-1" :class="online ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'"
               x-text="online ? 'Ready' : 'Offline'"></p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- INCOMING OFFER (Animated) --}}
    {{-- ============================================ --}}
    <template x-if="currentOffer">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 mb-6 border-2 border-orange-500 animate-pulse-slow relative overflow-hidden">
            {{-- Blinking indicator --}}
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-500 via-orange-400 to-orange-500 animate-pulse"></div>

            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center animate-bounce">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-lg text-gray-900 dark:text-white">New Delivery Offer</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Accept within <span class="font-bold text-red-600 dark:text-red-400" x-text="secondsLeft"></span>s</p>
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 mb-4 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-orange-500 to-red-500 rounded-full transition-all duration-1000"
                     :style="`width: ${(secondsLeft / currentOffer.expires_in) * 100}%`"></div>
            </div>

            <div class="space-y-3 text-sm mb-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-950/40 flex items-center justify-center flex-shrink-0">
                        <span class="text-base">🏪</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pickup</p>
                        <p class="font-semibold text-gray-900 dark:text-white" x-text="currentOffer.restaurant"></p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-green-50 dark:bg-green-950/40 flex items-center justify-center flex-shrink-0">
                        <span class="text-base">📍</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Drop-off</p>
                        <p class="font-semibold text-gray-900 dark:text-white text-sm" x-text="currentOffer.delivery_address"></p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-4 grid grid-cols-3 gap-2 text-center">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Food</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">₱<span x-text="currentOffer.food_cost"></span></p>
                </div>
                <div class="border-x border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Delivery Fee</p>
                    <p class="text-sm font-bold text-green-600 dark:text-green-400">₱<span x-text="currentOffer.delivery_fee"></span></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Collect</p>
                    <p class="text-sm font-bold text-orange-600 dark:text-orange-400">₱<span x-text="currentOffer.total_amount"></span></p>
                </div>
            </div>

            <button @click="acceptOffer()"
                    class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-3.5 rounded-xl hover:from-orange-600 hover:to-orange-700 font-bold shadow-lg hover:shadow-xl active:scale-98 transition transform flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Accept Delivery
            </button>
        </div>
    </template>

    {{-- ============================================ --}}
    {{-- CURRENT ORDER --}}
    {{-- ============================================ --}}
    @if ($currentOrder)
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg mb-6 overflow-hidden transition-colors">

            {{-- ORDER HEADER --}}
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

                {{-- Status Timeline --}}
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

            {{-- BODY --}}
            <div class="p-6">

                {{-- PICKUP --}}
                <div class="flex gap-4 mb-5 pb-5 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-11 h-11 rounded-xl bg-red-50 dark:bg-red-950/40 flex items-center justify-center flex-shrink-0">
                        <span class="text-xl">🏪</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mb-0.5">Pickup From</p>
                        <p class="font-bold text-gray-900 dark:text-white">{{ $currentOrder->restaurant->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{{ $currentOrder->restaurant->address }}</p>

                        <div class="flex items-center gap-2 mt-3 bg-red-50 dark:bg-red-950/30 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Pay restaurant:</span>
                            <span class="text-sm font-bold text-red-600 dark:text-red-400">₱{{ number_format($currentOrder->food_cost, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- DROP-OFF --}}
                <div class="flex gap-4 mb-5 pb-5 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-11 h-11 rounded-xl bg-green-50 dark:bg-green-950/40 flex items-center justify-center flex-shrink-0">
                        <span class="text-xl">📍</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mb-0.5">Deliver To</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $currentOrder->delivery_address }}</p>

                        <div class="flex items-center gap-2 mt-3 bg-green-50 dark:bg-green-950/30 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Collect from customer:</span>
                            <span class="text-sm font-bold text-green-600 dark:text-green-400">₱{{ number_format($currentOrder->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- ORDER ITEMS --}}
                <div class="mb-5">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mb-2">Order Items</p>
                    <div class="space-y-1.5 bg-gray-50 dark:bg-gray-800 rounded-xl p-3">
                        @foreach ($currentOrder->items as $item)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $item->quantity }}x</span>
                                    {{ $item->name }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex flex-wrap gap-2 mb-4">
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

                {{-- CHAT WITH CUSTOMER --}}
                @if ($currentOrder->canChat())
                    <div x-data="chatBox({{ $currentOrder->id }})"
                         x-init="init()"
                         class="rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">

                        {{-- HEADER --}}
                        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 flex justify-between items-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                             @click="isOpen = !isOpen">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-gray-900 dark:text-white">Chat with Customer</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Customer • Order #{{ $currentOrder->id }}</p>
                                </div>
                                <template x-if="unread > 0">
                                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse"
                                          x-text="unread"></span>
                                </template>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                 :class="isOpen ? 'rotate-180' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>

                        {{-- CHAT BODY --}}
                        <div x-show="isOpen" x-cloak x-transition>
                            <div x-ref="messagesContainer"
                                 @scroll="onScroll()"
                                 class="h-80 overflow-y-auto p-4 space-y-2 bg-gray-50 dark:bg-gray-800 scroll-smooth">
                                <template x-if="loading">
                                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 py-4">Loading messages...</p>
                                </template>

                                <template x-if="!loading && messages.length === 0">
                                    <div class="text-center py-8">
                                        <div class="text-4xl mb-2">💬</div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No messages yet</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Say hi to your customer!</p>
                                    </div>
                                </template>

                                <template x-for="msg in messages" :key="msg.id">
                                    <div :class="msg.sender_id === currentUserId ? 'flex justify-end' : 'flex justify-start'"
                                         class="chat-message">
                                        <div :class="msg.sender_id === currentUserId
                                                ? 'bg-orange-600 text-white rounded-br-none'
                                                : 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-bl-none'"
                                             class="max-w-[75%] px-3 py-2 rounded-2xl shadow-sm transition-all duration-200">
                                            <p class="text-xs font-medium mb-0.5 opacity-75"
                                               x-text="msg.sender_name"></p>
                                            <p class="text-sm break-words whitespace-pre-wrap"
                                               x-text="msg.body"></p>
                                            <div class="flex items-center justify-end gap-1 mt-1">
                                                <span class="text-[10px] opacity-60"
                                                      x-text="msg.created_at_human"></span>
                                                <template x-if="msg.sender_id === currentUserId">
                                                    <span class="text-[11px] leading-none"
                                                          :title="statusLabel(msg)"
                                                          :class="msg.status === 'seen' ? 'text-blue-200 font-bold' : 'opacity-70'">
                                                        <span x-show="msg.status === 'sent'">🕐</span>
                                                        <span x-show="msg.status === 'delivered'">✓</span>
                                                        <span x-show="msg.status === 'seen'">✓✓</span>
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="typingName">
                                    <div class="flex justify-start chat-message">
                                        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 px-3 py-2 rounded-2xl rounded-bl-none shadow-sm">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1" x-text="typingName + ' is typing'"></p>
                                            <div class="flex gap-1">
                                                <span class="typing-dot"></span>
                                                <span class="typing-dot" style="animation-delay: 0.15s"></span>
                                                <span class="typing-dot" style="animation-delay: 0.3s"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 p-3 bg-white dark:bg-gray-900">
                                <form @submit.prevent="sendMessage()" class="flex gap-2">
                                    <input type="text"
                                           x-model="newMessage"
                                           @input="onTypingInput()"
                                           @keydown.enter.prevent="sendMessage()"
                                           @blur="stopTyping()"
                                           placeholder="Type a message..."
                                           maxlength="1000"
                                           class="flex-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                                    <button type="submit"
                                            :disabled="!newMessage.trim() || sending"
                                            :class="(!newMessage.trim() || sending) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-orange-700'"
                                            class="bg-orange-600 text-white px-5 py-2 rounded-full text-sm font-medium transition">
                                        <span x-show="!sending">Send</span>
                                        <span x-show="sending">...</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- PAYMENT RECORDING --}}
                @if ($currentOrder->status === 'delivered' && !$currentOrder->payment)
                    <div class="mt-4 p-5 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/30 dark:to-emerald-950/30 rounded-xl border border-green-200 dark:border-green-800">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                                <span class="text-lg">💰</span>
                            </div>
                            <div>
                                <p class="font-semibold text-green-900 dark:text-green-300">Record Payment</p>
                                <p class="text-xs text-green-700 dark:text-green-400">Complete this to finish the order</p>
                            </div>
                        </div>
                        <div class="bg-white/60 dark:bg-gray-900/60 rounded-lg p-3 mb-3 text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Paid to restaurant</span>
                                <span class="font-semibold text-red-600 dark:text-red-400">₱{{ number_format($currentOrder->food_cost, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Collected from customer</span>
                                <span class="font-semibold text-gray-900 dark:text-white">₱{{ number_format($currentOrder->total_amount, 2) }}</span>
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
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-10 text-center transition-colors">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-950/40 mb-4">
                <span class="w-3 h-3 rounded-full bg-green-500 animate-ping"></span>
            </div>
            <h3 class="font-bold text-gray-900 dark:text-white mb-1">You're Online</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Waiting for delivery offers...</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-4">Stay on this page to receive orders</p>
        </div>
    @elseif (!$rider->is_online)
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-10 text-center transition-colors">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                <span class="text-2xl">⚪</span>
            </div>
            <h3 class="font-bold text-gray-900 dark:text-white mb-1">You're Offline</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Toggle online to start receiving delivery offers</p>
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
    .chat-message {
        animation: slideIn 0.25s ease-out;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
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
    .scroll-smooth { scroll-behavior: smooth; }
    .animate-pulse-slow {
        animation: pulseSlow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulseSlow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }
    .active\:scale-98:active {
        transform: scale(0.98);
    }
</style>
<script>
function riderDash(riderId) {
    return {
        online: {{ $rider->is_online ? 'true' : 'false' }},
        toggling: false,
        currentOffer: null,
        secondsLeft: 0,
        timer: null,

        init() {
            window.Echo.private(`rider.${riderId}`)
                .listen('.delivery.offer', (e) => {
                    this.currentOffer = e;
                    this.secondsLeft = e.expires_in;
                    this.startCountdown();
                    this.playBeep();
                });

            if (this.online) {
                this.startLocationSharing();
            }
        },

        startCountdown() {
            clearInterval(this.timer);
            this.timer = setInterval(() => {
                this.secondsLeft--;
                if (this.secondsLeft <= 0) {
                    clearInterval(this.timer);
                    this.currentOffer = null;
                }
            }, 1000);
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
                }, () => {
                    console.warn('Could not get location');
                });
            };
            send();
            setInterval(send, 10000);
        },

        async acceptOffer() {
            const res = await fetch(`/rider/orders/${this.currentOffer.order_id}/accept`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });

            if (res.ok || res.redirected) {
                alert('Order accepted! Proceed to the restaurant.');
                location.href = '{{ route('rider.dashboard') }}';
            } else {
                alert('Offer expired or another rider accepted first.');
                this.currentOffer = null;
            }
        }
    }
}

function chatBox(orderId) {
    return {
        isOpen: true,
        loading: true,
        sending: false,
        messages: [],
        newMessage: '',
        unread: 0,
        currentUserId: {{ auth()->id() }},
        channel: null,
        typingName: '',
        typingTimeout: null,
        isTypingSent: false,
        isAtBottom: true,

        init() {
            this.loadMessages();

            this.channel = window.Echo.private(`order.${orderId}.chat`)
                .listen('.message.sent', (e) => {
                    if (e.sender_id === this.currentUserId) return;
                    e.status = 'received';
                    this.messages.push(e);
                    if (!this.isOpen || !this.isAtBottom) {
                        this.unread++;
                    } else {
                        this.markAsRead();
                    }
                    if (this.isAtBottom) this.scrollToBottom();
                })
                .listen('.user.typing', (e) => {
                    if (e.user_id === this.currentUserId) return;
                    this.typingName = e.is_typing ? e.user_name : '';
                })
                .listen('.messages.read', (e) => {
                    if (e.reader_id === this.currentUserId) return;
                    this.messages.forEach(m => {
                        if (e.message_ids.includes(m.id) && m.sender_id === this.currentUserId) {
                            m.status = 'seen';
                            m.read_at = e.read_at;
                        }
                    });
                });
        },

        statusLabel(msg) {
            if (msg.status === 'seen') return 'Seen';
            if (msg.status === 'delivered') return 'Delivered';
            return 'Sending...';
        },

        async loadMessages() {
            try {
                const res = await fetch('{{ $currentOrder ? route('rider.chat.index', $currentOrder) : '' }}', {
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
                sender_name: '{{ auth()->user()->name }}',
                body: body,
                created_at: new Date().toISOString(),
                created_at_human: 'just now',
                status: 'sent',
            };
            this.messages.push(optimisticMsg);
            this.scrollToBottom();

            try {
                const res = await fetch('{{ $currentOrder ? route('rider.chat.store', $currentOrder) : '' }}', {
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
                await fetch('{{ $currentOrder ? route('rider.chat.typing', $currentOrder) : '' }}', {
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
                await fetch('{{ $currentOrder ? route('rider.chat.mark-read', $currentOrder) : '' }}', {
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