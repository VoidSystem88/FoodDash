<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\FavoriteFood;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class FavoriteFoodController extends Controller
{
    public function toggle(Request $request, MenuItem $menuItem)
    {
        $user = auth()->user();

        $existing = FavoriteFood::where('user_id', $user->id)
            ->where('menu_item_id', $menuItem->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $isFavorite = false;
        } else {
            FavoriteFood::create([
                'user_id' => $user->id,
                'menu_item_id' => $menuItem->id,
            ]);
            $isFavorite = true;
        }

        return response()->json([
            'ok' => true,
            'is_favorite' => $isFavorite,
        ]);
    }
}