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
        Schema::create('navbar_links', function (Blueprint $table) {
            $table->id();
            $table->integer('link_order')->nullable();
            $table->string('link_name')->nullable();
            $table->string('link_url')->nullable();
            $table->integer('icon_id')->nullable();
            $table->string('link_status')->nullable();//enum
            $table->string('link_open')->nullable();//enum
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
        Schema::dropIfExists('navbar_links');
    }
};
