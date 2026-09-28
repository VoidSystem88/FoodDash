<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewReply;
use App\Notifications\ReviewRepliedNotification;
use Illuminate\Http\Request;

class ReviewReplyController extends Controller
{
    /**
     * Restaurant owner replies to a review.
     */
    public function store(Request $request, Review $review)
    {
        $restaurant = auth()->user()->restaurant;
        abort_unless($restaurant && $review->restaurant_id === $restaurant->id, 403);

        // Only 1 reply per review
        if ($review->replies()->exists()) {
            return back()->with('error', 'Naka-reply ka na sa review na ito.');
        }

        $data = $request->validate([
            'body' => 'required|string|min:5|max:1000',
        ]);

        $reply = ReviewReply::create([
            'review_id' => $review->id,
            'user_id' => auth()->id(),
            'body' => $data['body'],
        ]);

        // Send notification sa customer
        try {
            $customer = $review->user;

            if ($customer) {
                $customer->notify(new ReviewRepliedNotification($reply));
            }
        } catch (\Throwable $e) {
            \Log::error('Failed to send review reply notification', [
                'reply_id' => $reply->id,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', 'Reply posted! Customer has been notified.');
    }
        /**
     * Show all reviews for this restaurant.
     */
    public function index(Request $request)
    {
        $restaurant = auth()->user()->restaurant;

        $query = Review::with(['user', 'order', 'replies.user'])
            ->where('restaurant_id', $restaurant->id)
            ->published();

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->rating);
        }

        // Filter by reply status
        if ($request->filled('status')) {
            if ($request->status === 'replied') {
                $query->whereHas('replies');
            } elseif ($request->status === 'unreplied') {
                $query->whereDoesntHave('replies');
            }
        }

        $reviews = $query->mostRecent()->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total' => Review::where('restaurant_id', $restaurant->id)->published()->count(),
            'avg' => round(Review::where('restaurant_id', $restaurant->id)->published()->avg('rating') ?? 0, 1),
            'replied' => Review::where('restaurant_id', $restaurant->id)->published()->whereHas('replies')->count(),
            'unreplied' => Review::where('restaurant_id', $restaurant->id)->published()->whereDoesntHave('replies')->count(),
        ];

        return view('restaurant.reviews.index', compact('restaurant', 'reviews', 'stats'));
    }
}