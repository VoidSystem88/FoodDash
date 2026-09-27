<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;

class RecalculateRestaurantRatings extends Command
{
    protected $signature = 'restaurants:recalculate-ratings';
    protected $description = 'Recalculate rating_avg and rating_count for all restaurants';

    public function handle()
    {
        $this->info('Recalculating restaurant ratings...');

        $restaurants = Restaurant::all();
        $updated = 0;

        foreach ($restaurants as $restaurant) {
            $publishedReviews = $restaurant->reviews()->published();

            $newAvg = round($publishedReviews->avg('rating') ?? 0, 2);
            $newCount = $publishedReviews->count();

            $oldAvg = $restaurant->rating_avg;
            $oldCount = $restaurant->rating_count;

            if ($oldAvg != $newAvg || $oldCount != $newCount) {
                $restaurant->update([
                    'rating_avg' => $newAvg,
                    'rating_count' => $newCount,
                ]);

                $this->line("  #{$restaurant->id} {$restaurant->name}: {$oldAvg} ({$oldCount}) → {$newAvg} ({$newCount})");
                $updated++;
            }
        }

        $this->info("✅ Updated {$updated} restaurant(s)");
    }
}