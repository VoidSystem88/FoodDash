<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cod', 'gcash', 'maya'])
                ->default('cod')
                ->after('total_amount');

            $table->string('payment_reference')->nullable()->after('payment_method');

            $table->enum('payment_status', ['pending', 'paid', 'failed'])
                ->default('pending')
                ->after('payment_reference');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_reference', 'payment_status']);
        });
    }
};