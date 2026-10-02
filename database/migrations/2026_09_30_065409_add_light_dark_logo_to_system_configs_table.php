<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_configs', function (Blueprint $table) {
            // Tiyakin na may logo_height column bago magdagdag ng ai_icon_path
            if (!Schema::hasColumn('system_configs', 'logo_height')) {
                $table->integer('logo_height')->default(40)->after('logo_path');
            }
            
            if (!Schema::hasColumn('system_configs', 'ai_icon_path')) {
                $table->string('ai_icon_path')->nullable()->after('logo_height');
            }
            if (!Schema::hasColumn('system_configs', 'ai_icon_size')) {
                $table->integer('ai_icon_size')->default(56)->after('ai_icon_path');
            }
            if (!Schema::hasColumn('system_configs', 'ai_icon_type')) {
                $table->string('ai_icon_type')->default('default')->after('ai_icon_size');
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_configs', function (Blueprint $table) {
            if (Schema::hasColumn('system_configs', 'ai_icon_path')) {
                $table->dropColumn('ai_icon_path');
            }
            if (Schema::hasColumn('system_configs', 'ai_icon_size')) {
                $table->dropColumn('ai_icon_size');
            }
            if (Schema::hasColumn('system_configs', 'ai_icon_type')) {
                $table->dropColumn('ai_icon_type');
            }
        });
    }
};