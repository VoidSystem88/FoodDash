<?php

namespace App\Services\Ai\Tools;

use App\Models\Order;

class GetCustomerOrders extends BaseTool
{
    public function name(): string { return 'get_customer_orders'; }

    public function description(): string
    {
        return 'Get the customer\'s recent orders with status, total, and restaurant name. Use when the customer asks about their orders, order history, or order status.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'period' => [
                    'type' => 'string',
                    'enum' => $this->periodEnum(),
                    'description' => 'Time period. Default "this_month".',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Max orders. Default 5, max 20.',
                ],
            ],
            'required' => ['period'],
        ];
    }

    public function execute(array $args): array
    {
        $user = auth()->user();
        $period = $args['period'] ?? 'this_month';
        $limit = min((int) ($args['limit'] ?? 5), 20);
        [$from, $to] = $this->getDateRange($period);

        $base = Order::where('customer_id', $user->id)
            ->whereBetween('created_at', [$from, $to]);

        $totalOrders = (clone $base)->count();
        $deliveredOrders = (clone $base)->where('status', 'delivered')->count();

        $orders = (clone $base)
            ->with('restaurant:id,name')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn($o) => [
                'order_id' => $o->id,
                'restaurant' => $o->restaurant?->name ?? 'Unknown',
                'status' => $o->status,
                'total_amount' => round((float) $o->total_amount, 2),
                'created_at' => $o->created_at->format('M d, Y g:i A'),
            ])
            ->toArray();

        return [
            'period' => $period,
            'total_orders' => $totalOrders,
            'delivered_orders' => $deliveredOrders,
            'recent_orders' => $orders,
        ];
    }
}