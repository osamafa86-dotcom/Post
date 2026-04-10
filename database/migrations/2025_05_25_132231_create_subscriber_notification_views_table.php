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
        Schema::create('subscriber_notification_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscriber_id')->nullable()->index();
            $table->unsignedBigInteger('subscriber_notification_id')->nullable()->index();
            $table->date('viewing_date')->nullable();
            $table->tinyInteger('is_favorite')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriber_notification_views');
    }
};
