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
        Schema::table('posts', function (Blueprint $table) {
                $table->integer('updates')->default(0)->nullable(false)->change();
            $table->integer('views')->default(0)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
              $table->integer('updates')->nullable()->default(null)->change();
            $table->integer('views')->nullable()->default(null)->change();
        });
    }
};
