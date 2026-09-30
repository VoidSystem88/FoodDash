<?php

namespace App\Services\Ai\Tools;

use App\Models\Order;

class GetOrdersCount extends BaseTool
{
    public function name(): string
    {
        return 'get_orders_count';
    }

    public function description(): string
    {
        return 'Get the count of orders for a given period, optionally filtered by status. Use this when the user asks "how many orders", "ilang orders", or wants a count of orders with a specific status (pending, delivered, cancelled, etc.).';
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
                'status' => [
                    'type' => 'string',
                    'enum' => [
                        'all',
                        'received',
                        'confirmed',
                        'preparing',
                        'ready_for_pickup',
                        'rider_assigned',
                        'picked_up',
                        'out_for_delivery',
                        'delivered',
                        'rejected',
                        'cancelled',
                        'no_rider',
                    ],
                    'description' => 'Filter by order status. Use "all" for total count regardless of status.',
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
        $status = $args['status'] ?? 'all';
        [$from, $to] = $this->getDateRange($period);

        $query = Order::where('restaurant_id', $restaurant->id)
            ->whereBetween('created_at', [$from, $to]);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $count = (clone $query)->count();

        // Breakdown by status para sa "all" queries
        $breakdown = null;
        if ($status === 'all') {
            $breakdown = Order::where('restaurant_id', $restaurant->id)
                ->whereBetween('created_at', [$from, $to])
                ->select('status', \DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        }

        return [
            'period' => $period,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'status_filter' => $status,
            'count' => $count,
            'breakdown' => $breakdown,
        ];
    }
}