<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // لو الجدول bigIncrements استخدم unsignedBigInteger
            if (!Schema::hasColumn('posts', 'old_id')) {
                $table->unsignedBigInteger('old_id')->nullable()->index()
                    ->comment('Original id from legacy tayqn tables (claims/news)');
            }

            // (اختياري) لو بدّك تميّز المصدر
            if (!Schema::hasColumn('posts', 'source_type')) {
                $table->string('source_type', 20)->nullable()->index(); // 'claim' or 'news'
            }

            // ملاحظة: لا تجعل old_id فريدًا لأن نفس الـ id سيتكرر عبر اللغات
            // ولو بدك منع تكرار (قد) تعمل مركّب:
            // $table->unique(['old_id','lang','source_type']);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'source_type')) $table->dropColumn('source_type');
            if (Schema::hasColumn('posts', 'old_id')) $table->dropColumn('old_id');
        });
    }
};
