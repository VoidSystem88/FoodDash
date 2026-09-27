@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ============================================ --}}
    {{-- PAGE HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">All Orders</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ number_format($orders->total()) }} {{ Str::plural('order', $orders->total()) }} found
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 font-medium px-3 py-2 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.orders.export', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export CSV
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FILTERS --}}
    {{-- ============================================ --}}
    <form method="GET" class="bg-white rounded-lg border border-gray-200 p-4 mb-4">

        <div class="flex items-center gap-2 mb-3">
            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <h2 class="text-sm font-semibold text-gray-900">Filters</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-3">

            {{-- STATUS --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Status</label>
                <select name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    <option value="">All Status</option>
                    @foreach(['received','confirmed','preparing','finding_rider','rider_assigned','picked_up','out_for_delivery','delivered','rejected','cancelled','no_rider'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>
                            {{ ucfirst(str_replace('_', ' ', $s)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- RESTAURANT --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Restaurant</label>
                <select name="restaurant_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    <option value="">All Restaurants</option>
                    @foreach ($restaurants as $r)
                        <option value="{{ $r->id }}" @selected(request('restaurant_id') == $r->id)>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- RIDER --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Rider</label>
                <select name="rider_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    <option value="">All Riders</option>
                    @foreach ($riders as $r)
                        <option value="{{ $r->id }}" @selected(request('rider_id') == $r->id)>
                            {{ $r->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- FROM --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">From</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent">
            </div>

            {{-- TO --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">To</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent">
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-100">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-gray-900 text-white hover:bg-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Apply Filters
            </button>

            @if (request()->hasAny(['status', 'restaurant_id', 'rider_id', 'from', 'to']))
                <a href="{{ route('admin.orders') }}"
                   class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear Filters
                </a>
            @endif
        </div>
    </form>

    {{-- ============================================ --}}
    {{-- ORDERS TABLE --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 text-xs uppercase tracking-wide">Order</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 text-xs uppercase tracking-wide">Date</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 text-xs uppercase tracking-wide">Customer</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 text-xs uppercase tracking-wide">Restaurant</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 text-xs uppercase tracking-wide">Rider</th>
                        <th class="text-right px-5 py-3 font-medium text-gray-600 text-xs uppercase tracking-wide">Total</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 text-xs uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($orders as $o)
                        @php
                            $statusStyles = [
                                'delivered' => 'bg-green-50 text-green-700 border-green-200',
                                'cancelled' => 'bg-gray-100 text-gray-700 border-gray-200',
                                'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                'no_rider' => 'bg-red-50 text-red-700 border-red-200',
                                'received' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200',
                            ];
                            $statusClass = $statusStyles[$o->status] ?? 'bg-amber-50 text-amber-700 border-amber-200';
                        @endphp
                        <tr class="hover:bg-gray-50 transition">

                            {{-- ORDER ID --}}
                            <td class="px-5 py-3">
                                <span class="font-semibold text-gray-900">#{{ $o->id }}</span>
                                @if ($o->is_external_order)
                                    <span class="ml-1.5 inline-block text-[10px] bg-purple-50 text-purple-700 border border-purple-200 px-1.5 py-0.5 rounded font-medium">
                                        EXTERNAL
                                    </span>
                                @endif
                            </td>

                            {{-- DATE --}}
                            <td class="px-5 py-3 text-xs text-gray-500 whitespace-nowrap">
                                <div>{{ $o->created_at->format('M d, Y') }}</div>
                                <div class="text-gray-400">{{ $o->created_at->format('H:i') }}</div>
                            </td>

                            {{-- CUSTOMER --}}
                            <td class="px-5 py-3 text-gray-700">
                                {{ $o->customer->name ?? '—' }}
                            </td>

                            {{-- RESTAURANT --}}
                            <td class="px-5 py-3 text-gray-700">
                                {{ $o->restaurant->name ?? '—' }}
                            </td>

                            {{-- RIDER --}}
                            <td class="px-5 py-3">
                                @if ($o->rider && $o->rider->user)
                                    <span class="text-gray-700">{{ $o->rider->user->name }}</span>
                                @else
                                    <span class="text-xs text-gray-400 italic">Unassigned</span>
                                @endif
                            </td>

                            {{-- TOTAL --}}
                            <td class="px-5 py-3 text-right font-semibold text-gray-900 whitespace-nowrap">
                                ₱{{ number_format($o->total_amount, 2) }}
                            </td>

                            {{-- STATUS --}}
                            <td class="px-5 py-3">
                                <span class="inline-block text-xs px-2 py-0.5 rounded-md border font-medium {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $o->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-sm text-gray-500 font-medium">No orders found</p>
                                @if (request()->hasAny(['status', 'restaurant_id', 'rider_id', 'from', 'to']))
                                    <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                                    <a href="{{ route('admin.orders') }}"
                                       class="inline-block mt-3 text-xs font-medium text-gray-700 hover:text-gray-900 underline">
                                        Clear all filters
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

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