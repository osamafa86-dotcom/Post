<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) أضف عمود رقمي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('alerts', 'type_int')) {
            if (Schema::hasColumn('alerts', 'type')) {
                Schema::table('alerts', function (Blueprint $table) {
                    $table->unsignedBigInteger('type_int')->nullable()->after('type');
                });
            } else {
                Schema::table('alerts', function (Blueprint $table) {
                    $table->unsignedBigInteger('type_int')->nullable();
                });
            }
        }

        // 2) حوّل النصوص → أرقام (لو العمود النصّي موجود)
        if (Schema::hasColumn('alerts', 'type')) {
            DB::statement("
                UPDATE `alerts` SET `type_int` =
                    CASE TRIM(`type`)
                        WHEN 'خطر' THEN 1
                        WHEN 'نجاح' THEN 2
                        ELSE `type_int`
                    END
            ");
        }

        // 3) احذف العمود النصّي القديم لو موجود
        if (Schema::hasColumn('alerts', 'type')) {
            Schema::table('alerts', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        // 4) أعد تسمية المؤقت للاسم الأصلي
        if (Schema::hasColumn('alerts', 'type_int')) {
            Schema::table('alerts', function (Blueprint $table) {
                $table->renameColumn('type_int', 'type');
            });
        }

        // 5) (اختياري) ثبّت النوع NOT NULL
        // إن رغبت بذلك، أزل التعليق:
         if (Schema::hasColumn('alerts', 'type')) {
             Schema::table('alerts', function (Blueprint $table) {
                 $table->unsignedBigInteger('type')->nullable()->change();
             });
         }
    }

    public function down(): void
    {
        // 1) عمود نصّي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('alerts', 'type_str')) {
            if (Schema::hasColumn('alerts', 'type')) {
                Schema::table('alerts', function (Blueprint $table) {
                    $table->string('type_str')->nullable()->after('type');
                });
            } else {
                Schema::table('alerts', function (Blueprint $table) {
                    $table->string('type_str')->nullable();
                });
            }
        }

        // 2) الأرقام → نصوص (لو العمود الرقمي موجود)
        if (Schema::hasColumn('alerts', 'type')) {
            DB::statement("
                UPDATE `alerts` SET `type_str` =
                    CASE `type`
                        WHEN 1 THEN 'خطر'
                        WHEN 2 THEN 'نجاح'
                        ELSE `type_str`
                    END
            ");
        }

        // 3) احذف الرقمي وأعد تسمية النصّي
        if (Schema::hasColumn('alerts', 'type')) {
            Schema::table('alerts', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasColumn('alerts', 'type_str')) {
            Schema::table('alerts', function (Blueprint $table) {
                $table->renameColumn('type_str', 'type');
            });
        }
    }
};
