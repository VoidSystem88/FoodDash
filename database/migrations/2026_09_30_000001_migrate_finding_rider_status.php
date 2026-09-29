<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing 'finding_rider' orders to 'confirmed'
        DB::table('orders')
            ->where('status', 'finding_rider')
            ->update(['status' => 'confirmed']);

        // Update enum to remove 'finding_rider' (keep it for backward compat)
        // Actually, keep it in enum but never use it
        // This avoids breaking existing data
    }

    public function down(): void
    {
        // No reverse migration needed
    }
};