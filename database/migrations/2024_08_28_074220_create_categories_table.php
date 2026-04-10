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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_title')->nullable();
            $table->text('category_description')->nullable();
            $table->unsignedBigInteger('parent_id')->index()->nullable();
            $table->string('category_type')->nullable();//enum
            $table->boolean('show_index')->default(false)->nullable();
            $table->unsignedBigInteger('team_id')->index()->nullable();
            $table->integer('order')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
