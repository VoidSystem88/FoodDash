<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * Kunin ang VAPID public key para sa frontend.
     */
    public function vapidPublicKey()
    {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key'),
        ]);
    }

    /**
     * I-save ang push subscription.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'endpoint' => 'required|string|max:500',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
            'contentEncoding' => 'nullable|string',
        ]);

        $user = Auth::user();

        // I-update o gumawa ng bagong subscription
        $user->updatePushSubscription(
            endpoint: $data['endpoint'],
            key: $data['keys']['p256dh'],
            token: $data['keys']['auth'],
            contentEncoding: $data['contentEncoding'] ?? 'aesgcm',
        );

        return response()->json([
            'ok' => true,
            'message' => 'Subscription saved.',
        ]);
    }

    /**
     * I-delete ang push subscription.
     */
    public function destroy(Request $request)
    {
        $data = $request->validate([
            'endpoint' => 'required|string',
        ]);

        $user = Auth::user();
        $user->deletePushSubscription($data['endpoint']);

        return response()->json([
            'ok' => true,
            'message' => 'Subscription removed.',
        ]);
    }

    /**
     * Check kung may active subscription ang user.
     */
        /**
     * Check kung may active subscription ang user.
     */
    public function status(Request $request)
    {
        $user = Auth::user();
        $count = $user->pushSubscriptions()->count();

        return response()->json([
            'subscribed' => $count > 0,
            'count' => $count,
        ]);
    }

    /**
     * Mag-send ng test notification.
     */
    public function test(Request $request)
    {
        $user = Auth::user();

        if ($user->pushSubscriptions()->count() === 0) {
            return response()->json([
                'ok' => false,
                'message' => 'No active subscription found.',
            ]);
        }

        try {
            $user->notify(new \App\Notifications\TestPushNotification());
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            \Log::error('Push test failed: ' . $e->getMessage());
            return response()->json([
                'ok' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ]);
        }
    }
}