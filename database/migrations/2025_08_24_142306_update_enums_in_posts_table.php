<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) أعمدة مؤقتة رقمية إن لم تكن موجودة
        if (!Schema::hasColumn('posts', 'publish_status_int')) {
            if (Schema::hasColumn('posts', 'publish_status')) {
                Schema::table('posts', function (Blueprint $table) {
                    $table->unsignedBigInteger('publish_status_int')->nullable()->after('publish_status');
                });
            } else {
                Schema::table('posts', function (Blueprint $table) {
                    $table->unsignedBigInteger('publish_status_int')->nullable();
                });
            }
        }

        if (!Schema::hasColumn('posts', 'image_size_int')) {
            if (Schema::hasColumn('posts', 'image_size')) {
                Schema::table('posts', function (Blueprint $table) {
                    $table->unsignedBigInteger('image_size_int')->nullable()->after('image_size');
                });
            } else {
                Schema::table('posts', function (Blueprint $table) {
                    $table->unsignedBigInteger('image_size_int')->nullable();
                });
            }
        }

        if (!Schema::hasColumn('posts', 'news_type_int')) {
            if (Schema::hasColumn('posts', 'news_type')) {
                Schema::table('posts', function (Blueprint $table) {
                    $table->unsignedBigInteger('news_type_int')->nullable()->after('news_type');
                });
            } else {
                Schema::table('posts', function (Blueprint $table) {
                    $table->unsignedBigInteger('news_type_int')->nullable();
                });
            }
        }

        // 2) تحويل النصوص → أرقام (فقط إذا الأعمدة النصية موجودة)
        if (Schema::hasColumn('posts', 'publish_status')) {
            DB::statement("
                UPDATE `posts` SET `publish_status_int` =
                    CASE TRIM(`publish_status`)
                        WHEN 'تم النشر' THEN 1
                        WHEN 'مسودة'    THEN 2
                        WHEN 'معلقة'    THEN 3
                        ELSE `publish_status_int`
                    END
            ");
        }

        if (Schema::hasColumn('posts', 'image_size')) {
            DB::statement("
                UPDATE `posts` SET `image_size_int` =
                    CASE TRIM(`image_size`)
                        WHEN 'صورة كبيرة'          THEN 1
                        WHEN 'صورة متوسطة'         THEN 2
                        WHEN 'صورة صغيرة'          THEN 3
                        WHEN 'مقالة'               THEN 4
                        WHEN 'محاذي لمنشور آخر'    THEN 5
                        ELSE `image_size_int`
                    END
            ");
        }

        if (Schema::hasColumn('posts', 'news_type')) {
            DB::statement("
                UPDATE `posts` SET `news_type_int` =
                    CASE TRIM(`news_type`)
                        WHEN 'خبر رئيسي' THEN 1
                        WHEN 'خبر فرعي'  THEN 2
                        ELSE `news_type_int`
                    END
            ");
        }

        // 3) حذف الأعمدة النصية القديمة لو موجودة
        if (Schema::hasColumn('posts', 'publish_status')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('publish_status');
            });
        }
        if (Schema::hasColumn('posts', 'image_size')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('image_size');
            });
        }
        if (Schema::hasColumn('posts', 'news_type')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('news_type');
            });
        }

        // 4) إعادة التسمية للأسماء الأصلية
        if (Schema::hasColumn('posts', 'publish_status_int')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->renameColumn('publish_status_int', 'publish_status');
            });
        }
        if (Schema::hasColumn('posts', 'image_size_int')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->renameColumn('image_size_int', 'image_size');
            });
        }
        if (Schema::hasColumn('posts', 'news_type_int')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->renameColumn('news_type_int', 'news_type');
            });
        }

        // 5) تثبيت النوع والافتراضي (غير قابل لـ NULL)
        if (Schema::hasColumn('posts', 'publish_status')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedBigInteger('publish_status')->nullable()->change();
            });
        }
        if (Schema::hasColumn('posts', 'image_size')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedBigInteger('image_size')->nullable()->change();
            });
        }
        if (Schema::hasColumn('posts', 'news_type')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedBigInteger('news_type')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) أعمدة نصية مؤقتة إن لم تكن موجودة
        if (!Schema::hasColumn('posts', 'publish_status_text')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('publish_status_text')->nullable()->after('publish_status');
            });
        }
        if (!Schema::hasColumn('posts', 'image_size_text')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('image_size_text')->nullable()->after('image_size');
            });
        }
        if (!Schema::hasColumn('posts', 'news_type_text')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('news_type_text')->nullable()->after('news_type');
            });
        }

        // 2) تحويل الأرقام → نصوص (إذا الأعمدة الرقمية موجودة)
        if (Schema::hasColumn('posts', 'publish_status')) {
            DB::statement("
                UPDATE `posts` SET `publish_status_text` =
                    CASE `publish_status`
                        WHEN 1 THEN 'تم النشر'
                        WHEN 2 THEN 'مسودة'
                        WHEN 3 THEN 'معلقة'
                        ELSE `publish_status_text`
                    END
            ");
        }
        if (Schema::hasColumn('posts', 'image_size')) {
            DB::statement("
                UPDATE `posts` SET `image_size_text` =
                    CASE `image_size`
                        WHEN 1 THEN 'صورة كبيرة'
                        WHEN 2 THEN 'صورة متوسطة'
                        WHEN 3 THEN 'صورة صغيرة'
                        WHEN 4 THEN 'مقالة'
                        WHEN 5 THEN 'محاذي لمنشور آخر'
                        ELSE `image_size_text`
                    END
            ");
        }
        if (Schema::hasColumn('posts', 'news_type')) {
            DB::statement("
                UPDATE `posts` SET `news_type_text` =
                    CASE `news_type`
                        WHEN 1 THEN 'خبر رئيسي'
                        WHEN 2 THEN 'خبر فرعي'
                        ELSE `news_type_text`
                    END
            ");
        }

        // 3) حذف الأعمدة الرقمية وإرجاع النصية
        if (Schema::hasColumn('posts', 'publish_status')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('publish_status');
            });
        }
        if (Schema::hasColumn('posts', 'image_size')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('image_size');
            });
        }
        if (Schema::hasColumn('posts', 'news_type')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('news_type');
            });
        }

        if (Schema::hasColumn('posts', 'publish_status_text')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->renameColumn('publish_status_text', 'publish_status');
            });
        }
        if (Schema::hasColumn('posts', 'image_size_text')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->renameColumn('image_size_text', 'image_size');
            });
        }
        if (Schema::hasColumn('posts', 'news_type_text')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->renameColumn('news_type_text', 'news_type');
            });
        }
    }
};
