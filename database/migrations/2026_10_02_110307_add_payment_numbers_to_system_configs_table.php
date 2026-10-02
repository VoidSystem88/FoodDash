<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_configs', function (Blueprint $table) {
            $table->string('gcash_number')->nullable()->after('commission_rate');
            $table->string('maya_number')->nullable()->after('gcash_number');
        });
    }

    public function down(): void
    {
        Schema::table('system_configs', function (Blueprint $table) {
            $table->dropColumn(['gcash_number', 'maya_number']);
        });
    }
};