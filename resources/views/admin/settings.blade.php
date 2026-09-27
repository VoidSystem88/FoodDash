@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ============================================ --}}
    {{-- PAGE HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-neutral-100">Admin Settings</h1>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">
                Manage branding and system configuration
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white font-medium transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>

    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    @php $config = \App\Models\SystemConfig::current(); @endphp

    {{-- ============================================ --}}
    {{-- BRANDING (LOGO CUSTOMIZER) --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 overflow-hidden mb-6 transition-colors"
         x-data="logoCustomizer()">

        <div class="px-5 py-3 border-b border-gray-100 dark:border-dark-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-neutral-100">Branding</h2>
        </div>

        <div class="p-5">
            <p class="text-xs text-gray-500 dark:text-neutral-400 mb-4">
                Upload your custom logo and adjust its size. Recommended: transparent PNG or SVG, at least 200×60px.
            </p>

            {{-- CURRENT LOGO PREVIEW --}}
            <div class="bg-gray-50 dark:bg-dark-850 rounded-lg border border-gray-200 dark:border-dark-600 p-6 mb-4 text-center">
                <p class="text-[10px] text-gray-400 dark:text-neutral-500 uppercase tracking-wider font-medium mb-3">Preview</p>

                @if ($config->hasLogo())
                    <div class="inline-block bg-white dark:bg-dark-800 rounded-lg px-6 py-4 border border-gray-200 dark:border-dark-600">
                        <img src="{{ $config->logo_url }}"
                             alt="Current logo"
                             class="w-auto"
                             style="height: {{ $config->logo_height_px }}px;">
                    </div>
                    <p class="text-xs text-gray-400 dark:text-neutral-500 mt-2">
                        Height: {{ $config->logo_height_px }}px
                    </p>
                @else
                    <div class="inline-block">
                        <span class="font-semibold text-2xl text-gray-400 dark:text-neutral-500">
                            {{ config('app.name', 'FoodDash') }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-neutral-500 mt-2">Using default text logo</p>
                @endif
            </div>

            {{-- UPLOAD FORM --}}
            <form method="POST"
                  action="{{ route('admin.config.logo.upload') }}"
                  enctype="multipart/form-data"
                  class="space-y-3">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300 mb-1.5">
                        {{ $config->hasLogo() ? 'Replace Logo' : 'Upload Logo' }}
                    </label>

                    <input type="file"
                           name="logo"
                           accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                           @change="validateFile($event)"
                           class="block w-full text-sm text-gray-700 dark:text-neutral-300
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-medium
                                  file:bg-gray-900 file:text-white
                                  hover:file:bg-gray-800
                                  file:cursor-pointer
                                  border border-gray-300 dark:border-dark-600 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1.5">
                        Accepted: PNG, JPG, SVG, WebP · Max 2MB
                    </p>
                    <p class="text-xs text-red-600 mt-1" x-show="error" x-text="error"></p>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-gray-900 dark:bg-dark-700 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        {{ $config->hasLogo() ? 'Replace' : 'Upload' }}
                    </button>
                </div>
            </form>

            {{-- LOGO SIZE CONTROL --}}
            @if ($config->hasLogo())
                <form method="POST"
                      action="{{ route('admin.config.logo.size') }}"
                      class="mt-5 pt-5 border-t border-gray-100 dark:border-dark-700 space-y-4">
                    @csrf

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-xs font-medium text-gray-700 dark:text-neutral-300">
                                Logo Height
                            </label>
                            <span class="text-xs font-bold text-gray-900 dark:text-neutral-100">
                                <span x-text="currentSize"></span>px
                            </span>
                        </div>

                        <input type="range"
                               name="logo_height"
                               min="24"
                               max="60"
                               step="2"
                               x-model="currentSize"
                               class="w-full h-2 bg-gray-200 dark:bg-dark-700 rounded-lg appearance-none cursor-pointer accent-gray-900 dark:accent-gray-400">

                        <div class="flex justify-between text-[10px] text-gray-400 dark:text-neutral-500 mt-1">
                            <span>24px (small)</span>
                            <span>40px (default)</span>
                            <span>60px (large)</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-gray-900 dark:bg-dark-700 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Apply Size
                        </button>

                        <button type="button"
                                @click="resetSize({{ $config->logo_height_px }})"
                                class="inline-flex items-center gap-2 border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-dark-850 transition">
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
                      class="mt-3 pt-5 border-t border-gray-100 dark:border-dark-700">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 border border-red-200 dark:border-red-800 hover:border-red-300 dark:hover:border-red-700 hover:bg-red-50 dark:hover:bg-red-950/30 px-4 py-2 rounded-lg text-sm font-medium transition">
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

    {{-- ============================================ --}}
    {{-- SYSTEM CONFIGURATION --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 overflow-hidden transition-colors"
         x-data="townConfig()">

        <div class="px-5 py-3 border-b border-gray-100 dark:border-dark-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-neutral-100">System Configuration</h2>
        </div>

        <form method="POST" action="{{ route('admin.config.update') }}" class="p-5 space-y-4">
            @csrf

            {{-- TOWN ADDRESS --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300 mb-1.5">
                    Town / City
                </label>
                <input type="text"
                       x-model="townAddress"
                       @input.debounce.800ms="geocode()"
                       placeholder="e.g. Cagayan de Oro City"
                       class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent">

                <div class="mt-1.5 text-xs">
                    <template x-if="geocoding">
                        <span class="text-gray-500 dark:text-neutral-400 flex items-center gap-1.5">
                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Looking up location...
                        </span>
                    </template>
                    <template x-if="!geocoding && lat && lng">
                        <span class="text-green-600 dark:text-green-400 flex items-center gap-1">
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

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300 mb-1.5">
                        Service Radius (km)
                    </label>
                    <input type="number" step="0.1" name="service_radius_km"
                           value="{{ $config->service_radius_km }}"
                           class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300 mb-1.5">
                        Default Delivery Fee (₱)
                    </label>
                    <input type="number" step="0.01" name="default_delivery_fee"
                           value="{{ $config->default_delivery_fee }}"
                           class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300 mb-1.5">
                        Platform Commission (%)
                    </label>
                    <div class="relative">
                        <input type="number" step="0.1" min="0" max="50" name="commission_rate"
                               value="{{ $config->commission_rate ?? 10 }}"
                               class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-lg pl-3 pr-10 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500 text-sm font-medium">%</span>
                    </div>
                    <p class="text-[10px] text-gray-400 dark:text-neutral-500 mt-1">
                        FoodDash earns {{ $config->commission_rate ?? 10 }}% of every food order
                    </p>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                        :disabled="!lat || !lng"
                        :class="(!lat || !lng) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-800 dark:hover:bg-gray-600'"
                        class="inline-flex items-center gap-2 bg-gray-900 dark:bg-dark-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Configuration
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function logoCustomizer() {
    return {
        error: '',
        currentSize: {{ \App\Models\SystemConfig::current()->logo_height_px }},

        validateFile(event) {
            this.error = '';
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                this.error = 'File is too large. Maximum 2MB.';
                event.target.value = '';
                return;
            }

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
</script>
@endpush