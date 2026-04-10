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
        if (!Schema::hasColumn('materials', 'type_int')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->unsignedBigInteger('type_int')->nullable()->after('type');
            });
        }

        // 2) حوّل القيم النصية إلى أرقام
        DB::statement("
            UPDATE `materials` SET `type_int` =
                CASE `type`
                    WHEN 'بودكاست' THEN 1
                    WHEN 'فيديو'    THEN 2
                    WHEN 'صورة'     THEN 3
                    ELSE `type_int`
                END
        ");

        // 3) احذف العمود القديم النصي لو موجود
        if (Schema::hasColumn('materials', 'type')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        // 4) أعد تسمية العمود المؤقت إلى الاسم الأصلي
        if (Schema::hasColumn('materials', 'type_int')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->renameColumn('type_int', 'type');
            });
        }

        // 5) (اختياري) امنع NULL وحدد افتراضي 1
        if (Schema::hasColumn('materials', 'type')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->unsignedBigInteger('type')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) أضف عمود نصي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('materials', 'type_text')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->string('type_text')->nullable()->after('type');
            });
        }

        // 2) رجّع الأرقام إلى نصوص
        DB::statement("
            UPDATE `materials` SET `type_text` =
                CASE `type`
                    WHEN 1 THEN 'بودكاست'
                    WHEN 2 THEN 'فيديو'
                    WHEN 3 THEN 'صورة'
                    ELSE `type_text`
                END
        ");

        // 3) احذف العمود الرقمي وأعد تسمية النصي للاسم الأصلي
        if (Schema::hasColumn('materials', 'type')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasColumn('materials', 'type_text')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->renameColumn('type_text', 'type');
            });
        }
    }
};
