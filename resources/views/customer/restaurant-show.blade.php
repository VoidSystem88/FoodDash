@extends('layouts.app')

@section('content')
<div x-data="orderForm({{ $restaurant->id }}, {{ $restaurant->menuItems->toJson() }})" x-init="init()">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative mb-6 bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden transition-colors">

        {{-- Decorative banner / Cover image --}}
        <div class="h-40 relative overflow-hidden">
            @if ($restaurant->cover_image_url)
                <img src="{{ $restaurant->cover_image_url }}"
                     alt="{{ $restaurant->name }}"
                     class="absolute inset-0 w-full h-full object-cover">
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500 via-orange-400 to-amber-400"></div>
                <div class="absolute inset-0 opacity-20"
                     style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 80%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
            @endif
        </div>

        {{-- Back button --}}
        <a href="{{ route('customer.restaurants') }}"
           class="absolute top-4 left-4 w-10 h-10 rounded-full bg-white/90 dark:bg-dark-800/90 backdrop-blur flex items-center justify-center shadow-md hover:bg-white dark:hover:bg-gray-900 transition z-10">
            <svg class="w-5 h-5 text-gray-700 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>

        {{-- Restaurant info --}}
        <div class="relative px-6 -mt-12">
            <div class="flex items-end gap-4">
                <div class="w-24 h-24 rounded-2xl bg-white dark:bg-dark-800 border-4 border-white dark:border-dark-800 shadow-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                    @if ($restaurant->profile_image_url)
                        <img src="{{ $restaurant->profile_image_url }}"
                             alt="{{ $restaurant->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-4xl">🍽️</span>
                    @endif
                </div>

                <div class="flex-1 pb-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-neutral-100 leading-tight">{{ $restaurant->name }}</h1>
                    @if ($restaurant->cuisine)
                        <span class="inline-block mt-1 text-xs px-2.5 py-1 rounded-full bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 font-semibold">
                            {{ $restaurant->cuisine }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-4 flex items-center gap-2 text-sm text-gray-500 dark:text-neutral-400">
                <svg class="w-4 h-4 text-gray-400 dark:text-neutral-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="line-clamp-1">{{ $restaurant->address }}</span>
            </div>

            <div class="mt-4 pb-6 flex items-center gap-5 text-sm border-t border-gray-100 dark:border-dark-700 pt-4">
                <div class="flex items-center gap-1.5">
                    <span class="text-yellow-500 text-lg">★</span>
                    <span class="font-semibold text-gray-900 dark:text-neutral-100">4.8</span>
                    <span class="text-gray-400 dark:text-neutral-500 text-xs">(120+)</span>
                </div>
                <div class="w-px h-4 bg-gray-200 dark:bg-dark-700"></div>
                <div class="flex items-center gap-1.5 text-gray-600 dark:text-neutral-400">
                    <svg class="w-4 h-4 text-gray-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ $restaurant->prep_time_minutes ?? 20 }} min</span>
                </div>
                <div class="w-px h-4 bg-gray-200 dark:bg-dark-700"></div>
                <div class="flex items-center gap-1.5 text-gray-600 dark:text-neutral-400">
                    <svg class="w-4 h-4 text-gray-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <span class="font-medium">{{ $restaurant->menuItems->count() }} items</span>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('customer.orders.store') }}" @submit="clearCartOnSubmit()" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">

        {{-- ============================================ --}}
        {{-- MENU --}}
        {{-- ============================================ --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden transition-colors">

                <div class="px-6 py-4 border-b border-gray-100 dark:border-dark-700 bg-gray-50 dark:bg-dark-850">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-950/40 dark:to-orange-900/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-900 dark:text-neutral-100">Menu</h2>
                                <p class="text-xs text-gray-500 dark:text-neutral-400">Choose your items</p>
                            </div>
                        </div>
                    </div>
                </div>

                @forelse ($restaurant->menuItems as $item)
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-dark-700 last:border-0 hover:bg-gray-50 dark:hover:bg-dark-850 transition group">
                        <div class="flex justify-between items-start gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start gap-3">
                                    @if ($item->image_path)
                                        <img src="{{ Storage::disk('public')->url($item->image_path) }}"
                                             alt="{{ $item->name }}"
                                             class="w-16 h-16 rounded-xl object-cover flex-shrink-0">
                                    @else
                                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-900 flex items-center justify-center flex-shrink-0">
                                            <span class="text-2xl">🍴</span>
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 dark:text-neutral-100 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition">
                                            {{ $item->name }}
                                        </p>
                                        @if ($item->description)
                                            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5 line-clamp-2">{{ $item->description }}</p>
                                        @endif
                                        <p class="text-base font-bold text-orange-600 dark:text-orange-400 mt-1.5">
                                            ₱{{ number_format($item->price, 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-shrink-0">
                                @auth
                                    {{-- LOGGED IN — show quantity controls --}}
                                    <template x-if="qty({{ $item->id }}) === 0">
                                        <button type="button"
                                                @click="inc({{ $item->id }})"
                                                class="flex items-center gap-1.5 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg active:scale-95 transition transform">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add
                                        </button>
                                    </template>

                                    <template x-if="qty({{ $item->id }}) > 0">
                                        <div class="flex items-center gap-3 bg-gray-50 dark:bg-dark-850 rounded-xl p-1">
                                            <button type="button"
                                                    @click="dec({{ $item->id }})"
                                                    class="w-8 h-8 rounded-lg bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 hover:bg-gray-100 dark:hover:bg-dark-850 text-gray-700 dark:text-neutral-300 flex items-center justify-center shadow-sm active:scale-90 transition">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <span class="w-8 text-center font-bold text-gray-900 dark:text-neutral-100 text-base"
                                                  x-text="qty({{ $item->id }})"></span>
                                            <button type="button"
                                                    @click="inc({{ $item->id }})"
                                                    class="w-8 h-8 rounded-lg bg-orange-600 hover:bg-orange-700 text-white flex items-center justify-center shadow-sm active:scale-90 transition">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                @else
                                    {{-- GUEST — show Add button that triggers sign-in modal --}}
                                    <button type="button"
                                            @click="showGuestModal = true"
                                            class="flex items-center gap-1.5 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg active:scale-95 transition transform">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add
                                    </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="text-5xl mb-3">🍽️</div>
                        <p class="text-gray-500 dark:text-neutral-400">No items available right now.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- CART — Desktop only, logged in only --}}
        {{-- ============================================ --}}
        <div class="lg:sticky lg:top-24 h-fit">
            @auth
                {{-- FULL CART (logged in) --}}
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 shadow-sm overflow-hidden transition-colors">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-dark-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="font-bold text-gray-900 dark:text-neutral-100">Your Order</h2>
                                    <p class="text-xs text-gray-500 dark:text-neutral-400" x-text="cartSize === 0 ? 'Empty cart' : cartSize + ' item' + (cartSize > 1 ? 's' : '')"></p>
                                </div>
                            </div>

                            <template x-if="cartSize > 0">
                                <span class="bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 text-xs font-bold px-2.5 py-1 rounded-full"
                                      x-text="cartSize"></span>
                            </template>
                        </div>
                    </div>

                    <div class="p-5 max-h-64 overflow-y-auto">
                        <template x-if="cartSize === 0">
                            <div class="py-8 text-center">
                                <div class="text-4xl mb-2 opacity-50">🛒</div>
                                <p class="text-sm text-gray-400 dark:text-neutral-500">Your cart is empty</p>
                                <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">Add items to get started</p>
                            </div>
                        </template>

                        <div class="space-y-3">
                            <template x-for="line in cartLines" :key="line.id">
                                <div class="flex justify-between items-start gap-3 text-sm">
                                    <div class="flex items-start gap-2 flex-1 min-w-0">
                                        <span class="w-6 h-6 rounded-md bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 text-xs font-bold flex items-center justify-center flex-shrink-0"
                                              x-text="line.quantity"></span>
                                        <span class="text-gray-700 dark:text-neutral-300 line-clamp-2" x-text="line.name"></span>
                                    </div>
                                    <span class="text-gray-900 dark:text-neutral-100 font-semibold flex-shrink-0">
                                        ₱<span x-text="line.subtotal.toFixed(2)"></span>
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <template x-if="cartSize > 0">
                        <div class="px-5 py-4 border-t border-gray-100 dark:border-dark-700 space-y-2 text-sm bg-gray-50 dark:bg-dark-850">
                            <div class="flex justify-between text-gray-600 dark:text-neutral-400">
                                <span>Food cost</span>
                                <span class="font-medium text-gray-900 dark:text-neutral-100">₱<span x-text="foodCost.toFixed(2)"></span></span>
                            </div>
                            <div class="flex justify-between text-gray-600 dark:text-neutral-400">
                                <span>Delivery fee</span>
                                <span class="font-medium text-gray-900 dark:text-neutral-100">₱{{ number_format(\App\Models\SystemConfig::current()->default_delivery_fee, 2) }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-gray-900 dark:text-neutral-100 pt-3 border-t border-gray-200 dark:border-dark-600 text-base">
                                <span>Total</span>
                                <span class="text-orange-600 dark:text-orange-400">₱<span x-text="total.toFixed(2)"></span></span>
                            </div>
                        </div>
                    </template>

                    <div class="px-5 pb-5 pt-4 space-y-4 border-t border-gray-100 dark:border-dark-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <label class="text-sm font-semibold text-gray-900 dark:text-neutral-100">Delivery Address</label>
                        </div>

                        <div class="relative">
                            <div class="relative">
                                <input type="text"
                                       x-model="address"
                                       @input.debounce.500ms="searchAddress()"
                                       @focus="showSuggestions = address.length >= 3 && suggestions.length > 0"
                                       @keydown.escape="showSuggestions = false"
                                       @keydown.arrow-down.prevent="highlightNext()"
                                       @keydown.arrow-up.prevent="highlightPrev()"
                                       @keydown.enter.prevent="selectHighlighted()"
                                       placeholder="Start typing your address..."
                                       autocomplete="off"
                                       required
                                       class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition pr-10">

                                <div x-show="searching" x-cloak
                                     class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <svg class="animate-spin h-4 w-4 text-orange-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>

                                <div x-show="!searching && lat && lng" x-cloak
                                     class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>

                            <div x-show="showSuggestions && suggestions.length > 0"
                                 x-cloak
                                 @click.outside="showSuggestions = false"
                                 class="absolute z-30 left-0 right-0 mt-2 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 rounded-xl shadow-xl max-h-72 overflow-y-auto">

                                <template x-for="(s, index) in suggestions" :key="index">
                                    <button type="button"
                                            @click="selectSuggestion(index)"
                                            @mouseenter="highlightedIndex = index"
                                            :class="highlightedIndex === index ? 'bg-orange-50 dark:bg-orange-950/30' : ''"
                                            class="w-full text-left px-4 py-3 text-sm hover:bg-orange-50 dark:hover:bg-orange-950/30 border-b border-gray-100 dark:border-dark-700 last:border-0 transition flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-dark-850 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-gray-900 dark:text-neutral-100 font-semibold line-clamp-1" x-text="s.display_name.split(',')[0]"></p>
                                            <p class="text-xs text-gray-500 dark:text-neutral-400 line-clamp-2 mt-0.5" x-text="s.display_name"></p>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <input type="hidden" name="delivery_address" :value="address">
                        <input type="hidden" name="delivery_lat" :value="lat">
                        <input type="hidden" name="delivery_lng" :value="lng">

                        <button type="button"
                                @click="useCurrentLocation()"
                                :disabled="locating"
                                class="w-full flex items-center justify-center gap-2 text-sm text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 border-2 border-dashed border-orange-300 dark:border-orange-800 hover:border-orange-500 dark:hover:border-orange-600 rounded-xl py-2.5 transition disabled:opacity-50 bg-orange-50 dark:bg-orange-950/30 hover:bg-orange-100 dark:hover:bg-orange-950/40">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span x-show="!locating" class="font-medium">Use my current location</span>
                            <span x-show="locating" class="font-medium">Getting location...</span>
                        </button>

                        <template x-if="!searching && lat && lng">
                            <div class="flex items-center gap-2 text-xs text-green-600 dark:text-green-400 font-medium">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Location confirmed
                            </div>
                        </template>

                        <template x-for="line in cartLines" :key="'input-' + line.id">
                            <div>
                                <input type="hidden" :name="'items[' + line.index + '][menu_item_id]'" :value="line.id">
                                <input type="hidden" :name="'items[' + line.index + '][quantity]'" :value="line.quantity">
                            </div>
                        </template>

                        <button type="submit"
                                :disabled="cartSize === 0 || !lat || !lng"
                                :class="(cartSize === 0 || !lat || !lng)
                                        ? 'opacity-40 cursor-not-allowed'
                                        : 'hover:from-orange-600 hover:to-orange-700 hover:shadow-xl active:scale-98'"
                                class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3.5 rounded-xl text-sm font-bold shadow-lg transition transform flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Place Order
                        </button>

                        <p class="text-center text-xs text-gray-400 dark:text-neutral-500">
                            <span x-show="cartSize === 0">Add items to continue</span>
                            <span x-show="cartSize > 0 && (!lat || !lng)" x-cloak>Set delivery address to continue</span>
                            <span x-show="cartSize > 0 && lat && lng" x-cloak>Ready to place order</span>
                        </p>
                    </div>
                </div>
            @else
                {{-- GUEST CART — Sign-in prompt --}}
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 shadow-sm overflow-hidden transition-colors">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-dark-700 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-950/30 dark:to-amber-950/30">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-900 dark:text-neutral-100">Want to order?</h2>
                                <p class="text-xs text-gray-500 dark:text-neutral-400">Sign in to add items to cart</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 text-center">
                        <div class="w-16 h-16 rounded-full bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl">🔐</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-neutral-100 mb-1">Create an account to order</h3>
                        <p class="text-sm text-gray-500 dark:text-neutral-400 mb-5">
                            Sign in or sign up to place your order, track delivery, and rate restaurants.
                        </p>

                        <a href="{{ route('login') }}"
                           class="block w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl text-sm font-bold shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform mb-2">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}"
                           class="block w-full border-2 border-orange-500 text-orange-600 dark:text-orange-400 py-3 rounded-xl text-sm font-bold hover:bg-orange-50 dark:hover:bg-orange-950/30 active:scale-98 transition transform">
                            Create Account
                        </a>

                        <p class="text-xs text-gray-400 dark:text-neutral-500 mt-4">
                            Browsing is free · No credit card needed
                        </p>
                    </div>
                </div>
            @endauth
        </div>
    </form>

    {{-- ============================================ --}}
    {{-- EXIT CONFIRMATION MODAL (kung may cart at nag-navigate) --}}
    {{-- ============================================ --}}
    @auth
        <div x-show="showExitModal"
             x-cloak
             x-transition.opacity
             @keydown.escape.window="cancelExit()"
             @click.self="cancelExit()"
             class="fixed inset-0 bg-black/60 z-[110] flex items-center justify-center p-4">

            <div x-show="showExitModal"
                 x-transition.scale.origin.center
                 class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl max-w-md w-full p-6 relative">

                {{-- ICON + TITLE --}}
                <div class="flex items-start gap-4 mb-5">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                        <span class="text-2xl">🛒</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100 mb-1">
                            Your cart still has items!
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-neutral-400">
                            You still have <strong x-text="cartSize"></strong> item<span x-show="cartSize > 1">s</span> in your cart.
                            Would you like to continue?
                        </p>
                    </div>
                </div>

                {{-- CART PREVIEW --}}
                <div class="bg-orange-50 dark:bg-orange-950/30 rounded-xl p-3 mb-5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-700 dark:text-neutral-300">
                            <strong x-text="cartSize"></strong> item<span x-show="cartSize > 1">s</span>
                        </span>
                        <span class="font-bold text-orange-600 dark:text-orange-400">
                            ₱<span x-text="foodCost.toFixed(2)"></span>
                        </span>
                    </div>
                </div>

                {{-- INFO --}}
                <div class="flex items-start gap-2 mb-5 p-3 bg-blue-50 dark:bg-blue-950/30 rounded-xl">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-xs text-blue-800 dark:text-blue-300 leading-relaxed">
    <strong>Notice:</strong> Your cart has been saved. When you come back, your items will still be there (within 24 hours).
</p>
                </div>

                {{-- ACTIONS --}}
                <div class="space-y-2">
                    <button type="button"
                            @click="confirmExit()"
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl text-sm font-bold shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Continue & Save Cart
                    </button>

                    <button type="button"
                            @click="cancelExit()"
                            class="w-full border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 py-3 rounded-xl text-sm font-semibold hover:bg-gray-50 dark:hover:bg-dark-850 active:scale-98 transition transform">
                        Back to Cart
                    </button>
                </div>
            </div>
        </div>
    @endauth

    {{-- ============================================ --}}
    {{-- GUEST SIGN-IN MODAL (kapag clinick ang Add button) --}}
    {{-- ============================================ --}}
    @guest
        <div x-show="showGuestModal"
             x-cloak
             x-transition.opacity
             @keydown.escape.window="showGuestModal = false"
             @click.self="showGuestModal = false"
             class="fixed inset-0 bg-black/60 z-[100] flex items-center justify-center p-4">

            <div x-show="showGuestModal"
                 x-transition.scale.origin.center
                 class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center relative">

                <button type="button"
                        @click="showGuestModal = false"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="w-16 h-16 rounded-full bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl">🔐</span>
                </div>

                <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100 mb-2">
                    Sign in to order
                </h3>

                <p class="text-sm text-gray-500 dark:text-neutral-400 mb-6">
                    You need an account to place an order. Sign in or create a free account to continue.
                </p>

                <div class="space-y-2">
                    <a href="{{ route('login') }}"
                       class="block w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl text-sm font-bold shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}"
                       class="block w-full border-2 border-orange-500 text-orange-600 dark:text-orange-400 py-3 rounded-xl text-sm font-bold hover:bg-orange-50 dark:hover:bg-orange-950/30 active:scale-98 transition transform">
                        Create Account
                    </a>
                    <button type="button"
                            @click="showGuestModal = false"
                            class="block w-full text-gray-500 dark:text-neutral-400 py-2 text-sm hover:text-gray-700 dark:hover:text-gray-300 transition">
                        Continue browsing
                    </button>
                </div>
            </div>
        </div>
    @endguest
</div>
@endsection

@push('scripts')
<style>
    .active\:scale-95:active { transform: scale(0.95); }
    .active\:scale-90:active { transform: scale(0.90); }
    .active\:scale-98:active { transform: scale(0.98); }
</style>
<script>
function orderForm(restaurantId, menuItems) {
    return {
        menu: menuItems,
        cart: {},
        deliveryFee: {{ \App\Models\SystemConfig::current()->default_delivery_fee }},

        // Guest modal
        showGuestModal: false,

        // ⭐ Exit modal state
        showExitModal: false,
        pendingNavigation: null,
        listenersSetup: false,

        locating: false,
        geocoding: false,
        address: '',
        lat: '',
        lng: '',

        suggestions: [],
        showSuggestions: false,
        highlightedIndex: -1,
        searching: false,
        searchTimeout: null,

        // ⭐ Cart persistence keys
        storageKey: 'fooddash_cart_' + restaurantId,
        addressKey: 'fooddash_address_' + restaurantId,
        cartExpiryHours: 24,

        init() {
            console.log('🛒 orderForm init for restaurant:', restaurantId);

            // Initialize cart from menu
            this.menu.forEach(m => { this.cart[m.id] = 0; });

            // RESTORE cart from localStorage
            this.restoreCart();

            // RESTORE address
            this.restoreAddress();

            // WATCH cart changes → auto-save
            this.$watch('cart', () => {
                this.saveCart();
            });

            // WATCH address changes → auto-save
            this.$watch('address', () => {
                this.saveAddress();
            });

            // ⭐ SETUP navigation interceptor
            this.$nextTick(() => {
                this.setupNavigationInterceptor();
            });
        },

        // ============================================
        // EXIT CONFIRMATION (navigation interceptor)
        // ============================================
        setupNavigationInterceptor() {
            if (this.listenersSetup) return;
            this.listenersSetup = true;

            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href]');
                if (!link) return;

                // Skip kung may data-no-cart-warning attribute
                if (link.hasAttribute('data-no-cart-warning')) return;

                // Skip kung walang items sa cart
                if (!this.hasItemsInCart()) return;

                // Skip kung same-page anchor o javascript: links
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

                // Skip kung external link
                if (href.startsWith('http') && !href.includes(window.location.host)) return;

                // Skip kung same restaurant page (OK lang)
                const restaurantUrl = `/restaurants/${restaurantId}`;
                if (href.endsWith(restaurantUrl) || href.includes(restaurantUrl + '/')) return;

                // ⭐ PREVENT navigation at i-show ang modal
                e.preventDefault();
                e.stopPropagation();

                this.pendingNavigation = href;
                this.showExitModal = true;
            }, true);
        },

        hasItemsInCart() {
            return this.cartSize > 0;
        },

        confirmExit() {
            console.log('✅ Confirming exit — cart saved');

            const url = this.pendingNavigation;
            this.pendingNavigation = null;
            this.showExitModal = false;

            if (url) {
                window.location.href = url;
            }
        },

        cancelExit() {
            console.log('❌ Cancelled exit');

            this.pendingNavigation = null;
            this.showExitModal = false;

            this.$nextTick(() => {
                const cartEl = document.querySelector('.lg\\:sticky');
                if (cartEl) {
                    cartEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        },

        // ============================================
        // CART PERSISTENCE
        // ============================================
        saveCart() {
            try {
                const cartData = {
                    cart: this.cart,
                    savedAt: Date.now(),
                    restaurantId: restaurantId,
                };
                localStorage.setItem(this.storageKey, JSON.stringify(cartData));
            } catch (err) {
                console.warn('Failed to save cart:', err);
            }
        },

        restoreCart() {
            try {
                const stored = localStorage.getItem(this.storageKey);
                if (!stored) return;

                const data = JSON.parse(stored);

                const savedAt = data.savedAt || 0;
                const hoursSinceSaved = (Date.now() - savedAt) / (1000 * 60 * 60);

                if (hoursSinceSaved > this.cartExpiryHours) {
                    console.log('🗑️ Cart expired, clearing...');
                    this.clearCartStorage();
                    return;
                }

                if (data.restaurantId !== restaurantId) {
                    console.log('🗑️ Different restaurant, clearing...');
                    this.clearCartStorage();
                    return;
                }

                const validIds = this.menu.map(m => m.id);
                Object.keys(data.cart).forEach(id => {
                    const numericId = parseInt(id);
                    if (validIds.includes(numericId) && data.cart[id] > 0) {
                        this.cart[numericId] = data.cart[id];
                    }
                });

                const itemCount = Object.values(this.cart).reduce((a, b) => a + b, 0);
                if (itemCount > 0) {
                    this.showRestoreNotification(itemCount);
                }

                console.log('✅ Cart restored:', itemCount, 'items');
            } catch (err) {
                console.warn('Failed to restore cart:', err);
                this.clearCartStorage();
            }
        },

        clearCartStorage() {
            try {
                localStorage.removeItem(this.storageKey);
                localStorage.removeItem(this.addressKey);
            } catch (err) {
                console.warn('Failed to clear cart:', err);
            }
        },

        clearCartOnSubmit() {
            console.log('🗑️ Clearing cart after order placed');
            this.clearCartStorage();
        },

        // ============================================
        // ADDRESS PERSISTENCE
        // ============================================
        saveAddress() {
            try {
                if (this.address && this.lat && this.lng) {
                    const addressData = {
                        address: this.address,
                        lat: this.lat,
                        lng: this.lng,
                        savedAt: Date.now(),
                    };
                    localStorage.setItem(this.addressKey, JSON.stringify(addressData));
                }
            } catch (err) {
                console.warn('Failed to save address:', err);
            }
        },

        restoreAddress() {
            try {
                const stored = localStorage.getItem(this.addressKey);
                if (!stored) return;

                const data = JSON.parse(stored);

                const savedAt = data.savedAt || 0;
                const hoursSinceSaved = (Date.now() - savedAt) / (1000 * 60 * 60);

                if (hoursSinceSaved > this.cartExpiryHours) {
                    localStorage.removeItem(this.addressKey);
                    return;
                }

                this.address = data.address || '';
                this.lat = data.lat || '';
                this.lng = data.lng || '';

                console.log('✅ Address restored');
            } catch (err) {
                console.warn('Failed to restore address:', err);
            }
        },

        // ============================================
        // RESTORE NOTIFICATION
        // ============================================
        showRestoreNotification(itemCount) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-20 right-4 bg-green-500 text-white px-5 py-3 rounded-xl shadow-lg z-50';
            toast.style.animation = 'slideIn 0.3s ease-out';
            toast.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="text-xl">🛒</span>
                    <div>
                        <p class="font-bold text-sm">Cart Restored</p>
                        <p class="text-xs opacity-90">${itemCount} item${itemCount > 1 ? 's' : ''} from previous session</p>
                    </div>
                    <button onclick="this.closest('div').parentElement.remove()" class="ml-2 text-white/70 hover:text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        },

        // ============================================
        // CART HELPERS
        // ============================================
        qty(id) { return this.cart[id] || 0; },
        inc(id) { this.cart[id] = (this.cart[id] || 0) + 1; },
        dec(id) { this.cart[id] = Math.max(0, (this.cart[id] || 0) - 1); },

        get cartSize() {
            return Object.values(this.cart).reduce((a, b) => a + b, 0);
        },

        get cartLines() {
            let index = 0;
            const lines = [];
            for (const m of this.menu) {
                const qty = this.cart[m.id] || 0;
                if (qty > 0) {
                    lines.push({
                        index: index++,
                        id: m.id,
                        name: m.name,
                        quantity: qty,
                        subtotal: qty * parseFloat(m.price)
                    });
                }
            }
            return lines;
        },

        get foodCost() {
            return this.cartLines.reduce((sum, l) => sum + l.subtotal, 0);
        },

        get total() {
            return this.foodCost + (this.cartSize > 0 ? this.deliveryFee : 0);
        },

        async searchAddress() {
            if (!this.address || this.address.length < 3) {
                this.suggestions = [];
                this.showSuggestions = false;
                this.lat = '';
                this.lng = '';
                return;
            }

            if (this.locating) return;

            const town = '{{ \App\Models\SystemConfig::current()->town_address ?? "Cagayan de Oro" }}';
            const query = `${this.address}, ${town}, Philippines`;

            this.searching = true;

            try {
                const res = await fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&addressdetails=1&countrycodes=ph`,
                    { headers: { 'Accept-Language': 'en', 'User-Agent': 'FoodDash/1.0' } }
                );

                const data = await res.json();
                this.suggestions = data || [];
                this.highlightedIndex = -1;
                this.showSuggestions = this.suggestions.length > 0;

                if (this.suggestions.length === 0) {
                    this.lat = '';
                    this.lng = '';
                }
            } catch (err) {
                console.warn('Address search failed:', err);
                this.suggestions = [];
                this.showSuggestions = false;
            }

            this.searching = false;
        },

        selectSuggestion(index) {
            const s = this.suggestions[index];
            if (!s) return;
            this.address = s.display_name;
            this.lat = s.lat;
            this.lng = s.lon;
            this.showSuggestions = false;
            this.suggestions = [];
        },

        highlightNext() {
            if (this.suggestions.length === 0) return;
            this.highlightedIndex = (this.highlightedIndex + 1) % this.suggestions.length;
        },

        highlightPrev() {
            if (this.suggestions.length === 0) return;
            this.highlightedIndex = this.highlightedIndex <= 0
                ? this.suggestions.length - 1
                : this.highlightedIndex - 1;
        },

        selectHighlighted() {
            if (this.highlightedIndex >= 0 && this.suggestions[this.highlightedIndex]) {
                this.selectSuggestion(this.highlightedIndex);
            }
        },

        async useCurrentLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation not supported.');
                return;
            }

            this.locating = true;

            navigator.geolocation.getCurrentPosition(
                async (pos) => {
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
                    this.suggestions = [];
                    this.showSuggestions = false;
                },
                () => {
                    alert('Could not get your location. Please type your address instead.');
                    this.locating = false;
                }
            );
        }
    }
}
</script>
@endpush