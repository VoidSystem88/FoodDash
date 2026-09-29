@extends('layouts.app')

@section('content')
<div x-data="orderTracker({{ $order->id }})" x-init="init()" class="max-w-3xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Order #{{ $order->id }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $order->restaurant->name }}</p>
        </div>
        <a href="{{ route('customer.orders') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:text-orange-500 dark:hover:text-orange-500 transition group">
            <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>
    </div>

    {{-- ============================================ --}}
    {{-- STATUS CARD --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 transition-colors">
        <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400 mb-1 font-medium">Current Status</p>
        <p class="text-lg font-bold text-zinc-900 dark:text-white" x-text="statusLabel"></p>

        {{-- PROGRESS --}}
        <div class="mt-6 flex items-center">
            <template x-for="(step, i) in steps" :key="i">
                <div class="flex items-center flex-1">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                         :class="stepIndex >= i ? 'bg-orange-500 text-white' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400'">
                        <span x-text="i + 1"></span>
                    </div>
                    <div class="flex-1 h-0.5 mx-1"
                         :class="stepIndex > i ? 'bg-orange-500' : 'bg-zinc-200 dark:bg-zinc-800'"
                         x-show="i < steps.length - 1"></div>
                </div>
            </template>
        </div>
        <div class="flex justify-between mt-2">
            <template x-for="(step, i) in steps" :key="'lbl' + i">
                <span class="text-xs" :class="stepIndex >= i ? 'text-zinc-900 dark:text-white font-medium' : 'text-zinc-400 dark:text-zinc-500'" x-text="step"></span>
            </template>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- REJECTION REASON --}}
    {{-- ============================================ --}}
    @if ($order->status === 'rejected' && $order->rejection_reason)
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-red-200 dark:border-red-800 p-5">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-red-600 dark:text-red-400 font-bold mb-1">Order Rejected</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">Reason: {{ $order->rejection_reason }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- CANCELLATION REASON --}}
    {{-- ============================================ --}}
    @if ($order->status === 'cancelled' && $order->cancellation_reason)
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-5">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-zinc-800 dark:bg-zinc-200 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white dark:text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-zinc-700 dark:text-zinc-300 font-bold mb-1">Order Cancelled</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">Reason: {{ $order->cancellation_reason }}</p>
                    @if ($order->cancelled_at)
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2">
                            Cancelled {{ $order->cancelled_at->diffForHumans() }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- CANCEL ORDER FORM --}}
    {{-- ============================================ --}}
    @if ($order->canBeCancelledByCustomer())
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 transition-colors"
             x-data="{ showCancel: false }">
            <div class="flex justify-between items-center gap-3">
                <div>
                    <p class="text-sm font-bold text-zinc-900 dark:text-white">Need to cancel?</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        You can cancel this order while it's still being processed.
                    </p>
                </div>
                <button type="button"
                        @click="showCancel = !showCancel"
                        class="text-red-600 dark:text-red-400 bg-white dark:bg-zinc-900 border border-red-200 dark:border-red-800 hover:bg-red-50 dark:hover:bg-red-950/30 px-4 py-2 rounded-lg text-sm font-medium transition">
                    Cancel Order
                </button>
            </div>

            <div x-show="showCancel"
                 x-cloak
                 x-transition
                 class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <form method="POST"
                      action="{{ route('customer.orders.cancel', $order) }}"
                      class="space-y-3"
                      onsubmit="return confirm('Are you sure you want to cancel this order? This cannot be undone.');">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                            Reason for cancellation
                        </label>
                        <select name="cancellation_reason"
                                required
                                class="w-full border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option value="">Select a reason...</option>
                            <option value="Changed my mind">Changed my mind</option>
                            <option value="Ordered by mistake">Ordered by mistake</option>
                            <option value="Found a better option">Found a better option</option>
                            <option value="Delivery taking too long">Delivery taking too long</option>
                            <option value="Need to modify my order">Need to modify my order</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-600 transition">
                            Confirm Cancellation
                        </button>
                        <button type="button"
                                @click="showCancel = false"
                                class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                            Keep Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- REVIEW SECTION --}}
    {{-- ============================================ --}}
    @if ($order->status === 'delivered' && $order->canBeReviewed())
        <div x-data="reviewForm({{ $order->id }})"
             class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 transition-colors">

            <div class="flex items-start gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-zinc-900 dark:text-white">
                        Rate your experience
                    </p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                        Help other customers by sharing your review for
                        <strong class="text-zinc-700 dark:text-zinc-300">{{ $order->restaurant->name }}</strong>.
                    </p>
                </div>
            </div>

            <button type="button"
                    @click="showModal = true"
                    class="w-full bg-orange-500 text-white py-3 rounded-lg font-medium text-sm hover:bg-orange-600 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Write a Review
            </button>

            {{-- Review Modal --}}
            <div x-show="showModal"
                 x-cloak
                 x-transition.opacity
                 @keydown.escape.window="showModal = false"
                 @click.self="showModal = false"
                 class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/60">

                <div x-show="showModal"
                     x-transition.scale.origin.center
                     class="bg-white dark:bg-zinc-900 rounded-lg shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-6">

                    <div class="flex justify-between items-start mb-5">
                        <div>
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Rate your experience</h2>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $order->restaurant->name }}
                            </p>
                        </div>
                        <button type="button"
                                @click="showModal = false"
                                class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submit()" class="space-y-4">

                        {{-- Star Rating --}}
                        <div>
                            <label class="block text-sm font-bold text-zinc-900 dark:text-white mb-2">
                                Overall Rating
                            </label>
                            <div class="flex gap-2">
                                <template x-for="i in 5" :key="i">
                                    <button type="button"
                                            @click="rating = i"
                                            @mouseenter="hoverRating = i"
                                            @mouseleave="hoverRating = 0"
                                            class="transition-transform hover:scale-110">
                                        <svg class="w-10 h-10"
                                             :class="i <= (hoverRating || rating) 
                                                ? 'text-orange-500 fill-orange-500' 
                                                : 'text-zinc-300 dark:text-zinc-600'"
                                             viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <p x-show="rating > 0"
                               x-text="['', 'Terrible', 'Poor', 'Average', 'Good', 'Excellent'][rating]"
                               class="text-sm font-medium text-zinc-600 dark:text-zinc-400 mt-2"></p>
                        </div>

                        {{-- Title --}}
                        <div>
                            <label class="block text-sm font-bold text-zinc-900 dark:text-white mb-2">
                                Title <span class="text-zinc-400 dark:text-zinc-500 font-normal">(optional)</span>
                            </label>
                            <input type="text"
                                   x-model="title"
                                   maxlength="100"
                                   placeholder="e.g. Best pizza in town!"
                                   class="w-full border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>

                        {{-- Body --}}
                        <div>
                            <label class="block text-sm font-bold text-zinc-900 dark:text-white mb-2">
                                Your Review
                            </label>
                            <textarea x-model="body"
                                      required
                                      minlength="10"
                                      maxlength="2000"
                                      rows="5"
                                      placeholder="How was your experience? Was the food and service good?"
                                      class="w-full border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"></textarea>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                <span x-text="body.length"></span>/2000 characters (min 10)
                            </p>
                        </div>

                        {{-- Photos --}}
                        <div>
                            <label class="block text-sm font-bold text-zinc-900 dark:text-white mb-2">
                                Add Photos <span class="text-zinc-400 dark:text-zinc-500 font-normal">(optional, max 5)</span>
                            </label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(preview, i) in photoPreviews" :key="i">
                                    <div class="relative w-20 h-20">
                                        <img :src="preview" class="w-full h-full object-cover rounded-lg">
                                        <button type="button"
                                                @click="removePhoto(i)"
                                                class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-red-600 transition">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>

                                <button type="button"
                                        x-show="photoPreviews.length < 5"
                                        @click="$refs.photoInput.click()"
                                        class="w-20 h-20 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-lg flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 hover:border-orange-500 hover:text-orange-500 transition">
                                    <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-xs">Add</span>
                                </button>

                                <input type="file"
                                       x-ref="photoInput"
                                       @change="onPhotoChange($event)"
                                       accept="image/jpeg,image/jpg,image/png,image/webp"
                                       multiple
                                       class="hidden">
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-3 flex gap-2">
                            <svg class="w-4 h-4 text-orange-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Your review will be visible to everyone. Please base it on your genuine experience.
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2 pt-2">
                            <button type="button"
                                    @click="showModal = false"
                                    class="flex-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white py-3 rounded-lg font-medium text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                    :disabled="submitting || rating === 0 || body.length < 10"
                                    :class="(submitting || rating === 0 || body.length < 10) 
                                        ? 'opacity-40 cursor-not-allowed' 
                                        : 'hover:bg-orange-600 active:scale-98'"
                                    class="flex-1 bg-orange-500 text-white py-3 rounded-lg font-medium text-sm transition">
                                <span x-show="!submitting">Submit Review</span>
                                <span x-show="submitting">Submitting...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- EXISTING REVIEW --}}
    {{-- ============================================ --}}
    @if ($order->status === 'delivered' && $order->hasBeenReviewed())
        @php $review = $order->review; @endphp
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-zinc-900 dark:text-white">Review Submitted</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Thank you for your review.
                    </p>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4">
                <div class="flex items-center gap-1 mb-2">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-orange-500 fill-orange-500' : 'text-zinc-300 dark:text-zinc-600' }}"
                             viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                </div>
                @if ($review->title)
                    <p class="font-bold text-zinc-900 dark:text-white mb-1 text-sm">{{ $review->title }}</p>
                @endif
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $review->body }}</p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-2">
                    Submitted {{ $review->created_at->diffForHumans() }}
                </p>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- ORDER PICKED UP BADGE --}}
    {{-- ============================================ --}}
    @if ($order->verified_pickup_at)
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-orange-500 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-zinc-900 dark:text-white">Order Picked Up</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        The rider has picked up your order at {{ $order->verified_pickup_at->format('g:i A') }}.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- CHAT WITH RIDER --}}
    {{-- ============================================ --}}
    @if ($order->canChat())
        @php
            $unreadChat = $order->messages()
                ->where('sender_id', '!=', auth()->id())
                ->whereNull('read_at')
                ->count();
        @endphp

        <a href="{{ route('customer.chat.show', $order) }}"
           class="flex items-center justify-between gap-3 bg-orange-500 hover:bg-orange-600 text-white px-5 py-4 rounded-lg transition group">

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>

                <div>
                    <p class="font-bold text-sm">Chat with Rider</p>
                    <p class="text-xs text-white/80">
                        {{ $order->rider?->user?->name ?? 'Rider' }}
                    </p>
                </div>

                @if ($unreadChat > 0)
                    <span class="bg-white text-orange-600 text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $unreadChat }} new
                    </span>
                @endif
            </div>

            <svg class="w-5 h-5 text-white/80 group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    @endif

    {{-- ============================================ --}}
    {{-- LIVE MAP --}}
    {{-- ============================================ --}}
    @if ($order->rider && in_array($order->status, ['rider_assigned', 'preparing', 'ready_for_pickup', 'picked_up', 'out_for_delivery']))
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 transition-colors">
            <div class="flex justify-between items-center mb-3">
                <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400 font-bold">Live Rider Location</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="lastUpdated"></p>
            </div>
            <div id="map" class="w-full h-80 rounded-lg border border-zinc-200 dark:border-zinc-800 z-0"></div>

            <div class="mt-3 grid grid-cols-2 gap-3">
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-3">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Distance to you</p>
                    <p class="text-lg font-bold text-zinc-900 dark:text-white" x-text="distanceText || '—'"></p>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-3">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Estimated arrival</p>
                    <p class="text-lg font-bold text-zinc-900 dark:text-white" x-text="etaText || '—'"></p>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- DELIVERY --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 transition-colors">
        <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400 mb-2 font-bold">Delivery Address</p>
        <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $order->delivery_address }}</p>

        @if ($order->rider)
            <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400 mb-1 font-bold">Your Rider</p>
                <p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $order->rider->user->name }}</p>
                @if ($order->rider->vehicle_type)
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        {{ $order->rider->vehicle_type }}
                        @if ($order->rider->vehicle_plate)
                            · {{ $order->rider->vehicle_plate }}
                        @endif
                    </p>
                @endif
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- ITEMS --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 transition-colors">
        <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400 mb-4 font-bold">Order Summary</p>

        <div class="space-y-2">
            @foreach ($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-700 dark:text-zinc-300">{{ $item->quantity }}× {{ $item->name }}</span>
                    <span class="font-medium text-zinc-900 dark:text-white">₱{{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
            @endforeach
        </div>

        <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800 space-y-1.5 text-sm">
            <div class="flex justify-between text-zinc-500 dark:text-zinc-400">
                <span>Food cost</span>
                <span>₱{{ number_format($order->food_cost, 2) }}</span>
            </div>
            <div class="flex justify-between text-zinc-500 dark:text-zinc-400">
                <span>Delivery fee</span>
                <span>₱{{ number_format($order->delivery_fee, 2) }}</span>
            </div>
            <div class="flex justify-between font-bold text-zinc-900 dark:text-white pt-2 border-t border-zinc-200 dark:border-zinc-800">
                <span>Total</span>
                <span class="text-orange-500">₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ============================================
// ORDER TRACKER (with LIVE MAP)
// ============================================
function orderTracker(orderId) {
    return {
        status: '{{ $order->status }}',
        steps: ['Placed', 'Confirmed', 'Preparing', 'Rider', 'On the way', 'Delivered'],
        map: null,
        riderMarker: null,
        restaurantMarker: null,
        deliveryMarker: null,
        routeLine: null,
        lastUpdated: '',
        distanceText: '',
        etaText: '',
        mapInitialized: false,
        routeDebounce: null,

        init() {
            window.Echo.private(`order.${orderId}`)
                .listen('.order.status', (e) => { this.status = e.status; location.reload(); })
                .listen('.rider.assigned', (e) => { this.status = e.status; location.reload(); })
                .listen('.no.rider', () => { this.status = 'no_rider'; });

            window.Echo.private(`order.${orderId}`)
                .listen('.rider.location', (e) => {
                    this.updateRiderLocation(e.latitude, e.longitude);
                });

            @if ($order->rider && in_array($order->status, ['rider_assigned', 'preparing', 'ready_for_pickup', 'picked_up', 'out_for_delivery']))
                this.$nextTick(() => { this.initMap(); });
            @endif
        },

        initMap() {
            if (this.mapInitialized) return;

            const mapContainer = document.getElementById('map');
            if (!mapContainer) return;

            if (mapContainer._leaflet_id) {
                mapContainer._leaflet_id = null;
            }

            const restaurantLat = {{ $order->restaurant->latitude }};
            const restaurantLng = {{ $order->restaurant->longitude }};
            const deliveryLat = {{ $order->delivery_lat }};
            const deliveryLng = {{ $order->delivery_lng }};
            const riderLat = {{ $order->rider->latitude ?? $order->restaurant->latitude }};
            const riderLng = {{ $order->rider->longitude ?? $order->restaurant->longitude }};

            this.map = L.map('map').setView([riderLat, riderLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19,
            }).addTo(this.map);

            @php
                $trackingRestaurantUrl = $order->restaurant->profile_image_url ?? '';
                $trackingRestaurantInitial = strtoupper(substr($order->restaurant->name, 0, 1));
            @endphp

            const trackingRestaurantUrl = '{{ $trackingRestaurantUrl }}';
            const trackingRestaurantInitial = '{{ $trackingRestaurantInitial }}';

            const restaurantIcon = L.divIcon({
                html: trackingRestaurantUrl ? `
                    <div style="position:relative;width:52px;height:52px;">
                        <div style="width:52px;height:52px;border-radius:50%;overflow:hidden;border:3px solid #f97316;box-shadow:0 3px 8px rgba(249,115,22,0.5);background:white;">
                            <img src="${trackingRestaurantUrl}" style="width:100%;height:100%;object-fit:cover;" alt="Restaurant">
                        </div>
                        <div style="position:absolute;bottom:-4px;left:50%;transform:translateX(-50%);background:#f97316;color:white;padding:1px 6px;border-radius:8px;font-size:9px;font-weight:bold;border:2px solid white;white-space:nowrap;">STORE</div>
                    </div>
                ` : `
                    <div style="position:relative;width:52px;height:52px;">
                        <div style="background:#f97316;color:white;width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:bold;border:3px solid white;box-shadow:0 3px 8px rgba(0,0,0,0.4);">${trackingRestaurantInitial}</div>
                        <div style="position:absolute;bottom:-4px;left:50%;transform:translateX(-50%);background:#f97316;color:white;padding:1px 6px;border-radius:8px;font-size:9px;font-weight:bold;border:2px solid white;white-space:nowrap;">STORE</div>
                    </div>
                `,
                className: '',
                iconSize: [52, 60],
                iconAnchor: [26, 30],
            });

            @php
                $trackingCustomerIcon = auth()->user()->gender === 'female'
                    ? '/images/customergirl.png'
                    : '/images/customerman.png';
            @endphp

            const deliveryIcon = L.divIcon({
                html: `
                    <div style="position:relative;width:48px;height:48px;">
                        <img src="{{ $trackingCustomerIcon }}" style="width:48px;height:48px;object-fit:contain;filter:drop-shadow(0 3px 6px rgba(0,0,0,0.4));" alt="You">
                    </div>
                `,
                className: '',
                iconSize: [48, 48],
                iconAnchor: [24, 24],
            });

            const riderIcon = L.divIcon({
                html: `
                    <div style="position:relative;width:56px;height:56px;">
                        <img src="/images/rider.png" style="width:56px;height:56px;object-fit:contain;filter:drop-shadow(0 4px 8px rgba(0,0,0,0.5));" alt="Rider">
                    </div>
                `,
                className: '',
                iconSize: [56, 56],
                iconAnchor: [28, 28],
            });

            this.restaurantMarker = L.marker([restaurantLat, restaurantLng], { icon: restaurantIcon })
                .addTo(this.map)
                .bindPopup('{{ $order->restaurant->name }}');

            this.deliveryMarker = L.marker([deliveryLat, deliveryLng], { icon: deliveryIcon })
                .addTo(this.map)
                .bindPopup('Your Address');

            @if ($order->rider)
                this.riderMarker = L.marker([riderLat, riderLng], { icon: riderIcon })
                    .addTo(this.map)
                    .bindPopup('{{ $order->rider->user->name }}');
            @endif

            this.drawRoute(restaurantLat, restaurantLng, deliveryLat, deliveryLng);
            this.mapInitialized = true;
        },

        async drawRoute(fromLat, fromLng, toLat, toLng) {
            try {
                const url = `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${toLng},${toLat}?overview=full&geometries=geojson`;
                const res = await fetch(url);
                const data = await res.json();

                if (!data.routes || data.routes.length === 0) {
                    this.drawStraightLine(fromLat, fromLng, toLat, toLng);
                    return;
                }

                const route = data.routes[0];
                const coordinates = route.geometry.coordinates;
                const latlngs = coordinates.map(coord => [coord[1], coord[0]]);

                this.routeLine = L.polyline(latlngs, {
                    color: '#f97316',
                    weight: 4,
                    opacity: 0.7,
                    lineJoin: 'round',
                    lineCap: 'round',
                }).addTo(this.map);

                const distanceKm = route.distance / 1000;
                const durationMin = Math.round(route.duration / 60);

                this.distanceText = distanceKm < 1
                    ? Math.round(distanceKm * 1000) + ' m'
                    : distanceKm.toFixed(1) + ' km';

                this.etaText = durationMin < 1 ? 'Arriving' : durationMin + ' min';

                if (this.routeLine) {
                    const bounds = this.routeLine.getBounds();
                    this.map.fitBounds(bounds, { padding: [50, 50] });
                }
            } catch (err) {
                this.drawStraightLine(fromLat, fromLng, toLat, toLng);
            }
        },

        drawStraightLine(fromLat, fromLng, toLat, toLng) {
            this.routeLine = L.polyline([
                [fromLat, fromLng],
                [toLat, toLng],
            ], {
                color: '#f97316',
                weight: 3,
                opacity: 0.5,
                dashArray: '8, 8',
            }).addTo(this.map);
        },

        updateRiderLocation(lat, lng) {
            if (!this.riderMarker) return;
            this.riderMarker.setLatLng([lat, lng]);
            this.lastUpdated = 'Updated: ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            const deliveryLat = {{ $order->delivery_lat }};
            const deliveryLng = {{ $order->delivery_lng }};

            clearTimeout(this.routeDebounce);
            this.routeDebounce = setTimeout(() => {
                this.redrawRouteFromRider(lat, lng, deliveryLat, deliveryLng);
            }, 1000);
        },

        async redrawRouteFromRider(fromLat, fromLng, toLat, toLng) {
            if (this.routeLine) {
                this.map.removeLayer(this.routeLine);
                this.routeLine = null;
            }

            try {
                const url = `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${toLng},${toLat}?overview=full&geometries=geojson`;
                const res = await fetch(url);
                const data = await res.json();

                if (!data.routes || data.routes.length === 0) return;

                const route = data.routes[0];
                const coordinates = route.geometry.coordinates;
                const latlngs = coordinates.map(coord => [coord[1], coord[0]]);

                this.routeLine = L.polyline(latlngs, {
                    color: '#f97316',
                    weight: 4,
                    opacity: 0.7,
                    lineJoin: 'round',
                    lineCap: 'round',
                }).addTo(this.map);

                const distanceKm = route.distance / 1000;
                const durationMin = Math.round(route.duration / 60);

                this.distanceText = distanceKm < 1
                    ? Math.round(distanceKm * 1000) + ' m'
                    : distanceKm.toFixed(1) + ' km';

                this.etaText = durationMin < 1 ? 'Arriving' : durationMin + ' min';
            } catch (err) {
                console.warn('Route redraw failed:', err);
            }
        },

        get statusLabel() {
            const labels = {
                'received': 'Waiting for restaurant confirmation',
                'confirmed': 'Restaurant confirmed your order',
                'preparing': 'Preparing your food',
                'finding_rider': 'Finding a rider',
                'rider_assigned': 'Rider assigned — waiting for restaurant to prepare',
                'picked_up': 'Rider picked up your order',
                'out_for_delivery': 'Order is on the way',
                'delivered': 'Order delivered',
                'no_rider': 'No rider available',
                'cancelled': 'Order cancelled',
                'rejected': 'Order rejected by restaurant',
            };
            return labels[this.status] || this.status.replace(/_/g, ' ');
        },

        get stepIndex() {
            const map = {
                'received': 0, 'confirmed': 1, 'preparing': 2,
                'finding_rider': 3, 'rider_assigned': 3,
                'picked_up': 4, 'out_for_delivery': 4, 'delivered': 5,
                'no_rider': 0, 'cancelled': 0, 'rejected': 0,
            };
            return map[this.status] ?? 0;
        }
    }
}

// ============================================
// REVIEW FORM
// ============================================
function reviewForm(orderId) {
    return {
        showModal: false,
        rating: 0,
        hoverRating: 0,
        title: '',
        body: '',
        photos: [],
        photoPreviews: [],
        submitting: false,

        onPhotoChange(e) {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                if (this.photos.length >= 5) return;

                if (file.size > 5 * 1024 * 1024) {
                    alert('File too large (max 5MB): ' + file.name);
                    return;
                }

                this.photos.push(file);

                const reader = new FileReader();
                reader.onload = (ev) => this.photoPreviews.push(ev.target.result);
                reader.readAsDataURL(file);
            });
            e.target.value = '';
        },

        removePhoto(i) {
            this.photos.splice(i, 1);
            this.photoPreviews.splice(i, 1);
        },

        async submit() {
            if (this.rating === 0) {
                alert('Please select a rating.');
                return;
            }
            if (this.body.length < 10) {
                alert('Please write at least 10 characters.');
                return;
            }

            this.submitting = true;

            const formData = new FormData();
            formData.append('rating', this.rating);
            formData.append('title', this.title || '');
            formData.append('body', this.body);
            this.photos.forEach(photo => formData.append('images[]', photo));

            try {
                const res = await fetch(`/orders/${orderId}/review`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                });

                if (res.ok) {
                    window.location.reload();
                } else {
                    const data = await res.json();
                    alert(data.message || 'Could not submit review.');
                    this.submitting = false;
                }
            } catch (err) {
                console.error(err);
                alert('Network error. Please try again.');
                this.submitting = false;
            }
        }
    }
}
</script>
@endpush