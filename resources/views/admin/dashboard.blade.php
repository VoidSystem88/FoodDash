@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ============================================ --}}
    {{-- PAGE HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">System overview and management</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.orders') }}"
               class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                All Orders
            </a>
            <a href="{{ route('admin.reports') }}"
               class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Reports
            </a>
            <a href="{{ route('admin.accounts') }}"
               class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Manage Accounts
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SUMMARY STATS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">

        {{-- TOTAL ORDERS --}}
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Orders</p>
                <div class="w-7 h-7 rounded-md bg-gray-100 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_orders']) }}</p>
        </div>

        {{-- DELIVERED --}}
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Delivered</p>
                <div class="w-7 h-7 rounded-md bg-green-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-green-600">{{ number_format($stats['delivered_orders']) }}</p>
        </div>

        {{-- TOTAL SALES --}}
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Sales</p>
                <div class="w-7 h-7 rounded-md bg-emerald-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">₱{{ number_format($stats['total_sales'], 0) }}</p>
        </div>

        {{-- ACTIVE RESTAURANTS --}}
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Active Restos</p>
                <div class="w-7 h-7 rounded-md bg-blue-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_restaurants']) }}</p>
        </div>

        {{-- ONLINE RIDERS --}}
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Online Riders</p>
                <div class="w-7 h-7 rounded-md bg-cyan-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['online_riders']) }}</p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- PENDING APPROVALS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        {{-- PENDING RESTAURANTS --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-900">Pending Restaurants</h2>
                </div>
                @if ($pendingRestaurants->count() > 0)
                    <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-semibold">
                        {{ $pendingRestaurants->count() }}
                    </span>
                @endif
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($pendingRestaurants as $user)
                    <div class="px-5 py-3 flex justify-between items-center hover:bg-gray-50 transition">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-xs font-semibold text-green-700 hover:text-green-800 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-md transition">
                                Approve
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <p class="text-sm text-gray-400">No pending restaurants</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- PENDING RIDERS --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-900">Pending Riders</h2>
                </div>
                @if ($pendingRiders->count() > 0)
                    <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-semibold">
                        {{ $pendingRiders->count() }}
                    </span>
                @endif
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($pendingRiders as $user)
                    <div class="px-5 py-3 flex justify-between items-center hover:bg-gray-50 transition">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-xs font-semibold text-green-700 hover:text-green-800 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-md transition">
                                Approve
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <p class="text-sm text-gray-400">No pending riders</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

        {{-- ============================================ --}}
    {{-- BRANDING (LOGO CUSTOMIZER) --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-6 transition-colors"
         x-data="logoCustomizer()">

        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h2 class="text-sm font-semibold text-gray-900">Branding</h2>
        </div>

        <div class="p-5">
            <p class="text-xs text-gray-500 mb-4">
                Upload your custom logo and adjust its size. Recommended: transparent PNG or SVG, at least 200×60px.
            </p>

            @php $config = \App\Models\SystemConfig::current(); @endphp

            {{-- CURRENT LOGO PREVIEW --}}
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 mb-4 text-center">
                <p class="text-[10px] text-gray-400 uppercase tracking-wider font-medium mb-3">Preview</p>

                @if ($config->hasLogo())
                    <div class="inline-block bg-white rounded-lg px-6 py-4 border border-gray-200">
                        <img src="{{ $config->logo_url }}"
                             alt="Current logo"
                             class="w-auto"
                             style="height: {{ $config->logo_height_px }}px;">
                    </div>
                    <p class="text-xs text-gray-400 mt-2">
                        Height: {{ $config->logo_height_px }}px
                    </p>
                @else
                    <div class="inline-block">
                        <span class="font-semibold text-2xl text-gray-400">
                            {{ config('app.name', 'FoodDash') }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Using default text logo</p>
                @endif
            </div>

            {{-- UPLOAD FORM --}}
            <form method="POST"
                  action="{{ route('admin.config.logo.upload') }}"
                  enctype="multipart/form-data"
                  class="space-y-3">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">
                        {{ $config->hasLogo() ? 'Replace Logo' : 'Upload Logo' }}
                    </label>

                    <input type="file"
                           name="logo"
                           accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                           @change="validateFile($event)"
                           class="block w-full text-sm text-gray-700
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-medium
                                  file:bg-gray-900 file:text-white
                                  hover:file:bg-gray-800
                                  file:cursor-pointer
                                  border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <p class="text-xs text-gray-500 mt-1.5">
                        Accepted: PNG, JPG, SVG, WebP · Max 2MB
                    </p>
                    <p class="text-xs text-red-600 mt-1" x-show="error" x-text="error"></p>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-gray-900 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        {{ $config->hasLogo() ? 'Replace' : 'Upload' }}
                    </button>
                </div>
            </form>

            {{-- LOGO SIZE CONTROL (only if has logo) --}}
            @if ($config->hasLogo())
                <form method="POST"
                      action="{{ route('admin.config.logo.size') }}"
                      class="mt-5 pt-5 border-t border-gray-100 space-y-4">
                    @csrf

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-xs font-medium text-gray-700">
                                Logo Height
                            </label>
                            <span class="text-xs font-bold text-gray-900">
                                <span x-text="currentSize"></span>px
                            </span>
                        </div>

                        <input type="range"
                               name="logo_height"
                               min="24"
                               max="60"
                               step="2"
                               x-model="currentSize"
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-gray-900">

                        <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                            <span>24px (small)</span>
                            <span>40px (default)</span>
                            <span>60px (large)</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-gray-900 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Apply Size
                        </button>

                        <button type="button"
                                @click="resetSize({{ $config->logo_height_px }})"
                                class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                            Reset
                        </button>
                    </div>
                </form>
            @endif

            {{-- REMOVE LOGO --}}
            @if ($config->hasLogo())
                <form method="POST"
                      action="{{ route('admin.config.logo.remove') }}"
                      onsubmit="return confirm('Remove the custom logo and use the default text?');"
                      class="mt-3 pt-5 border-t border-gray-100">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 text-red-600 hover:text-red-700 border border-red-200 hover:border-red-300 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Remove Custom Logo
                    </button>
                </form>
            @endif
        </div>
    </div>

        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h2 class="text-sm font-semibold text-gray-900">System Configuration</h2>
        </div>

        <form method="POST" action="{{ route('admin.config.update') }}" class="p-5 space-y-4">
            @csrf

            {{-- TOWN ADDRESS --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">
                    Town / City
                </label>
                <input type="text"
                       x-model="townAddress"
                       @input.debounce.800ms="geocode()"
                       placeholder="e.g. Cagayan de Oro City"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent">

                <div class="mt-1.5 text-xs">
                    <template x-if="geocoding">
                        <span class="text-gray-500 flex items-center gap-1.5">
                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Looking up location...
                        </span>
                    </template>
                    <template x-if="!geocoding && lat && lng">
                        <span class="text-green-600 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Location set
                        </span>
                    </template>
                    <template x-if="!geocoding && (!lat || !lng) && townAddress.length >= 3">
                        <span class="text-red-600">Please enter a valid town or city</span>
                    </template>
                </div>
            </div>

            {{-- HIDDEN LAT/LNG --}}
            <input type="hidden" name="town_address" :value="townAddress">
            <input type="hidden" name="town_center_lat" :value="lat">
            <input type="hidden" name="town_center_lng" :value="lng">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">
                        Service Radius (km)
                    </label>
                    <input type="number" step="0.1" name="service_radius_km"
                           value="{{ \App\Models\SystemConfig::current()->service_radius_km }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">
                        Default Delivery Fee (₱)
                    </label>
                    <input type="number" step="0.01" name="default_delivery_fee"
                           value="{{ \App\Models\SystemConfig::current()->default_delivery_fee }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                        :disabled="!lat || !lng"
                        :class="(!lat || !lng) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-800'"
                        class="inline-flex items-center gap-2 bg-gray-900 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Configuration
                </button>
            </div>
        </form>
    </div>

    {{-- ============================================ --}}
    {{-- RECENT ORDERS --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">

        <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-sm font-semibold text-gray-900">Recent Orders</h2>
            </div>
            <a href="{{ route('admin.orders') }}"
               class="text-xs text-gray-600 hover:text-gray-900 font-medium inline-flex items-center gap-1">
                View All
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        @if ($recentOrders->isEmpty())
            <div class="px-5 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-sm text-gray-400">No orders yet</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-2.5 font-medium text-gray-600 text-xs uppercase tracking-wide">#</th>
                            <th class="text-left px-5 py-2.5 font-medium text-gray-600 text-xs uppercase tracking-wide">Restaurant</th>
                            <th class="text-left px-5 py-2.5 font-medium text-gray-600 text-xs uppercase tracking-wide">Customer</th>
                            <th class="text-left px-5 py-2.5 font-medium text-gray-600 text-xs uppercase tracking-wide">Total</th>
                            <th class="text-left px-5 py-2.5 font-medium text-gray-600 text-xs uppercase tracking-wide">Status</th>
                            <th class="text-left px-5 py-2.5 font-medium text-gray-600 text-xs uppercase tracking-wide">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($recentOrders as $order)
                            @php
                                $statusStyles = [
                                    'delivered' => 'bg-green-50 text-green-700',
                                    'cancelled' => 'bg-gray-100 text-gray-600',
                                    'rejected' => 'bg-red-50 text-red-700',
                                    'no_rider' => 'bg-red-50 text-red-700',
                                ];
                                $statusClass = $statusStyles[$order->status] ?? 'bg-amber-50 text-amber-700';
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 font-medium text-gray-900">#{{ $order->id }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ $order->restaurant->name }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ $order->customer->name ?? 'N/A' }}</td>
                                <td class="px-5 py-3 font-medium text-gray-900">₱{{ number_format($order->total_amount, 2) }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-block text-xs px-2 py-0.5 rounded {{ $statusClass }} font-medium">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-500">{{ $order->created_at->format('M d, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function townConfig() {
    return {
        townAddress: '{{ \App\Models\SystemConfig::current()->town_address ?? "Cagayan de Oro City" }}',
        lat: '{{ \App\Models\SystemConfig::current()->town_center_lat }}',
        lng: '{{ \App\Models\SystemConfig::current()->town_center_lng }}',
        geocoding: false,

        async geocode() {
            if (!this.townAddress || this.townAddress.length < 3) {
                this.lat = '';
                this.lng = '';
                return;
            }

            this.geocoding = true;

            try {
                const res = await fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.townAddress)}&limit=1`,
                    { headers: { 'Accept-Language': 'en' } }
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
function logoCustomizer() {
    return {
        error: '',
        currentSize: {{ \App\Models\SystemConfig::current()->logo_height_px }},

        validateFile(event) {
            this.error = '';
            const file = event.target.files[0];
            if (!file) return;

            // Validate size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                this.error = 'File is too large. Maximum 2MB.';
                event.target.value = '';
                return;
            }

            // Validate type
            const allowed = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp'];
            if (!allowed.includes(file.type)) {
                this.error = 'Invalid file type. Use PNG, JPG, SVG, or WebP.';
                event.target.value = '';
                return;
            }
        },

        resetSize(defaultSize) {
            this.currentSize = defaultSize;
        }
    }
}
</script>
@endpush