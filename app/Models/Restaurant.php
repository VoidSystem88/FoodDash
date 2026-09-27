<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    // Sa relationships section
public function reviews()
{
    return $this->hasMany(Review::class)->published();
}

public function allReviews()
{
    return $this->hasMany(Review::class);
}

// Sa accessors/helpers section
public function updateRatingStats(): void
{
    $this->update([
        'rating_avg' => round($this->reviews()->avg('rating') ?? 0, 2),
        'rating_count' => $this->reviews()->count(),
    ]);
}

public function getRatingBreakdownAttribute(): array
{
    $breakdown = [];
    $total = $this->rating_count;

    foreach ([5, 4, 3, 2, 1] as $stars) {
        $count = $this->reviews()->where('rating', $stars)->count();
        $breakdown[$stars] = [
            'count' => $count,
            'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
        ];
    }

    return $breakdown;
}


    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'cuisine',
	'cover_image',
  	'profile_image',
        'latitude',
        'longitude',
        'is_open',
        'prep_time_minutes',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'prep_time_minutes' => 'integer',
    ];

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

    public function getTodayHoursAttribute()
    {
        return $this->hours()->where('day_of_week', now()->dayOfWeek)->first();
    }
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
    /**
     * Check kung may schedule ba na naka-set.
     */
    public function hasSchedule(): bool
    {
        return $this->hours()->count() > 0;
    }

    /**
     * Check kung bukas ngayon.
     *
     * Logic:
     * 1. Manual toggle OFF → CLOSED
     * 2. Walang schedule → follow `is_open` flag (manual toggle)
     * 3. May schedule → check today's hours + current time
     */
    public function isOpenNow(): bool
    {
        // 1. Manual override — kung naka-close manually, closed pa rin
        if (!$this->is_open) {
            return false;
        }

        // 2. Kung WALANG schedule, gamitin ang manual is_open flag
        if (!$this->hasSchedule()) {
            return true; // Bukas kasi is_open = true at walang schedule restriction
        }

        // 3. May schedule — check today's hours
        $today = $this->todayHours;

        // Walang hours para sa araw na ito = closed
        if (!$today || !$today->is_open) {
            return false;
        }

        if (!$today->open_time || !$today->close_time) {
            return false;
        }

        $now = now()->format('H:i:s');

        return $now >= $today->open_time && $now < $today->close_time;
    }

    /**
     * Kunin ang status label.
     */
    public function getStatusLabelAttribute(): string
    {
        // 1. Walang schedule — base sa manual is_open flag
        if (!$this->hasSchedule()) {
            return $this->is_open ? 'Open now' : 'Closed';
        }

        // 2. Manual override — closed
        if (!$this->is_open) {
            return 'Closed';
        }

        // 3. May schedule — check today
        $today = $this->todayHours;

        if (!$today || !$today->is_open) {
            // Walang schedule para sa araw na ito — hanapin next open day
            $next = $this->getNextOpenDay();
            if ($next) {
                return 'Closed · Opens ' . $next['time'] . ' ' . $next['day'];
            }
            return 'Closed';
        }

        $now = now()->format('H:i:s');

        // Bago mag-open
        if ($now < $today->open_time) {
            return 'Closed · Opens at ' . Carbon::parse($today->open_time)->format('g:i A');
        }

        // Pagkatapos mag-close
        if ($now >= $today->close_time) {
            $next = $this->getNextOpenDay();
            if ($next) {
                return 'Closed · Opens ' . $next['time'] . ' ' . $next['day'];
            }
            return 'Closed';
        }

        // Bukas ngayon
        return 'Open · Closes at ' . Carbon::parse($today->close_time)->format('g:i A');
    }

    /**
     * Kunin ang next open day.
     */
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