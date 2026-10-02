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
        'payment_method',
        'payment_reference',
        'payment_status',
        'status',
        'rejection_reason',
        'restaurant_started_preparing_at',
        'restaurant_marked_ready_at', 
        'cancellation_reason',
        'cancelled_at',
        'hidden_for_rider_at',     
        'hidden_for_customer_at',
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
        'verified_pickup_at',
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
        'verified_pickup_at' => 'datetime',
        'is_external_order' => 'boolean',
        'cancelled_at' => 'datetime',
        'restaurant_started_preparing_at' => 'datetime',  
        'restaurant_marked_ready_at' => 'datetime', 
        'hidden_for_rider_at' => 'datetime',     
        'hidden_for_customer_at' => 'datetime',
    ];
// ============================================
// RIDER STATE HELPERS
// ============================================
// Sa helpers section

public function isPrepaid(): bool
{
    return in_array($this->payment_method, ['gcash', 'maya'])
        && $this->payment_status === 'paid';
}

public function isCashOnDelivery(): bool
{
    return $this->payment_method === 'cod';
}

public function getPaymentMethodLabelAttribute(): string
{
    return match ($this->payment_method) {
        'gcash' => 'GCash',
        'maya' => 'Maya (PayMaya)',
        'cod' => 'Cash on Delivery',
        default => 'Unknown',
    };
}
/**
 * Check kung verified na ang order para sa rider.
 * Verified = restaurant nag-start ng preparing.
 */
public function isVerifiedForRider(): bool
{
    return $this->restaurant_started_preparing_at !== null;
}

/**
 * Check kung ready na ang food para i-pickup.
 */
public function isReadyForPickup(): bool
{
    return $this->restaurant_marked_ready_at !== null;
}

/**
 * Get rider-facing state.
 */
public function getRiderStateAttribute(): string
{
    return match ($this->status) {
        'rider_assigned' => $this->isVerifiedForRider() ? 'verified' : 'waiting',
        'preparing' => 'verified',
        'ready_for_pickup' => 'ready',
        'picked_up' => 'picked_up',
        'out_for_delivery' => 'delivering',
        'delivered' => 'done',
        'cancelled' => 'cancelled',
        'rejected' => 'rejected',
        'no_rider' => 'no_rider',
        default => 'unknown',
    };
}

/**
 * Get rider-facing state label.
 */
public function getRiderStateLabelAttribute(): string
{
    return match ($this->rider_state) {
        'waiting' => 'Waiting for restaurant to start preparing',
        'verified' => 'Verified — proceed to restaurant',
        'ready' => 'Food ready — pickup now',
        'picked_up' => 'Picked up — on the way',
        'delivering' => 'Delivering to customer',
        'done' => 'Delivered',
        'cancelled' => 'Cancelled by customer',
        'rejected' => 'Rejected by restaurant',
        'no_rider' => 'No rider available',
        default => 'Processing',
    };
}

/**
 * Check kung pwede nang mag-pickup ang rider.
 */
public function canBePickedUpByRider(): bool
{
    return $this->status === 'ready_for_pickup'
        && $this->rider_id !== null;
}

/**
 * Check kung pwede pang mag-cancel ang customer.
 */
public function canBeCancelledByCustomer(): bool
{
    return in_array($this->status, ['received', 'confirmed', 'rider_assigned']);
}

/**
 * Check kung may active rider na.
 */
public function hasActiveRider(): bool
{
    return $this->rider_id !== null
        && in_array($this->status, ['rider_assigned', 'preparing', 'ready_for_pickup', 'picked_up', 'out_for_delivery']);
}
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

    public function isPreparingByRestaurant(): bool
    {
        return $this->restaurant_started_preparing_at !== null;
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


public function isPreparing(): bool
{
    return $this->status === 'preparing';
}

public function canBeAcceptedByRider(): bool
{
    // Rider can only accept kapag ready na ang order
    return $this->status === 'ready_for_pickup'
        && $this->rider_id === null;
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