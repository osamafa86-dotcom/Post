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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
          //  $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('video_type')->nullable();
            $table->string('link')->nullable();
            $table->string('publish_status');
            $table->unsignedBigInteger('album_id')->index()->nullable();
            $table->unsignedBigInteger('team_id')->index()->nullable();
            //  $table->unsignedBigInteger('category_id')->index()->nullable();
            // $table->unsignedBigInteger('tag_id')->index()->nullable();
            $table->bigInteger('views')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
