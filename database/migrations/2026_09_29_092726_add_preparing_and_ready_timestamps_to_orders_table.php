<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('restaurant_started_preparing_at')->nullable()->after('cancelled_at');
            $table->timestamp('restaurant_marked_ready_at')->nullable()->after('restaurant_started_preparing_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'restaurant_started_preparing_at',
                'restaurant_marked_ready_at',
            ]);
        });
    }
};