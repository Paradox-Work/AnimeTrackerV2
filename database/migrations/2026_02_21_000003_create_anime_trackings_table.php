<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anime_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('anime_id');
            $table->unsignedInteger('watched_episodes')->default(0);
            $table->string('status')->default('plan_to_watch');
            $table->decimal('score', 3, 1)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'anime_id']);
            $table->index('anime_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anime_trackings');
    }
};
