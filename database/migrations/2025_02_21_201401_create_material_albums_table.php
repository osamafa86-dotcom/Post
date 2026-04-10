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
        Schema::create('material_albums', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            //  $table->string('image')->nullable();
            $table->text('description')->nullable();
            //  $table->unsignedBigInteger('category_id')->index()->nullable();
            // $table->unsignedBigInteger('tag_id')->index()->nullable();
            $table->unsignedBigInteger('team_id')->index()->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_albums');
    }
};
