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
        Schema::create('user_detail_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_details_id')->index();
            $table->unsignedBigInteger('tag_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tags', function (Blueprint $table) {
            //
        });
    }
};
