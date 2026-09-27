<?php

use App\Models\Order;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// Rider private channel — for delivery offers
Broadcast::channel('rider.{riderId}', function ($user, $riderId) {
    return $user->rider && (int) $user->rider->id === (int) $riderId;
});

// Order channel — customer, restaurant, rider, admin
Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    $order = Order::find($orderId);
    if (!$order) {
        return false;
    }

    if ($user->id === $order->customer_id) {
        return true;
    }

    if ($user->restaurant && $user->restaurant->id === $order->restaurant_id) {
        return true;
    }

    if ($user->rider && $user->rider->id === $order->rider_id) {
        return true;
    }

    if ($user->isAdmin()) {
        return true;
    }

    return false;
});

// Chat channel — customer and rider only
Broadcast::channel('order.{orderId}.chat', function ($user, $orderId) {
    $order = Order::find($orderId);
    if (!$order) {
        return false;
    }

    if ($user->id === $order->customer_id) {
        return true;
    }

    if ($user->rider && $user->rider->id === $order->rider_id) {
        return true;
    }

    return false;
});

// User notification channel
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// ⭐ Restaurant private channel — for real-time new orders
Broadcast::channel('restaurant.{restaurantId}', function ($user, $restaurantId) {
    return $user->restaurant && (int) $user->restaurant->id === (int) $restaurantId;
});