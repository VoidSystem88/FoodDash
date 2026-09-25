<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('restaurants', 'prep_time_minutes')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->integer('prep_time_minutes')->default(20)->after('is_open');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('restaurants', 'prep_time_minutes')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->dropColumn('prep_time_minutes');
            });
        }
    }
};