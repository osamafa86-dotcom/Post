<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add composite indexes for post_relations table to improve filtering performance
        Schema::table('post_relations', function (Blueprint $table) {
            // Composite index for filtering by post_id + relationable_type + is_main
            if (!$this->indexExists('post_relations', 'pr_post_type_main_idx')) {
                $table->index(['post_id', 'relationable_type', 'relationable_is_main'], 'pr_post_type_main_idx');
            }
            
            // Composite index for filtering by relationable (reverse lookup)
            if (!$this->indexExists('post_relations', 'pr_relationable_post_idx')) {
                $table->index(['relationable_type', 'relationable_id', 'post_id'], 'pr_relationable_post_idx');
            }
            
            // Index for main relations only
            if (!$this->indexExists('post_relations', 'pr_main_post_idx')) {
                $table->index(['relationable_is_main', 'post_id'], 'pr_main_post_idx');
            }
        });

        // Add fulltext index for posts table to improve search performance
        if (DB::getDriverName() === 'mysql') {
            if (!$this->indexExists('posts', 'posts_search_idx')) {
                DB::statement('ALTER TABLE posts ADD FULLTEXT INDEX posts_search_idx (title, description)');
            }
        }
        
        // Add index for publish_date sorting
        Schema::table('posts', function (Blueprint $table) {
            if (!$this->indexExists('posts', 'posts_publish_date_status_idx')) {
                $table->index(['publish_status', 'publish_date'], 'posts_publish_date_status_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_relations', function (Blueprint $table) {
            $table->dropIndex('pr_post_type_main_idx');
            $table->dropIndex('pr_relationable_post_idx');
            $table->dropIndex('pr_main_post_idx');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE posts DROP INDEX posts_search_idx');
        }
        
        Schema::table('posts', function (Blueprint $table) {
            if ($this->indexExists('posts', 'posts_publish_date_status_idx')) {
                $table->dropIndex('posts_publish_date_status_idx');
            }
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);
        return !empty($indexes);
    }
};
