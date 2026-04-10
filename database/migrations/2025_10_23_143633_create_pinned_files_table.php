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
        Schema::create('pinned_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_id')->index()->nullable();
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->dateTime('pinned_at')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinned_files');
    }
};
