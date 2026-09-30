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

    public function chat(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array|max:10',
        ]);

        $user = $request->user();

        // Customer only (defense-in-depth)
        if (!$user->isCustomer()) {
            return response()->json([
                'ok' => false,
                'message' => 'The AI Assistant is available to customers only.',
                'customer_only' => true,
            ], 403);
        }

        $rateKey = 'ai-chat:' . $user->id;
        $dailyLimit = config('groq.daily_message_limit', 20);

        if (RateLimiter::tooManyAttempts($rateKey, $dailyLimit)) {
            $seconds = RateLimiter::availableIn($rateKey);
            $hours = ceil($seconds / 3600);

            return response()->json([
                'ok' => false,
                'message' => "You've reached your daily limit ({$dailyLimit} messages). Try again in {$hours} hours.",
                'limit_reached' => true,
            ]);
        }

        $history = collect($data['history'] ?? [])
            ->filter(fn($m) => isset($m['role'], $m['content'])
                && in_array($m['role'], ['user', 'assistant']))
            ->take(-10)
            ->values()
            ->toArray();

        try {
            $result = $this->assistant->ask($data['message'], $history);

            RateLimiter::hit($rateKey, 86400);

            return response()->json([
                'ok' => true,
                'reply' => $result['reply'],
                'tool_calls' => $result['tool_calls'] ?? [],
                'remaining' => max(0, $dailyLimit - RateLimiter::attempts($rateKey)),
            ]);

        } catch (\Throwable $e) {
            Log::error('AI Assistant error', [
                'user_id' => $user->id,
                'role' => $user->role,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Sorry, there was an error. Please try again.',
            ]);
        }
    }

    public function status(Request $request)
    {
        $user = $request->user();

        if (!$user->isCustomer()) {
            return response()->json([
                'ok' => false,
                'customer_only' => true,
                'remaining' => 0,
            ], 403);
        }

        $rateKey = 'ai-chat:' . $user->id;
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