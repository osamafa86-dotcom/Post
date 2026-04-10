<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Indexes to speed up dashboard statistics queries.
     */
    public function up(): void
    {
        // visitor_logs: most queried table in dashboard (7+ queries)
        Schema::table('visitor_logs', function (Blueprint $table) {
            $table->index('visited_at', 'idx_visitor_logs_visited_at');
            $table->index(['visited_at', 'device_type'], 'idx_visitor_logs_visited_device');
            $table->index(['visited_at', 'referrer'], 'idx_visitor_logs_visited_referrer');
            $table->index(['visited_at', 'country'], 'idx_visitor_logs_visited_country');
        });

        // views: queried for post views KPI + chart
        Schema::table('views', function (Blueprint $table) {
            $table->index(['viewable_type', 'created_at'], 'idx_views_type_created');
        });

        // posts: queried for posts count + drafts KPI
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['publish_status', 'publish_date'], 'idx_posts_status_date');
        });

        // files: queried for file upload KPI
        Schema::table('files', function (Blueprint $table) {
            $table->index(['created_at', 'extension'], 'idx_files_created_ext');
        });

        // participants: queried for participants KPI
        Schema::table('participants', function (Blueprint $table) {
            $table->index(['created_at', 'type'], 'idx_participants_created_type');
        });

        // materials: queried for materials count
        Schema::table('materials', function (Blueprint $table) {
            $table->index(['created_at', 'type'], 'idx_materials_created_type');
        });

        // post_relations & material_relations: queried for most used tags/categories
        Schema::table('post_relations', function (Blueprint $table) {
            $table->index(['relationable_type', 'relationable_id'], 'idx_post_rel_type_id');
        });

        Schema::table('material_relations', function (Blueprint $table) {
            $table->index(['relationable_type', 'relationable_id'], 'idx_material_rel_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitor_logs', function (Blueprint $table) {
            $table->dropIndex('idx_visitor_logs_visited_at');
            $table->dropIndex('idx_visitor_logs_visited_device');
            $table->dropIndex('idx_visitor_logs_visited_referrer');
            $table->dropIndex('idx_visitor_logs_visited_country');
        });

        Schema::table('views', function (Blueprint $table) {
            $table->dropIndex('idx_views_type_created');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('idx_posts_status_date');
        });

        Schema::table('files', function (Blueprint $table) {
            $table->dropIndex('idx_files_created_ext');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropIndex('idx_participants_created_type');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropIndex('idx_materials_created_type');
        });

        Schema::table('post_relations', function (Blueprint $table) {
            $table->dropIndex('idx_post_rel_type_id');
        });

        Schema::table('material_relations', function (Blueprint $table) {
            $table->dropIndex('idx_material_rel_type_id');
        });
    }
};
