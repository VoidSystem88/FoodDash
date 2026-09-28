<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'badge',
        'cuisine',
        'cover_image',
        'profile_image',
        'latitude',
        'longitude',
        'is_open',
        'prep_time_minutes',
        'rating_avg',
        'rating_count',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'prep_time_minutes' => 'integer',
        'rating_avg' => 'float',
        'rating_count' => 'integer',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================
    public function getDisplayBadgeAttribute(): ?string
    {
        return $this->badge ?: $this->cuisine;
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function hours()
    {
        return $this->hasMany(RestaurantHour::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('status', 'published');
    }

    public function allReviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getTodayHoursAttribute()
    {
        return $this->hours()->where('day_of_week', now()->dayOfWeek)->first();
    }

    // ============================================
    // IMAGE ACCESSORS
    // ============================================

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image && \Storage::disk('public')->exists($this->cover_image)) {
            return \Storage::disk('public')->url($this->cover_image);
        }
        return '';
    }

    public function getProfileImageUrlAttribute(): string
    {
        if ($this->profile_image && \Storage::disk('public')->exists($this->profile_image)) {
            return \Storage::disk('public')->url($this->profile_image);
        }
        return '';
    }

    // ============================================
    // ⭐ CORE FIX: RATING STATS
    // ============================================

    public function updateRatingStats(): void
    {
        $stats = \App\Models\Review::where('restaurant_id', $this->id)
            ->where('status', 'published')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as count, COALESCE(AVG(rating), 0) as avg')
            ->first();

        $newCount = (int) ($stats->count ?? 0);
        $newAvg = round((float) ($stats->avg ?? 0), 2);

        \DB::table('restaurants')
            ->where('id', $this->id)
            ->update([
                'rating_avg' => $newAvg,
                'rating_count' => $newCount,
                'updated_at' => now(),
            ]);

        $this->refresh();
    }

    public function getRatingBreakdownAttribute(): array
    {
        $breakdown = [];

        $total = \App\Models\Review::where('restaurant_id', $this->id)
            ->where('status', 'published')
            ->whereNull('deleted_at')
            ->count();

        foreach ([5, 4, 3, 2, 1] as $stars) {
            $count = \App\Models\Review::where('restaurant_id', $this->id)
                ->where('status', 'published')
                ->whereNull('deleted_at')
                ->where('rating', $stars)
                ->count();

            $breakdown[$stars] = [
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        }

        return $breakdown;
    }

    // ============================================
    // OPEN/CLOSE HELPERS
    // ============================================

    public function hasSchedule(): bool
    {
        return $this->hours()->count() > 0;
    }

    public function isOpenNow(): bool
    {
        if (!$this->is_open) return false;
        if (!$this->hasSchedule()) return true;

        $today = $this->todayHours;
        if (!$today || !$today->is_open) return false;
        if (!$today->open_time || !$today->close_time) return false;

        $now = now()->format('H:i:s');
        return $now >= $today->open_time && $now < $today->close_time;
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->hasSchedule()) {
            return $this->is_open ? 'Open now' : 'Closed';
        }

        if (!$this->is_open) return 'Closed';

        $today = $this->todayHours;

        if (!$today || !$today->is_open) {
            $next = $this->getNextOpenDay();
            if ($next) return 'Closed · Opens ' . $next['time'] . ' ' . $next['day'];
            return 'Closed';
        }

        $now = now()->format('H:i:s');

        if ($now < $today->open_time) {
            return 'Closed · Opens at ' . Carbon::parse($today->open_time)->format('g:i A');
        }

        if ($now >= $today->close_time) {
            $next = $this->getNextOpenDay();
            if ($next) return 'Closed · Opens ' . $next['time'] . ' ' . $next['day'];
            return 'Closed';
        }

        return 'Open · Closes at ' . Carbon::parse($today->close_time)->format('g:i A');
    }

    public function getNextOpenDay(): ?array
    {
        for ($i = 1; $i <= 7; $i++) {
            $checkDate = now()->addDays($i);
            $dayOfWeek = $checkDate->dayOfWeek;
            $hours = $this->hours()->where('day_of_week', $dayOfWeek)->first();

            if ($hours && $hours->is_open && $hours->open_time) {
                $dayLabel = $i === 1 ? 'tomorrow' : $checkDate->format('D');
                return [
                    'time' => Carbon::parse($hours->open_time)->format('g:i A'),
                    'day' => $dayLabel,
                ];
            }
        }
        return null;
    }
}