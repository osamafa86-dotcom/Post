<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        if (!Schema::hasColumn('user_logs', 'actionable_type_int')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->unsignedTinyInteger('actionable_type_int')->nullable()->after('actionable_type');
            });
        }


        if (Schema::hasColumn('user_logs', 'actionable_type')) {
            DB::statement("
                UPDATE user_logs SET actionable_type_int =
                    CASE actionable_type
                        WHEN 'App\\\\Models\\\\Material'       THEN 1
                        WHEN 'App\\\\Models\\\\Post'          THEN 2
                        WHEN 'App\\\\Models\\\\Participant'   THEN 3
                        WHEN 'App\\\\Models\\\\Category'      THEN 4
                        WHEN 'App\\\\Models\\\\PodcastAlbum'  THEN 5
                        WHEN 'App\\\\Models\\\\VideoAlbum'    THEN 6
                        WHEN 'App\\\\Models\\\\Setting'       THEN 7
                        WHEN 'App\\\\Models\\\\BreakingNews'  THEN 8
                        WHEN 'App\\\\Models\\\\Type'          THEN 9
                        WHEN 'App\\\\Models\\\\SpecialFile'   THEN 10
                        WHEN 'App\\\\Models\\\\Tag'           THEN 11
                        WHEN 'App\\\\Models\\\\Quote'         THEN 12
                        WHEN 'App\\\\Models\\\\SpecialPage'   THEN 13
                        WHEN 'App\\\\Models\\\\Advertisement' THEN 14
                        WHEN 'App\\\\Models\\\\PodcastTrack'  THEN 15
                        WHEN 'App\\\\Models\\\\VideoTrack'    THEN 16
                        WHEN 'App\\\\Models\\\\Alert'         THEN 17
                        WHEN 'App\\\\Models\\\\User'          THEN 18
                        WHEN 'App\\\\Models\\\\ContactUs'     THEN 19
                        WHEN 'App\\\\Models\\\\SendNews'      THEN 20
                        ELSE actionable_type_int
                    END
            ");
        }


        if (Schema::hasColumn('user_logs', 'actionable_type')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->dropColumn('actionable_type');
            });
        }


        if (Schema::hasColumn('user_logs', 'actionable_type_int')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->renameColumn('actionable_type_int', 'actionable_type');
            });
        }


        if (Schema::hasColumn('user_logs', 'actionable_type')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->unsignedTinyInteger('actionable_type')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {

        if (!Schema::hasColumn('user_logs', 'actionable_type_text')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->string('actionable_type_text')->nullable()->after('actionable_type');
            });
        }


        if (Schema::hasColumn('user_logs', 'actionable_type')) {
            DB::statement("
                UPDATE user_logs SET actionable_type_text =
                    CASE actionable_type
                        WHEN 1  THEN 'App\\\\Models\\\\Material'
                        WHEN 2  THEN 'App\\\\Models\\\\Post'
                        WHEN 3  THEN 'App\\\\Models\\\\Participant'
                        WHEN 4  THEN 'App\\\\Models\\\\Category'
                        WHEN 5  THEN 'App\\\\Models\\\\PodcastAlbum'
                        WHEN 6  THEN 'App\\\\Models\\\\VideoAlbum'
                        WHEN 7  THEN 'App\\\\Models\\\\Setting'
                        WHEN 8  THEN 'App\\\\Models\\\\BreakingNews'
                        WHEN 9  THEN 'App\\\\Models\\\\Type'
                        WHEN 10 THEN 'App\\\\Models\\\\SpecialFile'
                        WHEN 11 THEN 'App\\\\Models\\\\Tag'
                        WHEN 12 THEN 'App\\\\Models\\\\Quote'
                        WHEN 13 THEN 'App\\\\Models\\\\SpecialPage'
                        WHEN 14 THEN 'App\\\\Models\\\\Advertisement'
                        WHEN 15 THEN 'App\\\\Models\\\\PodcastTrack'
                        WHEN 16 THEN 'App\\\\Models\\\\VideoTrack'
                        WHEN 17 THEN 'App\\\\Models\\\\Alert'
                        WHEN 18 THEN 'App\\\\Models\\\\User'
                        WHEN 19 THEN 'App\\\\Models\\\\ContactUs'
                        WHEN 20 THEN 'App\\\\Models\\\\SendNews'
                        ELSE actionable_type_text
                    END
            ");
        }


        if (Schema::hasColumn('user_logs', 'actionable_type')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->dropColumn('actionable_type');
            });
        }


        if (Schema::hasColumn('user_logs', 'actionable_type_text')) {
            Schema::table('user_logs', function (Blueprint $table) {
                $table->renameColumn('actionable_type_text', 'actionable_type');
            });
        }
    }
};
