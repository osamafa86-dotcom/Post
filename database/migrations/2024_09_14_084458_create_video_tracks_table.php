<?php

use App\Enums\PublishEnum;
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
        Schema::create('video_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->unsignedBigInteger('video_album_id')->index()->nullable();
            $table->string('publish_status', 20)->default(PublishEnum::DRAFT->value);
            $table->unsignedBigInteger('publisher_id')->index()->nullable();
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
        Schema::dropIfExists('video_tracks');
    }
};
