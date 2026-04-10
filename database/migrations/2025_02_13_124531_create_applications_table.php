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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('town')->nullable();
            $table->string('gender')->nullable();
            $table->text('specialization')->nullable();
            $table->string('portfolio')->nullable();
            $table->string('image')->nullable();
            $table->text('skills')->nullable();
            $table->string('course')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('facebook')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
