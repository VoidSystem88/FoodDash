@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto" x-data="profileAvatar()">

    {{-- HEADER --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">My Profile</h1>
            <p class="text-sm text-gray-500 mt-1">View your account information</p>
        </div>

        <a href="{{ route('settings.index') }}"
           class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Settings
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- AVATAR + NAME --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-4 text-center">

        {{-- CLICKABLE AVATAR → opens file picker directly --}}
        <button type="button"
                @click="$refs.fileInput.click()"
                class="inline-block relative mb-3 group cursor-pointer focus:outline-none">

            <div class="relative inline-block">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}"
                         alt="{{ $user->name }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg group-hover:opacity-75 transition">
                @else
                    <div class="w-24 h-24 rounded-full {{ $user->avatar_color }} flex items-center justify-center text-white text-3xl font-bold border-4 border-white shadow-lg group-hover:opacity-75 transition">
                        {{ $user->initials }}
                    </div>
                @endif

                {{-- HOVER OVERLAY --}}
                <div class="absolute inset-0 rounded-full bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>

                {{-- GREEN/GRAY DOT --}}
                @if ($user->isOnline())
                    <span class="absolute bottom-1 right-1 w-5 h-5 bg-green-500 border-4 border-white rounded-full"></span>
                @else
                    <span class="absolute bottom-1 right-1 w-5 h-5 bg-gray-400 border-4 border-white rounded-full"></span>
                @endif
            </div>
        </button>

        {{-- HIDDEN FILE INPUT --}}
        <input type="file"
               x-ref="fileInput"
               @change="onFileChange($event)"
               accept="image/jpeg,image/jpg,image/png,image/webp"
               class="hidden">

        <p class="text-xs text-gray-400 mb-3">Click picture to change</p>

        <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
        <p class="text-sm text-gray-500">{{ $user->email }}</p>

        @if ($user->phone)
            <p class="text-sm text-gray-500 mt-0.5">{{ $user->phone }}</p>
        @endif

        <p class="text-xs text-gray-400 mt-2">
            <span class="inline-block px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 font-medium">
                {{ ucfirst($user->role) }}
            </span>
        </p>
    </div>

    {{-- STATS --}}
    @if ($stats)
        <div class="grid grid-cols-3 gap-3 mb-4">
            @if ($user->isCustomer())
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-xs text-gray-500">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-xs text-gray-500">Delivered</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['delivered_orders'] }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-xs text-gray-500">Total Spent</p>
                    <p class="text-2xl font-bold text-gray-900">₱{{ number_format($stats['total_spent'], 0) }}</p>
                </div>
            @elseif ($user->isRider())
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-xs text-gray-500">Deliveries</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_deliveries'] }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-xs text-gray-500">Earnings</p>
                    <p class="text-2xl font-bold text-green-600">₱{{ number_format($stats['total_earnings'], 0) }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-xs text-gray-500">Rating</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['avg_rating'] ? round($stats['avg_rating'], 1) : 'N/A' }}</p>
                </div>
            @elseif ($user->isRestaurant())
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-xs text-gray-500">Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-xs text-gray-500">Sales</p>
                    <p class="text-2xl font-bold text-green-600">₱{{ number_format($stats['total_sales'], 0) }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-xs text-gray-500">Rating</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['avg_rating'] ? round($stats['avg_rating'], 1) : 'N/A' }}</p>
                </div>
            @endif
        </div>
    @endif

    {{-- ACCOUNT INFO --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-4">
        <h2 class="font-semibold text-gray-900 mb-4">Account Information</h2>

        <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Email</span>
                <span class="text-sm text-gray-900 font-medium">{{ $user->email }}</span>
            </div>

            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Phone</span>
                <span class="text-sm text-gray-900 font-medium">{{ $user->phone ?? 'Not set' }}</span>
            </div>

            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Role</span>
                <span class="text-sm text-gray-900 font-medium">{{ ucfirst($user->role) }}</span>
            </div>

            <div class="flex justify-between items-center py-2">
                <span class="text-sm text-gray-500">Member since</span>
                <span class="text-sm text-gray-900 font-medium">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- QUICK LINKS --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <a href="{{ route('settings.index') }}"
           class="flex items-center justify-between p-4 hover:bg-gray-50 transition border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">Edit profile & settings</span>
            </div>
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        <a href="{{ route('notifications.index') }}"
           class="flex items-center justify-between p-4 hover:bg-gray-50 transition border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">Notifications</span>
            </div>
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        {{-- LOGOUT --}}
        <div x-data="{ showLogout: false }">
            <button @click="showLogout = true"
                    type="button"
                    class="w-full flex items-center justify-between p-4 hover:bg-red-50 transition">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-red-600">Logout</span>
                </div>
                <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            {{-- LOGOUT MODAL --}}
            <div x-show="showLogout"
                 x-cloak
                 x-transition.opacity
                 @keydown.escape.window="showLogout = false"
                 class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
                 @click.self="showLogout = false">

                <div x-show="showLogout"
                     x-transition.scale.origin.center
                     class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6">

                    <div class="flex justify-center mb-4">
                        <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
                            <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                    </div>

                    <div class="text-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Logout?</h3>
                        <p class="text-sm text-gray-500 mt-1">Are you sure you want to logout?</p>
                    </div>

                    <div class="flex gap-2">
                        <button @click="showLogout = false"
                                type="button"
                                class="flex-1 border border-gray-300 text-gray-700 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                            Cancel
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-red-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition">
                                Yes, Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
function profileAvatar() {
    return {
        onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('File is too large. Maximum 5MB.');
                e.target.value = '';
                return;
            }

            // Validate type
            if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)) {
                alert('Invalid file type. Use JPG, PNG, or WebP.');
                e.target.value = '';
                return;
            }

            // Store temporarily sa sessionStorage
            const reader = new FileReader();
            reader.onload = (ev) => {
                sessionStorage.setItem('temp_avatar_image', ev.target.result);

                // Redirect sa crop page
                window.location.href = '{{ route('profile.avatar') }}';
            };
            reader.readAsDataURL(file);
        }
    }
}
</script>
@endpush