<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Groq API Key
    |--------------------------------------------------------------------------
    |
    | Ang API key mo mula sa console.groq.com/keys.
    | Nasa .env file ito, hindi sa code.
    |
    */

    'api_key' => env('GROQ_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | Groq's OpenAI-compatible endpoint.
    |
    */

    'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),

    /*
    |--------------------------------------------------------------------------
    | Default Model
    |--------------------------------------------------------------------------
    |
    | Available models:
    | - openai/gpt-oss-120b   (best for tool calling, recommended)
    | - openai/gpt-oss-20b    (cheaper, still good)
    | - qwen/qwen3.6-27b      (multimodal, has vision)
    |
    */

    'model' => env('GROQ_MODEL', 'openai/gpt-oss-120b'),

    /*
    |--------------------------------------------------------------------------
    | Model Parameters
    |--------------------------------------------------------------------------
    */

    'max_tokens' => (int) env('GROQ_MAX_TOKENS', 2048),

    'temperature' => (float) env('GROQ_TEMPERATURE', 0.3),

    /*
    |--------------------------------------------------------------------------
    | Timeout (seconds)
    |--------------------------------------------------------------------------
    |
    | Gaano katagal maghintay bago mag-timeout ang HTTP request.
    | Analytics queries ay usually mabilis, pero bigyan natin ng buffer.
    |
    */

    'timeout' => (int) env('GROQ_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Max messages per restaurant per day.
    |
    */

    'daily_message_limit' => (int) env('GROQ_DAILY_LIMIT', 20),

];