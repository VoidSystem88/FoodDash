<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqClient
{
    /**
     * Send a chat completion request to Groq.
     *
     * @param array $messages   Array of message objects: [['role' => 'user', 'content' => '...']]
     * @param array $tools      Optional array of tool definitions (function calling)
     * @param string|null $model  Override default model
     * @return array            Full API response as array
     * @throws \Exception       If API call fails
     */
    public function chat(array $messages, array $tools = [], ?string $model = null): array
    {
        $apiKey = config('groq.api_key');

        if (empty($apiKey)) {
            throw new \Exception('Groq API key is not configured. Check your .env file.');
        }

        $payload = [
            'model' => $model ?? config('groq.model'),
            'messages' => $messages,
            'max_tokens' => config('groq.max_tokens'),
            'temperature' => config('groq.temperature'),
        ];

        // Add tools if provided
        if (!empty($tools)) {
            $payload['tools'] = $tools;
            $payload['tool_choice'] = 'auto';
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(config('groq.timeout'))
                ->acceptJson()
                ->post(config('groq.base_url') . '/chat/completions', $payload);

            if ($response->failed()) {
                Log::error('Groq API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception(
                    'Groq API request failed: ' . $response->status() . ' — ' . $response->body()
                );
            }

            return $response->json();

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Groq connection timeout', ['error' => $e->getMessage()]);
            throw new \Exception('Connection to Groq timed out. Please try again.');

        } catch (\Throwable $e) {
            Log::error('Groq client error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Helper: Extract just the assistant's message from response.
     */
    public function getMessage(array $response): array
    {
        return $response['choices'][0]['message'] ?? [];
    }

    /**
     * Helper: Extract the content (text) from response.
     */
    public function getContent(array $response): string
    {
        return $this->getMessage($response)['content'] ?? '';
    }

    /**
     * Helper: Check kung may tool calls ang response.
     */
    public function hasToolCalls(array $response): bool
    {
        $message = $this->getMessage($response);
        return !empty($message['tool_calls']);
    }

    /**
     * Helper: Extract tool calls from response.
     */
    public function getToolCalls(array $response): array
    {
        return $this->getMessage($response)['tool_calls'] ?? [];
    }
}