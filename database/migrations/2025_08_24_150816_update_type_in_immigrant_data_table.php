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
        if (!Schema::hasColumn('immigrant_data', 'type_int')) {
            if (Schema::hasColumn('immigrant_data', 'type')) {
                Schema::table('immigrant_data', function (Blueprint $table) {
                    $table->unsignedBigInteger('type_int')->nullable()->after('type');
                });
            } else {
                Schema::table('immigrant_data', function (Blueprint $table) {
                    $table->unsignedBigInteger('type_int')->nullable();
                });
            }
        }

        // 2) حوّل النصوص → أرقام (لو العمود النصّي موجود)
        if (Schema::hasColumn('immigrant_data', 'type')) {
            DB::statement("
                UPDATE `immigrant_data` SET `type_int` =
                    CASE TRIM(`type`)
                        WHEN 'كود'  THEN 1
                        WHEN 'ملف'  THEN 2
                        WHEN 'فرصة' THEN 3
                        ELSE `type_int`
                    END
            ");
        }

        // 3) احذف العمود النصّي القديم لو موجود
        if (Schema::hasColumn('immigrant_data', 'type')) {
            Schema::table('immigrant_data', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        // 4) أعد تسمية العمود المؤقت للاسم الأصلي
        if (Schema::hasColumn('immigrant_data', 'type_int')) {
            Schema::table('immigrant_data', function (Blueprint $table) {
                $table->renameColumn('type_int', 'type');
            });
        }

        // 5) (اختياري) ثبّت النوع كـ NOT NULL
        if (Schema::hasColumn('immigrant_data', 'type')) {
            Schema::table('immigrant_data', function (Blueprint $table) {
                $table->unsignedBigInteger('type')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) عمود نصّي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('immigrant_data', 'type_text')) {
            Schema::table('immigrant_data', function (Blueprint $table) {
                $table->string('type_text')->nullable()->after('type');
            });
        }

        // 2) الأرقام → نصوص (لو العمود الرقمي موجود)
        if (Schema::hasColumn('immigrant_data', 'type')) {
            DB::statement("
                UPDATE `immigrant_data` SET `type_text` =
                    CASE `type`
                        WHEN 1 THEN 'كود'
                        WHEN 2 THEN 'ملف'
                        WHEN 3 THEN 'فرصة'
                        ELSE `type_text`
                    END
            ");
        }

        // 3) احذف الرقمي وأعد تسمية النصّي
        if (Schema::hasColumn('immigrant_data', 'type')) {
            Schema::table('immigrant_data', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
        if (Schema::hasColumn('immigrant_data', 'type_text')) {
            Schema::table('immigrant_data', function (Blueprint $table) {
                $table->renameColumn('type_text', 'type');
            });
        }
    }
};
