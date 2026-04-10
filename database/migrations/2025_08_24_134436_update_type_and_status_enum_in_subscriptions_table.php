<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) أضف أعمدة مؤقتة رقمية إن لم تكن موجودة
        if (!Schema::hasColumn('subscriptions', 'status_int')) {
            if (Schema::hasColumn('subscriptions', 'status')) {
                Schema::table('subscriptions', function (Blueprint $table) {
                    $table->unsignedBigInteger('status_int')->nullable()->after('status');
                });
            } else {
                Schema::table('subscriptions', function (Blueprint $table) {
                    $table->unsignedBigInteger('status_int')->nullable();
                });
            }
        }

        if (!Schema::hasColumn('subscriptions', 'type_int')) {
            if (Schema::hasColumn('subscriptions', 'type')) {
                Schema::table('subscriptions', function (Blueprint $table) {
                    $table->unsignedBigInteger('type_int')->nullable()->after('type');
                });
            } else {
                Schema::table('subscriptions', function (Blueprint $table) {
                    $table->unsignedBigInteger('type_int')->nullable();
                });
            }
        }

        // 2) تحويل النصوص إلى أرقام (فقط إذا الأعمدة النصية موجودة)
        if (Schema::hasColumn('subscriptions', 'status')) {
            DB::statement("
                UPDATE `subscriptions` SET `status_int` =
                    CASE `status`
                        WHEN 'فعال'      THEN 1
                        WHEN 'غير فعال' THEN 2
                        ELSE `status_int`
                    END
            ");
        }

        if (Schema::hasColumn('subscriptions', 'type')) {
            DB::statement("
                UPDATE `subscriptions` SET `type_int` =
                    CASE `type`
                        WHEN 'شهري' THEN 1
                        WHEN 'سنوي' THEN 2
                        ELSE `type_int`
                    END
            ");
        }

        // 3) حذف الأعمدة النصية القديمة لو موجودة
        if (Schema::hasColumn('subscriptions', 'status')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        if (Schema::hasColumn('subscriptions', 'type')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        // 4) إعادة تسمية الأعمدة المؤقتة إلى الأسماء الأصلية
        if (Schema::hasColumn('subscriptions', 'status_int')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->renameColumn('status_int', 'status');
            });
        }

        if (Schema::hasColumn('subscriptions', 'type_int')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->renameColumn('type_int', 'type');
            });
        }

        // 5) تثبيت النوع والافتراضي (اختياري لكنه مُستحسن)
        if (Schema::hasColumn('subscriptions', 'status')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->unsignedBigInteger('status')->nullable()->change();
            });
        }
        if (Schema::hasColumn('subscriptions', 'type')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->unsignedBigInteger('type')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) أعمدة نصّية مؤقتة إن لم تكن موجودة
        if (!Schema::hasColumn('subscriptions', 'status_text')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->string('status_text')->nullable()->after('status');
            });
        }
        if (!Schema::hasColumn('subscriptions', 'type_text')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->string('type_text')->nullable()->after('type');
            });
        }

        // 2) تحويل الأرقام إلى نصوص (إذا الأعمدة الرقمية موجودة)
        if (Schema::hasColumn('subscriptions', 'status')) {
            DB::statement("
                UPDATE `subscriptions` SET `status_text` =
                    CASE `status`
                        WHEN 1 THEN 'فعال'
                        WHEN 2 THEN 'غير فعال'
                        ELSE `status_text`
                    END
            ");
        }
        if (Schema::hasColumn('subscriptions', 'type')) {
            DB::statement("
                UPDATE `subscriptions` SET `type_text` =
                    CASE `type`
                        WHEN 1 THEN 'شهري'
                        WHEN 2 THEN 'سنوي'
                        ELSE `type_text`
                    END
            ");
        }

        // 3) حذف الأعمدة الرقمية وإرجاع النصية
        if (Schema::hasColumn('subscriptions', 'status')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
        if (Schema::hasColumn('subscriptions', 'type')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasColumn('subscriptions', 'status_text')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->renameColumn('status_text', 'status');
            });
        }
        if (Schema::hasColumn('subscriptions', 'type_text')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->renameColumn('type_text', 'type');
            });
        }
    }
};
