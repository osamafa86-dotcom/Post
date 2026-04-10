<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) أضف عمود رقمي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('participants', 'type_int')) {
            if (Schema::hasColumn('participants', 'type')) {
                Schema::table('participants', function (Blueprint $table) {
                    $table->unsignedBigInteger('type_int')->nullable()->after('type');
                });
            } else {
                Schema::table('participants', function (Blueprint $table) {
                    $table->unsignedBigInteger('type_int')->nullable();
                });
            }
        }

        // 2) حوّل النصوص → أرقام (لو العمود النصّي موجود)
        if (Schema::hasColumn('participants', 'type')) {
            DB::statement("
                UPDATE `participants` SET `type_int` =
                    CASE TRIM(`type`)
                        WHEN 'الكتاب'     THEN 1
                        WHEN 'المقدمين'   THEN 2
                        WHEN 'الضيوف'     THEN 3
                        WHEN 'العملاء'    THEN 4
                        WHEN 'الشركاء'    THEN 5
                        WHEN 'الفريق'     THEN 6
                        WHEN 'الداعمون'   THEN 7
                        ELSE `type_int`
                    END
            ");
        }

        // 3) احذف العمود النصّي لو موجود
        if (Schema::hasColumn('participants', 'type')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        // 4) أعد تسمية المؤقت للاسم الأصلي
        if (Schema::hasColumn('participants', 'type_int')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->renameColumn('type_int', 'type');
            });
        }

        // 5) (اختياري) ثبّت النوع كـ tinyint NOT NULL مع افتراضي
         if (Schema::hasColumn('participants', 'type')) {
             Schema::table('participants', function (Blueprint $table) {
                 $table->unsignedBigInteger('type')->nullable()->change();
             });
         }
    }

    public function down(): void
    {
        // 1) عمود نصّي مؤقت
        if (!Schema::hasColumn('participants', 'type_text')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->string('type_text')->nullable()->after('type');
            });
        }

        // 2) الأرقام → نصوص (لو العمود الرقمي موجود)
        if (Schema::hasColumn('participants', 'type')) {
            DB::statement("
                UPDATE `participants` SET `type_text` =
                    CASE `type`
                        WHEN 1 THEN 'الكتاب'
                        WHEN 2 THEN 'المقدمين'
                        WHEN 3 THEN 'الضيوف'
                        WHEN 4 THEN 'العملاء'
                        WHEN 5 THEN 'الشركاء'
                        WHEN 6 THEN 'الفريق'
                        WHEN 7 THEN 'الداعمون'
                        ELSE `type_text`
                    END
            ");
        }

        // 3) احذف الرقمي وأعد تسمية النصّي
        if (Schema::hasColumn('participants', 'type')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
        if (Schema::hasColumn('participants', 'type_text')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->renameColumn('type_text', 'type');
            });
        }
    }
};
