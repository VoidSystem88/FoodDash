<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_configs', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(10.00)->after('default_delivery_fee');
        });
    }

    public function down(): void
    {
        Schema::table('system_configs', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });
    }
};