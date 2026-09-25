<?php

namespace App\Http\Controllers;

use App\Events\MessagesRead;
use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Get all messages for an order.
     */
    public function index(Request $request, Order $order)
    {
        $this->authorizeChat($request, $order);

        // Mark messages as delivered (nasa chat page ka na = delivered)
        $order->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('delivered_at')
            ->update(['delivered_at' => now()]);

        // Mark messages as read
        $readIds = $order->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->pluck('id')
            ->toArray();

        if (!empty($readIds)) {
            $order->messages()
                ->whereIn('id', $readIds)
                ->update(['read_at' => now()]);

            broadcast(new MessagesRead($order->id, $request->user()->id, $readIds));
        }

        $messages = $order->messages()->with('sender:id,name,role')->get();

        return response()->json([
            'messages' => $messages->map(fn($m) => [
                'id' => $m->id,
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender->name,
                'sender_role' => $m->sender->role,
                'body' => $m->body,
                'created_at' => $m->created_at->toIso8601String(),
                'created_at_human' => $m->created_at->diffForHumans(),
                'delivered_at' => $m->delivered_at?->toIso8601String(),
                'read_at' => $m->read_at?->toIso8601String(),
            ]),
        ]);
    }

    /**
     * Send a new message.
     */
    public function store(Request $request, Order $order)
    {
        $this->authorizeChat($request, $order);

        $data = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'order_id' => $order->id,
            'sender_id' => $request->user()->id,
            'body' => $data['body'],
            'delivered_at' => now(), // auto-delivered sa real-time
        ]);

        broadcast(new MessageSent($message->load('sender')))->toOthers();
// Notify receiver (database notification)
$order->load('customer', 'rider.user');
$receiver = null;

if ($request->user()->id === $order->customer_id) {
    // Nag-send si customer → i-notify ang rider
    $receiver = $order->rider?->user;
} else {
    // Nag-send si rider → i-notify ang customer
    $receiver = $order->customer;
}

if ($receiver && $receiver->id !== $request->user()->id) {
    $receiver->notify(new \App\Notifications\NewChatMessageNotification($message));
}
        return response()->json([
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name,
                'sender_role' => $message->sender->role,
                'body' => $message->body,
                'created_at' => $message->created_at->toIso8601String(),
                'created_at_human' => $message->created_at->diffForHumans(),
                'delivered_at' => $message->delivered_at?->toIso8601String(),
                'read_at' => $message->read_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Broadcast typing indicator.
     */
    public function typing(Request $request, Order $order)
    {
        $this->authorizeChat($request, $order);

        $data = $request->validate([
            'is_typing' => 'required|boolean',
        ]);

        broadcast(new UserTyping(
            orderId: $order->id,
            userId: $request->user()->id,
            userName: $request->user()->name,
            isTyping: $data['is_typing'],
        ))->toOthers();

        return response()->json(['ok' => true]);
    }

    /**
     * Mark messages as read.
     */
    public function markRead(Request $request, Order $order)
    {
        $this->authorizeChat($request, $order);

        $readIds = $order->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->pluck('id')
            ->toArray();

        if (empty($readIds)) {
            return response()->json(['ok' => true, 'count' => 0]);
        }

        $order->messages()
            ->whereIn('id', $readIds)
            ->update([
                'read_at' => now(),
                'delivered_at' => now(),
            ]);

        broadcast(new MessagesRead($order->id, $request->user()->id, $readIds));

        return response()->json(['ok' => true, 'count' => count($readIds)]);
    }

    protected function authorizeChat(Request $request, Order $order): void
    {
        $user = $request->user();

        if ($user->id === $order->customer_id) {
            return;
        }

        if ($user->rider && $user->rider->id === $order->rider_id) {
            return;
        }

        abort(403, 'Unauthorized chat access.');
    }
}