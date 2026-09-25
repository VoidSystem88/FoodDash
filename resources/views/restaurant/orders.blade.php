@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 via-blue-500 to-blue-600 rounded-2xl shadow-xl text-white">

        {{-- Decorative --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-cyan-300 rounded-full blur-3xl opacity-20 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            <div class="flex justify-between items-start mb-5">
                <div class="flex items-center gap-3">
                    <a href="{{ route('restaurant.dashboard') }}"
                       class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur flex items-center justify-center transition">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <p class="text-xs text-white/70 uppercase tracking-wider font-medium">Restaurant</p>
                        <h1 class="text-xl font-bold leading-tight">Order History</h1>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-full px-3 py-1.5">
                    <span class="text-xs font-bold uppercase tracking-wide">
                        {{ $orders->total() }} Orders
                    </span>
                </div>
            </div>

            {{-- SUMMARY STATS --}}
            <div class="grid grid-cols-3 gap-3 pt-5 border-t border-white/20">
                <div>
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Total Sales</p>
                    <p class="text-xl font-bold mt-1">₱{{ number_format($stats['total_sales'], 0) }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Delivered</p>
                    <p class="text-xl font-bold mt-1">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Rating</p>
                    <div class="flex items-center gap-1 mt-1">
                        @if ($stats['avg_rating'] !== 'N/A')
                            <span class="text-xl font-bold">{{ $stats['avg_rating'] }}</span>
                            <svg class="w-4 h-4 fill-yellow-300 text-yellow-300" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @else
                            <span class="text-sm font-medium text-white/70">N/A</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FILTERS --}}
    {{-- ============================================ --}}
    <form method="GET" class="bg-white rounded-2xl border border-gray-200 p-5">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <p class="text-sm font-semibold text-gray-900">Filters</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

            {{-- STATUS --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Status</label>
                <select name="status"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white">
                    <option value="">All Status</option>
                    @foreach (['received','confirmed','preparing','finding_rider','rider_assigned','picked_up','out_for_delivery','delivered','cancelled','rejected','no_rider'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>
                            {{ ucfirst(str_replace('_', ' ', $s)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- FROM --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">From</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            {{-- TO --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">To</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            {{-- ACTIONS --}}
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform">
                    Apply
                </button>
                <a href="{{ route('restaurant.orders') }}"
                   class="px-4 py-2.5 text-sm font-semibold text-gray-700 border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- ============================================ --}}
    {{-- ORDER LIST --}}
    {{-- ============================================ --}}
    @if ($orders->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-1">No orders found</h3>
            <p class="text-sm text-gray-500">Try adjusting your filters</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($orders as $order)
                @php
                    $statusStyles = [
                        'received' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700', 'icon' => '🔔'],
                        'confirmed' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-700', 'icon' => '✅'],
                        'preparing' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-700', 'icon' => '👨‍🍳'],
                        'finding_rider' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'text' => 'text-purple-700', 'icon' => '🔍'],
                        'rider_assigned' => ['bg' => 'bg-cyan-50', 'border' => 'border-cyan-200', 'text' => 'text-cyan-700', 'icon' => '🛵'],
                        'picked_up' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'text' => 'text-indigo-700', 'icon' => '📦'],
                        'out_for_delivery' => ['bg' => 'bg-violet-50', 'border' => 'border-violet-200', 'text' => 'text-violet-700', 'icon' => '🚀'],
                        'delivered' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-700', 'icon' => '🎉'],
                        'rejected' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-700', 'icon' => '⚠️'],
                        'cancelled' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-700', 'icon' => '❌'],
                        'no_rider' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-700', 'icon' => '😔'],
                    ];
                    $ss = $statusStyles[$order->status] ?? ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-700', 'icon' => '📋'];
                @endphp

                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-md transition">

                    {{-- HEADER --}}
                    <div class="px-5 py-3 {{ $ss['bg'] }} {{ $ss['border'] }} border-b flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">{{ $ss['icon'] }}</span>
                            <div>
                                <p class="font-bold text-gray-900 text-sm">Order #{{ $order->id }}</p>
                                <p class="text-xs {{ $ss['text'] }} font-semibold uppercase tracking-wide">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y · H:i') }}</p>
                    </div>

                    {{-- BODY --}}
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">
                            {{-- LEFT: INFO --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-4 mb-3">
                                    <div>
                                        <p class="text-xs text-gray-500">Items</p>
                                        <p class="text-sm font-bold text-gray-900">
                                            {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                        </p>
                                    </div>
                                    <div class="w-px h-8 bg-gray-200"></div>
                                    <div>
                                        <p class="text-xs text-gray-500">Total</p>
                                        <p class="text-sm font-bold text-orange-600">₱{{ number_format($order->total_amount, 2) }}</p>
                                    </div>
                                    @if ($order->is_external_order)
                                        <div class="w-px h-8 bg-gray-200"></div>
                                        <span class="text-[10px] bg-gray-100 text-gray-700 px-2 py-1 rounded-full font-semibold uppercase tracking-wide">
                                            External
                                        </span>
                                    @endif
                                </div>

                                {{-- CUSTOMER --}}
                                @if ($order->customer)
                                    <div class="flex items-center gap-2 text-xs text-gray-600 mb-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>{{ $order->customer->name }}</span>
                                    </div>
                                @endif

                                {{-- DELIVERY ADDRESS --}}
                                <div class="flex items-start gap-2 text-xs text-gray-600 mb-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="line-clamp-2">{{ $order->delivery_address }}</span>
                                </div>

                                {{-- RIDER --}}
                                @if ($order->rider)
                                    <div class="flex items-center gap-2 text-xs text-green-700 font-medium mt-2">
                                        <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3 h-3 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        Rider: {{ $order->rider->user->name }}
                                    </div>
                                @endif

                                {{-- RATINGS --}}
                                @if ($order->restaurant_rating)
                                    <div class="flex items-center gap-2 mt-2">
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Rating:</p>
                                        <div class="flex gap-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-3 h-3 {{ $i <= $order->restaurant_rating ? 'text-amber-500 fill-amber-500' : 'text-gray-300' }}"
                                                     viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- RIGHT: ITEM PREVIEW --}}
                            <div class="hidden sm:block flex-shrink-0">
                                <div class="bg-gray-50 rounded-xl p-3 min-w-[140px]">
                                    <p class="text-[10px] text-gray-500 uppercase tracking-wide font-medium mb-2">Items</p>
                                    <div class="space-y-1">
                                        @foreach ($order->items->take(3) as $item)
                                            <p class="text-xs text-gray-700 line-clamp-1">
                                                <span class="font-semibold text-gray-900">{{ $item->quantity }}×</span>
                                                {{ $item->name }}
                                            </p>
                                        @endforeach
                                        @if ($order->items->count() > 3)
                                            <p class="text-[10px] text-gray-400 italic">
                                                + {{ $order->items->count() - 3 }} more
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- PAGINATION --}}
    {{-- ============================================ --}}
    @if ($orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
<style>
    .active\:scale-98:active { transform: scale(0.98); }
</style>
@endpush