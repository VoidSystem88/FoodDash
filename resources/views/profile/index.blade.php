@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto" x-data="profileAvatar()">

    {{-- HEADER --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">My Profile</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">View your account information</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- AVATAR + NAME --}}
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6 mb-4 text-center transition-colors">

        {{-- CLICKABLE AVATAR --}}
        <button type="button"
                @click="$refs.fileInput.click()"
                class="inline-block relative mb-3 group cursor-pointer focus:outline-none">

            <div class="relative inline-block">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}"
                         alt="{{ $user->name }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white dark:border-gray-900 shadow-lg group-hover:opacity-75 transition">
                @else
                    <div class="w-24 h-24 rounded-full {{ $user->avatar_color }} flex items-center justify-center text-white text-3xl font-bold border-4 border-white dark:border-gray-900 shadow-lg group-hover:opacity-75 transition">
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
                    <span class="absolute bottom-1 right-1 w-5 h-5 bg-green-500 border-4 border-white dark:border-gray-900 rounded-full"></span>
                @else
                    <span class="absolute bottom-1 right-1 w-5 h-5 bg-gray-400 border-4 border-white dark:border-gray-900 rounded-full"></span>
                @endif
            </div>
        </button>

        {{-- HIDDEN FILE INPUT --}}
        <input type="file"
               x-ref="fileInput"
               @change="onFileChange($event)"
               accept="image/jpeg,image/jpg,image/png,image/webp"
               class="hidden">

        <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Click picture to change</p>

        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>

        @if ($user->phone)
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $user->phone }}</p>
        @endif

        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
            <span class="inline-block px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300 font-medium">
                {{ ucfirst($user->role) }}
            </span>
        </p>
    </div>

    {{-- ============================================ --}}
    {{-- APPEARANCE (DARK MODE) --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden mb-4 transition-colors"
         x-data="themeToggle()">

        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-50 dark:from-indigo-900/40 dark:to-indigo-950/40 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">Appearance</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Choose your preferred theme</p>
            </div>
        </div>

        <div class="p-4">
            <div class="grid grid-cols-3 gap-2">

                {{-- LIGHT --}}
                <button type="button"
                        @click="setTheme('light')"
                        :class="theme === 'light'
                                ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-950/40 ring-2 ring-indigo-500'
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
                        class="relative border-2 rounded-xl p-3 flex flex-col items-center gap-2 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         :class="theme === 'light' ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Light</span>

                    {{-- Check badge sa upper-right --}}
                    <template x-if="theme === 'light'">
                        <span class="absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-indigo-500 flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                    </template>
                </button>

                {{-- DARK --}}
                <button type="button"
                        @click="setTheme('dark')"
                        :class="theme === 'dark'
                                ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-950/40 ring-2 ring-indigo-500'
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
                        class="relative border-2 rounded-xl p-3 flex flex-col items-center gap-2 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         :class="theme === 'dark' ? 'text-indigo-400' : 'text-gray-400 dark:text-gray-500'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Dark</span>

                    {{-- Check badge sa upper-right --}}
                    <template x-if="theme === 'dark'">
                        <span class="absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-indigo-500 flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                    </template>
                </button>

                {{-- SYSTEM --}}
                <button type="button"
                        @click="setTheme('system')"
                        :class="theme === 'system'
                                ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-950/40 ring-2 ring-indigo-500'
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
                        class="relative border-2 rounded-xl p-3 flex flex-col items-center gap-2 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         :class="theme === 'system' ? 'text-indigo-500' : 'text-gray-400 dark:text-gray-500'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">System</span>

                    {{-- Check badge sa upper-right --}}
                    <template x-if="theme === 'system'">
                        <span class="absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-indigo-500 flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                    </template>
                </button>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 text-center">
                <template x-if="theme === 'light'">
                    <span>Light mode — bright background</span>
                </template>
                <template x-if="theme === 'dark'">
                    <span>Dark mode — dim background</span>
                </template>
                <template x-if="theme === 'system'">
                    <span>Follows your device settings</span>
                </template>
            </p>
        </div>
    </div>
    {{-- ============================================ --}}
    {{-- PUSH NOTIFICATIONS --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden mb-4 transition-colors"
         x-data="pushNotifications()"
         x-init="init()">

        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-900/40 dark:to-orange-950/40 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">Push Notifications</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Get alerts even when the browser is closed</p>
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
                                    : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700')">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                 :class="subscribed
                                        ? 'bg-green-100 dark:bg-green-900/40'
                                        : (permission === 'denied'
                                            ? 'bg-red-100 dark:bg-red-900/40'
                                            : 'bg-gray-200 dark:bg-gray-700')">
                                {{-- Bell icon based on state --}}
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
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                              : 'text-gray-900 dark:text-white')"
                                   x-text="subscribed ? 'Enabled' : (permission === 'denied' ? 'Blocked' : 'Disabled')"></p>
                                <p class="text-xs"
                                   :class="subscribed
                                          ? 'text-green-700 dark:text-green-400'
                                          : (permission === 'denied'
                                              ? 'text-red-700 dark:text-red-400'
                                              : 'text-gray-500 dark:text-gray-400')"
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
                            <p class="text-sm text-gray-500 dark:text-gray-400">Processing...</p>
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
                        {{-- ENABLE BUTTON --}}
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

                        {{-- DISABLE BUTTON --}}
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

                        {{-- TEST BUTTON (only if subscribed) --}}
                        <template x-if="subscribed">
                            <button type="button"
                                    @click="testNotification()"
                                    :disabled="loading"
                                    :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-800'"
                                    class="inline-flex items-center gap-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 px-5 py-2.5 rounded-xl font-semibold text-sm transition">
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
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-800">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <strong>Paano i-unblock:</strong>
                                I-click ang <strong>lock icon</strong> sa address bar → hanapin ang "Notifications" → i-set sa <strong>Allow</strong> → i-refresh ang page.
                            </p>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
    {{-- STATS --}}
    @if ($stats)
        <div class="grid grid-cols-3 gap-3 mb-4">
            @if ($user->isCustomer())
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Delivered</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['delivered_orders'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Spent</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">₱{{ number_format($stats['total_spent'], 0) }}</p>
                </div>
            @elseif ($user->isRider())
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Deliveries</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_deliveries'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Earnings</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">₱{{ number_format($stats['total_earnings'], 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Rating</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['avg_rating'] ? round($stats['avg_rating'], 1) : 'N/A' }}</p>
                </div>
            @elseif ($user->isRestaurant())
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Orders</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Sales</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">₱{{ number_format($stats['total_sales'], 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Rating</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['avg_rating'] ? round($stats['avg_rating'], 1) : 'N/A' }}</p>
                </div>
            @endif
        </div>
    @endif

    {{-- ACCOUNT INFO --}}
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6 mb-4 transition-colors">
        <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Account Information</h2>

        <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $user->email }}</span>
            </div>

            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                <span class="text-sm text-gray-500 dark:text-gray-400">Phone</span>
                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $user->phone ?? 'Not set' }}</span>
            </div>

            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                <span class="text-sm text-gray-500 dark:text-gray-400">Role</span>
                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ ucfirst($user->role) }}</span>
            </div>

            <div class="flex justify-between items-center py-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Member since</span>
                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- QUICK LINKS --}}
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden transition-colors">
        <a href="{{ route('settings.index') }}"
           class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white">Edit profile & settings</span>
            </div>
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        

        {{-- LOGOUT --}}
        <div x-data="{ showLogout: false }">
            <button @click="showLogout = true"
                    type="button"
                    class="w-full flex items-center justify-between p-4 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-red-600 dark:text-red-400">Logout</span>
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
                     class="bg-white dark:bg-gray-900 rounded-lg shadow-xl max-w-sm w-full p-6">

                    <div class="flex justify-center mb-4">
                        <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                            <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                    </div>

                    <div class="text-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Logout?</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Are you sure you want to logout?</p>
                    </div>

                    <div class="flex gap-2">
                        <button @click="showLogout = false"
                                type="button"
                                class="flex-1 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition">
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

function themeToggle() {
    return {
        theme: 'system',

        init() {
            const stored = localStorage.getItem('theme');
            this.theme = stored || 'system';
        },

        setTheme(mode) {
            this.theme = mode;
            localStorage.setItem('theme', mode);

            const isDark = mode === 'dark' ||
                (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }
}
function pushNotifications() {
    return {
        supported: false,
        subscribed: false,
        permission: 'default',
        loading: false,
        error: '',
        successMessage: '',

        async init() {
            // Check support
            this.supported = 'serviceWorker' in navigator &&
                             'PushManager' in window &&
                             'Notification' in window;

            if (!this.supported) return;

            // Get permission status
            this.permission = Notification.permission;

            // Check subscription status
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