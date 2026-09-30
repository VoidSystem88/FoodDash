<?php

namespace App\Services\Ai\Tools;

class GetFavoriteRestaurants extends BaseTool
{
    public function name(): string { return 'get_favorite_restaurants'; }

    public function description(): string
    {
        return 'Get the customer\'s favorite restaurants and favorite foods. Use when asked about favorites or saved items.';
    }

    public function parameters(): array
    {
        return ['type' => 'object', 'properties' => (object) []];
    }

    public function execute(array $args): array
    {
        $user = auth()->user();

        $restaurants = $user->favoriteRestaurants()
            ->select('restaurants.id', 'restaurants.name', 'restaurants.cuisine', 'restaurants.address')
            ->limit(10)
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'cuisine' => $r->cuisine,
                'address' => $r->address,
            ])
            ->toArray();

        $foods = $user->favoriteFoods()
            ->with('restaurant:id,name')
            ->limit(10)
            ->get()
            ->map(fn($f) => [
                'name' => $f->name,
                'price' => round((float) $f->price, 2),
                'restaurant' => $f->restaurant?->name ?? 'Unknown',
            ])
            ->toArray();

        return [
            'favorite_restaurants' => $restaurants,
            'favorite_foods' => $foods,
            'count_restaurants' => count($restaurants),
            'count_foods' => count($foods),
        ];
    }
}