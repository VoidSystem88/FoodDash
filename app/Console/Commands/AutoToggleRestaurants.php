<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoToggleRestaurants extends Command
{
    protected $signature = 'restaurants:auto-toggle';
    protected $description = 'Auto open/close restaurants based on their schedule';

    public function handle()
    {
        $this->info('Checking restaurant schedules...');

        $now = now();
        $today = $now->dayOfWeek; // 0=Sunday, 6=Saturday
        $currentTime = $now->format('H:i:s');

        $restaurants = Restaurant::with(['hours' => function ($q) use ($today) {
            $q->where('day_of_week', $today);
        }])->get();

        $updated = 0;

        foreach ($restaurants as $restaurant) {
            $todayHours = $restaurant->hours->first();

            if (!$todayHours || !$todayHours->is_open) {
                // Bukas ba dapat ngayon? Hindi — skip
                continue;
            }

            $shouldBeOpen = false;

            if ($todayHours->open_time && $todayHours->close_time) {
                $shouldBeOpen = $currentTime >= $todayHours->open_time
                             && $currentTime < $todayHours->close_time;
            }

            // Update kung naiiba
            if ($restaurant->is_open !== $shouldBeOpen) {
                // Huwag i-force close kung naka-open manually at nasa schedule pa naman
                $restaurant->update(['is_open' => $shouldBeOpen]);
                $updated++;

                $status = $shouldBeOpen ? 'OPENED' : 'CLOSED';
                $this->line("  #{$restaurant->id} {$restaurant->name} → {$status}");
            }
        }

        $this->info("✅ Updated {$updated} restaurant(s)");
    }
}