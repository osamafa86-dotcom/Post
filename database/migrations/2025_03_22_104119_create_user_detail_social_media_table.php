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
        Schema::create('user_detail_social_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_detail_id')->index()->nullable();
            $table->string('social_media_name')->nullable();
            $table->string('social_media_link')->nullable();
            $table->longText('social_media_icon')->nullable();
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
        Schema::dropIfExists('user_detail_social_media');
    }
};
