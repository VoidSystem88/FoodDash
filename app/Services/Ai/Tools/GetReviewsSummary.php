<?php

namespace App\Services\Ai\Tools;

use App\Models\Review;

class GetReviewsSummary extends BaseTool
{
    public function name(): string
    {
        return 'get_reviews_summary';
    }

    public function description(): string
    {
        return 'Get review statistics and recent reviews for a given period. Returns total reviews, average rating, rating breakdown (5/4/3/2/1 stars), and the most recent reviews. Use this when the user asks about customer feedback, ratings, reviews, or satisfaction.';
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
                'recent_limit' => [
                    'type' => 'integer',
                    'description' => 'Number of recent reviews to include. Default 3, max 10.',
                    'minimum' => 1,
                    'maximum' => 10,
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
        $limit = min((int) ($args['recent_limit'] ?? 3), 10);
        [$from, $to] = $this->getDateRange($period);

        $baseQuery = Review::where('restaurant_id', $restaurant->id)
            ->where('status', 'published')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$from, $to]);

        $totalReviews = (clone $baseQuery)->count();
        $avgRating = (clone $baseQuery)->avg('rating');

        // Rating breakdown
        $breakdown = [];
        foreach ([5, 4, 3, 2, 1] as $stars) {
            $count = (clone $baseQuery)->where('rating', $stars)->count();
            $breakdown[$stars] = [
                'count' => $count,
                'percentage' => $totalReviews > 0 ? round(($count / $totalReviews) * 100, 1) : 0,
            ];
        }

        // Recent reviews
        $recentReviews = (clone $baseQuery)
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn($r) => [
                'rating' => $r->rating,
                'title' => $r->title,
                'body' => \Illuminate\Support\Str::limit($r->body, 200),
                'customer_name' => $r->user?->name ?? 'Anonymous',
                'created_at' => $r->created_at->format('Y-m-d'),
            ])
            ->toArray();

        return [
            'period' => $period,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'total_reviews' => $totalReviews,
            'avg_rating' => $avgRating ? round((float) $avgRating, 2) : null,
            'rating_breakdown' => $breakdown,
            'recent_reviews' => $recentReviews,
        ];
    }
}