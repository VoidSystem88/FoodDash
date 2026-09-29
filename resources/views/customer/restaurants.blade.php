@extends('layouts.app')

@section('content')
<div x-data="restaurantList()" x-init="init()" class="max-w-5xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- GUEST WELCOME BANNER --}}
    {{-- ============================================ --}}
    @guest
       
    @endguest

    {{-- ============================================ --}}
    {{-- HERO HEADER WITH SEARCH --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-500 to-orange-600 rounded-2xl shadow-xl text-white">

        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-yellow-300 rounded-full blur-3xl opacity-20 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center border-2 border-white/30">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-white/70 uppercase tracking-wider font-medium">Discover</p>
                    <h1 class="text-xl font-bold leading-tight">Restaurants</h1>
                </div>
            </div>

            {{-- SEARCH BAR --}}
            <form method="GET" action="{{ route('customer.restaurants') }}" class="mb-4">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-neutral-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search restaurants or locations..."
                           class="w-full bg-white dark:bg-[#141414] border-0 rounded-xl pl-12 pr-12 py-3 text-sm text-neutral-900 dark:text-white placeholder-neutral-400 dark:placeholder-neutral-500 focus:ring-2 focus:ring-white/50 focus:outline-none shadow-lg">

                    @if (request('search'))
                        <a href="{{ route('customer.restaurants', ['cuisine' => request('cuisine')]) }}"
                           class="absolute right-4 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif

                    @if (request('cuisine'))
                        <input type="hidden" name="cuisine" value="{{ request('cuisine') }}">
                    @endif
                </div>
            </form>

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
        <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('customer.restaurants', ['search' => request('search')]) }}"
                   class="text-xs px-3.5 py-2 rounded-full border-2 transition font-semibold {{ !request('cuisine')
                        ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white border-transparent shadow-md'
                        : 'bg-white dark:bg-[#0a0a0a] text-neutral-700 dark:text-neutral-300 border-neutral-200 dark:border-[#262626] hover:border-orange-400 dark:hover:border-orange-700' }}">
                    All Cuisines
                </a>
                @foreach ($cuisines as $cuisine)
                    <a href="{{ route('customer.restaurants', ['search' => request('search'), 'cuisine' => $cuisine]) }}"
                       class="text-xs px-3.5 py-2 rounded-full border-2 transition font-semibold {{ request('cuisine') === $cuisine
                            ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white border-transparent shadow-md'
                            : 'bg-white dark:bg-[#0a0a0a] text-neutral-700 dark:text-neutral-300 border-neutral-200 dark:border-[#262626] hover:border-orange-400 dark:hover:border-orange-700' }}">
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
        <div class="bg-gradient-to-r from-orange-50 to-orange-100/50 dark:from-orange-950/30 dark:to-orange-900/10 border border-orange-200 dark:border-orange-800/50 rounded-2xl p-4 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-sm">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </div>
                <p class="text-xs text-neutral-700 dark:text-neutral-300">
                    Showing
                    @if (request('search'))
                        results for "<strong class="text-neutral-900 dark:text-white">{{ request('search') }}</strong>"
                    @endif
                    @if (request('cuisine'))
                        in <strong class="text-neutral-900 dark:text-white">{{ request('cuisine') }}</strong>
                    @endif
                    · <span class="text-orange-600 dark:text-orange-400 font-bold">{{ $restaurants->count() }} {{ Str::plural('result', $restaurants->count()) }}</span>
                </p>
            </div>

            <a href="{{ route('customer.restaurants') }}"
               class="text-xs text-orange-600 dark:text-orange-400 hover:underline font-bold">
                Clear
            </a>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- HEADER WITH VIEW TOGGLE --}}
    {{-- ============================================ --}}
    @if ($restaurants->count() > 0)
        <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-4">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-neutral-900 dark:text-white">Restaurants</h2>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $restaurants->count() }} available</p>
                    </div>
                </div>

                {{-- VIEW TOGGLE --}}
                <div class="flex items-center gap-1 bg-neutral-200 dark:bg-[#0a0a0a] rounded-lg p-1 border border-neutral-200 dark:border-[#262626]">
                    <button type="button" @click="viewMode = 'grid'"
                            :class="viewMode === 'grid' ? 'bg-gradient-to-r from-orange-500 to-orange-600 shadow-sm text-white' : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-700'"
                            class="p-2 rounded-md transition" title="Card view">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    <button type="button" @click="viewMode = 'list'"
                            :class="viewMode === 'list' ? 'bg-gradient-to-r from-orange-500 to-orange-600 shadow-sm text-white' : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-700'"
                            class="p-2 rounded-md transition" title="List view">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- EMPTY STATE --}}
    {{-- ============================================ --}}
    @if ($restaurants->isEmpty())
        <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-12 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-950/40 dark:to-orange-900/20 mb-4">
                <svg class="w-10 h-10 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <h3 class="font-semibold text-neutral-900 dark:text-white mb-1">
                @if (request('search') || request('cuisine'))
                    No restaurants found
                @else
                    No restaurants available
                @endif
            </h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">
                @if (request('search') || request('cuisine'))
                    Try adjusting your search or filter
                @else
                    Check back later for available restaurants
                @endif
            </p>
            @if (request('search') || request('cuisine'))
                <a href="{{ route('customer.restaurants') }}"
                   class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg transition">
                    Clear Filters
                </a>
            @endif
        </div>
    @else

        {{-- ============================================ --}}
        {{-- GRID VIEW -- 2-3 columns --}}
        {{-- ============================================ --}}
        <div x-show="viewMode === 'grid'" class="grid grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
            @foreach ($restaurants as $restaurant)
                @php $isOpen = $restaurant->isOpenNow(); @endphp

                <a href="{{ route('customer.restaurants.show', $restaurant) }}"
                   class="group block bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden hover:shadow-lg hover:border-orange-300 dark:hover:border-orange-800/50 transition-all duration-200">

                    {{-- COVER IMAGE --}}
                    <div class="relative aspect-square overflow-hidden">
                        @if ($restaurant->cover_image_url)
                            <img src="{{ $restaurant->cover_image_url }}"
                                 alt="{{ $restaurant->name }}"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 flex items-center justify-center">
                                <svg class="w-20 h-20 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        @endif

                        {{-- STATUS BADGE --}}
                        <div class="absolute top-2 left-2 z-10">
                            @if ($isOpen)
                                <span class="inline-flex items-center gap-1 bg-white/95 dark:bg-[#0a0a0a]/95 backdrop-blur text-orange-600 dark:text-orange-400 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full shadow-sm border border-orange-200 dark:border-orange-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                    Open
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-white/95 dark:bg-[#0a0a0a]/95 backdrop-blur text-neutral-600 dark:text-neutral-400 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full shadow-sm border border-neutral-200 dark:border-[#262626]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-neutral-400"></span>
                                    Closed
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- BODY --}}
                    <div class="p-3">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h2 class="font-bold text-neutral-900 dark:text-white text-sm leading-tight line-clamp-1 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition">
                                {{ $restaurant->name }}
                            </h2>
                        </div>
                        {{-- BADGE — custom o cuisine --}}
                        @if ($restaurant->display_badge)
                            <span class="inline-block text-[10px] bg-gradient-to-r from-orange-500 to-orange-600 text-white px-2 py-0.5 rounded-full font-bold uppercase tracking-wider mb-2 shadow-sm">
                                {{ $restaurant->display_badge }}
                            </span>
                        @endif

                        <div class="flex items-center gap-1.5 text-xs text-neutral-500 dark:text-neutral-400 mb-2">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="line-clamp-1">{{ $restaurant->address }}</span>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-neutral-100 dark:border-[#262626]">
                            <div class="flex items-center gap-1 text-xs">
                                @if ($restaurant->rating_count > 0)
                                    <svg class="w-3.5 h-3.5 text-orange-500 fill-orange-500" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="font-bold text-neutral-900 dark:text-white">{{ number_format($restaurant->rating_avg, 1) }}</span>
                                    <span class="text-neutral-400 dark:text-neutral-500">({{ $restaurant->rating_count }})</span>
                                @else
                                    <svg class="w-3.5 h-3.5 text-neutral-300 dark:text-neutral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                    <span class="text-neutral-400 dark:text-neutral-500 italic">No ratings</span>
                                @endif
                            </div>

                            <span class="text-[10px] bg-neutral-100 dark:bg-[#262626] text-neutral-600 dark:text-neutral-400 px-2 py-0.5 rounded-full font-bold whitespace-nowrap">
                                {{ $restaurant->menu_items_count }} items
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- ============================================ --}}
        {{-- LIST VIEW --}}
        {{-- ============================================ --}}
        <div x-show="viewMode === 'list'" x-cloak
             class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden">
            @foreach ($restaurants as $restaurant)
                @php $isOpen = $restaurant->isOpenNow(); @endphp

                <a href="{{ route('customer.restaurants.show', $restaurant) }}"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] transition border-b border-neutral-100 dark:border-[#262626] last:border-0 group">

                    {{-- Cover thumbnail --}}
                    <div class="relative w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                        @if ($restaurant->cover_image_url)
                            <img src="{{ $restaurant->cover_image_url }}"
                                 alt="{{ $restaurant->name }}"
                                 class="absolute inset-0 w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center">
                                <svg class="w-8 h-8 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        @endif

                        {{-- Status dot --}}
                        <span class="absolute top-1 right-1 w-3 h-3 rounded-full border-2 border-white dark:border-[#141414] {{ $isOpen ? 'bg-orange-500' : 'bg-neutral-400' }}"></span>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <p class="font-bold text-sm text-neutral-900 dark:text-white truncate group-hover:text-orange-600 dark:group-hover:text-orange-400 transition">
                                {{ $restaurant->name }}
                            </p>
                            @if ($restaurant->cuisine)
                                <span class="text-[10px] bg-gradient-to-r from-orange-500 to-orange-600 text-white px-1.5 py-0.5 rounded-full font-bold uppercase tracking-wide flex-shrink-0">
                                    {{ $restaurant->cuisine }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-1.5 text-xs text-neutral-500 dark:text-neutral-400">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">{{ $restaurant->address }}</span>
                        </div>
                    </div>

                    {{-- Right side: rating + items --}}
                    <div class="flex-shrink-0 text-right">
                        <div class="flex items-center justify-end gap-1 text-xs mb-1">
                            @if ($restaurant->rating_count > 0)
                                <svg class="w-3.5 h-3.5 text-orange-500 fill-orange-500" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="font-bold text-neutral-900 dark:text-white">{{ number_format($restaurant->rating_avg, 1) }}</span>
                            @else
                                <span class="text-[10px] text-neutral-400 dark:text-neutral-500 italic">No ratings</span>
                            @endif
                        </div>
                        <p class="text-[10px] text-neutral-500 dark:text-neutral-400">
                            {{ $restaurant->menu_items_count }} items
                        </p>
                    </div>

                    {{-- Arrow --}}
                    <svg class="w-4 h-4 text-neutral-400 flex-shrink-0 group-hover:translate-x-0.5 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function restaurantList() {
    return {
        viewMode: 'grid',
        viewKey: 'fooddash_restaurants_view',

        init() {
            const saved = localStorage.getItem(this.viewKey);
            if (saved && ['grid', 'list'].includes(saved)) {
                this.viewMode = saved;
            }

            this.$watch('viewMode', (v) => localStorage.setItem(this.viewKey, v));
        }
    }
}
</script>
@endpush