<?php

namespace App\Services\Ai\Tools;

use App\Models\MenuItem;

class GetMenuItems extends BaseTool
{
    public function name(): string { return 'get_menu_items'; }

    public function description(): string
    {
        return 'Get menu items from restaurants. Use when the customer asks about specific foods, "what menu items are available?", "anong pagkain meron?", or wants to see food options. Can filter by restaurant name or cuisine.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'restaurant_name' => [
                    'type' => 'string',
                    'description' => 'Optional restaurant name to filter menu items.',
                ],
                'cuisine' => [
                    'type' => 'string',
                    'description' => 'Optional cuisine filter (e.g. "Pizza", "Japanese", "Filipino").',
                ],
                'exclude_keyword' => [
                    'type' => 'string',
                    'description' => 'Optional keyword to exclude from results (e.g. "pizza" if user wants non-pizza items).',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Max results. Default 10, max 20.',
                ],
            ],
        ];
    }

    public function execute(array $args): array
    {
        $restaurantName = $args['restaurant_name'] ?? null;
        $cuisine = $args['cuisine'] ?? null;
        $excludeKeyword = $args['exclude_keyword'] ?? null;
        $limit = min((int) ($args['limit'] ?? 10), 20);

        $builder = MenuItem::with('restaurant:id,name,cuisine,is_open')
            ->where('is_available', true)
            ->whereHas('restaurant', function ($q) use ($restaurantName, $cuisine) {
                $q->where('is_open', true);

                if ($restaurantName) {
                    $q->where('name', 'like', "%{$restaurantName}%");
                }

                if ($cuisine) {
                    $q->where('cuisine', 'like', "%{$cuisine}%");
                }
            });

        if ($excludeKeyword) {
            $builder->where('name', 'not like', "%{$excludeKeyword}%");
        }

        $items = $builder->limit($limit)->get()->map(fn($item) => [
            'name' => $item->name,
            'description' => $item->description,
            'price' => round((float) $item->price, 2),
            'restaurant' => $item->restaurant?->name ?? 'Unknown',
            'cuisine' => $item->restaurant?->cuisine,
        ])->toArray();

        return [
            'filters' => [
                'restaurant_name' => $restaurantName,
                'cuisine' => $cuisine,
                'exclude_keyword' => $excludeKeyword,
            ],
            'menu_items' => $items,
            'count' => count($items),
        ];
    }
}