<?php

namespace App\Services\Ai;

use App\Services\Ai\Tools\GetCustomerOrders;
use App\Services\Ai\Tools\GetFavoriteRestaurants;
use App\Services\Ai\Tools\GetMenuItems;
use App\Services\Ai\Tools\GetRecommendedFoods;
use App\Services\Ai\Tools\GetRestaurantSearch;
use Illuminate\Support\Facades\Log;

class AnalyticsAssistant
{
    protected GroqClient $client;
    protected array $tools;
    protected int $maxIterations = 5;

    public function __construct(GroqClient $client)
    {
        $this->client = $client;

        // Customer-focused tools
        $this->tools = [
            new GetRestaurantSearch(),
            new GetMenuItems(),
            new GetRecommendedFoods(),
            new GetCustomerOrders(),
            new GetFavoriteRestaurants(),
        ];
    }

    public function ask(string $userMessage, array $history = []): array
    {
        $user = auth()->user();
        if (!$user) {
            return ['reply' => 'You must be logged in to use the assistant.'];
        }

        if (!$user->isCustomer()) {
            return [
                'reply' => 'I am a FoodDash customer assistant only. I cannot help with your request.',
                'tool_calls' => [],
            ];
        }

        $systemPrompt = $this->buildSystemPrompt($user);

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach (array_slice($history, -10) as $msg) {
            $messages[] = $msg;
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $toolsArray = array_map(fn($t) => $t->toArray(), $this->tools);
        $executedToolCalls = [];

        for ($i = 0; $i < $this->maxIterations; $i++) {
            $response = $this->client->chat($messages, $toolsArray);
            $assistantMessage = $this->client->getMessage($response);

            if (empty($assistantMessage['tool_calls'])) {
                return [
                    'reply' => $assistantMessage['content'] ?? 'Sorry, I could not generate a response.',
                    'tool_calls' => $executedToolCalls,
                ];
            }

            $messages[] = $assistantMessage;

            foreach ($assistantMessage['tool_calls'] as $toolCall) {
                $toolName = $toolCall['function']['name'];
                $arguments = json_decode($toolCall['function']['arguments'] ?? '{}', true);

                $tool = $this->findTool($toolName);
                if (!$tool) {
                    $result = ['error' => "Tool {$toolName} not found."];
                } else {
                    try {
                        $result = $tool->execute($arguments);
                        $executedToolCalls[] = [
                            'name' => $toolName,
                            'arguments' => $arguments,
                        ];
                    } catch (\Throwable $e) {
                        Log::error("Tool execution failed: {$toolName}", [
                            'error' => $e->getMessage(),
                        ]);
                        $result = ['error' => $e->getMessage()];
                    }
                }

                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $toolCall['id'],
                    'content' => json_encode($result, JSON_UNESCAPED_UNICODE),
                ];
            }
        }

        return [
            'reply' => 'Sorry, I could not complete your request. Please try a simpler question.',
            'tool_calls' => $executedToolCalls,
        ];
    }

    protected function buildSystemPrompt($user): string
    {
        $name = $user->name;
        $today = now()->format('l, F j, Y');

        return <<<PROMPT
You are Dash, a friendly AI assistant for FoodDash — a food delivery platform in the Philippines.

You are speaking with customer: {$name}
Today's date: {$today}

CRITICAL RULE — NEVER HALLUCINATE:
You MUST use the available tools to answer ANY question about restaurants, menu items, orders, or favorites.
NEVER make up restaurant names, menu items, prices, or any data.
If a tool returns no results, say so honestly: "I couldn't find any restaurants matching your request."

Your job is to help customers with:
- Finding restaurants (by name, cuisine, or location)
- Discovering menu items and food options
- Getting food recommendations
- Checking their order history
- Viewing their favorites

Available tools you MUST use:
- search_restaurants: Search restaurants by name/cuisine/location
- get_menu_items: Get menu items (supports filtering by restaurant, cuisine, and EXCLUDING keywords)
- get_recommended_foods: Get popular food picks from open restaurants
- get_customer_orders: Get the customer's order history
- get_favorite_restaurants: Get the customer's favorites

EXAMPLES of correct tool usage:
- "What restaurants are open?" → call search_restaurants
- "Anong ibang menu bukod sa pizza?" → call get_menu_items with exclude_keyword="pizza"
- "What should I eat?" → call get_recommended_foods
- "Show me my orders" → call get_customer_orders
- "What are my favorites?" → call get_favorite_restaurants

IMPORTANT RULES:
1. ALWAYS use tools for restaurant/menu/order questions. Never make up data.
2. Answer in ENGLISH only. Do not use Tagalog or Filipino.
3. Be warm, friendly, and concise.
4. Use ₱ for Philippine Peso (e.g., ₱250).
5. When listing items, use clean bullet points.
6. If a tool returns no results, say so politely.
7. If asked about non-customer topics (restaurant analytics, rider earnings, admin tasks), politely redirect: "I'm a customer assistant only. For that, please contact the appropriate department."
8. NEVER invent menu items, restaurants, or prices.

You represent FoodDash — be a helpful brand ambassador for customers!
PROMPT;
    }

    protected function findTool(string $name): ?object
    {
        foreach ($this->tools as $tool) {
            if ($tool->name() === $name) {
                return $tool;
            }
        }
        return null;
    }
}