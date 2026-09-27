@extends('layouts.app')

@section('content')
<div x-data="orderTracker({{ $order->id }})" x-init="init()">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Order #{{ $order->id }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $order->restaurant->name }}</p>
        </div>
        <a href="{{ route('customer.orders') }}" class="text-sm text-gray-500 hover:text-gray-700">
            Back
        </a>
    </div>

    {{-- STATUS --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-4">
        <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Current status</p>
        <p class="text-lg font-semibold text-gray-900" x-text="statusLabel"></p>

        {{-- PROGRESS --}}
        <div class="mt-6 flex items-center">
            <template x-for="(step, i) in steps" :key="i">
                <div class="flex items-center flex-1">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium flex-shrink-0"
                         :class="stepIndex >= i ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-500'">
                        <span x-text="i + 1"></span>
                    </div>
                    <div class="flex-1 h-0.5 mx-1"
                         :class="stepIndex > i ? 'bg-orange-600' : 'bg-gray-200'"
                         x-show="i < steps.length - 1"></div>
                </div>
            </template>
        </div>
        <div class="flex justify-between mt-2">
            <template x-for="(step, i) in steps" :key="'lbl' + i">
                <span class="text-xs" :class="stepIndex >= i ? 'text-gray-700 font-medium' : 'text-gray-400'" x-text="step"></span>
            </template>
        </div>
    </div>

    {{-- REJECTION REASON --}}
    @if ($order->status === 'rejected' && $order->rejection_reason)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <p class="text-xs uppercase tracking-wide text-red-700 font-medium mb-1">Order rejected</p>
            <p class="text-sm text-red-800">Reason: {{ $order->rejection_reason }}</p>
        </div>
    @endif

    {{-- CANCELLATION REASON --}}
    @if ($order->status === 'cancelled' && $order->cancellation_reason)
        <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 mb-4">
            <p class="text-xs uppercase tracking-wide text-gray-700 font-medium mb-1">
                Order cancelled
            </p>
            <p class="text-sm text-gray-800">
                Reason: {{ $order->cancellation_reason }}
            </p>
            @if ($order->cancelled_at)
                <p class="text-xs text-gray-500 mt-2">
                    Cancelled {{ $order->cancelled_at->diffForHumans() }}
                </p>
            @endif
        </div>
    @endif

    {{-- CANCEL ORDER FORM --}}
    @if ($order->canBeCancelledByCustomer())
        <div class="bg-white rounded-lg border border-red-200 p-6 mb-4"
             x-data="{ showCancel: false }">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm font-medium text-gray-900">Need to cancel?</p>
                    <p class="text-xs text-gray-500 mt-1">
                        You can cancel this order while it's still being processed.
                    </p>
                </div>
                <button type="button"
                        @click="showCancel = !showCancel"
                        class="text-red-600 border border-red-300 hover:bg-red-50 px-4 py-2 rounded text-sm font-medium">
                    Cancel Order
                </button>
            </div>

            <div x-show="showCancel"
                 x-cloak
                 x-transition
                 class="mt-4 pt-4 border-t border-red-100">
                <form method="POST"
                      action="{{ route('customer.orders.cancel', $order) }}"
                      class="space-y-3"
                      onsubmit="return confirm('Are you sure you want to cancel this order? This cannot be undone.');">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-1">
                            Reason for cancellation
                        </label>
                        <select name="cancellation_reason"
                                required
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Select a reason...</option>
                            <option value="Changed my mind">Changed my mind</option>
                            <option value="Ordered by mistake">Ordered by mistake</option>
                            <option value="Found a better option">Found a better option</option>
                            <option value="Delivery taking too long">Delivery taking too long</option>
                            <option value="Need to modify my order">Need to modify my order</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-red-700">
                            Confirm Cancellation
                        </button>
                        <button type="button"
                                @click="showCancel = false"
                                class="border border-gray-300 px-4 py-2 rounded text-sm hover:bg-gray-50">
                            Keep Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- CHAT WITH RIDER --}}
    {{-- ============================================ --}}
    @if ($order->canChat())
        <div x-data="chatBox({{ $order->id }})"
             x-init="init()"
             class="bg-white rounded-lg border border-gray-200 mb-4 overflow-hidden">

            {{-- HEADER --}}
            <div class="bg-orange-600 text-white px-5 py-3 flex justify-between items-center cursor-pointer"
                 @click="isOpen = !isOpen">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="font-semibold">Chat with Rider</span>
                    <template x-if="unread > 0">
                        <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full animate-pulse"
                              x-text="unread"></span>
                    </template>
                </div>
                <svg class="w-5 h-5 transition-transform duration-200"
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
                     class="h-96 overflow-y-auto p-4 space-y-2 bg-gray-50 scroll-smooth">

                    <template x-if="loading">
                        <p class="text-center text-sm text-gray-500">Loading messages...</p>
                    </template>

                    <template x-if="!loading && messages.length === 0">
                        <div class="text-center py-12">
                            <p class="text-4xl mb-2">💬</p>
                            <p class="text-sm text-gray-500">No messages yet.</p>
                            <p class="text-xs text-gray-400 mt-1">Say hi to your rider!</p>
                        </div>
                    </template>

                    <template x-for="msg in messages" :key="msg.id">
                        <div :class="msg.sender_id === currentUserId ? 'flex justify-end' : 'flex justify-start'"
                             class="chat-message">
                            <div :class="msg.sender_id === currentUserId
                                    ? 'bg-orange-600 text-white rounded-br-none'
                                    : 'bg-white text-gray-900 border border-gray-200 rounded-bl-none'"
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

                    {{-- TYPING INDICATOR --}}
                    <template x-if="typingName">
                        <div class="flex justify-start chat-message">
                            <div class="bg-white border border-gray-200 px-3 py-2 rounded-2xl rounded-bl-none shadow-sm">
                                <p class="text-xs text-gray-500 mb-1" x-text="typingName + ' is typing'"></p>
                                <div class="flex gap-1">
                                    <span class="typing-dot"></span>
                                    <span class="typing-dot" style="animation-delay: 0.15s"></span>
                                    <span class="typing-dot" style="animation-delay: 0.3s"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- INPUT --}}
                <div class="border-t border-gray-200 p-3 bg-white">
                    <form @submit.prevent="sendMessage()" class="flex gap-2">
                        <input type="text"
                               x-model="newMessage"
                               @input="onTypingInput()"
                               @keydown.enter.prevent="sendMessage()"
                               @blur="stopTyping()"
                               placeholder="Type a message..."
                               maxlength="1000"
                               class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
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

    {{-- LIVE MAP --}}
    @if ($order->rider && in_array($order->status, ['rider_assigned', 'picked_up', 'out_for_delivery']))
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-4">
            <div class="flex justify-between items-center mb-3">
                <p class="text-xs uppercase tracking-wide text-gray-400">Live Rider Location</p>
                <p class="text-xs text-gray-500" x-text="lastUpdated"></p>
            </div>
            <div id="map" class="w-full h-80 rounded-lg border border-gray-200 z-0"></div>

            <div class="mt-3 grid grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded p-3">
                    <p class="text-xs text-gray-500">Distance to you</p>
                    <p class="text-lg font-bold text-gray-900" x-text="distanceText || '—'"></p>
                </div>
                <div class="bg-gray-50 rounded p-3">
                    <p class="text-xs text-gray-500">Estimated arrival</p>
                    <p class="text-lg font-bold text-gray-900" x-text="etaText || '—'"></p>
                </div>
            </div>
        </div>
    @endif

    {{-- DELIVERY --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-4">
        <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">Delivery address</p>
        <p class="text-sm text-gray-700">{{ $order->delivery_address }}</p>

        @if ($order->rider)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Your rider</p>
                <p class="text-sm font-medium text-gray-900">{{ $order->rider->user->name }}</p>
                @if ($order->rider->vehicle_type)
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $order->rider->vehicle_type }}
                        @if ($order->rider->vehicle_plate)
                            · {{ $order->rider->vehicle_plate }}
                        @endif
                    </p>
                @endif
            </div>
        @endif
    </div>

    {{-- ITEMS --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-4">
        <p class="text-xs uppercase tracking-wide text-gray-400 mb-4">Order summary</p>

        <div class="space-y-2">
            @foreach ($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-700">{{ $item->quantity }}× {{ $item->name }}</span>
                    <span class="text-gray-900">₱{{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
            @endforeach
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 space-y-1.5 text-sm">
            <div class="flex justify-between text-gray-500">
                <span>Food cost</span>
                <span>₱{{ number_format($order->food_cost, 2) }}</span>
            </div>
            <div class="flex justify-between text-gray-500">
                <span>Delivery fee</span>
                <span>₱{{ number_format($order->delivery_fee, 2) }}</span>
            </div>
            <div class="flex justify-between font-semibold text-gray-900 pt-2 border-t border-gray-100">
                <span>Total</span>
                <span>₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- RATING FORM --}}
    @if ($order->status === 'delivered' && !$order->restaurant_rating)
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <p class="text-xs uppercase tracking-wide text-gray-400 mb-4">Rate your order</p>
            <form method="POST" action="{{ route('customer.orders.rate', $order) }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm text-gray-700 mb-2">Restaurant</label>
                    <div class="flex gap-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="restaurant_rating" value="{{ $i }}" class="peer sr-only" required>
                                <span class="block text-3xl text-gray-300 peer-checked:text-orange-500 hover:text-orange-400 transition">★</span>
                            </label>
                        @endfor
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">Rider</label>
                    <div class="flex gap-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="rider_rating" value="{{ $i }}" class="peer sr-only" required>
                                <span class="block text-3xl text-gray-300 peer-checked:text-orange-500 hover:text-orange-400 transition">★</span>
                            </label>
                        @endfor
                    </div>
                </div>

                <button class="w-full bg-orange-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-orange-700">
                    Submit
                </button>
            </form>
        </div>
    @endif

    {{-- SHOW RATINGS --}}
    @if ($order->status === 'delivered' && $order->restaurant_rating)
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <p class="text-xs uppercase tracking-wide text-gray-400 mb-4">Your ratings</p>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700">Restaurant</span>
                    <div class="flex gap-0.5">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="text-xl {{ $i <= $order->restaurant_rating ? 'text-orange-500' : 'text-gray-300' }}">★</span>
                        @endfor
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700">Rider</span>
                    <div class="flex gap-0.5">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="text-xl {{ $i <= $order->rider_rating ? 'text-orange-500' : 'text-gray-300' }}">★</span>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="//unpkg.com/alpinejs" defer></script>
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
</style>
<script>
function orderTracker(orderId) {
    return {
        status: '{{ $order->status }}',
        steps: ['Placed', 'Confirmed', 'Preparing', 'Rider', 'On the way', 'Delivered'],
        map: null,
        riderMarker: null,
        restaurantMarker: null,
        deliveryMarker: null,
        routeLine: null,
        lastUpdated: '',
        distanceText: '',
        etaText: '',

        init() {
            window.Echo.private(`order.${orderId}`)
                .listen('.order.status', (e) => { this.status = e.status; location.reload(); })
                .listen('.rider.assigned', (e) => { this.status = e.status; location.reload(); })
                .listen('.no.rider', () => { this.status = 'no_rider'; });

            window.Echo.private(`order.${orderId}`)
                .listen('.rider.location', (e) => {
                    this.updateRiderLocation(e.latitude, e.longitude);
                });

            @if ($order->rider && in_array($order->status, ['rider_assigned', 'picked_up', 'out_for_delivery']))
                this.$nextTick(() => { this.initMap(); });
            @endif
        },

        initMap() {
            const restaurantLat = {{ $order->restaurant->latitude }};
            const restaurantLng = {{ $order->restaurant->longitude }};
            const deliveryLat = {{ $order->delivery_lat }};
            const deliveryLng = {{ $order->delivery_lng }};
            const riderLat = {{ $order->rider->latitude ?? $order->restaurant->latitude }};
            const riderLng = {{ $order->rider->longitude ?? $order->restaurant->longitude }};

            this.map = L.map('map').setView([riderLat, riderLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19,
            }).addTo(this.map);

            const restaurantIcon = L.divIcon({
                html: '<div style="background:#ef4444;color:white;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;border:3px solid white;box-shadow:0 2px 4px rgba(0,0,0,0.3)">🏪</div>',
                className: '',
                iconSize: [36, 36],
                iconAnchor: [18, 18],
            });

            const deliveryIcon = L.divIcon({
                html: '<div style="background:#10b981;color:white;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;border:3px solid white;box-shadow:0 2px 4px rgba(0,0,0,0.3)">🏠</div>',
                className: '',
                iconSize: [36, 36],
                iconAnchor: [18, 18],
            });

            const riderIcon = L.divIcon({
                html: '<div style="background:#f97316;color:white;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;border:3px solid white;box-shadow:0 2px 4px rgba(0,0,0,0.3)">🛵</div>',
                className: '',
                iconSize: [44, 44],
                iconAnchor: [22, 22],
            });

            this.restaurantMarker = L.marker([restaurantLat, restaurantLng], { icon: restaurantIcon })
                .addTo(this.map)
                .bindPopup('🏪 {{ $order->restaurant->name }}');

            this.deliveryMarker = L.marker([deliveryLat, deliveryLng], { icon: deliveryIcon })
                .addTo(this.map)
                .bindPopup('🏠 Your Address');

            @if ($order->rider)
                this.riderMarker = L.marker([riderLat, riderLng], { icon: riderIcon })
                    .addTo(this.map)
                    .bindPopup('🛵 {{ $order->rider->user->name }}');
            @endif

            this.routeLine = L.polyline([
                [restaurantLat, restaurantLng],
                [deliveryLat, deliveryLng],
            ], {
                color: '#f97316',
                weight: 3,
                opacity: 0.5,
                dashArray: '8, 8',
            }).addTo(this.map);

            const bounds = L.latLngBounds([
                [restaurantLat, restaurantLng],
                [deliveryLat, deliveryLng],
            ]);
            this.map.fitBounds(bounds, { padding: [50, 50] });

            this.updateDistance(restaurantLat, restaurantLng, deliveryLat, deliveryLng);
        },

        updateRiderLocation(lat, lng) {
            if (!this.riderMarker) return;
            this.riderMarker.setLatLng([lat, lng]);
            this.lastUpdated = 'Updated: ' + new Date().toLocaleTimeString();

            const deliveryLat = {{ $order->delivery_lat }};
            const deliveryLng = {{ $order->delivery_lng }};
            this.updateDistance(lat, lng, deliveryLat, deliveryLng);
        },

        updateDistance(fromLat, fromLng, toLat, toLng) {
            const R = 6371;
            const dLat = (toLat - fromLat) * Math.PI / 180;
            const dLng = (toLng - fromLng) * Math.PI / 180;
            const a = Math.sin(dLat/2)**2 +
                      Math.cos(fromLat * Math.PI/180) * Math.cos(toLat * Math.PI/180) *
                      Math.sin(dLng/2)**2;
            const distance = R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

            this.distanceText = distance < 1
                ? Math.round(distance * 1000) + ' m'
                : distance.toFixed(1) + ' km';

            const etaMinutes = Math.round((distance / 20) * 60);
            this.etaText = etaMinutes < 1 ? 'Arriving' : etaMinutes + ' min';
        },

        get statusLabel() {
            const labels = {
                'received': 'Waiting for restaurant confirmation',
                'confirmed': 'Restaurant confirmed your order',
                'preparing': 'Preparing your food',
                'finding_rider': 'Finding a rider',
                'rider_assigned': 'Rider on the way to restaurant',
                'picked_up': 'Rider picked up your order',
                'out_for_delivery': 'Order is on the way',
                'delivered': 'Order delivered',
                'no_rider': 'No rider available',
                'cancelled': 'Order cancelled',
                'rejected': 'Order rejected by restaurant',
            };
            return labels[this.status] || this.status.replace(/_/g, ' ');
        },

        get stepIndex() {
            const map = {
                'received': 0, 'confirmed': 1, 'preparing': 2,
                'finding_rider': 3, 'rider_assigned': 3,
                'picked_up': 4, 'out_for_delivery': 4, 'delivered': 5,
                'no_rider': 0, 'cancelled': 0, 'rejected': 0,
            };
            return map[this.status] ?? 0;
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

                    if (this.isAtBottom) {
                        this.scrollToBottom();
                    }
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
                const res = await fetch('{{ route('customer.chat.index', $order) }}', {
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
                const res = await fetch('{{ route('customer.chat.store', $order) }}', {
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
                    this.messages[idx] = {
                        ...data.message,
                        status: 'delivered',
                    };
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
                await fetch('{{ route('customer.chat.typing', $order) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ is_typing: isTyping })
                });
            } catch (err) {
                // Silent fail
            }
        },

        async markAsRead() {
            try {
                await fetch('{{ route('customer.chat.mark-read', $order) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                this.unread = 0;
            } catch (err) {
                // Silent fail
            }
        },

        onScroll() {
            const el = this.$refs.messagesContainer;
            if (!el) return;

            this.isAtBottom = (el.scrollHeight - el.scrollTop - el.clientHeight) < 50;

            if (this.isAtBottom && this.unread > 0) {
                this.markAsRead();
            }
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