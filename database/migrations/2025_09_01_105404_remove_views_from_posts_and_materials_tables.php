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
        Schema::table('posts_and_materials_tables', function (Blueprint $table) {
            Schema::table('posts', function (Blueprint $table) {
                if (Schema::hasColumn('posts', 'views')) {
                    $table->dropColumn('views');
                }
            });

            Schema::table('materials', function (Blueprint $table) {
                if (Schema::hasColumn('materials', 'views')) {
                    $table->dropColumn('views');
                }
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts_and_materials_tables', function (Blueprint $table) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedBigInteger('views')->default(0);
            });

            Schema::table('materials', function (Blueprint $table) {
                $table->unsignedBigInteger('views')->default(0);
            });
        });
    }
};
