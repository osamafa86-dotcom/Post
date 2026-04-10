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
            if (!Schema::hasColumn('posts', 'old_id')) {
                $table->bigInteger('old_id')->nullable()->after('id')->comment('Reference to original claim ID from db2');
                $table->index('old_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'old_id')) {
                $table->dropIndex(['old_id']);
                $table->dropColumn('old_id');
            }
        });
    }
};
