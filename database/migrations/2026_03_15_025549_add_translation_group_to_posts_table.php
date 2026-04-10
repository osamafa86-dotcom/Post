<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add translation_group column to link translated posts together.
     * Posts sharing the same translation_group UUID are translations of each other.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->uuid('translation_group')->nullable()->after('lang');
            $table->index('translation_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['translation_group']);
            $table->dropColumn('translation_group');
        });
    }
};
