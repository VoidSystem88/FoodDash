<?php

namespace App\Services\Ai\Tools;

use App\Models\Order;

class GetRejectedOrders extends BaseTool
{
    public function name(): string
    {
        return 'get_rejected_orders';
    }

    public function description(): string
    {
        return 'Get a list of rejected, cancelled, or no-rider orders for a given period with their reasons. Use this when the user asks about rejected orders, cancelled orders, failed deliveries, or reasons why orders didn\'t complete.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'period' => [
                    'type' => 'string',
                    'enum' => $this->periodEnum(),
                    'description' => 'The time period. Default to "this_month" if not specified.',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Max number of orders to return. Default 10, max 30.',
                    'minimum' => 1,
                    'maximum' => 30,
                ],
            ],
            'required' => ['period'],
        ];
    }

    public function execute(array $args): array
    {
        $restaurant = $this->getRestaurant();
        if (!$restaurant) {
            return ['error' => 'Restaurant not found for current user.'];
        }

        $period = $args['period'] ?? 'this_month';
        $limit = min((int) ($args['limit'] ?? 10), 30);
        [$from, $to] = $this->getDateRange($period);

        $query = Order::where('restaurant_id', $restaurant->id)
            ->whereIn('status', ['rejected', 'cancelled', 'no_rider'])
            ->whereBetween('created_at', [$from, $to]);

        $total = (clone $query)->count();

        $orders = (clone $query)
            ->with('customer:id,name')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($o) {
                return [
                    'order_id' => $o->id,
                    'status' => $o->status,
                    'reason' => $o->rejection_reason ?? $o->cancellation_reason ?? null,
                    'customer_name' => $o->customer?->name ?? 'Unknown',
                    'total_amount' => round((float) $o->total_amount, 2),
                    'created_at' => $o->created_at->format('Y-m-d H:i'),
                ];
            })
            ->toArray();

        // Breakdown by status
        $byStatus = [
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'no_rider' => (clone $query)->where('status', 'no_rider')->count(),
        ];

        return [
            'period' => $period,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'total' => $total,
            'by_status' => $byStatus,
            'orders' => $orders,
        ];
    }
}