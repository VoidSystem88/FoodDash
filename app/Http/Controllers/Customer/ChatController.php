<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Chat inbox — listahan ng lahat ng orders na may messages.
     */
    public function inbox(Request $request)
    {
        $userId = auth()->id();
        $showHidden = $request->boolean('show_hidden');

        $query = Order::with(['restaurant', 'rider.user', 'items'])
            ->where('customer_id', $userId)
            ->whereHas('messages');

        // Filter: kung hindi show_hidden, hindi ipakita ang hidden (o may bagong message)
        if (!$showHidden) {
            $query->where(function ($q) {
                $q->whereNull('hidden_for_customer_at')
                  ->orWhereHas('messages', function ($sub) {
                      $sub->whereColumn('messages.created_at', '>', 'orders.hidden_for_customer_at');
                  });
            });
        }

        $orders = $query
            ->orderByRaw("
                CASE
                    WHEN status IN ('rider_assigned', 'picked_up', 'out_for_delivery') THEN 0
                    ELSE 1
                END
            ")
            ->orderByDesc('updated_at')
            ->get();

        $conversations = $orders->map(function ($order) use ($userId) {
            $lastMessage = $order->messages()->latest()->first();
            $unreadCount = $order->messages()
                ->where('sender_id', '!=', $userId)
                ->whereNull('read_at')
                ->count();
            $totalMessages = $order->messages()->count();

            return [
                'order' => $order,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
                'total_messages' => $totalMessages,
                'is_active' => in_array($order->status, [
                    'rider_assigned', 'picked_up', 'out_for_delivery'
                ]),
                'is_hidden' => !is_null($order->hidden_for_customer_at),
            ];
        });

        return view('customer.chat-inbox', compact('conversations', 'showHidden'));
    }

    /**
     * Specific chat page.
     */
    public function show($order)
    {
        $currentOrder = Order::with(['restaurant', 'rider.user', 'items', 'payment'])
            ->where('id', $order)
            ->where('customer_id', auth()->id())
            ->first();

        if (!$currentOrder) {
            return redirect()->route('customer.chat')
                ->with('error', 'Chat not found.');
        }

        return view('customer.chat', compact('currentOrder'));
    }

    /**
     * Unread count for active order.
     */
    public function unreadActive()
    {
        $activeOrder = Order::where('customer_id', auth()->id())
            ->whereIn('status', ['rider_assigned', 'picked_up', 'out_for_delivery'])
            ->first();

        if (!$activeOrder) {
            return response()->json(['unread' => 0]);
        }

        $unread = $activeOrder->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread' => $unread]);
    }

    /**
     * Clear all chats (soft hide).
     */
    public function clearChats()
    {
        $updatedCount = Order::where('customer_id', auth()->id())
            ->whereHas('messages')
            ->update(['hidden_for_customer_at' => now()]);

        return back()->with('success', "Hidden {$updatedCount} chat conversations. Messages are still kept for records.");
    }
}