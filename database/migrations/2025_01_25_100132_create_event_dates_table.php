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
        Schema::create('event_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->index()->nullable();

            //if date_type is date + time
            $table->timestamp('start_date_time')->nullable();
            $table->timestamp('end_date_time')->nullable();

            //if date_type is time
            $table->string('day')->nullable();//enum
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
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
        Schema::dropIfExists('event_dates');
    }
};
