<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'received',
            'confirmed',
            'preparing',
            'ready_for_pickup',
            'rider_assigned',      -- ADVANCE BOOKING (waiting for restaurant to prep)
            'restaurant_preparing', -- ⭐ BAGO: Restaurant started cooking
            'rider_ready_to_pickup', -- ⭐ BAGO: Ready na — rider can come now
            'picked_up',
            'out_for_delivery',
            'delivered',
            'rejected',
            'cancelled',
            'no_rider'
        ) DEFAULT 'received'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'received', 'confirmed', 'preparing', 'ready_for_pickup',
            'finding_rider', 'rider_assigned', 'picked_up', 'out_for_delivery',
            'delivered', 'rejected', 'cancelled', 'no_rider'
        ) DEFAULT 'received'");
    }
};