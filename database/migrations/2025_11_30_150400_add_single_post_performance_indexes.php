<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Optimize single post page queries
     */
    public function up(): void
    {
        // Optimize views table for aggregation queries (polymorphic views)
        if (Schema::hasTable('views')) {
            Schema::table('views', function (Blueprint $table) {
                // Composite index for polymorphic views aggregation
                if (!$this->indexExists('views', 'views_viewable_idx')) {
                    $table->index(['viewable_type', 'viewable_id', 'views_number'], 'views_viewable_idx');
                }
            });
        }

        // Optimize post_relations table for category filtering
        if (Schema::hasTable('post_relations')) {
            Schema::table('post_relations', function (Blueprint $table) {
                // Composite index for filtering posts by category
                if (!$this->indexExists('post_relations', 'pr_post_type_id_idx')) {
                    $table->index(['post_id', 'relationable_type', 'relationable_id'], 'pr_post_type_id_idx');
                }
                
                // Reverse lookup for finding posts in a category
                if (!$this->indexExists('post_relations', 'pr_type_id_idx')) {
                    $table->index(['relationable_type', 'relationable_id'], 'pr_type_id_idx');
                }
            });
        }

        // Optimize posts table for id+slug lookup (single post)
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                // Compound unique index for fast single post lookup
                if (!$this->indexExists('posts', 'posts_id_slug_idx')) {
                    $table->index(['id', 'slug'], 'posts_id_slug_idx');
                }
            });
        }

        // Optimize categories table for random selection
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                // Index on id for faster random queries
                if (!$this->indexExists('categories', 'categories_id_idx')) {
                    $table->index(['id'], 'categories_id_idx');
                }
            });
        }

        // Add composite index for files attachments
        if (Schema::hasTable('attachments')) {
            Schema::table('attachments', function (Blueprint $table) {
                if (!$this->indexExists('attachments', 'att_attachable_order_idx')) {
                    $table->index(['attachable_type', 'attachable_id', 'attachment_order'], 'att_attachable_order_idx');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('views')) {
            Schema::table('views', function (Blueprint $table) {
                if ($this->indexExists('views', 'views_viewable_idx')) {
                    $table->dropIndex('views_viewable_idx');
                }
            });
        }

        if (Schema::hasTable('post_relations')) {
            Schema::table('post_relations', function (Blueprint $table) {
                if ($this->indexExists('post_relations', 'pr_post_type_id_idx')) {
                    $table->dropIndex('pr_post_type_id_idx');
                }
                if ($this->indexExists('post_relations', 'pr_type_id_idx')) {
                    $table->dropIndex('pr_type_id_idx');
                }
            });
        }

        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                if ($this->indexExists('posts', 'posts_id_slug_idx')) {
                    $table->dropIndex('posts_id_slug_idx');
                }
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if ($this->indexExists('categories', 'categories_id_idx')) {
                    $table->dropIndex('categories_id_idx');
                }
            });
        }

        if (Schema::hasTable('attachments')) {
            Schema::table('attachments', function (Blueprint $table) {
                if ($this->indexExists('attachments', 'att_attachable_order_idx')) {
                    $table->dropIndex('att_attachable_order_idx');
                }
            });
        }
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $index): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }
        
        try {
            $schema = DB::getDatabaseName();
            $res = DB::select(
                'SELECT COUNT(1) AS c
                 FROM information_schema.statistics
                 WHERE table_schema = ? AND table_name = ? AND index_name = ?',
                [$schema, $table, $index]
            );
            return !empty($res) && (int)$res[0]->c > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
};
