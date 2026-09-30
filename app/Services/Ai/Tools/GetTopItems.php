<?php

namespace App\Services\Ai\Tools;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class GetTopItems extends BaseTool
{
    public function name(): string
    {
        return 'get_top_items';
    }

    public function description(): string
    {
        return 'Get the best-selling menu items for a given period. Returns item name, quantity sold, and total revenue per item, ordered by quantity descending. Use this when the user asks about best sellers, popular items, top menu items, or what sells most.';
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
                    'description' => 'Number of top items to return. Default 5, max 20.',
                    'minimum' => 1,
                    'maximum' => 20,
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
        $limit = min((int) ($args['limit'] ?? 5), 20);
        [$from, $to] = $this->getDateRange($period);

        $items = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.restaurant_id', $restaurant->id)
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$from, $to])
            ->select(
                'order_items.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->groupBy('order_items.name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();

        return [
            'period' => $period,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'items' => $items->map(fn($item) => [
                'name' => $item->name,
                'total_quantity' => (int) $item->total_quantity,
                'total_revenue' => round((float) $item->total_revenue, 2),
            ])->toArray(),
            'currency' => 'PHP',
        ];
    }
}