<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'user_id',
        'restaurant_id',
        'rating',
        'title',
        'body',
        'images',
        'is_verified_purchase',
        'is_edited',
        'edited_at',
        'helpful_count',
        'not_helpful_count',
        'report_count',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
        'is_verified_purchase' => 'boolean',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'report_count' => 'integer',
        'rating' => 'integer',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function votes()
    {
        return $this->hasMany(ReviewVote::class);
    }

    public function replies()
    {
        return $this->hasMany(ReviewReply::class)->orderBy('created_at');
    }

    public function reports()
    {
        return $this->hasMany(ReviewReport::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeWithPhotos($query)
    {
        return $query->whereNotNull('images')->where('images', '!=', '[]');
    }

    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeMostHelpful($query)
    {
        return $query->orderByDesc('helpful_count')->orderByDesc('created_at');
    }

    public function scopeMostRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    public function scopeHighestRated($query)
    {
        return $query->orderByDesc('rating')->orderByDesc('created_at');
    }

    public function scopeLowestRated($query)
    {
        return $query->orderBy('rating')->orderByDesc('created_at');
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getImageUrlsAttribute(): array
    {
        if (empty($this->images)) {
            return [];
        }

        return collect($this->images)
            ->map(fn($path) => Storage::disk('public')->url($path))
            ->all();
    }

    public function getHasPhotosAttribute(): bool
    {
        return !empty($this->images);
    }

    public function getIsEditableAttribute(): bool
    {
        return $this->created_at && $this->created_at->diffInDays(now()) <= 7;
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    // ============================================
    // HELPERS
    // ============================================

    public function userVoteType(?int $userId = null): ?string
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return null;

        $vote = $this->votes()->where('user_id', $userId)->first();
        return $vote?->vote_type;
    }

    public function hasUserReported(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return false;

        return $this->reports()->where('user_id', $userId)->exists();
    }

    public function updateVoteCounts(): void
    {
        $this->update([
            'helpful_count' => $this->votes()->where('vote_type', 'helpful')->count(),
            'not_helpful_count' => $this->votes()->where('vote_type', 'not_helpful')->count(),
        ]);
    }

    public function incrementReportCount(): void
    {
        $this->increment('report_count');

        if ($this->fresh()->report_count >= 5 && $this->status === 'published') {
            $this->update(['status' => 'flagged']);
        }
    }

    public function markAsEdited(): void
    {
        $this->update([
            'is_edited' => true,
            'edited_at' => now(),
        ]);
    }
}