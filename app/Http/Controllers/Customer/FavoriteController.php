<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Favorites page — list ng lahat ng favorite restaurants.
     */
    public function index()
    {
        $favorites = auth()->user()
            ->favoriteRestaurants()
            ->where('is_open', true)
            ->withCount('menuItems')
            ->latest('favorites.created_at')
            ->get();

        return view('customer.favorites', compact('favorites'));
    }

    /**
     * Toggle favorite (AJAX).
     */
    public function toggle(Request $request, Restaurant $restaurant)
    {
        $user = auth()->user();

        $existing = Favorite::where('user_id', $user->id)
            ->where('restaurant_id', $restaurant->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $isFavorite = false;
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'restaurant_id' => $restaurant->id,
            ]);
            $isFavorite = true;
        }

        return response()->json([
            'ok' => true,
            'is_favorite' => $isFavorite,
            'count' => $user->favorites()->count(),
        ]);
    }
}