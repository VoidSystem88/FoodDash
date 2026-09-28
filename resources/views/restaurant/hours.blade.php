@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto" x-data="hoursForm()">

    {{-- HEADER --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Operating Hours</h1>
            <p class="text-sm text-gray-500 mt-1">I-set ang schedule ng iyong restaurant</p>
        </div>

        <a href="{{ route('restaurant.dashboard') }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            ← Back
        </a>
    </div>

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- INFO BOX --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <div class="flex gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Auto Open/Close</p>
                <p class="text-xs">
                    Ang system ay mag-a-auto open/close ng iyong restaurant base sa schedule.
                    Ang <strong>manual toggle</strong> (Open/Close button sa dashboard) ay mag-o-override sa auto-toggle.
                </p>
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <form method="POST" action="{{ route('restaurant.hours.update') }}" class="space-y-3">
        @csrf
        @method('PATCH')

        @foreach ($days as $day)
            <div class="bg-white rounded-xl border border-gray-200 p-4 transition hover:shadow-sm"
                 x-data="{ isOpen: {{ $day['is_open'] ? 'true' : 'false' }} }">

                <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                    {{-- DAY NAME + TOGGLE --}}
                    <div class="flex items-center gap-3 sm:w-48 flex-shrink-0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" :name="'days[{{ $day['day_of_week'] }}][is_open]'" :value="isOpen ? 1 : 0">
                            <input type="checkbox"
                                   x-model="isOpen"
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                        </label>

                        <div>
                            <p class="font-semibold text-gray-900">{{ $day['day_name'] }}</p>
                            <p class="text-xs" :class="isOpen ? 'text-green-600' : 'text-gray-400'"
                               x-text="isOpen ? 'Open' : 'Closed'"></p>
                        </div>
                    </div>

                    {{-- HOURS INPUTS --}}
                    <div class="flex-1 flex items-center gap-3"
                         :class="!isOpen && 'opacity-40 pointer-events-none'">

                        <input type="hidden" :name="'days[{{ $day['day_of_week'] }}][day_of_week]'" value="{{ $day['day_of_week'] }}">

                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Open</label>
                            <input type="time"
                                   name="days[{{ $day['day_of_week'] }}][open_time]"
                                   value="{{ $day['open_time'] }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>

                        <div class="text-gray-400 pt-5">→</div>

                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Close</label>
                            <input type="time"
                                   name="days[{{ $day['day_of_week'] }}][close_time]"
                                   value="{{ $day['close_time'] }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- QUICK ACTIONS --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-sm font-medium text-gray-700 mb-3">Quick Actions</p>
            <div class="flex flex-wrap gap-2">
                <button type="button"
                        @click="setAllDays(true, '09:00', '21:00')"
                        class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition">
                    Open All (9AM-9PM)
                </button>
                <button type="button"
                        @click="setAllDays(true, '10:00', '22:00')"
                        class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition">
                    Open All (10AM-10PM)
                </button>
                <button type="button"
                        @click="setAllDays(false)"
                        class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition">
                    Close All
                </button>
            </div>
        </div>

        {{-- SUBMIT --}}
        <div class="pt-2 flex gap-2">
            <button type="submit"
                    class="flex-1 sm:flex-none bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-3 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Schedule
            </button>
            <a href="{{ route('restaurant.dashboard') }}"
               class="flex-1 sm:flex-none border border-gray-300 text-gray-700 px-8 py-3 rounded-xl font-semibold text-sm hover:bg-gray-50 text-center transition">
                Cancel
            </a>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<style>
    .active\:scale-98:active { transform: scale(0.98); }
</style>
<script>
function hoursForm() {
    return {
        setAllDays(isOpen, openTime = '09:00', closeTime = '21:00') {
            // Set all time inputs
            document.querySelectorAll('input[type="time"]').forEach((el, idx) => {
                el.value = idx % 2 === 0 ? openTime : closeTime;
            });

            // Trigger Alpine models
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.checked = isOpen;
                cb.dispatchEvent(new Event('change'));
            });
        }
    }
}
</script>
@endpush