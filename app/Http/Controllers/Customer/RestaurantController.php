<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = Restaurant::where('is_open', true)->withCount('menuItems');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('cuisine')) {
            $query->where('cuisine', $request->cuisine);
        }

        $restaurants = $query->get();

        $cuisines = Restaurant::where('is_open', true)
            ->whereNotNull('cuisine')
            ->distinct()
            ->orderBy('cuisine')
            ->pluck('cuisine');

        return view('customer.restaurants', compact('restaurants', 'cuisines'));
    }
public function randomMenuItems()
{
    $items = \App\Models\MenuItem::with('restaurant')
        ->where('is_available', true)
        ->whereHas('restaurant', function ($q) {
            $q->where('is_open', true);
        })
        ->inRandomOrder()
        ->limit(8)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => (float) $item->price,
                'image_url' => $item->image_url,
                'restaurant_name' => $item->restaurant->name ?? '',
                'restaurant_url' => $item->restaurant
                    ? route('customer.restaurants.show', $item->restaurant)
                    : '#',
            ];
        });

    return response()->json(['items' => $items]);
}
    public function show(Restaurant $restaurant)
    {
        // ⭐ Force fresh load mula DB
        $restaurant->refresh();

        $restaurant->load(['menuItems' => function ($q) {
            $q->where('is_available', true);
        }]);

        $reviews = $restaurant->reviews()
            ->with(['user', 'replies.user', 'votes'])
            ->orderByDesc('created_at')
            ->paginate(5);

        return view('customer.restaurant-show', compact('restaurant', 'reviews'));
    }
}