<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) أضف عمود مؤقت للنوع الرقمي إن لم يكن موجوداً
        if (!Schema::hasColumn('materials', 'video_type_int')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->unsignedBigInteger('video_type_int')->nullable()->after('video_type');
            });
        }

        // 2) حوّل القيم النصية إلى أرقام
        DB::statement("
            UPDATE `materials` SET `video_type_int` =
                CASE `video_type`
                    WHEN 'يوتيوب'   THEN 1
                    WHEN 'تيليجرام' THEN 2
                    WHEN 'محلي'     THEN 3
                    ELSE `video_type_int`
                END
        ");

        // 3) احذف العمود القديم النصي لو موجود
        if (Schema::hasColumn('materials', 'video_type')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->dropColumn('video_type');
            });
        }

        // 4) أعد تسمية العمود المؤقت للاسم الأصلي
        if (Schema::hasColumn('materials', 'video_type_int')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->renameColumn('video_type_int', 'video_type');
            });
        }

        // 5) (اختياري) امنع NULL وحدد افتراضي
        if (Schema::hasColumn('materials', 'video_type')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->unsignedBigInteger('video_type')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) أضف عمود نصي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('materials', 'video_type_text')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->string('video_type_text')->nullable()->after('video_type');
            });
        }

        // 2) رجّع الأرقام إلى نصوص
        DB::statement("
            UPDATE `materials` SET `video_type_text` =
                CASE `video_type`
                    WHEN 1 THEN 'يوتيوب'
                    WHEN 2 THEN 'تيليجرام'
                    WHEN 3 THEN 'محلي'
                    ELSE `video_type_text`
                END
        ");

        // 3) احذف العمود الرقمي وأعد تسمية النصي للاسم الأصلي
        if (Schema::hasColumn('materials', 'video_type')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->dropColumn('video_type');
            });
        }

        if (Schema::hasColumn('materials', 'video_type_text')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->renameColumn('video_type_text', 'video_type');
            });
        }
    }
};
