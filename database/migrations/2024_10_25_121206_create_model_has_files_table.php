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
        Schema::create('model_has_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_id')->index();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id')->index()->nullable();
            $table->string('model_column')->nullable();
            $table->unsignedBigInteger('team_id')->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_has_files');
    }
};
