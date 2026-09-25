@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Notifications</h1>
            <p class="text-sm text-gray-500 mt-1">All your order updates and messages</p>
        </div>

        @if (auth()->user()->unreadNotifications()->count() > 0)
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button class="text-sm text-orange-600 hover:underline font-medium">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($notifications->isEmpty())
        <div class="bg-white rounded-lg border border-gray-200 p-16 text-center">
            <p class="text-5xl mb-4">🔕</p>
            <p class="text-gray-500">No notifications yet.</p>
            <p class="text-sm text-gray-400 mt-2">We'll notify you when something happens!</p>
        </div>
    @else
        <div class="bg-white rounded-lg border border-gray-200 divide-y divide-gray-100">
            @foreach ($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isRead = !is_null($notification->read_at);
                @endphp

                <a href="{{ route('notifications.mark-read', $notification->id) }}"
                   class="flex gap-4 p-4 hover:bg-gray-50 transition {{ $isRead ? '' : 'bg-orange-50' }}">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-xl">
                        {{ $data['icon'] ?? '🔔' }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start gap-2">
                            <p class="font-medium text-gray-900">
                                {{ $data['title'] ?? 'Notification' }}
                            </p>
                            @if (!$isRead)
                                <span class="flex-shrink-0 w-2 h-2 rounded-full bg-orange-500 mt-1.5"></span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-0.5">
                            {{ $data['body'] ?? '' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection