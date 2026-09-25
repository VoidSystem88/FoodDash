<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'sender_id',
        'body',
        'read_at',
        'delivered_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function markAsDelivered(): void
    {
        if (!$this->delivered_at) {
            $this->update(['delivered_at' => now()]);
        }
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update([
                'read_at' => now(),
                'delivered_at' => $this->delivered_at ?? now(),
            ]);
        }
    }

    public function getStatusFor(User $user): string
    {
        // Ikaw ang sender? Ipakita ang status
        if ($this->sender_id === $user->id) {
            if ($this->read_at) return 'seen';
            if ($this->delivered_at) return 'delivered';
            return 'sent';
        }

        return 'received';
    }
}