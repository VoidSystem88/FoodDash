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