<?php

namespace App\Services\Ai\Tools;

use App\Models\Restaurant;

class GetRestaurantSearch extends BaseTool
{
    public function name(): string { return 'search_restaurants'; }

    public function description(): string
    {
        return 'Search for restaurants by name, cuisine, or location. Use when the customer asks about restaurants, "what restaurants are available?", or wants to find a specific restaurant.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => [
                    'type' => 'string',
                    'description' => 'Search keyword (restaurant name, cuisine, or location).',
                ],
                'cuisine' => [
                    'type' => 'string',
                    'description' => 'Optional cuisine filter.',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Max results. Default 5, max 15.',
                ],
            ],
        ];
    }

    public function execute(array $args): array
    {
        $query = $args['query'] ?? null;
        $cuisine = $args['cuisine'] ?? null;
        $limit = min((int) ($args['limit'] ?? 5), 15);

        $builder = Restaurant::withCount('menuItems')
            ->where('is_open', true);

        if ($query) {
            $builder->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%")
                  ->orWhere('cuisine', 'like', "%{$query}%");
            });
        }

        if ($cuisine) {
            $builder->where('cuisine', 'like', "%{$cuisine}%");
        }

        $restaurants = $builder->limit($limit)->get()->map(fn($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'cuisine' => $r->cuisine,
            'address' => $r->address,
            'rating' => round((float) $r->rating_avg, 1),
            'rating_count' => $r->rating_count,
            'menu_items_count' => $r->menu_items_count,
        ])->toArray();

        return [
            'query' => $query,
            'cuisine' => $cuisine,
            'results' => $restaurants,
            'count' => count($restaurants),
        ];
    }
}