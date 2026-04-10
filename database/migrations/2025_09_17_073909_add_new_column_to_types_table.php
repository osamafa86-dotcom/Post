<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('types') && !Schema::hasColumns('types', ['show_index', 'order'])) {
            Schema::table('types', function (Blueprint $table) {
                $table->boolean('show_index')->default(false)->nullable();
                $table->integer('order')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('types', function (Blueprint $table) {
            //
        });
    }
};
