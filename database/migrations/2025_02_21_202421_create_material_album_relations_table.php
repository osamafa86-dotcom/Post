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
        Schema::create('material_album_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_album_id')->nullable()->index();
            $table->string('relationable_type')->nullable();
            $table->unsignedBigInteger('relationable_id')->nullable()->index();
            $table->boolean('relationable_is_main')->default(0);
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
        Schema::dropIfExists('material_album_relations');
    }
};
