<?php

namespace App\Services\Ai;

use App\Services\Ai\Tools\GetBusyHours;
use App\Services\Ai\Tools\GetOrdersCount;
use App\Services\Ai\Tools\GetRejectedOrders;
use App\Services\Ai\Tools\GetReviewsSummary;
use App\Services\Ai\Tools\GetSalesStats;
use App\Services\Ai\Tools\GetTopItems;
use Illuminate\Support\Facades\Log;

class AnalyticsAssistant
{
    protected GroqClient $client;
    protected array $tools;
    protected int $maxIterations = 5;

    public function __construct(GroqClient $client)
    {
        $this->client = $client;

        // Register all tools
        $this->tools = [
            new GetSalesStats(),
            new GetTopItems(),
            new GetBusyHours(),
            new GetReviewsSummary(),
            new GetRejectedOrders(),
            new GetOrdersCount(),
        ];
    }

    /**
     * Main entry point: ask the AI a question and return the response.
     *
     * @param string $userMessage  The user's question
     * @param array  $history      Previous messages (optional)
     * @return array               ['reply' => string, 'tool_calls' => array, 'usage' => array]
     */
    public function ask(string $userMessage, array $history = []): array
    {
        $restaurant = $this->getRestaurant();
        if (!$restaurant) {
            return ['reply' => 'Restaurant not found. Please log in as a restaurant owner.'];
        }

        // Build system prompt
        $systemPrompt = $this->buildSystemPrompt($restaurant);

        // Build initial messages
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Add history (last 10 messages to save tokens)
        $history = array_slice($history, -10);
        foreach ($history as $msg) {
            $messages[] = $msg;
        }

        // Add current user message
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        // Convert tools to Groq format
        $toolsArray = array_map(fn($t) => $t->toArray(), $this->tools);

        $executedToolCalls = [];
        $usage = [];

        // Loop: AI decides → tools execute → AI responds
        for ($i = 0; $i < $this->maxIterations; $i++) {
            $response = $this->client->chat($messages, $toolsArray);

            // Track usage
            if (isset($response['usage'])) {
                $usage[] = $response['usage'];
            }

            $assistantMessage = $this->client->getMessage($response);

            // If no tool calls, this is the final answer
            if (empty($assistantMessage['tool_calls'])) {
                return [
                    'reply' => $assistantMessage['content'] ?? 'Sorry, I could not generate a response.',
                    'tool_calls' => $executedToolCalls,
                    'usage' => $usage,
                ];
            }

            // Add assistant message to conversation
            $messages[] = $assistantMessage;

            // Execute each tool call
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
                            'result_summary' => $this->summarizeResult($result),
                        ];
                    } catch (\Throwable $e) {
                        Log::error("Tool execution failed: {$toolName}", [
                            'error' => $e->getMessage(),
                            'arguments' => $arguments,
                        ]);
                        $result = ['error' => $e->getMessage()];
                    }
                }

                // Add tool result as a message
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
            'usage' => $usage,
        ];
    }

    /**
     * Build the system prompt for the AI.
     */
    protected function buildSystemPrompt($restaurant): string
    {
        $name = $restaurant->name;
        $today = now()->format('l, F j, Y');

        return <<<PROMPT
You are a helpful analytics assistant for a restaurant owner named "{$name}" on FoodDash (a food delivery platform in the Philippines).

Today's date: {$today}

Your job is to help the restaurant owner understand their business performance by answering questions about sales, orders, customers, and reviews.

Guidelines:
- Always use the available tools to fetch real data. Never make up numbers.
- Be concise and friendly. Answer in a mix of English and Tagalog if the user writes in Tagalog.
- When presenting numbers, use ₱ for Philippine Peso (e.g., ₱1,234.56).
- If a question requires multiple tools, call them in sequence or in parallel.
- If the user asks something outside your scope (e.g., personal advice), politely redirect.
- If data is zero or empty, say so honestly instead of making up data.
- Use the correct period (today, yesterday, this_week, last_week, this_month, last_month, this_year, all_time) based on what the user asks.
- If unsure about the period, default to "this_month".
- When summarizing, focus on insights: trends, comparisons, anomalies.

Format your responses clearly with numbers and short bullet points when appropriate.
PROMPT;
    }

    /**
     * Find a tool by name.
     */
    protected function findTool(string $name): ?object
    {
        foreach ($this->tools as $tool) {
            if ($tool->name() === $name) {
                return $tool;
            }
        }
        return null;
    }

    /**
     * Summarize a tool result for logging.
     */
    protected function summarizeResult(array $result): string
    {
        if (isset($result['error'])) {
            return 'ERROR: ' . $result['error'];
        }

        $keys = array_keys($result);
        return 'keys: ' . implode(', ', array_slice($keys, 0, 6));
    }

    /**
     * Get current restaurant from auth.
     */
    protected function getRestaurant()
    {
        return auth()->user()?->restaurant;
    }
}