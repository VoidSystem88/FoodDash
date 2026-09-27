<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'restaurant', 'order']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('flagged')) {
            $query->where('report_count', '>=', 5);
        }

        $reviews = $query->latest()->paginate(30)->withQueryString();

        $counts = [
            'all' => Review::count(),
            'published' => Review::where('status', 'published')->count(),
            'hidden' => Review::where('status', 'hidden')->count(),
            'flagged' => Review::where('status', 'flagged')->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'counts'));
    }

    public function hide(Review $review)
    {
        $review->update(['status' => 'hidden']);
        $review->restaurant->updateRatingStats();

        return back()->with('success', 'Review hidden.');
    }

    public function publish(Review $review)
    {
        $review->update(['status' => 'published']);
        $review->restaurant->updateRatingStats();

        return back()->with('success', 'Review published.');
    }

    public function destroy(Review $review)
    {
        $restaurant = $review->restaurant;

        // Delete images
        if (!empty($review->images)) {
            foreach ($review->images as $path) {
                \Storage::disk('public')->delete($path);
            }
        }

        $review->forceDelete();
        $restaurant->updateRatingStats();

        return back()->with('success', 'Review deleted permanently.');
    }
}