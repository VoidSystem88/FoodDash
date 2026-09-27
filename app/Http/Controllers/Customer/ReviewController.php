<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\ReviewReport;
use App\Models\ReviewVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order)
    {
        abort_unless($order->customer_id === auth()->id(), 403);
        abort_unless($order->status === 'delivered', 422, 'You can only review delivered orders.');

        if ($order->review()->exists()) {
            return back()->with('error', 'You have already reviewed this order.');
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:100',
            'body' => 'required|string|min:10|max:2000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            $dir = storage_path('app/public/reviews');
            if (!file_exists($dir)) mkdir($dir, 0755, true);

            foreach ($request->file('images') as $image) {
                $filename = 'reviews/' . uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move($dir, basename($filename));
                $imagePaths[] = $filename;
            }
        }

        DB::transaction(function () use ($order, $data, $imagePaths) {
            Review::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'restaurant_id' => $order->restaurant_id,
                'rating' => $data['rating'],
                'title' => $data['title'] ?? null,
                'body' => $data['body'],
                'images' => $imagePaths,
                'is_verified_purchase' => true,
                'status' => 'published',
            ]);
        });

        // ⭐ RECALCULATE RATING STATS
        $restaurant = Restaurant::find($order->restaurant_id);
        if ($restaurant) {
            $restaurant->updateRatingStats();
        }

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', 'Salamat sa review mo! 🌟');
    }

    public function update(Request $request, Review $review)
    {
        abort_unless($review->user_id === auth()->id(), 403);

        if (!$review->is_editable) {
            throw ValidationException::withMessages(['review' => 'Edit window expired.']);
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:100',
            'body' => 'required|string|min:10|max:2000',
        ]);

        $review->update([
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'],
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        $review->restaurant->updateRatingStats();

        return back()->with('success', 'Review updated!');
    }

    public function destroy(Review $review)
    {
        abort_unless($review->user_id === auth()->id(), 403);

        if (!empty($review->images)) {
            foreach ($review->images as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $restaurant = $review->restaurant;
        $review->delete();
        $restaurant->updateRatingStats();

        return back()->with('success', 'Review deleted.');
    }

    // ⭐ CORE: VOTE method
    public function vote(Request $request, Review $review)
    {
        // Bawal i-vote ang sariling review
        if ($review->user_id === auth()->id()) {
            return response()->json([
                'ok' => false,
                'message' => 'Cannot vote on your own review.',
            ], 422);
        }

        $data = $request->validate([
            'vote_type' => 'required|in:helpful,not_helpful',
        ]);

        $existing = ReviewVote::where('review_id', $review->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            if ($existing->vote_type === $data['vote_type']) {
                // Toggle off
                $existing->delete();
                $userVote = null;
            } else {
                // Switch vote
                $existing->update(['vote_type' => $data['vote_type']]);
                $userVote = $data['vote_type'];
            }
        } else {
            ReviewVote::create([
                'review_id' => $review->id,
                'user_id' => auth()->id(),
                'vote_type' => $data['vote_type'],
            ]);
            $userVote = $data['vote_type'];
        }

        $review->updateVoteCounts();
        $review->refresh();

        return response()->json([
            'ok' => true,
            'helpful' => (int) $review->helpful_count,
            'not_helpful' => (int) $review->not_helpful_count,
            'user_vote' => $userVote,
        ]);
    }

    public function report(Request $request, Review $review)
    {
        if ($review->user_id === auth()->id()) {
            return response()->json(['ok' => false, 'message' => 'Cannot report your own review.'], 422);
        }

        if ($review->hasUserReported()) {
            return response()->json(['ok' => false, 'message' => 'Naka-report ka na.'], 422);
        }

        $data = $request->validate([
            'reason' => 'required|in:spam,offensive,fake,irrelevant,other',
            'details' => 'nullable|string|max:500',
        ]);

        ReviewReport::create([
            'review_id' => $review->id,
            'user_id' => auth()->id(),
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
        ]);

        $review->incrementReportCount();

        return response()->json(['ok' => true, 'message' => 'Salamat sa report!']);
    }
}