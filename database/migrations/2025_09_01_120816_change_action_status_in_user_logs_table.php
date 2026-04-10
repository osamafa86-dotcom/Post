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
        if (!Schema::hasColumn('user_logs', 'action_int')) {
            if (Schema::hasColumn('user_logs', 'action_status')) {
                Schema::table('user_logs', function (Blueprint $table) {
                    $table->unsignedBigInteger('action_int')->nullable()->after('action_status');
                });
            } else {
                Schema::table('user_logs', function (Blueprint $table) {
                    $table->unsignedBigInteger('action_int')->nullable();
                });
            }
        }

        // 2) حوّل النصوص → أرقام (لو العمود النصّي موجود)
        if (Schema::hasColumn('user_logs', 'action_status')) {
            DB::statement("
                UPDATE `user_logs` SET `action_int` =
                    CASE TRIM(`action_status`)
                        WHEN 'إضافة' THEN 1
                        WHEN 'تعديل' THEN 2
                        WHEN 'تعليق' THEN 3
                        WHEN 'حذف' THEN 4
                        WHEN 'تغيير حالة المنشور' THEN 5
                        ELSE `action_int`
                    END
            ");
        }

        // 3) احذف العمود النصّي القديم لو موجود
        if (Schema::hasColumn('user_logs', 'action_status')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->dropColumn('action_status');
            });
        }

        // 4) أعد تسمية العمود المؤقت للاسم الأصلي
        if (Schema::hasColumn('user_logs', 'action_int')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->renameColumn('action_int', 'action_status');
            });
        }

        // 5) (اختياري) ثبّت النوع كـ NOT NULL
        if (Schema::hasColumn('user_logs', 'action_status')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('action_status')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        // 1) عمود نصّي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('user_logs', 'action_text')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->string('action_text')->nullable()->after('action_status');
            });
        }

        // 2) الأرقام → نصوص (لو العمود الرقمي موجود)
        if (Schema::hasColumn('user_logs', 'action_status')) {
            DB::statement("
                UPDATE `user_logs` SET `action_text` =
                    CASE `action_status`
                        WHEN 1 THEN 'إضافة'
                        WHEN 2 THEN 'تعديل'
                        WHEN 3 THEN 'تعليق'
                        WHEN 4 THEN 'حذف'
                        WHEN 5 THEN 'تغيير حالة المنشور'
                        ELSE `action_text`
                    END
            ");
        }

        // 3) احذف الرقمي وأعد تسمية النصّي
        if (Schema::hasColumn('user_logs', 'action_status')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->dropColumn('action_status');
            });
        }
        if (Schema::hasColumn('user_logs', 'action_text')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->renameColumn('action_text', 'action_status');
            });
        }
    }
};
