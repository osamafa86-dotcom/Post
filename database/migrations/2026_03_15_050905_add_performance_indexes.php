<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add missing indexes to frequently queried columns.
     */
    public function up(): void
    {
        $this->addIndexSafe('posts', 'publish_date');
        $this->addIndexSafe('posts', 'slug');
        $this->addIndexSafe('posts', ['publish_status', 'lang'], 'posts_status_lang_index');

        $this->addIndexSafe('post_relations', ['post_id', 'relationable_type'], 'pr_post_type_index');
        $this->addIndexSafe('post_relations', ['relationable_type', 'relationable_id'], 'pr_type_id_index');

        $this->addIndexSafe('categories', 'lang');
        $this->addIndexSafe('categories', 'category_type');

        $this->addIndexSafe('tags', 'lang');
        $this->addIndexSafe('tags', 'tag_type');

        $this->addIndexSafe('files', 'extension');

        $this->addIndexSafe('model_has_files', ['model_type', 'model_id'], 'mhf_type_id_index');
        $this->addIndexSafe('model_has_files', 'model_column');

        $this->addIndexSafe('views', ['viewable_type', 'viewable_id'], 'views_type_id_index');

        $this->addIndexSafe('user_logs', ['actionable_type', 'actionable_id'], 'ul_type_id_index');

        $this->addIndexSafe('visitor_logs', 'created_at');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexSafe('posts', 'posts_publish_date_index');
        $this->dropIndexSafe('posts', 'posts_slug_index');
        $this->dropIndexSafe('posts', 'posts_status_lang_index');
        $this->dropIndexSafe('post_relations', 'pr_post_type_index');
        $this->dropIndexSafe('post_relations', 'pr_type_id_index');
        $this->dropIndexSafe('categories', 'categories_lang_index');
        $this->dropIndexSafe('categories', 'categories_category_type_index');
        $this->dropIndexSafe('tags', 'tags_lang_index');
        $this->dropIndexSafe('tags', 'tags_tag_type_index');
        $this->dropIndexSafe('files', 'files_extension_index');
        $this->dropIndexSafe('model_has_files', 'mhf_type_id_index');
        $this->dropIndexSafe('model_has_files', 'model_has_files_model_column_index');
        $this->dropIndexSafe('views', 'views_type_id_index');
        $this->dropIndexSafe('user_logs', 'ul_type_id_index');
        $this->dropIndexSafe('visitor_logs', 'visitor_logs_created_at_index');
    }

    /**
     * Add index only if it doesn't already exist.
     */
    private function addIndexSafe(string $table, string|array $columns, ?string $name = null): void
    {
        $indexName = $name ?? $this->generateIndexName($table, $columns);

        $exists = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        if (empty($exists)) {
            Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
                $t->index($columns, $indexName);
            });
        }
    }

    /**
     * Drop index only if it exists.
     */
    private function dropIndexSafe(string $table, string $indexName): void
    {
        $exists = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        if (!empty($exists)) {
            Schema::table($table, function (Blueprint $t) use ($indexName) {
                $t->dropIndex($indexName);
            });
        }
    }

    private function generateIndexName(string $table, string|array $columns): string
    {
        $cols = is_array($columns) ? implode('_', $columns) : $columns;
        return "{$table}_{$cols}_index";
    }
};
