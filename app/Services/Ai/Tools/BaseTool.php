<?php

namespace App\Services\Ai\Tools;

use App\Models\Restaurant;
use Carbon\Carbon;

abstract class BaseTool
{
    /**
     * Tool name — ito yung ipapakita sa AI.
     */
    abstract public function name(): string;

    /**
     * Tool description — ipapaliwanag sa AI kung ano ginagawa ng tool.
     */
    abstract public function description(): string;

    /**
     * Tool parameters — JSON schema para sa parameters.
     */
    abstract public function parameters(): array;

    /**
     * Execute the tool with given arguments.
     */
    abstract public function execute(array $args): array;

    /**
     * Return tool definition in OpenAI/Groq format.
     */
    public function toArray(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name(),
                'description' => $this->description(),
                'parameters' => $this->parameters(),
            ],
        ];
    }

    /**
     * Helper: Get date range from period string.
     */
    protected function getDateRange(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'today'      => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday'  => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'this_week'  => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'last_week'  => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            'this_year'  => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'all_time'   => [Carbon::create(2000, 1, 1), $now->copy()->endOfDay()],
            default      => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
        };
    }

    /**
     * Helper: Get current restaurant (from auth).
     */
    protected function getRestaurant(): ?Restaurant
    {
        return auth()->user()?->restaurant;
    }

    /**
     * Helper: Get period values for schema.
     */
    protected function periodEnum(): array
    {
        return [
            'today',
            'yesterday',
            'this_week',
            'last_week',
            'this_month',
            'last_month',
            'this_year',
            'all_time',
        ];
    }
}