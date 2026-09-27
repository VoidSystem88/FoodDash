<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — {{ config('app.name', 'FoodDash') }}</title>

    {{-- DARK MODE - Inline script para hindi mag-flash bago mag-load --}}
    <script>
    (function() {
        const stored = localStorage.getItem('theme');
        if (stored === 'dark' || (stored === 'system' || !stored) && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
    })();
    </script>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ff6b35">
    <link rel="apple-touch-icon" href="/icon-192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icon-192.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-dark-900 min-h-screen flex items-center justify-center p-4 transition-colors">

    @php $config = \App\Models\SystemConfig::current(); @endphp

    <div class="w-full max-w-md">

        {{-- ============================================ --}}
        {{-- LOGO / BRAND --}}
        {{-- ============================================ --}}
        <div class="text-center mb-8">
            <a href="{{ route('customer.restaurants') }}" class="inline-flex items-center justify-center gap-3">
                @if ($config->hasLogo())
                    <img src="{{ $config->logo_url }}"
                         alt="{{ config('app.name', 'FoodDash') }}"
                         class="w-auto"
                         style="height: {{ max(48, $config->logo_height_px) }}px;">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg">
                        <span class="text-3xl">🍕</span>
                    </div>
                    <span class="font-bold text-3xl text-gray-900 dark:text-neutral-100">
                        {{ config('app.name', 'FoodDash') }}
                    </span>
                @endif
            </a>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-3">
                Welcome back! Sign in to continue.
            </p>
        </div>

        {{-- ============================================ --}}
        {{-- MAIN CARD --}}
        {{-- ============================================ --}}
        <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-xl border border-gray-200 dark:border-dark-700 overflow-hidden transition-colors">

            <div class="p-6 sm:p-8">

                {{-- ============================================ --}}
                {{-- ALERTS --}}
                {{-- ============================================ --}}
                @if (session('success'))
                    <div class="mb-5 p-3 bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-xl text-sm flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl text-sm flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                {{-- ============================================ --}}
                {{-- LOGIN FORM --}}
                {{-- ============================================ --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ showPassword: false }">
                    @csrf

                    {{-- EMAIL --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1.5 uppercase tracking-wide">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus
                                   placeholder="you@example.com"
                                   class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-xl pl-11 pr-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                    </div>

                    {{-- PASSWORD --}}
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wide">
                                Password
                            </label>
                            <a href="{{ route('password.request') }}"
                               class="text-xs text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-medium transition">
                                Forgot?
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   required
                                   placeholder="••••••••"
                                   class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-xl pl-11 pr-11 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-400 dark:placeholder-gray-500">

                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 dark:text-neutral-500 hover:text-gray-600 dark:hover:text-gray-300 transition">
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- REMEMBER ME --}}
                    <div class="flex items-center">
                        <input type="checkbox"
                               name="remember"
                               id="remember"
                               class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500 dark:bg-dark-850">
                        <label for="remember" class="ml-2 text-sm text-gray-600 dark:text-neutral-400 select-none">
                            Remember me
                        </label>
                    </div>

                    {{-- SUBMIT --}}
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl text-sm font-bold shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Sign In
                    </button>
                </form>

                {{-- ============================================ --}}
                {{-- DIVIDER --}}
                {{-- ============================================ --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200 dark:border-dark-600"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-white dark:bg-dark-800 px-3 text-gray-400 dark:text-neutral-500 font-medium tracking-wider">
                            or
                        </span>
                    </div>
                </div>

                {{-- ============================================ --}}
                {{-- CONTINUE AS GUEST --}}
                {{-- ============================================ --}}
                <a href="{{ route('customer.restaurants') }}"
                   class="w-full flex items-center justify-center gap-2 border-2 border-dashed border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 py-3 rounded-xl text-sm font-semibold hover:border-orange-400 dark:hover:border-orange-600 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-950/20 active:scale-98 transition transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Continue as Guest
                </a>

                {{-- HINT --}}
                <p class="text-center text-xs text-gray-400 dark:text-neutral-500 mt-3">
                    Browse restaurants and menus · Sign in only when you order
                </p>
            </div>

            {{-- ============================================ --}}
            {{-- CARD FOOTER --}}
            {{-- ============================================ --}}
            <div class="px-6 sm:px-8 py-5 bg-gray-50 dark:bg-dark-850/50 border-t border-gray-100 dark:border-dark-700 text-center">
                <p class="text-sm text-gray-600 dark:text-neutral-400">
                    Don't have an account?
                    <a href="{{ route('register') }}"
                       class="font-bold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 transition">
                        Create one now
                    </a>
                </p>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- FOOTER --}}
        {{-- ============================================ --}}
        <p class="text-center text-xs text-gray-400 dark:text-neutral-500 mt-6">
            © {{ date('Y') }} {{ config('app.name', 'FoodDash') }} · All rights reserved
        </p>

    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>