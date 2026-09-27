<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'restaurant_id',
        'rider_id',
        'status',
        'rejection_reason',
        'cancellation_reason',
        'cancelled_at',
        'food_cost',
        'delivery_fee',
        'commission_rate',
        'commission_amount',
        'restaurant_earnings',
        'total_amount',
        'delivery_address',
        'delivery_lat',
        'delivery_lng',
        'is_external_order',
        'restaurant_rating',
        'rider_rating',
    ];

    protected $casts = [
        'food_cost' => 'float',
        'delivery_fee' => 'float',
        'commission_rate' => 'float',
        'commission_amount' => 'float',
        'restaurant_earnings' => 'float',
        'total_amount' => 'float',
        'delivery_lat' => 'float',
        'delivery_lng' => 'float',
        'is_external_order' => 'boolean',
        'cancelled_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function offers()
    {
        return $this->hasMany(DeliveryOffer::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function unreadMessagesFor(User $user)
    {
        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    public function canChat(): bool
    {
        return $this->rider_id
            && in_array($this->status, [
                'rider_assigned',
                'picked_up',
                'out_for_delivery',
            ]);
    }

    public function canBeCancelledByCustomer(): bool
    {
        return in_array($this->status, [
            'received',
            'confirmed',
            'preparing',
            'finding_rider',
        ]);
    }
    // Sa relationships section
public function review()
{
    return $this->hasOne(Review::class);
}

// Sa helpers section
public function canBeReviewed(): bool
{
    return $this->status === 'delivered'
        && !$this->review()->exists();
}

public function hasBeenReviewed(): bool
{
    return $this->review()->exists();
}
}