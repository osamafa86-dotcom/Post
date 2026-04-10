<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('podcast_albums', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('small_image')->nullable();
            $table->string('background_image')->nullable();
            $table->text('description')->nullable();
            $table->string('sound_cloud')->nullable();
            $table->string('google_cast')->nullable();
            $table->string('spotify')->nullable();
            $table->string('apple_cast')->nullable();
            $table->unsignedBigInteger('team_id')->index()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('podcast_albums');
    }
};
