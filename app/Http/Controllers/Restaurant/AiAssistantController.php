<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Services\Ai\AnalyticsAssistant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AiAssistantController extends Controller
{
    protected AnalyticsAssistant $assistant;

    public function __construct(AnalyticsAssistant $assistant)
    {
        $this->assistant = $assistant;
    }

    /**
     * Handle AI chat request.
     */
    public function chat(Request $request)
    {
        // 1. Validate input
        $data = $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array|max:10',
        ]);

        $user = $request->user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json([
                'ok' => false,
                'message' => 'Restaurant not found.',
            ], 403);
        }

        // 2. Rate limiting — 20 messages/day per restaurant
        $rateKey = 'ai-chat:' . $restaurant->id;
        $dailyLimit = config('groq.daily_message_limit', 20);

        if (RateLimiter::tooManyAttempts($rateKey, $dailyLimit)) {
            $seconds = RateLimiter::availableIn($rateKey);
            $hours = ceil($seconds / 3600);

            return response()->json([
                'ok' => false,
                'message' => "Na-hit mo na yung daily limit ({$dailyLimit} messages). Subukan muli sa {$hours} oras.",
                'limit_reached' => true,
            ], 429);
        }

        // 3. Validate history structure
        $history = collect($data['history'] ?? [])
            ->filter(fn($m) => isset($m['role'], $m['content'])
                && in_array($m['role'], ['user', 'assistant']))
            ->take(-10)
            ->values()
            ->toArray();

        // 4. Call AI assistant
        try {
            $result = $this->assistant->ask($data['message'], $history);

            // 5. Hit the rate limiter
            RateLimiter::hit($rateKey, 86400); // 24 hours

            return response()->json([
                'ok' => true,
                'reply' => $result['reply'],
                'tool_calls' => $result['tool_calls'] ?? [],
                'remaining' => max(0, $dailyLimit - RateLimiter::attempts($rateKey)),
            ]);

        } catch (\Throwable $e) {
            Log::error('AI Assistant error', [
                'user_id' => $user->id,
                'restaurant_id' => $restaurant->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Paumanhin, may error sa AI. Subukan muli.',
            ], 500);
        }
    }

    /**
     * Get remaining messages today.
     */
    public function status(Request $request)
    {
        $restaurant = $request->user()->restaurant;

        if (!$restaurant) {
            return response()->json(['ok' => false], 403);
        }

        $rateKey = 'ai-chat:' . $restaurant->id;
        $dailyLimit = config('groq.daily_message_limit', 20);
        $used = RateLimiter::attempts($rateKey);

        return response()->json([
            'ok' => true,
            'limit' => $dailyLimit,
            'used' => $used,
            'remaining' => max(0, $dailyLimit - $used),
        ]);
    }
}