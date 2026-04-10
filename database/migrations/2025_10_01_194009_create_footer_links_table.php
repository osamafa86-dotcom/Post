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
        Schema::create('footer_links', function (Blueprint $table) {
            $table->id();
            $table->string('lang')->nullable();
            $table->integer('link_order')->nullable();
            $table->string('link_name')->nullable();
            $table->string('link_url')->nullable();
            $table->integer('icon_id')->nullable();
            $table->unsignedBigInteger('link_status')->nullable();
            $table->unsignedBigInteger('link_open')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
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
        Schema::dropIfExists('footer_links');
    }
};
