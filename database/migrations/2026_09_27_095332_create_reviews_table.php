<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('rating');
            $table->string('title', 100)->nullable();
            $table->text('body');

            $table->json('images')->nullable();

            $table->boolean('is_verified_purchase')->default(true);
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();

            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);
            $table->unsignedInteger('report_count')->default(0);

            $table->enum('status', ['published', 'hidden', 'flagged', 'pending'])->default('published');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['restaurant_id', 'status', 'created_at']);
            $table->index(['restaurant_id', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};