@extends('layouts.app')

@section('content')
<div x-data="orderTracker({{ $order->id }})" x-init="init()">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-neutral-100">Order #{{ $order->id }}</h1>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">{{ $order->restaurant->name }}</p>
        </div>
        <a href="{{ route('customer.orders') }}" class="text-sm text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200">
            Back
        </a>
    </div>

    {{-- STATUS --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-6 mb-4">
        <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-neutral-500 mb-1">Current status</p>
        <p class="text-lg font-semibold text-gray-900 dark:text-neutral-100" x-text="statusLabel"></p>

        {{-- PROGRESS --}}
        <div class="mt-6 flex items-center">
            <template x-for="(step, i) in steps" :key="i">
                <div class="flex items-center flex-1">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium flex-shrink-0"
                         :class="stepIndex >= i ? 'bg-orange-600 text-white' : 'bg-gray-200 dark:bg-dark-700 text-gray-500 dark:text-neutral-400'">
                        <span x-text="i + 1"></span>
                    </div>
                    <div class="flex-1 h-0.5 mx-1"
                         :class="stepIndex > i ? 'bg-orange-600' : 'bg-gray-200 dark:bg-dark-700'"
                         x-show="i < steps.length - 1"></div>
                </div>
            </template>
        </div>
        <div class="flex justify-between mt-2">
            <template x-for="(step, i) in steps" :key="'lbl' + i">
                <span class="text-xs" :class="stepIndex >= i ? 'text-gray-700 dark:text-neutral-300 font-medium' : 'text-gray-400 dark:text-neutral-500'" x-text="step"></span>
            </template>
        </div>
    </div>

    {{-- REJECTION REASON --}}
    @if ($order->status === 'rejected' && $order->rejection_reason)
        <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-4">
            <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-medium mb-1">Order rejected</p>
            <p class="text-sm text-red-800 dark:text-red-200">Reason: {{ $order->rejection_reason }}</p>
        </div>
    @endif

    {{-- CANCELLATION REASON --}}
    @if ($order->status === 'cancelled' && $order->cancellation_reason)
        <div class="bg-gray-50 dark:bg-dark-850 border border-gray-300 dark:border-dark-600 rounded-lg p-4 mb-4">
            <p class="text-xs uppercase tracking-wide text-gray-700 dark:text-neutral-300 font-medium mb-1">
                Order cancelled
            </p>
            <p class="text-sm text-gray-800 dark:text-neutral-200">
                Reason: {{ $order->cancellation_reason }}
            </p>
            @if ($order->cancelled_at)
                <p class="text-xs text-gray-500 dark:text-neutral-400 mt-2">
                    Cancelled {{ $order->cancelled_at->diffForHumans() }}
                </p>
            @endif
        </div>
    @endif

    {{-- CANCEL ORDER FORM --}}
    @if ($order->canBeCancelledByCustomer())
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-red-200 dark:border-red-800 p-6 mb-4"
             x-data="{ showCancel: false }">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-neutral-100">Need to cancel?</p>
                    <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">
                        You can cancel this order while it's still being processed.
                    </p>
                </div>
                <button type="button"
                        @click="showCancel = !showCancel"
                        class="text-red-600 dark:text-red-400 border border-red-300 dark:border-red-800 hover:bg-red-50 dark:hover:bg-red-950/30 px-4 py-2 rounded text-sm font-medium">
                    Cancel Order
                </button>
            </div>

            <div x-show="showCancel"
                 x-cloak
                 x-transition
                 class="mt-4 pt-4 border-t border-red-100 dark:border-red-900">
                <form method="POST"
                      action="{{ route('customer.orders.cancel', $order) }}"
                      class="space-y-3"
                      onsubmit="return confirm('Are you sure you want to cancel this order? This cannot be undone.');">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-neutral-200 mb-1">
                            Reason for cancellation
                        </label>
                        <select name="cancellation_reason"
                                required
                                class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
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
                                class="bg-red-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-red-700">
                            Confirm Cancellation
                        </button>
                        <button type="button"
                                @click="showCancel = false"
                                class="border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 px-4 py-2 rounded text-sm hover:bg-gray-50 dark:hover:bg-dark-850">
                            Keep Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- REVIEW SECTION — para sa delivered orders --}}
    {{-- ============================================ --}}
    @if ($order->status === 'delivered' && $order->canBeReviewed())
        <div x-data="reviewForm({{ $order->id }})"
             class="bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-950/20 dark:to-orange-950/20 rounded-2xl border-2 border-orange-200 dark:border-orange-800 p-6 mb-4">

            <div class="flex items-start gap-4 mb-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center flex-shrink-0 shadow-lg">
                    <span class="text-2xl">⭐</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100">
                        Rate your experience
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-neutral-400">
                        Tulungan mo ang ibang customers sa review mo para sa
                        <strong>{{ $order->restaurant->name }}</strong>.
                    </p>
                </div>
            </div>

            <button type="button"
                    @click="showModal = true"
                    class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl font-bold shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg transition">
                ⭐ Write a Review
            </button>

            {{-- Review Modal --}}
            <div x-show="showModal"
                 x-cloak
                 x-transition.opacity
                 @keydown.escape.window="showModal = false"
                 @click.self="showModal = false"
                 class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/60"
                 style="display: none;">

                <div x-show="showModal"
                     x-transition.scale.origin.center
                     class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-6">

                    <div class="flex justify-between items-start mb-5">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100">Rate your experience</h2>
                            <p class="text-sm text-gray-500 dark:text-neutral-400">
                                {{ $order->restaurant->name }}
                            </p>
                        </div>
                        <button type="button"
                                @click="showModal = false"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submit()" class="space-y-4">

                        {{-- Star Rating --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-neutral-100 mb-2">
                                Overall Rating *
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
                                                ? 'text-yellow-400 fill-yellow-400' 
                                                : 'text-gray-300 dark:text-neutral-600'"
                                             viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <p x-show="rating > 0"
                               x-text="['', 'Terrible 😞', 'Poor 😕', 'Average 😐', 'Good 😊', 'Excellent 🤩'][rating]"
                               class="text-sm font-medium text-gray-600 dark:text-neutral-400 mt-2"></p>
                        </div>

                        {{-- Title --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-neutral-100 mb-2">
                                Title <span class="text-gray-400 font-normal">(optional)</span>
                            </label>
                            <input type="text"
                                   x-model="title"
                                   maxlength="100"
                                   placeholder="e.g. Best pizza in CDO!"
                                   class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>

                        {{-- Body --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-neutral-100 mb-2">
                                Your Review *
                            </label>
                            <textarea x-model="body"
                                      required
                                      minlength="10"
                                      maxlength="2000"
                                      rows="5"
                                      placeholder="Ano ang experience mo? Naging maayos ba ang pagkain at service?"
                                      class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"></textarea>
                            <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">
                                <span x-text="body.length"></span>/2000 characters (min 10)
                            </p>
                        </div>

                        {{-- Photos --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-neutral-100 mb-2">
                                Add Photos <span class="text-gray-400 font-normal">(optional, max 5)</span>
                            </label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(preview, i) in photoPreviews" :key="i">
                                    <div class="relative w-20 h-20">
                                        <img :src="preview" class="w-full h-full object-cover rounded-xl">
                                        <button type="button"
                                                @click="removePhoto(i)"
                                                class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-red-600 transition">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>

                                <button type="button"
                                        x-show="photoPreviews.length < 5"
                                        @click="$refs.photoInput.click()"
                                        class="w-20 h-20 border-2 border-dashed border-gray-300 dark:border-dark-600 rounded-xl flex flex-col items-center justify-center text-gray-400 dark:text-neutral-500 hover:border-orange-500 hover:text-orange-500 dark:hover:border-orange-500 transition">
                                    <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
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
                        <div class="bg-blue-50 dark:bg-blue-950/30 rounded-xl p-3 flex gap-2">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs text-blue-800 dark:text-blue-300 leading-relaxed">
                                Ang review mo ay makikita ng lahat. I-base ito sa tunay na experience mo.
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2 pt-2">
                            <button type="button"
                                    @click="showModal = false"
                                    class="flex-1 border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 py-3 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-dark-850 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                    :disabled="submitting || rating === 0 || body.length < 10"
                                    :class="(submitting || rating === 0 || body.length < 10) 
                                        ? 'opacity-40 cursor-not-allowed' 
                                        : 'hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98'"
                                    class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl font-bold shadow-md transition transform">
                                <span x-show="!submitting">Submit Review</span>
                                <span x-show="submitting">Submitting...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- SHOW EXISTING REVIEW --}}
    @if ($order->status === 'delivered' && $order->hasBeenReviewed())
        @php $review = $order->review; @endphp
        <div class="bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800 rounded-2xl p-6 mb-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-green-900 dark:text-green-300">Review submitted</p>
                    <p class="text-xs text-green-700 dark:text-green-400">
                        Salamat sa feedback mo!
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-800 rounded-xl p-4">
                <div class="flex items-center gap-1 mb-2">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 dark:text-neutral-600' }}"
                             viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                </div>
                @if ($review->title)
                    <p class="font-bold text-gray-900 dark:text-neutral-100 mb-1">{{ $review->title }}</p>
                @endif
                <p class="text-sm text-gray-700 dark:text-neutral-300">{{ $review->body }}</p>
                <p class="text-xs text-gray-400 dark:text-neutral-500 mt-2">
                    Submitted {{ $review->created_at->diffForHumans() }}
                </p>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- CHAT WITH RIDER --}}
    {{-- ============================================ --}}
    @if ($order->canChat())
        <div x-data="chatBox({{ $order->id }})"
             x-init="init()"
             class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 mb-4 overflow-hidden">

            {{-- HEADER --}}
            <div class="bg-orange-600 text-white px-5 py-3 flex justify-between items-center cursor-pointer"
                 @click="isOpen = !isOpen">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="font-semibold">Chat with Rider</span>
                    <template x-if="unread > 0">
                        <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full animate-pulse"
                              x-text="unread"></span>
                    </template>
                </div>
                <svg class="w-5 h-5 transition-transform duration-200"
                     :class="isOpen ? 'rotate-180' : ''"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            {{-- CHAT BODY --}}
            <div x-show="isOpen" x-cloak x-transition>
                <div x-ref="messagesContainer"
                     @scroll="onScroll()"
                     class="chat-scroll h-96 overflow-y-auto p-4 space-y-2 bg-gray-50 dark:bg-dark-850 scroll-smooth">

                    <template x-if="loading">
                        <p class="text-center text-sm text-gray-500 dark:text-neutral-400">Loading messages...</p>
                    </template>

                    <template x-if="!loading && messages.length === 0">
                        <div class="text-center py-12">
                            <p class="text-4xl mb-2">💬</p>
                            <p class="text-sm text-gray-500 dark:text-neutral-400">No messages yet.</p>
                            <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">Say hi to your rider!</p>
                        </div>
                    </template>

                    <template x-for="msg in messages" :key="msg.id">
                        <div :class="msg.sender_id === currentUserId ? 'flex justify-end' : 'flex justify-start'"
                             class="chat-message gap-2 items-end">

                            {{-- AVATAR — left side para sa kausap --}}
                            <template x-if="msg.sender_id !== currentUserId">
                                <div class="flex-shrink-0">
                                    <template x-if="msg.sender_avatar_url">
                                        <img :src="msg.sender_avatar_url"
                                             :alt="msg.sender_name"
                                             class="w-8 h-8 rounded-full object-cover border-2 border-white dark:border-dark-800 shadow-sm">
                                    </template>
                                    <template x-if="!msg.sender_avatar_url">
                                        <div :class="msg.sender_avatar_color || 'bg-gray-500'"
                                             class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold border-2 border-white dark:border-dark-800 shadow-sm"
                                             x-text="msg.sender_initials || '?'"></div>
                                    </template>
                                </div>
                            </template>

                            {{-- MESSAGE BUBBLE --}}
                            <div :class="msg.sender_id === currentUserId
                                    ? 'bg-orange-600 text-white rounded-br-none'
                                    : 'bg-white dark:bg-dark-800 text-gray-900 dark:text-neutral-100 border border-gray-200 dark:border-dark-700 rounded-bl-none'"
                                 class="max-w-[75%] px-3 py-2 rounded-2xl shadow-sm transition-all duration-200">
                                <p class="text-xs font-medium mb-0.5 opacity-75"
                                   x-text="msg.sender_name"></p>
                                <p class="text-sm break-words whitespace-pre-wrap"
                                   x-text="msg.body"></p>
                                <div class="flex items-center justify-end gap-1 mt-1">
                                    <span class="text-[10px] opacity-60"
                                          x-text="msg.created_at_human"></span>
                                    <template x-if="msg.sender_id === currentUserId">
                                        <span class="text-[11px] leading-none"
                                              :title="statusLabel(msg)"
                                              :class="msg.status === 'seen' ? 'text-blue-200 font-bold' : 'opacity-70'">
                                            <span x-show="msg.status === 'sent'">🕐</span>
                                            <span x-show="msg.status === 'delivered'">✓</span>
                                            <span x-show="msg.status === 'seen'">✓✓</span>
                                        </span>
                                    </template>
                                </div>
                            </div>

                            {{-- AVATAR — right side para sa sarili --}}
                            <template x-if="msg.sender_id === currentUserId">
                                <div class="flex-shrink-0">
                                    <template x-if="msg.sender_avatar_url">
                                        <img :src="msg.sender_avatar_url"
                                             :alt="msg.sender_name"
                                             class="w-8 h-8 rounded-full object-cover border-2 border-white dark:border-dark-800 shadow-sm">
                                    </template>
                                    <template x-if="!msg.sender_avatar_url">
                                        <div :class="msg.sender_avatar_color || 'bg-orange-500'"
                                             class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold border-2 border-white dark:border-dark-800 shadow-sm"
                                             x-text="msg.sender_initials || '?'"></div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- TYPING INDICATOR --}}
                    <template x-if="typingName">
                        <div class="flex justify-start chat-message gap-2 items-end">
                            <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-dark-700 flex-shrink-0"></div>
                            <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 px-3 py-2 rounded-2xl rounded-bl-none shadow-sm">
                                <p class="text-xs text-gray-500 dark:text-neutral-400 mb-1" x-text="typingName + ' is typing'"></p>
                                <div class="flex gap-1">
                                    <span class="typing-dot"></span>
                                    <span class="typing-dot" style="animation-delay: 0.15s"></span>
                                    <span class="typing-dot" style="animation-delay: 0.3s"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- INPUT --}}
                <div class="border-t border-gray-200 dark:border-dark-700 p-3 bg-white dark:bg-dark-800">
                    <form @submit.prevent="sendMessage()" class="flex gap-2">
                        <input type="text"
                               x-model="newMessage"
                               @input="onTypingInput()"
                               @keydown.enter.prevent="sendMessage()"
                               @blur="stopTyping()"
                               placeholder="Type a message..."
                               maxlength="1000"
                               class="flex-1 border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        <button type="submit"
                                :disabled="!newMessage.trim() || sending"
                                :class="(!newMessage.trim() || sending) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-orange-700'"
                                class="bg-orange-600 text-white px-5 py-2 rounded-full text-sm font-medium transition">
                            <span x-show="!sending">Send</span>
                            <span x-show="sending">...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- LIVE MAP --}}
    @if ($order->rider && in_array($order->status, ['rider_assigned', 'picked_up', 'out_for_delivery']))
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-6 mb-4">
            <div class="flex justify-between items-center mb-3">
                <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-neutral-500">Live Rider Location</p>
                <p class="text-xs text-gray-500 dark:text-neutral-400" x-text="lastUpdated"></p>
            </div>
            <div id="map" class="w-full h-80 rounded-lg border border-gray-200 dark:border-dark-700 z-0"></div>

            <div class="mt-3 grid grid-cols-2 gap-3">
                <div class="bg-gray-50 dark:bg-dark-850 rounded p-3">
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Distance to you</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-neutral-100" x-text="distanceText || '—'"></p>
                </div>
                <div class="bg-gray-50 dark:bg-dark-850 rounded p-3">
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Estimated arrival</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-neutral-100" x-text="etaText || '—'"></p>
                </div>
            </div>
        </div>
    @endif

    {{-- DELIVERY --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-6 mb-4">
        <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-neutral-500 mb-2">Delivery address</p>
        <p class="text-sm text-gray-700 dark:text-neutral-300">{{ $order->delivery_address }}</p>

        @if ($order->rider)
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-dark-700">
                <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-neutral-500 mb-1">Your rider</p>
                <p class="text-sm font-medium text-gray-900 dark:text-neutral-100">{{ $order->rider->user->name }}</p>
                @if ($order->rider->vehicle_type)
                    <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">
                        {{ $order->rider->vehicle_type }}
                        @if ($order->rider->vehicle_plate)
                            · {{ $order->rider->vehicle_plate }}
                        @endif
                    </p>
                @endif
            </div>
        @endif
    </div>

    {{-- ITEMS --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-6 mb-4">
        <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-neutral-500 mb-4">Order summary</p>

        <div class="space-y-2">
            @foreach ($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-700 dark:text-neutral-300">{{ $item->quantity }}× {{ $item->name }}</span>
                    <span class="text-gray-900 dark:text-neutral-100">₱{{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
            @endforeach
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-dark-700 space-y-1.5 text-sm">
            <div class="flex justify-between text-gray-500 dark:text-neutral-400">
                <span>Food cost</span>
                <span>₱{{ number_format($order->food_cost, 2) }}</span>
            </div>
            <div class="flex justify-between text-gray-500 dark:text-neutral-400">
                <span>Delivery fee</span>
                <span>₱{{ number_format($order->delivery_fee, 2) }}</span>
            </div>
            <div class="flex justify-between font-semibold text-gray-900 dark:text-neutral-100 pt-2 border-t border-gray-100 dark:border-dark-700">
                <span>Total</span>
                <span>₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- RATING FORM (OLD — Restaurant & Rider stars only) --}}
    @if ($order->status === 'delivered' && !$order->restaurant_rating)
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-6 mb-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-neutral-500 mb-4">Quick Rating</p>
            <form method="POST" action="{{ route('customer.orders.rate', $order) }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm text-gray-700 dark:text-neutral-300 mb-2">Restaurant</label>
                    <div class="flex gap-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="restaurant_rating" value="{{ $i }}" class="peer sr-only" required>
                                <span class="block text-3xl text-gray-300 dark:text-neutral-600 peer-checked:text-orange-500 hover:text-orange-400 transition">★</span>
                            </label>
                        @endfor
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-700 dark:text-neutral-300 mb-2">Rider</label>
                    <div class="flex gap-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="rider_rating" value="{{ $i }}" class="peer sr-only" required>
                                <span class="block text-3xl text-gray-300 dark:text-neutral-600 peer-checked:text-orange-500 hover:text-orange-400 transition">★</span>
                            </label>
                        @endfor
                    </div>
                </div>

                <button class="w-full bg-orange-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-orange-700">
                    Submit
                </button>
            </form>
        </div>
    @endif

    {{-- SHOW RATINGS --}}
    @if ($order->status === 'delivered' && $order->restaurant_rating)
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-6">
            <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-neutral-500 mb-4">Your ratings</p>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700 dark:text-neutral-300">Restaurant</span>
                    <div class="flex gap-0.5">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="text-xl {{ $i <= $order->restaurant_rating ? 'text-orange-500' : 'text-gray-300 dark:text-neutral-600' }}">★</span>
                        @endfor
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700 dark:text-neutral-300">Rider</span>
                    <div class="flex gap-0.5">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="text-xl {{ $i <= $order->rider_rating ? 'text-orange-500' : 'text-gray-300 dark:text-neutral-600' }}">★</span>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    /* Completely hide scrollbar (pero scrollable pa rin) */
    .chat-scroll::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }
    .chat-scroll {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .chat-message {
        animation: slideIn 0.25s ease-out;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .typing-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #9ca3af;
        animation: bounceDot 1.4s infinite ease-in-out both;
    }
    @keyframes bounceDot {
        0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
        40% { transform: scale(1); opacity: 1; }
    }
    .scroll-smooth { scroll-behavior: smooth; }
</style>
<script>
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

            @if ($order->rider && in_array($order->status, ['rider_assigned', 'picked_up', 'out_for_delivery']))
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
                $restaurantProfileUrl = $order->restaurant->profile_image_url;
                $restaurantInitial = strtoupper(substr($order->restaurant->name, 0, 1));
            @endphp

            const restaurantProfileUrl = '{{ $restaurantProfileUrl }}';
            const restaurantInitial = '{{ $restaurantInitial }}';

            const restaurantIcon = L.divIcon({
                html: restaurantProfileUrl ? `
                    <div style="position:relative;width:56px;height:56px;">
                        <div style="
                            width:56px;
                            height:56px;
                            border-radius:50%;
                            overflow:hidden;
                            border:4px solid #ef4444;
                            box-shadow:0 4px 10px rgba(239,68,68,0.5), 0 2px 6px rgba(0,0,0,0.3);
                            background:white;
                        ">
                            <img src="${restaurantProfileUrl}"
                                 onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=&quot;width:100%;height:100%;background:#ef4444;color:white;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:bold;&quot;>${restaurantInitial}</div>';"
                                 style="width:100%;height:100%;object-fit:cover;"
                                 alt="Restaurant">
                        </div>
                        <div style="
                            position:absolute;
                            bottom:-2px;
                            right:-2px;
                            background:#ef4444;
                            color:white;
                            width:20px;
                            height:20px;
                            border-radius:50%;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:12px;
                            border:2px solid white;
                            box-shadow:0 2px 4px rgba(0,0,0,0.3);
                        ">🏪</div>
                    </div>
                ` : `
                    <div style="position:relative;width:56px;height:56px;">
                        <div style="
                            background:#ef4444;
                            color:white;
                            width:56px;
                            height:56px;
                            border-radius:50%;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:24px;
                            font-weight:bold;
                            border:4px solid white;
                            box-shadow:0 4px 10px rgba(0,0,0,0.4);
                        ">${restaurantInitial}</div>
                    </div>
                `,
                className: '',
                iconSize: [56, 56],
                iconAnchor: [28, 28],
                popupAnchor: [0, -28],
            });

            const deliveryIcon = L.divIcon({
                html: `
                    <div style="position:relative;width:48px;height:48px;">
                        <img src="/images/cusicon.png"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                             style="width:48px;height:48px;object-fit:contain;filter:drop-shadow(0 3px 6px rgba(0,0,0,0.4));"
                             alt="Customer">
                        <div style="display:none;background:#10b981;color:white;width:44px;height:44px;border-radius:50%;align-items:center;justify-content:center;font-size:22px;border:3px solid white;box-shadow:0 3px 6px rgba(0,0,0,0.4);position:absolute;top:2px;left:2px;">🏠</div>
                    </div>
                `,
                className: '',
                iconSize: [48, 48],
                iconAnchor: [24, 24],
                popupAnchor: [0, -24],
            });

            const riderIcon = L.divIcon({
                html: `
                    <div style="position:relative;width:56px;height:56px;">
                        <img src="/images/ridicon.png"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                             style="width:56px;height:56px;object-fit:contain;filter:drop-shadow(0 4px 8px rgba(0,0,0,0.5));"
                             alt="Rider">
                        <div style="display:none;background:#f97316;color:white;width:52px;height:52px;border-radius:50%;align-items:center;justify-content:center;font-size:26px;border:3px solid white;box-shadow:0 3px 6px rgba(0,0,0,0.4);position:absolute;top:2px;left:2px;">🛵</div>
                    </div>
                `,
                className: '',
                iconSize: [56, 56],
                iconAnchor: [28, 28],
                popupAnchor: [0, -28],
            });

            this.restaurantMarker = L.marker([restaurantLat, restaurantLng], { icon: restaurantIcon })
                .addTo(this.map)
                .bindPopup('🏪 {{ $order->restaurant->name }}');

            this.deliveryMarker = L.marker([deliveryLat, deliveryLng], { icon: deliveryIcon })
                .addTo(this.map)
                .bindPopup('🏠 Your Address');

            @if ($order->rider)
                this.riderMarker = L.marker([riderLat, riderLng], { icon: riderIcon })
                    .addTo(this.map)
                    .bindPopup('🛵 {{ $order->rider->user->name }}');
            @endif

            this.drawRoute(restaurantLat, restaurantLng, deliveryLat, deliveryLng);

            this.mapInitialized = true;
            console.log('✅ Map initialized');
        },

        async drawRoute(fromLat, fromLng, toLat, toLng) {
            console.log('🛣️ Fetching road route...');

            try {
                const url = `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${toLng},${toLat}?overview=full&geometries=geojson`;
                const res = await fetch(url);
                const data = await res.json();

                if (!data.routes || data.routes.length === 0) {
                    console.warn('⚠️ No route, falling back');
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

                console.log('✅ Road route drawn:', latlngs.length, 'points');

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
                console.error('❌ Route fetch failed:', err);
                this.drawStraightLine(fromLat, fromLng, toLat, toLng);
            }
        },

        drawStraightLine(fromLat, fromLng, toLat, toLng) {
            console.log('📏 Drawing straight fallback');
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
            this.lastUpdated = 'Updated: ' + new Date().toLocaleTimeString();

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
                'rider_assigned': 'Rider on the way to restaurant',
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

function chatBox(orderId) {
    return {
        isOpen: true,
        loading: true,
        sending: false,
        messages: [],
        newMessage: '',
        unread: 0,
        currentUserId: {{ auth()->id() }},
        currentUserName: '{{ auth()->user()->name }}',
        currentUserAvatar: '{{ auth()->user()->avatar_url }}',
        currentUserInitials: '{{ auth()->user()->initials }}',
        currentUserAvatarColor: '{{ auth()->user()->avatar_color }}',
        channel: null,
        typingName: '',
        typingTimeout: null,
        isTypingSent: false,
        isAtBottom: true,
        listenerAttached: false,

        init() {
            console.log('💬 chatBox.init() for order:', orderId);

            if (window.Echo) {
                window.Echo.leave(`order.${orderId}.chat`);
                console.log('🚪 Left old channel');
            }

            this.loadMessages();

            setTimeout(() => {
                this.subscribeToChat(orderId);
            }, 300);
        },

        subscribeToChat(orderId) {
            if (this.listenerAttached) return;

            if (typeof window.Echo === 'undefined') {
                console.error('❌ Echo not available');
                return;
            }

            console.log('🔌 Subscribing to order.' + orderId + '.chat');

            const chatChannel = window.Echo.private(`order.${orderId}.chat`);

            chatChannel.subscribed(() => {
                console.log('✅ SUBSCRIBED to order.' + orderId + '.chat');
            });

            chatChannel.error((err) => {
                console.error('❌ Subscription error:', err);
            });

            chatChannel.listen('.message.sent', (e) => {
                console.log('🔥 MESSAGE RECEIVED:', e);

                if (e.sender_id === this.currentUserId) {
                    console.log('⏭️ Skipping own message');
                    return;
                }

                e.status = 'received';
                this.messages.push(e);
                console.log('✅ Pushed. Total:', this.messages.length);

                if (!this.isOpen || !this.isAtBottom) {
                    this.unread++;
                } else {
                    this.markAsRead();
                }

                if (this.isAtBottom) {
                    this.$nextTick(() => this.scrollToBottom());
                }
            });

            chatChannel.listen('.user.typing', (e) => {
                if (e.user_id === this.currentUserId) return;

                const wasTyping = this.typingName;
                this.typingName = e.is_typing ? e.user_name : '';

                if (e.is_typing && !wasTyping && this.isAtBottom) {
                    this.$nextTick(() => this.scrollToBottom());
                }
            });

            chatChannel.listen('.messages.read', (e) => {
                if (e.reader_id === this.currentUserId) return;
                this.messages.forEach(m => {
                    if (e.message_ids.includes(m.id) && m.sender_id === this.currentUserId) {
                        m.status = 'seen';
                        m.read_at = e.read_at;
                    }
                });
            });

            this.channel = chatChannel;
            this.listenerAttached = true;
            console.log('🎯 Listener attached');
        },

        statusLabel(msg) {
            if (msg.status === 'seen') return 'Seen';
            if (msg.status === 'delivered') return 'Delivered';
            return 'Sending...';
        },

        async loadMessages() {
            try {
                const res = await fetch('{{ route('customer.chat.index', $order) }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                const data = await res.json();

                this.messages = (data.messages || []).map(m => ({
                    ...m,
                    status: m.sender_id === this.currentUserId
                        ? (m.read_at ? 'seen' : (m.delivered_at ? 'delivered' : 'sent'))
                        : 'received',
                }));

                this.unread = 0;
                this.loading = false;
                this.$nextTick(() => this.scrollToBottom());
            } catch (err) {
                console.error('Failed to load messages', err);
                this.loading = false;
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.sending) return;

            this.sending = true;
            const body = this.newMessage.trim();
            this.newMessage = '';
            this.stopTyping();

            const optimisticId = 'temp-' + Date.now();
            const optimisticMsg = {
                id: optimisticId,
                sender_id: this.currentUserId,
                sender_name: this.currentUserName,
                sender_avatar_url: this.currentUserAvatar,
                sender_initials: this.currentUserInitials,
                sender_avatar_color: this.currentUserAvatarColor,
                body: body,
                created_at: new Date().toISOString(),
                created_at_human: 'just now',
                status: 'sent',
            };
            this.messages.push(optimisticMsg);
            this.scrollToBottom();

            try {
                const res = await fetch('{{ route('customer.chat.store', $order) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ body })
                });

                const data = await res.json();

                const idx = this.messages.findIndex(m => m.id === optimisticId);
                if (idx !== -1) {
                    this.messages[idx] = {
                        ...data.message,
                        status: 'delivered',
                    };
                }
            } catch (err) {
                console.error('Failed to send message', err);
                const idx = this.messages.findIndex(m => m.id === optimisticId);
                if (idx !== -1) this.messages.splice(idx, 1);
                this.newMessage = body;
            }

            this.sending = false;
        },

        onTypingInput() {
            if (!this.isTypingSent && this.newMessage.trim()) {
                this.sendTyping(true);
                this.isTypingSent = true;
            }

            clearTimeout(this.typingTimeout);
            this.typingTimeout = setTimeout(() => {
                this.stopTyping();
            }, 2000);
        },

        stopTyping() {
            if (this.isTypingSent) {
                this.sendTyping(false);
                this.isTypingSent = false;
            }
            clearTimeout(this.typingTimeout);
        },

        async sendTyping(isTyping) {
            try {
                await fetch('{{ route('customer.chat.typing', $order) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ is_typing: isTyping })
                });
            } catch (err) {
                // Silent fail
            }
        },

        async markAsRead() {
            try {
                await fetch('{{ route('customer.chat.mark-read', $order) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                this.unread = 0;
            } catch (err) {
                // Silent fail
            }
        },

        onScroll() {
            const el = this.$refs.messagesContainer;
            if (!el) return;

            this.isAtBottom = (el.scrollHeight - el.scrollTop - el.clientHeight) < 50;

            if (this.isAtBottom && this.unread > 0) {
                this.markAsRead();
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.messagesContainer;
                if (el) {
                    el.scrollTop = el.scrollHeight;
                    this.isAtBottom = true;
                }
            });
        }
    }
}

// ⭐ Review Form Function
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