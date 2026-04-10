<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) أضف عمود مؤقت TinyInt إن لم يكن موجوداً
        if (!Schema::hasColumn('material_albums', 'type_int')) {
            if (Schema::hasColumn('material_albums', 'type')) {
                Schema::table('material_albums', function (Blueprint $table) {
                    $table->unsignedBigInteger('type_int')->nullable()->after('type');
                });
            } else {
                Schema::table('material_albums', function (Blueprint $table) {
                    $table->unsignedBigInteger('type_int')->nullable();
                });
            }
        }

        // 2) حوّل القيم النصية إلى أرقام (فقط إذا العمود النصي موجود)
        if (Schema::hasColumn('material_albums', 'type')) {
            DB::statement("
                UPDATE `material_albums` SET `type_int` =
                    CASE `type`
                        WHEN 'بودكاست' THEN 1
                        WHEN 'فيديو'    THEN 2
                        WHEN 'صورة'     THEN 3
                        ELSE `type_int`
                    END
            ");
        }

        // 3) احذف العمود القديم النصي لو موجود
        if (Schema::hasColumn('material_albums', 'type')) {
            Schema::table('material_albums', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        // 4) أعد تسمية العمود المؤقت للاسم الأصلي
        if (Schema::hasColumn('material_albums', 'type_int')) {
            Schema::table('material_albums', function (Blueprint $table) {
                $table->renameColumn('type_int', 'type');
            });
        }

        // 5) (اختياري) امنع NULL وحدد افتراضي 1
        if (Schema::hasColumn('material_albums', 'type')) {
            Schema::table('material_albums', function (Blueprint $table) {
                $table->unsignedBigInteger('type')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) أضف عمود نصي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('material_albums', 'type_text')) {
            Schema::table('material_albums', function (Blueprint $table) {
                $table->string('type_text')->nullable()->after('type');
            });
        }

        // 2) رجّع الأرقام إلى نصوص (إذا العمود الرقمي موجود)
        if (Schema::hasColumn('material_albums', 'type')) {
            DB::statement("
                UPDATE `material_albums` SET `type_text` =
                    CASE `type`
                        WHEN 1 THEN 'بودكاست'
                        WHEN 2 THEN 'فيديو'
                        WHEN 3 THEN 'صورة'
                        ELSE `type_text`
                    END
            ");
        }

        // 3) احذف العمود الرقمي وأعد تسمية النصي للاسم الأصلي
        if (Schema::hasColumn('material_albums', 'type')) {
            Schema::table('material_albums', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasColumn('material_albums', 'type_text')) {
            Schema::table('material_albums', function (Blueprint $table) {
                $table->renameColumn('type_text', 'type');
            });
        }
    }
};
