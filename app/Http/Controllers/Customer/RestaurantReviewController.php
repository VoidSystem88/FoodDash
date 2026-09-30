<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantReviewController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        $reviews = $restaurant->reviews()
            ->with(['user', 'replies.user'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'reviews' => $reviews,
        ]);
    }
}