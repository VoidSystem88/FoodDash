<?php

namespace App\Services\Ai\Tools;

use App\Models\MenuItem;

class GetRecommendedFoods extends BaseTool
{
    public function name(): string { return 'get_recommended_foods'; }

    public function description(): string
    {
        return 'Get popular food recommendations from open restaurants. Use when the customer asks "what should I eat?", "recommend something", or wants suggestions.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'cuisine' => [
                    'type' => 'string',
                    'description' => 'Optional cuisine filter.',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Number of recommendations. Default 5, max 10.',
                ],
            ],
        ];
    }

    public function execute(array $args): array
    {
        $cuisine = $args['cuisine'] ?? null;
        $limit = min((int) ($args['limit'] ?? 5), 10);

        $items = MenuItem::with('restaurant:id,name,cuisine,is_open')
            ->where('is_available', true)
            ->whereHas('restaurant', function ($q) use ($cuisine) {
                $q->where('is_open', true);
                if ($cuisine) {
                    $q->where('cuisine', 'like', "%{$cuisine}%");
                }
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'name' => $item->name,
                'description' => $item->description,
                'price' => round((float) $item->price, 2),
                'restaurant' => $item->restaurant?->name ?? 'Unknown',
                'cuisine' => $item->restaurant?->cuisine,
            ])
            ->toArray();

        return [
            'cuisine_filter' => $cuisine,
            'recommendations' => $items,
            'count' => count($items),
        ];
    }
}