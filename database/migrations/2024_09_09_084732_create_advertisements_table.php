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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->string('type')->nullable();//enum
            $table->string('place')->nullable();//enum
            $table->string('url')->nullable();
            $table->string('url_target')->nullable();
            $table->longText('code')->nullable();
            $table->string('end_hour_time')->nullable();
            $table->string('end_min_time')->nullable();
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->string('publish_status')->nullable();//enum
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
        Schema::dropIfExists('advertisements');
    }
};
