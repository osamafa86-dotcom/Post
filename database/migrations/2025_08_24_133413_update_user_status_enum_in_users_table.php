<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) أضف عمود مؤقت رقمي إن لم يكن موجوداً
        if (!Schema::hasColumn('users', 'user_status_int')) {
            if (Schema::hasColumn('users', 'user_status')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->unsignedBigInteger('user_status_int')->nullable()->after('user_status');
                });
            } else {
                Schema::table('users', function (Blueprint $table) {
                    $table->unsignedBigInteger('user_status_int')->nullable();
                });
            }
        }

        // 2) حوّل القيم النصية إلى أرقام (فقط إذا العمود النصي موجود)
        if (Schema::hasColumn('users', 'user_status')) {
            DB::statement("
                UPDATE `users` SET `user_status_int` =
                    CASE `user_status`
                        WHEN 'مسؤول'        THEN 1
                        WHEN 'مدخل بيانات'  THEN 2
                        WHEN 'محرر'         THEN 3
                        WHEN 'معلق صوتي'    THEN 4
                        WHEN 'مشترك'        THEN 5
                        ELSE `user_status_int`
                    END
            ");
        }

        // 3) احذف العمود القديم النصّي لو موجود
        if (Schema::hasColumn('users', 'user_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('user_status');
            });
        }

        // 4) أعد تسمية العمود المؤقت للاسم الأصلي
        if (Schema::hasColumn('users', 'user_status_int')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('user_status_int', 'user_status');
            });
        }

        // 5) ثبّت النوع/الافتراضي (اختياري لكنه مُستحسن)
        if (Schema::hasColumn('users', 'user_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('user_status')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) أضف عمود نصّي مؤقت للرجوع إن لم يكن موجوداً
        if (!Schema::hasColumn('users', 'user_status_text')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('user_status_text')->nullable()->after('user_status');
            });
        }

        // 2) حوّل القيم الرقمية إلى نصوص (فقط إذا العمود الرقمي موجود)
        if (Schema::hasColumn('users', 'user_status')) {
            DB::statement("
                UPDATE `users` SET `user_status_text` =
                    CASE `user_status`
                        WHEN 1 THEN 'مسؤول'
                        WHEN 2 THEN 'مدخل بيانات'
                        WHEN 3 THEN 'محرر'
                        WHEN 4 THEN 'معلق صوتي'
                        WHEN 5 THEN 'مشترك'
                        ELSE `user_status_text`
                    END
            ");
        }

        // 3) احذف العمود الرقمي وأعد تسمية النصي للاسم الأصلي
        if (Schema::hasColumn('users', 'user_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('user_status');
            });
        }

        if (Schema::hasColumn('users', 'user_status_text')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('user_status_text', 'user_status');
            });
        }
    }
};
