@extends('layouts.app')

@section('content')
<div x-data="riderDash({{ $rider->id }})" x-init="init()" class="max-w-3xl mx-auto">

    {{-- HERO HEADER --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-500 to-orange-600 rounded-2xl shadow-xl text-white mb-6">
        {{-- Decorative Blobs --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-yellow-300 rounded-full blur-3xl opacity-20 -ml-16 -mb-16"></div>

        <div class="relative p-6">
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
                    <button type="button" disabled
                            class="px-5 py-2.5 rounded-full font-semibold text-sm shadow-md opacity-70 cursor-not-allowed bg-white text-orange-600 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span>Online</span>
                    </button>
                @else
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
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-gray-200 dark:border-dark-700 p-4">
            <p class="text-xs text-gray-500 dark:text-neutral-400">Completed</p>
            <p class="text-xl font-bold text-gray-900 dark:text-neutral-100">{{ $completedToday }}</p>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-gray-200 dark:border-dark-700 p-4">
            <p class="text-xs text-gray-500 dark:text-neutral-400">Earnings</p>
            <p class="text-xl font-bold text-green-600 dark:text-green-400">₱{{ number_format($earningsToday, 0) }}</p>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-gray-200 dark:border-dark-700 p-4">
            <p class="text-xs text-gray-500 dark:text-neutral-400">Status</p>
            <p class="text-sm font-bold mt-1" :class="online ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-neutral-400'"
               x-text="online ? 'Ready' : 'Offline'"></p>
        </div>
    </div>

    {{-- AVAILABLE OFFERS --}}
    <template x-if="offers.length > 0">
        <div class="mb-6">
            <h2 class="font-bold text-gray-900 dark:text-neutral-100 flex items-center gap-2 mb-3">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                </span>
                Available Offers
                <span class="text-xs bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 px-2 py-0.5 rounded-full font-bold"
                      x-text="offers.length"></span>
            </h2>

            <div class="space-y-3">
                <template x-for="offer in offers" :key="offer.order_id">
                    <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-md border-2 border-orange-300 dark:border-orange-800 overflow-hidden">

                        <div class="h-1.5 bg-gray-100 dark:bg-dark-850 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-orange-500 to-orange-400"
                                 :style="`width: ${(offer.secondsLeft / offer.expires_in) * 100}%`"></div>
                        </div>

                        <div class="px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white flex items-center justify-between">
                            <div>
                                <p class="font-bold text-sm" x-text="offer.restaurant"></p>
                                <p class="text-[10px] text-white/80">Delivery Offer</p>
                            </div>
                            <p class="text-base font-bold" x-text="offer.secondsLeft + 's'"></p>
                        </div>

                        <div class="p-4 space-y-2">
                            <p class="text-xs text-gray-500 dark:text-neutral-400">
                                <strong>Pickup:</strong> <span x-text="offer.restaurant_address"></span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-neutral-400">
                                <strong>Deliver to:</strong> <span x-text="offer.delivery_address"></span>
                            </p>

                            <div class="grid grid-cols-3 gap-2 bg-gray-50 dark:bg-dark-850 rounded-xl p-3 text-center">
                                <div>
                                    <p class="text-[10px] text-gray-500 dark:text-neutral-400">Food</p>
                                    <p class="text-sm font-bold" x-text="'₱' + offer.food_cost"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-500 dark:text-neutral-400">Fee</p>
                                    <p class="text-sm font-bold text-green-600" x-text="'₱' + offer.delivery_fee"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-500 dark:text-neutral-400">Items</p>
                                    <p class="text-sm font-bold text-orange-600" x-text="offer.items_count"></p>
                                </div>
                            </div>

                            <div class="flex gap-2 pt-1">
                                <button @click="declineOffer(offer.order_id)" type="button"
                                        class="flex-1 border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 py-2.5 rounded-xl text-sm font-semibold">
                                    Decline
                                </button>
                                <button @click="acceptOffer(offer.order_id)" type="button"
                                        class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white py-2.5 rounded-xl text-sm font-bold shadow-md">
                                    Accept
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- CURRENT ACTIVE ORDER --}}
    @if ($currentOrder)
        <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg mb-6 overflow-hidden">
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
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-neutral-400 uppercase">Pickup From</p>
                    <p class="font-bold text-gray-900 dark:text-neutral-100">{{ $currentOrder->restaurant->name ?? '—' }}</p>
                    <p class="text-sm text-gray-600 dark:text-neutral-400">{{ $currentOrder->restaurant->address ?? '' }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 dark:text-neutral-400 uppercase">Deliver To</p>
                    <p class="font-bold text-gray-900 dark:text-neutral-100">{{ $currentOrder->customer->name ?? '—' }}</p>
                    <p class="text-sm text-gray-600 dark:text-neutral-400">{{ $currentOrder->delivery_address }}</p>
                </div>

                <div class="flex flex-wrap gap-2 pt-2">
                    @if ($currentOrder->status === 'rider_assigned')
                        <form method="POST" action="{{ route('rider.orders.status', $currentOrder) }}" class="flex-1">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="picked_up">
                            <button class="w-full bg-blue-500 text-white px-4 py-3 rounded-xl font-semibold text-sm">Mark as Picked Up</button>
                        </form>
                    @elseif ($currentOrder->status === 'picked_up')
                        <form method="POST" action="{{ route('rider.orders.status', $currentOrder) }}" class="flex-1">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="out_for_delivery">
                            <button class="w-full bg-purple-500 text-white px-4 py-3 rounded-xl font-semibold text-sm">Out for Delivery</button>
                        </form>
                    @elseif ($currentOrder->status === 'out_for_delivery')
                        <form method="POST" action="{{ route('rider.orders.status', $currentOrder) }}" class="flex-1">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="delivered">
                            <button class="w-full bg-green-500 text-white px-4 py-3 rounded-xl font-semibold text-sm">Mark as Delivered</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- WAITING / OFFLINE --}}
    @if (!$currentOrder && $rider->is_online)
        <div x-show="offers.length === 0" class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-10 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-950/40 mb-4">
                <span class="w-3 h-3 rounded-full bg-green-500 animate-ping"></span>
            </div>
            <h3 class="font-bold text-gray-900 dark:text-neutral-100 mb-1">You're Online</h3>
            <p class="text-sm text-gray-500 dark:text-neutral-400">Waiting for delivery offers...</p>
        </div>
    @elseif (!$rider->is_online)
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-10 text-center">
            <h3 class="font-bold text-gray-900 dark:text-neutral-100 mb-1">You're Offline</h3>
            <p class="text-sm text-gray-500 dark:text-neutral-400">Toggle online to start receiving offers</p>
            <button @click="toggleOnline()"
                    class="mt-4 bg-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm">
                Go Online
            </button>
        </div>
    @endif

</div>
@endsection

@push('scripts')
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
</script>
@endpush