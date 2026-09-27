@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-5" x-data="menuManager()">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-purple-500 via-purple-500 to-purple-600 rounded-2xl shadow-xl text-white">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-pink-300 rounded-full blur-3xl opacity-20 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            <div class="flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <a href="{{ route('restaurant.dashboard') }}"
                       class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur flex items-center justify-center transition">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <p class="text-xs text-white/70 uppercase tracking-wider font-medium">Restaurant</p>
                        <h1 class="text-xl font-bold leading-tight">Menu Items</h1>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-full px-3 py-1.5">
                    <span class="text-xs font-bold uppercase tracking-wide">{{ $items->count() }} Items</span>
                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="grid grid-cols-3 gap-3 pt-5 border-t border-white/20 mt-5">
                <div>
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Total</p>
                    <p class="text-xl font-bold mt-1">{{ $items->count() }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Available</p>
                    <p class="text-xl font-bold mt-1">{{ $items->where('is_available', true)->count() }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">With Photos</p>
                    <p class="text-xl font-bold mt-1">{{ $items->whereNotNull('image_path')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
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

    {{-- ============================================ --}}
    {{-- ADD NEW ITEM --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">

        <button type="button"
                @click="showAddForm = !showAddForm"
                class="w-full flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div class="text-left">
                    <p class="font-bold text-gray-900">Add New Item</p>
                    <p class="text-xs text-gray-500">Idagdag ang bagong menu item kasama ang larawan</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                 :class="showAddForm ? 'rotate-180' : ''"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div x-show="showAddForm" x-cloak x-transition class="border-t border-gray-100">
            <form method="POST" action="{{ route('menu-items.store') }}" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Item Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" placeholder="e.g. Pepperoni Pizza" required
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Price (₱) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">₱</span>
                            <input type="number" name="price" placeholder="0.00" step="0.01" min="0" required
                                   class="w-full border border-gray-300 rounded-xl pl-8 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Description <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <textarea name="description" rows="2" placeholder="Ilarawan ang item..."
                              class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent"></textarea>
                </div>

                {{-- IMAGE UPLOAD --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Food Photo <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <label class="block cursor-pointer">
                        <input type="file"
                               name="image"
                               accept="image/jpeg,image/jpg,image/png,image/webp"
                               @change="onFileChange($event)"
                               class="hidden">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-orange-500 hover:bg-orange-50 transition">
                            <template x-if="!imagePreview">
                                <div>
                                    <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm text-gray-600">
                                        <span class="text-orange-600 font-semibold">Click to upload</span> photo
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP · Max 2MB</p>
                                </div>
                            </template>
                            <template x-if="imagePreview">
                                <div class="flex flex-col items-center">
                                    <img :src="imagePreview" class="w-32 h-32 rounded-xl object-cover shadow-md">
                                    <p class="text-xs text-orange-600 font-medium mt-2">Click to change</p>
                                </div>
                            </template>
                        </div>
                    </label>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit"
                            class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Add Item
                    </button>
                    <button type="button"
                            @click="showAddForm = false; resetForm()"
                            class="border border-gray-300 text-gray-700 px-6 py-2.5 rounded-xl font-semibold text-sm hover:bg-gray-50 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- MENU ITEMS LIST --}}
    {{-- ============================================ --}}
    @if ($items->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-1">No menu items yet</h3>
            <p class="text-sm text-gray-500 mb-4">Start by adding your first menu item</p>
            <button type="button"
                    @click="showAddForm = true"
                    class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-95 transition transform">
                + Add First Item
            </button>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($items as $item)
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-md transition">
                    <div class="p-4 flex items-center gap-4">

                        {{-- IMAGE THUMBNAIL --}}
                        <div class="flex-shrink-0">
                            @if ($item->image_url)
                                <img src="{{ $item->image_url }}"
                                     alt="{{ $item->name }}"
                                     class="w-24 h-24 rounded-xl object-cover shadow-sm">
                            @else
                                <div class="w-24 h-24 rounded-xl bg-gradient-to-br from-gray-100 to-gray-50 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- INFO --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="font-bold text-gray-900 truncate">{{ $item->name }}</p>
                                @if (!$item->is_available)
                                    <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold uppercase tracking-wide">
                                        Unavailable
                                    </span>
                                @else
                                    <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold uppercase tracking-wide">
                                        Available
                                    </span>
                                @endif
                            </div>

                            @if ($item->description)
                                <p class="text-sm text-gray-500 line-clamp-1">{{ $item->description }}</p>
                            @endif

                            <p class="text-base font-bold text-orange-600 mt-1">₱{{ number_format($item->price, 2) }}</p>
                        </div>

                        {{-- ACTIONS --}}
                        <div class="flex-shrink-0 flex items-center gap-2">

                            {{-- TOGGLE AVAILABILITY --}}
                            <form method="POST" action="{{ route('menu-items.toggle', $item) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        title="{{ $item->is_available ? 'Mark unavailable' : 'Mark available' }}"
                                        class="w-9 h-9 rounded-lg {{ $item->is_available ? 'bg-green-50 hover:bg-green-100 text-green-600' : 'bg-gray-100 hover:bg-gray-200 text-gray-500' }} flex items-center justify-center transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        @if ($item->is_available)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        @endif
                                    </svg>
                                </button>
                            </form>

                            {{-- EDIT --}}
                            <button type="button"
                                    onclick="alert('Edit feature coming soon!');"
                                    title="Edit"
                                    class="w-9 h-9 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>

                            {{-- DELETE --}}
                            <form method="POST" action="{{ route('menu-items.destroy', $item) }}"
                                  onsubmit="return confirm('Delete {{ $item->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        title="Delete"
                                        class="w-9 h-9 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<style>
    .active\:scale-98:active { transform: scale(0.98); }
    .active\:scale-95:active { transform: scale(0.95); }
</style>
<script>
function menuManager() {
    return {
        showAddForm: {{ $errors->any() ? 'true' : 'false' }},
        imagePreview: '',

        onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File is too large. Max 2MB.');
                e.target.value = '';
                return;
            }

            if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)) {
                alert('Invalid file type. Use JPG, PNG, or WebP.');
                e.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (ev) => {
                this.imagePreview = ev.target.result;
            };
            reader.readAsDataURL(file);
        },

        resetForm() {
            this.imagePreview = '';
        }
    }
}
</script>
@endpush