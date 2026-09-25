<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email — FoodDash</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-10">
    <div class="w-full max-w-md bg-white rounded-lg shadow p-6">

        <h1 class="text-2xl font-bold text-orange-600 mb-6 text-center">🍕 FoodDash</h1>
        <h2 class="text-lg font-semibold mb-2 text-center">Verify your email</h2>

        <p class="text-sm text-gray-500 text-center mb-6">
            We sent a 6-digit code to
            <strong class="text-gray-700">{{ $email ?? 'your email' }}</strong>.
            Enter it below to continue.
        </p>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('otp.verify') }}" class="space-y-5" id="otpForm">
            @csrf

            {{-- OTP INPUTS --}}
            <div>
                <label class="block text-sm font-medium mb-2 text-center text-gray-700">
                    Verification code
                </label>

                <div class="flex justify-center gap-2">
                    <input type="text" inputmode="numeric" maxlength="1" data-otp-index="0"
                           class="otp-box w-11 h-12 text-center text-lg font-semibold border-2 border-gray-300 rounded-md focus:border-orange-500 focus:ring-2 focus:ring-orange-200 focus:outline-none transition">
                    <input type="text" inputmode="numeric" maxlength="1" data-otp-index="1"
                           class="otp-box w-11 h-12 text-center text-lg font-semibold border-2 border-gray-300 rounded-md focus:border-orange-500 focus:ring-2 focus:ring-orange-200 focus:outline-none transition">
                    <input type="text" inputmode="numeric" maxlength="1" data-otp-index="2"
                           class="otp-box w-11 h-12 text-center text-lg font-semibold border-2 border-gray-300 rounded-md focus:border-orange-500 focus:ring-2 focus:ring-orange-200 focus:outline-none transition">
                    <input type="text" inputmode="numeric" maxlength="1" data-otp-index="3"
                           class="otp-box w-11 h-12 text-center text-lg font-semibold border-2 border-gray-300 rounded-md focus:border-orange-500 focus:ring-2 focus:ring-orange-200 focus:outline-none transition">
                    <input type="text" inputmode="numeric" maxlength="1" data-otp-index="4"
                           class="otp-box w-11 h-12 text-center text-lg font-semibold border-2 border-gray-300 rounded-md focus:border-orange-500 focus:ring-2 focus:ring-orange-200 focus:outline-none transition">
                    <input type="text" inputmode="numeric" maxlength="1" data-otp-index="5"
                           class="otp-box w-11 h-12 text-center text-lg font-semibold border-2 border-gray-300 rounded-md focus:border-orange-500 focus:ring-2 focus:ring-orange-200 focus:outline-none transition">
                </div>

                {{-- HIDDEN INPUT — actual OTP value na isusubmit --}}
                <input type="hidden" name="otp" id="otpHidden">
            </div>

            <button type="submit"
                    id="otpSubmit"
                    disabled
                    class="w-full bg-orange-600 text-white py-2.5 rounded font-medium transition opacity-40 cursor-not-allowed">
                Verify Email
            </button>
        </form>

        {{-- RESEND --}}
        <form method="POST" action="{{ route('otp.resend') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full text-sm text-gray-600 hover:text-orange-600 transition">
                Resend verification code
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button class="w-full text-xs text-gray-400 hover:text-gray-600">
                Cancel and go back
            </button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const boxes = document.querySelectorAll('.otp-box');
        const hidden = document.getElementById('otpHidden');
        const submit = document.getElementById('otpSubmit');
        const form = document.getElementById('otpForm');

        // ================================
        // Focus first box on load
        // ================================
        if (boxes.length > 0) {
            boxes[0].focus();
        }

        // ================================
        // Update hidden input + button state
        // ================================
        function syncValue() {
            let value = '';
            boxes.forEach(b => value += b.value);
            hidden.value = value;

            if (value.length === 6) {
                submit.disabled = false;
                submit.classList.remove('opacity-40', 'cursor-not-allowed');
                submit.classList.add('hover:bg-orange-700');
            } else {
                submit.disabled = true;
                submit.classList.add('opacity-40', 'cursor-not-allowed');
                submit.classList.remove('hover:bg-orange-700');
            }
        }

        // ================================
        // AUTO-JUMP + INPUT HANDLING
        // ================================
        boxes.forEach((box, index) => {

            // INPUT — type 1 digit → jump next
            box.addEventListener('input', function (e) {
                // Alisin ang non-digits
                let val = e.target.value.replace(/\D/g, '');

                // Kung walang laman, clear at tapos
                if (val.length === 0) {
                    e.target.value = '';
                    syncValue();
                    return;
                }

                // Kunin lang first digit
                const digit = val[0];
                e.target.value = digit;

                // AUTO-JUMP sa next box
                if (index < boxes.length - 1) {
                    boxes[index + 1].focus();
                }

                syncValue();
            });

            // KEYDOWN — Backspace + Arrows
            box.addEventListener('keydown', function (e) {

                // BACKSPACE
                if (e.key === 'Backspace') {
                    if (box.value) {
                        // May laman — i-clear lang, hindi lumipat
                        box.value = '';
                        syncValue();
                        e.preventDefault();
                    } else {
                        // Walang laman — lumipat sa previous at i-clear
                        if (index > 0) {
                            boxes[index - 1].value = '';
                            boxes[index - 1].focus();
                            syncValue();
                            e.preventDefault();
                        }
                    }
                    return;
                }

                // ARROW LEFT
                if (e.key === 'ArrowLeft' && index > 0) {
                    boxes[index - 1].focus();
                    e.preventDefault();
                    return;
                }

                // ARROW RIGHT
                if (e.key === 'ArrowRight' && index < boxes.length - 1) {
                    boxes[index + 1].focus();
                    e.preventDefault();
                    return;
                }

                // ENTER — submit kung complete
                if (e.key === 'Enter' && hidden.value.length === 6) {
                    form.submit();
                }
            });

            // PASTE — auto-fill lahat
            box.addEventListener('paste', function (e) {
                e.preventDefault();

                const text = (e.clipboardData || window.clipboardData).getData('text');
                const pasted = text.replace(/\D/g, '').slice(0, 6);

                if (!pasted) return;

                // Fill each box
                for (let i = 0; i < boxes.length; i++) {
                    boxes[i].value = pasted[i] || '';
                }

                // Focus last filled or last box
                const lastIndex = Math.min(pasted.length, boxes.length - 1);
                boxes[lastIndex].focus();

                syncValue();
            });

            // CLICK — select content
            box.addEventListener('click', function () {
                box.select();
            });
        });

        // ================================
        // Initial sync
        // ================================
        syncValue();
    });
    </script>
</body>
</html>