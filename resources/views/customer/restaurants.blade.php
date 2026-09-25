@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER WITH SEARCH --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-500 to-orange-600 rounded-2xl shadow-xl text-white">

        {{-- Decorative --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-yellow-300 rounded-full blur-3xl opacity-20 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            {{-- TITLE --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center border-2 border-white/30">
                    <span class="text-2xl">🍽️</span>
                </div>
                <div>
                    <p class="text-xs text-white/70 uppercase tracking-wider font-medium">Discover</p>
                    <h1 class="text-xl font-bold leading-tight">Restaurants</h1>
                </div>
            </div>

            {{-- SEARCH BAR --}}
            <form method="GET" action="{{ route('customer.restaurants') }}" class="mb-4">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search restaurants or locations..."
                           class="w-full bg-white dark:bg-gray-900 border-0 rounded-xl pl-12 pr-12 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-white/50 focus:outline-none shadow-lg">

                    @if (request('search'))
                        <a href="{{ route('customer.restaurants', ['cuisine' => request('cuisine')]) }}"
                           class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif

                    @if (request('cuisine'))
                        <input type="hidden" name="cuisine" value="{{ request('cuisine') }}">
                    @endif
                </div>
            </form>

            {{-- MINI STATS --}}
            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-white/20">
                <div>
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Available</p>
                    <p class="text-xl font-bold mt-1">{{ $restaurants->count() }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Cuisines</p>
                    <p class="text-xl font-bold mt-1">{{ isset($cuisines) ? $cuisines->count() : 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- CUISINE CHIPS --}}
    {{-- ============================================ --}}
    @if (isset($cuisines) && $cuisines->count() > 0)
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4 transition-colors">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('customer.restaurants', ['search' => request('search')]) }}"
                   class="text-xs px-3.5 py-2 rounded-full border-2 transition font-medium {{ !request('cuisine') ? 'bg-orange-600 text-white border-orange-600 shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-600' }}">
                    All Cuisines
                </a>
                @foreach ($cuisines as $cuisine)
                    <a href="{{ route('customer.restaurants', ['search' => request('search'), 'cuisine' => $cuisine]) }}"
                       class="text-xs px-3.5 py-2 rounded-full border-2 transition font-medium {{ request('cuisine') === $cuisine ? 'bg-orange-600 text-white border-orange-600 shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-600' }}">
                        {{ $cuisine }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- ACTIVE FILTERS --}}
    {{-- ============================================ --}}
    @if (request('search') || request('cuisine'))
        <div class="bg-orange-50 dark:bg-orange-950/30 border border-orange-200 dark:border-orange-800 rounded-2xl p-4 flex items-center justify-between transition-colors">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <p class="text-xs text-gray-700 dark:text-gray-300">
                    Showing
                    @if (request('search'))
                        results for "<strong class="text-gray-900 dark:text-white">{{ request('search') }}</strong>"
                    @endif
                    @if (request('cuisine'))
                        in <strong class="text-gray-900 dark:text-white">{{ request('cuisine') }}</strong>
                    @endif
                    · <span class="text-orange-600 dark:text-orange-400 font-semibold">{{ $restaurants->count() }} {{ Str::plural('result', $restaurants->count()) }}</span>
                </p>
            </div>

            <a href="{{ route('customer.restaurants') }}"
               class="text-xs text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-semibold">
                Clear
            </a>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- RESTAURANTS LIST --}}
    {{-- ============================================ --}}
    @if ($restaurants->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-12 text-center transition-colors">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                <span class="text-4xl">🔍</span>
            </div>
            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                @if (request('search') || request('cuisine'))
                    No restaurants found
                @else
                    No restaurants available
                @endif
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                @if (request('search') || request('cuisine'))
                    Try adjusting your search or filter
                @else
                    Check back later for available restaurants
                @endif
            </p>
            @if (request('search') || request('cuisine'))
                <a href="{{ route('customer.restaurants') }}"
                   class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-95 transition transform">
                    Clear Filters
                </a>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($restaurants as $restaurant)
                @php
                    $isOpen = $restaurant->isOpenNow();
                @endphp

                <a href="{{ route('customer.restaurants.show', $restaurant) }}"
                   class="group block bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-lg hover:border-gray-300 dark:hover:border-gray-700 transition-all duration-200">

                    {{-- BANNER --}}
                    <div class="relative h-32 overflow-hidden">
                        @if ($restaurant->cover_image_url)
                            <img src="{{ $restaurant->cover_image_url }}"
                                 alt="{{ $restaurant->name }}"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-orange-400 via-orange-500 to-amber-500"></div>
                            <div class="absolute inset-0 opacity-10"
                                 style="background-image: radial-gradient(circle at 20% 50%, white 2px, transparent 2px), radial-gradient(circle at 80% 80%, white 2px, transparent 2px); background-size: 40px 40px;"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-5xl opacity-40">🍽️</span>
                            </div>
                        @endif

                        {{-- STATUS PILL --}}
                        <div class="absolute top-3 left-3">
                            @if ($isOpen)
                                <span class="inline-flex items-center gap-1.5 bg-white/95 dark:bg-gray-900/95 backdrop-blur text-green-700 dark:text-green-400 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                    Open
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 bg-white/95 dark:bg-gray-900/95 backdrop-blur text-red-700 dark:text-red-400 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Closed
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- BODY --}}
                    <div class="p-5">
                        {{-- NAME + ITEM COUNT --}}
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <h2 class="font-bold text-gray-900 dark:text-white text-base leading-tight group-hover:text-orange-600 dark:group-hover:text-orange-400 transition line-clamp-1">
                                {{ $restaurant->name }}
                            </h2>
                            <span class="text-[10px] bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 px-2 py-1 rounded-full font-semibold whitespace-nowrap">
                                {{ $restaurant->menu_items_count }} items
                            </span>
                        </div>

                        {{-- CUISINE --}}
                        @if ($restaurant->cuisine)
                            <span class="inline-block text-[10px] bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 px-2 py-0.5 rounded-full font-semibold uppercase tracking-wide mb-2">
                                {{ $restaurant->cuisine }}
                            </span>
                        @endif

                        {{-- ADDRESS --}}
                        <div class="flex items-start gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-3">
                            <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="line-clamp-1">{{ $restaurant->address }}</span>
                        </div>

                        {{-- FOOTER --}}
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center gap-1 text-xs">
                                @if ($isOpen)
                                    <span class="font-medium text-green-600 dark:text-green-400">Open now</span>
                                @else
                                    <span class="font-medium text-red-600 dark:text-red-400 line-clamp-1">{{ $restaurant->status_label }}</span>
                                @endif
                            </div>

                            <span class="text-xs font-semibold text-orange-600 dark:text-orange-400 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                                View Menu
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection

@push('scripts')
{{-- Alpine.js na-load na sa layouts/app.blade.php --}}
@endpush