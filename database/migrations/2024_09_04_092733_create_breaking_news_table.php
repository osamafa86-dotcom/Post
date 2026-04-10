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
        Schema::create('breaking_news', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->dateTime('hide_date')->nullable();
            $table->string('url')->nullable();
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
        Schema::dropIfExists('breaking_news');
    }
};
