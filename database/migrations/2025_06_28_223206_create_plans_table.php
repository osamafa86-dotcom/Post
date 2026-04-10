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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_id');
            $table->string('type')->comment('india or international')->default('india');
            $table->string('period');
            $table->string('item_id')->nullable();
            $table->string('item_name')->nullable();
            $table->text('item_description')->nullable();
            $table->string('item_amount')->nullable();
            $table->string('item_currency')->nullable();
            $table->json('item');
            $table->json('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
