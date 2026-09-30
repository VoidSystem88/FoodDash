<?php

namespace App\Services\Ai\Tools;

use App\Models\Order;

class GetSalesStats extends BaseTool
{
    public function name(): string
    {
        return 'get_sales_stats';
    }

    public function description(): string
    {
        return 'Get sales statistics for a given period. Returns total sales, total orders, average order value, total commission, and restaurant earnings. Use this when the user asks about sales, revenue, income, or earnings for a specific time period.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'period' => [
                    'type' => 'string',
                    'enum' => $this->periodEnum(),
                    'description' => 'The time period for the statistics. Default to "this_month" if not specified.',
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
        [$from, $to] = $this->getDateRange($period);

        $orders = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$from, $to]);

        $totalOrders = (clone $orders)->count();
        $totalSales = (clone $orders)->sum('food_cost');
        $totalCommission = (clone $orders)->sum('commission_amount');
        $restaurantEarnings = (clone $orders)->sum('restaurant_earnings');
        $totalDeliveryFees = (clone $orders)->sum('delivery_fee');

        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        return [
            'period' => $period,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'total_orders' => $totalOrders,
            'total_sales' => round((float) $totalSales, 2),
            'total_commission' => round((float) $totalCommission, 2),
            'restaurant_earnings' => round((float) $restaurantEarnings, 2),
            'total_delivery_fees' => round((float) $totalDeliveryFees, 2),
            'avg_order_value' => round((float) $avgOrderValue, 2),
            'currency' => 'PHP',
        ];
    }
}