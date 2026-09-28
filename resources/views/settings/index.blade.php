@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-neutral-100 mt-2">Advanced Settings</h1>
        <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">Manage your account settings</p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- PERSONAL INFO --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-6 mb-4 transition-colors">
        <h2 class="font-semibold text-gray-900 dark:text-neutral-100 mb-4">Personal Information</h2>

        <form method="POST" action="{{ route('settings.profile') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Email</label>
                <input type="email" value="{{ $user->email }}" disabled
                       class="w-full border border-gray-300 dark:border-dark-600 rounded-lg px-3 py-2 text-sm bg-gray-100 dark:bg-dark-850 text-gray-500 dark:text-neutral-500 cursor-not-allowed">
                <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">Email cannot be changed.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       placeholder="09171234567"
                       class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <button type="submit"
                    class="bg-orange-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-orange-700 transition">
                Save Changes
            </button>
        </form>
    </div>

    {{-- CHANGE PASSWORD --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-6 mb-4 transition-colors" x-data="{ showPassword: false }">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-gray-900 dark:text-neutral-100">Change Password</h2>
            <button @click="showPassword = !showPassword"
                    type="button"
                    class="text-sm text-orange-600 dark:text-orange-400 hover:underline font-medium">
                <span x-show="!showPassword">Change</span>
                <span x-show="showPassword" x-cloak>Cancel</span>
            </button>
        </div>

        <div x-show="showPassword" x-cloak>
            <form method="POST" action="{{ route('settings.password') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Current Password</label>
                    <input type="password" name="current_password" required
                           class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">New Password</label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">Minimum 8 characters.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <button type="submit"
                        class="bg-orange-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-orange-700 transition">
                    Update Password
                </button>
            </form>
        </div>
    </div>
    {{-- ============================================ --}}
    {{-- PUSH NOTIFICATIONS --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 overflow-hidden mb-4 transition-colors"
         x-data="pushNotifications()"
         x-init="init()">

        <div class="px-6 py-4 border-b border-gray-100 dark:border-dark-700 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-900/40 dark:to-orange-950/40 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900 dark:text-neutral-100">Push Notifications</h2>
                <p class="text-xs text-gray-500 dark:text-neutral-400">Get alerts even when the browser is closed</p>
            </div>
        </div>

        <div class="p-6 space-y-4">

            {{-- UNSUPPORTED BROWSER --}}
            <template x-if="!supported">
                <div class="p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-xl">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="font-semibold text-sm text-amber-900 dark:text-amber-300">Not supported</p>
                            <p class="text-xs text-amber-800 dark:text-amber-400 mt-1">
                                Your browser doesn't support push notifications. Try Chrome, Firefox, or Edge on desktop. On iOS, add this app to your home screen first.
                            </p>
                        </div>
                    </div>
                </div>
            </template>

            {{-- SUPPORTED BROWSER --}}
            <template x-if="supported">
                <div class="space-y-4">

                    {{-- STATUS INDICATOR --}}
                    <div class="flex items-center justify-between p-4 rounded-xl border transition-colors"
                         :class="subscribed
                                ? 'bg-green-50 dark:bg-green-950/30 border-green-200 dark:border-green-800'
                                : (permission === 'denied'
                                    ? 'bg-red-50 dark:bg-red-950/30 border-red-200 dark:border-red-800'
                                    : 'bg-gray-50 dark:bg-dark-850 border-gray-200 dark:border-dark-600')">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                 :class="subscribed
                                        ? 'bg-green-100 dark:bg-green-900/40'
                                        : (permission === 'denied'
                                            ? 'bg-red-100 dark:bg-red-900/40'
                                            : 'bg-gray-200 dark:bg-dark-700')">
                                <template x-if="subscribed">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </template>
                                <template x-if="!subscribed && permission === 'denied'">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </template>
                                <template x-if="!subscribed && permission !== 'denied'">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </template>
                            </div>
                            <div>
                                <p class="font-semibold text-sm"
                                   :class="subscribed
                                          ? 'text-green-900 dark:text-green-300'
                                          : (permission === 'denied'
                                              ? 'text-red-900 dark:text-red-300'
                                              : 'text-gray-900 dark:text-neutral-100')"
                                   x-text="subscribed ? 'Enabled' : (permission === 'denied' ? 'Blocked' : 'Disabled')"></p>
                                <p class="text-xs"
                                   :class="subscribed
                                          ? 'text-green-700 dark:text-green-400'
                                          : (permission === 'denied'
                                              ? 'text-red-700 dark:text-red-400'
                                              : 'text-gray-500 dark:text-neutral-400')"
                                   x-text="subscribed
                                          ? 'You will receive push notifications on this device'
                                          : (permission === 'denied'
                                              ? 'Notifications are blocked. Enable them in browser settings.'
                                              : 'Enable to receive order updates even when the tab is closed')"></p>
                            </div>
                        </div>
                    </div>

                    {{-- LOADING --}}
                    <template x-if="loading">
                        <div class="text-center py-2">
                            <p class="text-sm text-gray-500 dark:text-neutral-400">Processing...</p>
                        </div>
                    </template>

                    {{-- ERROR MESSAGE --}}
                    <template x-if="error">
                        <div class="p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-800 dark:text-red-300"
                             x-text="error"></div>
                    </template>

                    {{-- SUCCESS MESSAGE --}}
                    <template x-if="successMessage">
                        <div class="p-3 bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-800 dark:text-green-300"
                             x-text="successMessage"></div>
                    </template>

                    {{-- ACTIONS --}}
                    <div class="flex flex-wrap gap-2">
                        <template x-if="!subscribed && permission !== 'denied'">
                            <button type="button"
                                    @click="enable()"
                                    :disabled="loading"
                                    :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-orange-700 active:scale-98'"
                                    class="inline-flex items-center gap-2 bg-orange-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:shadow-lg transition transform">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                Enable Push Notifications
                            </button>
                        </template>

                        <template x-if="subscribed">
                            <button type="button"
                                    @click="disable()"
                                    :disabled="loading"
                                    :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-50 dark:hover:bg-red-950/30'"
                                    class="inline-flex items-center gap-2 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-5 py-2.5 rounded-xl font-semibold text-sm transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                Disable
                            </button>
                        </template>

                        <template x-if="subscribed">
                            <button type="button"
                                    @click="testNotification()"
                                    :disabled="loading"
                                    :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-dark-850'"
                                    class="inline-flex items-center gap-2 border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 px-5 py-2.5 rounded-xl font-semibold text-sm transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                                Send Test
                            </button>
                        </template>
                    </div>

                    {{-- BLOCKED HELP --}}
                    <template x-if="permission === 'denied'">
                        <div class="pt-3 border-t border-gray-100 dark:border-dark-700">
                            <p class="text-xs text-gray-500 dark:text-neutral-400">
                                <strong>Paano i-unblock:</strong>
                                I-click ang <strong>lock icon</strong> sa address bar → hanapin ang "Notifications" → i-set sa <strong>Allow</strong> → i-refresh ang page.
                            </p>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
    {{-- DANGER ZONE (customer only) --}}
    @if ($user->isCustomer())
        <div class="bg-white dark:bg-dark-800 rounded-lg border-2 border-red-200 dark:border-red-800 p-6 transition-colors" x-data="{ showDelete: false }">
            <div class="flex items-start gap-3 mb-4">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="font-semibold text-red-900 dark:text-red-300">Danger Zone</h2>
                    <p class="text-sm text-red-700 dark:text-red-400 mt-1">
                        Once you delete your account, there is no going back. All your orders, ratings, and data will be permanently removed.
                    </p>
                </div>
            </div>

            <button @click="showDelete = true"
                    type="button"
                    class="bg-red-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition">
                Delete My Account
            </button>

            {{-- DELETE MODAL --}}
            <div x-show="showDelete"
                 x-cloak
                 x-transition.opacity
                 @keydown.escape.window="showDelete = false"
                 class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
                 @click.self="showDelete = false">

                <div x-show="showDelete"
                     x-transition.scale.origin.center
                     class="bg-white dark:bg-dark-800 rounded-lg shadow-xl max-w-md w-full p-6"
                     x-data="{ showPassword: false }">

                    <div class="flex items-start gap-3 mb-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-950/40 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-neutral-100">Delete Account?</h3>
                            <p class="text-sm text-gray-600 dark:text-neutral-400 mt-1">
                                This action cannot be undone. All your data will be permanently deleted.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('settings.destroy') }}" class="space-y-4">
                        @csrf
                        @method('DELETE')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
                                Enter your password to confirm
                            </label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'"
                                       name="password"
                                       required
                                       placeholder="Your password"
                                       class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-lg px-3 py-2 pr-10 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">

                                <button type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <label class="flex items-start gap-2 cursor-pointer">
                            <input type="checkbox"
                                   name="confirm_delete"
                                   value="1"
                                   required
                                   class="mt-0.5 rounded border-gray-300 dark:border-gray-600 text-red-600 focus:ring-red-500">
                            <span class="text-xs text-gray-600 dark:text-neutral-400">
                                I understand that this action is <strong class="text-red-600 dark:text-red-400">permanent</strong> and cannot be undone.
                            </span>
                        </label>

                        <div class="flex gap-2 pt-2">
                            <button type="button"
                                    @click="showDelete = false"
                                    class="flex-1 border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-dark-850 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition">
                                Delete Permanently
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
@push('scripts')
<script>
function pushNotifications() {
    return {
        supported: false,
        subscribed: false,
        permission: 'default',
        loading: false,
        error: '',
        successMessage: '',

        async init() {
            this.supported = 'serviceWorker' in navigator &&
                             'PushManager' in window &&
                             'Notification' in window;

            if (!this.supported) return;

            this.permission = Notification.permission;
            await this.checkSubscription();
        },

        async checkSubscription() {
            try {
                const subscribed = await window.PushNotifications.isSubscribed();
                this.subscribed = subscribed;
            } catch (err) {
                console.warn('Could not check subscription:', err);
            }
        },

        async enable() {
            this.loading = true;
            this.error = '';
            this.successMessage = '';

            try {
                await window.PushNotifications.subscribeToPush();
                this.subscribed = true;
                this.permission = Notification.permission;
                this.successMessage = 'Push notifications enabled! You will now receive alerts on this device.';

                setTimeout(() => { this.successMessage = ''; }, 5000);
            } catch (err) {
                console.error('Enable failed:', err);
                this.error = err.message || 'Could not enable notifications.';
                this.permission = Notification.permission;
            }

            this.loading = false;
        },

        async disable() {
            if (!confirm('Disable push notifications on this device?')) return;

            this.loading = true;
            this.error = '';
            this.successMessage = '';

            try {
                await window.PushNotifications.unsubscribeFromPush();
                this.subscribed = false;
                this.successMessage = 'Push notifications disabled.';

                setTimeout(() => { this.successMessage = ''; }, 5000);
            } catch (err) {
                console.error('Disable failed:', err);
                this.error = err.message || 'Could not disable notifications.';
            }

            this.loading = false;
        },

        async testNotification() {
            this.loading = true;
            this.error = '';
            this.successMessage = '';

            try {
                const res = await fetch('/push/test', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await res.json();

                if (data.ok) {
                    this.successMessage = 'Test notification sent! Check your notification area.';
                    setTimeout(() => { this.successMessage = ''; }, 5000);
                } else {
                    this.error = data.message || 'Could not send test notification.';
                }
            } catch (err) {
                console.error('Test failed:', err);
                this.error = 'Could not send test notification.';
            }

            this.loading = false;
        }
    }
}
</script>
@endpush
@endsection