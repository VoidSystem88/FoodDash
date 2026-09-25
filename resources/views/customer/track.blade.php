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

    {{-- LIVE MAP --}}
    @if ($order->rider && in_array($order->status, ['rider_assigned', 'picked_up', 'out_for_delivery']))
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-4">
            <div class="flex justify-between items-center mb-3">
                <p class="text-xs uppercase tracking-wide text-gray-400">Live Rider Location</p>
                <p class="text-xs text-gray-500" x-text="lastUpdated"></p>
            </div>
            <div id="map" class="w-full h-80 rounded-lg border border-gray-200 z-0"></div>

            {{-- DISTANCE / ETA --}}
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
            // Real-time order status
            window.Echo.private(`order.${orderId}`)
                .listen('.order.status', (e) => { this.status = e.status; location.reload(); })
                .listen('.rider.assigned', (e) => { this.status = e.status; location.reload(); })
                .listen('.no.rider', () => { this.status = 'no_rider'; });

            // Real-time rider location
            window.Echo.private(`order.${orderId}`)
                .listen('.rider.location', (e) => {
                    this.updateRiderLocation(e.latitude, e.longitude);
                });

            // Initialize map kung may rider
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
            // Haversine formula
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

            // ETA: assuming 20 km/h average
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
</script>
@endpush