@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-5" x-data="imageUploader()">

    {{-- HEADER --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Restaurant Profile</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your restaurant details and images</p>
        </div>

        <a href="{{ route('restaurant.dashboard') }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            ← Back
        </a>
    </div>

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- PREVIEW BANNER --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">

        <div class="relative h-40 bg-gradient-to-br from-orange-500 via-orange-400 to-amber-400">
            @if ($restaurant->cover_image_url)
                <img src="{{ $restaurant->cover_image_url }}"
                     alt="Cover"
                     class="absolute inset-0 w-full h-full object-cover">
            @else
                {{-- Decorative pattern --}}
                <div class="absolute inset-0 opacity-20"
                     style="background-image: radial-gradient(circle at 20% 50%, white 2px, transparent 2px), radial-gradient(circle at 80% 80%, white 2px, transparent 2px); background-size: 40px 40px;"></div>
            @endif

            {{-- COVER UPLOAD OVERLAY --}}
            <div class="absolute inset-0 bg-black/0 hover:bg-black/30 transition flex items-center justify-center group">
                <label class="cursor-pointer opacity-0 group-hover:opacity-100 transition">
                    <input type="file"
                           @change="submitFile($event, '{{ route('restaurant.profile.cover') }}')"
                           accept="image/*"
                           class="hidden">
                    <span class="bg-white/95 backdrop-blur text-gray-900 px-5 py-2.5 rounded-full font-semibold text-sm shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $restaurant->cover_image_url ? 'Change Cover' : 'Upload Cover' }}
                    </span>
                </label>
            </div>

            @if ($restaurant->cover_image_url)
                <form method="POST" action="{{ route('restaurant.profile.cover.remove') }}"
                      onsubmit="return confirm('Remove cover image?');"
                      class="absolute top-3 right-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-white/95 backdrop-blur text-red-600 px-3 py-1.5 rounded-full font-semibold text-xs shadow-md hover:bg-white transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Remove
                    </button>
                </form>
            @endif
        </div>

        {{-- PROFILE IMAGE + NAME --}}
        <div class="relative px-6 -mt-12">
            <div class="flex items-end gap-4">
                {{-- PROFILE IMAGE --}}
                <div class="relative group flex-shrink-0">
                    <label class="cursor-pointer block">
                        <input type="file"
                               @change="submitFile($event, '{{ route('restaurant.profile.image') }}')"
                               accept="image/*"
                               class="hidden">
                        @if ($restaurant->profile_image_url)
                            <img src="{{ $restaurant->profile_image_url }}"
                                 alt="{{ $restaurant->name }}"
                                 class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-lg group-hover:opacity-75 transition">
                        @else
                            <div class="w-24 h-24 rounded-2xl bg-white border-4 border-white shadow-lg flex items-center justify-center group-hover:opacity-75 transition">
                                <span class="text-4xl">🍽️</span>
                            </div>
                        @endif

                        <div class="absolute inset-0 rounded-2xl bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </label>

                    @if ($restaurant->profile_image_url)
                        <form method="POST" action="{{ route('restaurant.profile.image.remove') }}"
                              onsubmit="return confirm('Remove profile image?');"
                              class="absolute -bottom-1 -right-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-7 h-7 rounded-full bg-red-600 text-white flex items-center justify-center shadow-md hover:bg-red-700 transition">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>

                <div class="flex-1 pb-1">
                    <h2 class="text-xl font-bold text-gray-900">{{ $restaurant->name }}</h2>
                    <p class="text-xs text-gray-400 mt-1">Hover sa image para mag-upload ng bago</p>
                </div>
            </div>
        </div>

        {{-- INFO --}}
        <div class="p-6 pt-5">
            <div class="flex flex-wrap gap-3 text-sm text-gray-500">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $restaurant->address }}
                </div>

                @if ($restaurant->cuisine)
                    <span class="text-xs px-2.5 py-1 rounded-full bg-orange-100 text-orange-700 font-semibold">
                        {{ $restaurant->cuisine }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- PROFILE DETAILS FORM --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900">Restaurant Details</h2>
                <p class="text-xs text-gray-500">I-edit ang impormasyon ng iyong restaurant</p>
            </div>
        </div>

        <form method="POST" action="{{ route('restaurant.profile.update') }}" class="p-6 space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Restaurant Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" required
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Address <span class="text-red-500">*</span>
                </label>
                <input type="text" name="address" value="{{ old('address', $restaurant->address) }}" required
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Latitude <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="latitude" value="{{ old('latitude', $restaurant->latitude) }}" required
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Longitude <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="longitude" value="{{ old('longitude', $restaurant->longitude) }}" required
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Preparation Time (minutes)
                </label>
                <input type="number" name="prep_time_minutes"
                       value="{{ old('prep_time_minutes', $restaurant->prep_time_minutes ?? 20) }}"
                       min="1" max="120"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1.5">Ilang minuto kadalasan bago matapos ang isang order</p>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit"
                        class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </button>
                <a href="{{ route('restaurant.dashboard') }}"
                   class="border border-gray-300 text-gray-700 px-6 py-2.5 rounded-xl font-semibold text-sm hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
function imageUploader() {
    return {
        submitFile(event, url) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('File is too large. Max 5MB.');
                event.target.value = '';
                return;
            }

            // Validate type
            if (!file.type.startsWith('image/')) {
                alert('Invalid file type.');
                event.target.value = '';
                return;
            }

            // Create form and submit
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');

            // Determine field name based on URL
            if (url.includes('cover')) {
                formData.append('cover', file);
            } else {
                formData.append('profile_image', file);
            }

            // Create dynamic form for submission
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.enctype = 'multipart/form-data';
            form.style.display = 'none';

            // CSRF
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // File input
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.name = url.includes('cover') ? 'cover' : 'profile_image';

            // Create DataTransfer to set files
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            form.appendChild(fileInput);

            document.body.appendChild(form);
            form.submit();
        }
    }
}
</script>
@endpush