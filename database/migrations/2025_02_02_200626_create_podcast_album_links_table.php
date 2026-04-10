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
        Schema::create('podcast_album_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('podcast_album_id')->index()->nullable();
            $table->string('title')->nullable();
            $table->unsignedBigInteger('icon_id')->index()->nullable();
            $table->string('url')->nullable();
            $table->unsignedBigInteger('team_id')->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('podcast_album_links');
    }
};
