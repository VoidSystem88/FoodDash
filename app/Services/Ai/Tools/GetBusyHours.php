<?php

namespace App\Services\Ai\Tools;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class GetBusyHours extends BaseTool
{
    public function name(): string
    {
        return 'get_busy_hours';
    }

    public function description(): string
    {
        return 'Get peak/busy hours for the restaurant based on order count per hour of the day. Returns a list of hours (0-23) with order counts, ordered by hour ascending. Use this when the user asks about peak hours, busiest time, or best time to prepare staff.';
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

        $rows = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$from, $to])
            ->select(
                DB::raw('HOUR(created_at) as hour_of_day'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('hour_of_day')
            ->orderBy('hour_of_day')
            ->get();

        $hours = collect(range(0, 23))->map(function ($hour) use ($rows) {
            $row = $rows->firstWhere('hour_of_day', $hour);
            return [
                'hour' => $hour,
                'label' => sprintf('%02d:00', $hour),
                'order_count' => $row ? (int) $row->order_count : 0,
            ];
        })->toArray();

        // Find peak hour
        $peakHour = collect($hours)->sortByDesc('order_count')->first();

        return [
            'period' => $period,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'peak_hour' => $peakHour,
            'hours' => $hours,
        ];
    }
}