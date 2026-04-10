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
        if (!Schema::hasColumn('tags', 'tag_type_int')) {
            Schema::table('tags', function (Blueprint $table) {
                $table->unsignedBigInteger('tag_type_int')->nullable()->after('tag_type');
            });
        }

        // 2) انقل القيم النصية إلى أرقام (CASE)
        DB::statement("
            UPDATE `tags` SET `tag_type_int` =
                CASE `tag_type`
                    WHEN 'أخبار'      THEN 1
                    WHEN 'اهتمامات'   THEN 2
                    WHEN 'الاحداث'    THEN 3
                    WHEN 'مواد'       THEN 4
                    WHEN 'المهارات'   THEN 5
                    WHEN 'التخصص'     THEN 6
                    WHEN 'المستخدمين' THEN 7
                    WHEN 'اللغات'     THEN 8
                    WHEN 'اللهجات'    THEN 9
                    ELSE `tag_type_int`
                END
        ");

        // 3) احذف العمود القديم النصي لو موجود
        if (Schema::hasColumn('tags', 'tag_type')) {
            Schema::table('tags', function (Blueprint $table) {
                $table->dropColumn('tag_type');
            });
        }

        // 4) أعد تسمية العمود المؤقت إلى الاسم الأصلي
        if (Schema::hasColumn('tags', 'tag_type_int')) {
            Schema::table('tags', function (Blueprint $table) {
                $table->renameColumn('tag_type_int', 'tag_type');
            });
        }

        // 5) (اختياري) امنع NULL وحدد افتراضي 1
        if (Schema::hasColumn('tags', 'tag_type')) {
            Schema::table('tags', function (Blueprint $table) {
                $table->unsignedBigInteger('tag_type')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) أضف عمود نصي مؤقت
        if (!Schema::hasColumn('tags', 'tag_type_text')) {
            Schema::table('tags', function (Blueprint $table) {
                $table->string('tag_type_text')->nullable()->after('tag_type');
            });
        }

        // 2) رجّع الأرقام إلى نصوص
        DB::statement("
            UPDATE `tags` SET `tag_type_text` =
                CASE `tag_type`
                    WHEN 1 THEN 'أخبار'
                    WHEN 2 THEN 'اهتمامات'
                    WHEN 3 THEN 'الاحداث'
                    WHEN 4 THEN 'مواد'
                    WHEN 5 THEN 'المهارات'
                    WHEN 6 THEN 'التخصص'
                    WHEN 7 THEN 'المستخدمين'
                    WHEN 8 THEN 'اللغات'
                    WHEN 9 THEN 'اللهجات'
                    ELSE `tag_type_text`
                END
        ");

        // 3) احذف العمود الرقمي وأعد تسمية النصي للاسم الأصلي
        if (Schema::hasColumn('tags', 'tag_type')) {
            Schema::table('tags', function (Blueprint $table) {
                $table->dropColumn('tag_type');
            });
        }

        if (Schema::hasColumn('tags', 'tag_type_text')) {
            Schema::table('tags', function (Blueprint $table) {
                $table->renameColumn('tag_type_text', 'tag_type');
            });
        }
    }
};
