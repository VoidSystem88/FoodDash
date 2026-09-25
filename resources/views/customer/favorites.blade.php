@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-red-500 via-red-500 to-pink-600 rounded-2xl shadow-xl text-white">

        {{-- Decorative --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-pink-300 rounded-full blur-3xl opacity-20 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            {{-- TITLE --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center border-2 border-white/30">
                    <span class="text-2xl">❤️</span>
                </div>
                <div>
                    <p class="text-xs text-white/70 uppercase tracking-wider font-medium">Saved</p>
                    <h1 class="text-xl font-bold leading-tight">My Favorites</h1>
                </div>
            </div>

            <p class="text-sm text-white/90 max-w-md mb-5">
                List of your favorite restaurants and cuisines
            </p>

            {{-- MINI STATS --}}
            <div class="grid grid-cols-2 gap-3 pt-5 border-t border-white/20">
                <div>
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Saved</p>
                    <p class="text-xl font-bold mt-1">{{ $favorites->count() }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Total Cuisines</p>
                    <p class="text-xl font-bold mt-1">{{ $favorites->pluck('cuisine')->filter()->unique()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
    @if (session('success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-xl text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- FAVORITES LIST --}}
    {{-- ============================================ --}}
    @if ($favorites->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-12 text-center transition-colors">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-50 dark:bg-red-950/40 mb-4">
                <span class="text-4xl">💔</span>
            </div>
            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">No favorites yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Tap the heart button to save your favorite restaurants
            </p>
            <a href="{{ route('customer.restaurants') }}"
               class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-95 transition transform">
                Browse Restaurants
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($favorites as $restaurant)
                @php
                    $isOpen = $restaurant->isOpenNow();
                @endphp

                <div class="relative group bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-lg hover:border-gray-300 dark:hover:border-gray-700 transition-all duration-200"
                     x-data="favoriteToggle({{ $restaurant->id }})">

                    {{-- LINK WRAPPER --}}
                    <a href="{{ route('customer.restaurants.show', $restaurant) }}"
                       class="block">

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
                                    <span class="inline-flex items-center gap-1.5 bg-white/95 dark:bg-gray-900/95 backdrop-blur text-red-700 dark:text-red-400 text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm px-2.5 py-1">
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

                    {{-- HEART BUTTON --}}
                    <button @click.stop.prevent="removeFavorite()"
                            type="button"
                            :disabled="loading"
                            :class="loading ? 'opacity-50' : ''"
                            title="Remove from favorites"
                            class="absolute top-3 right-3 z-10 w-9 h-9 rounded-full bg-white/95 dark:bg-gray-900/95 backdrop-blur shadow-md flex items-center justify-center hover:scale-110 active:scale-95 transition transform">
                        <svg class="w-5 h-5 text-red-500 fill-red-500"
                             viewBox="0 0 24 24"
                             fill="currentColor">
                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function favoriteToggle(restaurantId) {
    return {
        restaurantId,
        loading: false,

        async removeFavorite() {
            if (this.loading) return;

            this.loading = true;

            try {
                const res = await fetch(`/favorites/${this.restaurantId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await res.json();

                // Nasa favorites page — i-reload para ma-update ang list
                if (!data.is_favorite) {
                    setTimeout(() => location.reload(), 200);
                }
            } catch (err) {
                console.error(err);
                this.loading = false;
            }
        }
    }
}
</script>
@endpush