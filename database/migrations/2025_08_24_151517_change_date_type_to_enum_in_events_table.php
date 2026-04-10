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
        if (!Schema::hasColumn('events', 'date_type_int')) {
            if (Schema::hasColumn('events', 'date_type')) {
                Schema::table('events', function (Blueprint $table) {
                    $table->unsignedBigInteger('date_type_int')->nullable()->after('date_type');
                });
            } else {
                Schema::table('events', function (Blueprint $table) {
                    $table->unsignedBigInteger('date_type_int')->nullable();
                });
            }
        }

        // 2) حوّل النصوص → أرقام (لو العمود النصّي موجود)
        if (Schema::hasColumn('events', 'date_type')) {
            DB::statement("
                UPDATE `events` SET `date_type_int` =
                    CASE TRIM(`date_type`)
                        WHEN 'وقت' THEN 1
                        WHEN 'تاريخ و وقت' THEN 2
                        ELSE `date_type_int`
                    END
            ");
        }

        // 3) احذف العمود النصّي القديم لو موجود
        if (Schema::hasColumn('events', 'date_type')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('date_type');
            });
        }

        // 4) أعد تسمية العمود المؤقت للاسم الأصلي
        if (Schema::hasColumn('events', 'date_type_int')) {
            Schema::table('events', function (Blueprint $table) {
                $table->renameColumn('date_type_int', 'date_type');
            });
        }

        // 5) (اختياري) ثبّت النوع NOT NULL
        if (Schema::hasColumn('events', 'date_type')) {
            Schema::table('events', function (Blueprint $table) {
                $table->unsignedBigInteger('date_type')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) عمود نصّي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('events', 'date_type_text')) {
            if (Schema::hasColumn('events', 'date_type')) {
                Schema::table('events', function (Blueprint $table) {
                    $table->string('date_type_text')->nullable()->after('date_type');
                });
            } else {
                Schema::table('events', function (Blueprint $table) {
                    $table->string('date_type_text')->nullable();
                });
            }
        }

        // 2) الأرقام → نصوص (لو العمود الرقمي موجود)
        if (Schema::hasColumn('events', 'date_type')) {
            DB::statement("
                UPDATE `events` SET `date_type_text` =
                    CASE `date_type`
                        WHEN 1 THEN 'وقت'
                        WHEN 2 THEN 'تاريخ و وقت'
                        ELSE `date_type_text`
                    END
            ");
        }

        // 3) احذف الرقمي وأعد تسمية النصّي
        if (Schema::hasColumn('events', 'date_type')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('date_type');
            });
        }
        if (Schema::hasColumn('events', 'date_type_text')) {
            Schema::table('events', function (Blueprint $table) {
                $table->renameColumn('date_type_text', 'date_type');
            });
        }
    }
};
