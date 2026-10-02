<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_configs', function (Blueprint $table) {
            if (!Schema::hasColumn('system_configs', 'ai_icon_path')) {
                $table->string('ai_icon_path')->nullable()->after('logo_height');
            }
            if (!Schema::hasColumn('system_configs', 'ai_icon_type')) {
                $table->string('ai_icon_type')->default('default')->after('ai_icon_path');
            }
            if (!Schema::hasColumn('system_configs', 'ai_icon_html')) {
                $table->text('ai_icon_html')->nullable()->after('ai_icon_type');
            }
            if (!Schema::hasColumn('system_configs', 'ai_icon_size')) {
                $table->integer('ai_icon_size')->default(56)->after('ai_icon_html');
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_configs', function (Blueprint $table) {
            $table->dropColumn([
                'ai_icon_path',
                'ai_icon_type',
                'ai_icon_html',
                'ai_icon_size',
            ]);
        });
    }
};