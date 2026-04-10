<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // visitor_logs
        $this->addIndexIfMissing('visitor_logs', ['visited_at'], 'visitor_logs_visited_at_index');
        $this->addIndexIfMissing('visitor_logs', ['device_type','visited_at'], 'visitor_logs_device_type_visited_at_index');

        // views
        $this->addIndexIfMissing('views', ['created_at'], 'views_created_at_index');
        $this->addIndexIfMissing('views', ['viewable_type','created_at'], 'views_viewable_type_created_at_index');

        // posts
        $this->addIndexIfMissing('posts', ['publish_status','publish_date'], 'posts_publish_status_publish_date_index');

        // materials
        $this->addIndexIfMissing('materials', ['created_at'], 'materials_created_at_index');
        $this->addIndexIfMissing('materials', ['type'], 'materials_type_index');

        // events
        $this->addIndexIfMissing('events', ['created_at'], 'events_created_at_index');

        // participants
        $this->addIndexIfMissing('participants', ['type','created_at'], 'participants_type_created_at_index');

        // files
        $this->addIndexIfMissing('files', ['created_at'], 'files_created_at_index');
        $this->addIndexIfMissing('files', ['extension'], 'files_extension_index');

        // user_logs
        $this->addIndexIfMissing('user_logs', ['actionable_type','action_status','created_at'], 'user_logs_actionable_type_action_status_created_at_index');
    }

    public function down(): void
    {
        // visitor_logs
        $this->dropIndexIfExists('visitor_logs', 'visitor_logs_visited_at_index');
        $this->dropIndexIfExists('visitor_logs', 'visitor_logs_device_type_visited_at_index');

        // views
        $this->dropIndexIfExists('views', 'views_created_at_index');
        $this->dropIndexIfExists('views', 'views_viewable_type_created_at_index');

        // posts
        $this->dropIndexIfExists('posts', 'posts_publish_status_publish_date_index');

        // materials
        $this->dropIndexIfExists('materials', 'materials_created_at_index');
        $this->dropIndexIfExists('materials', 'materials_type_index');

        // events
        $this->dropIndexIfExists('events', 'events_created_at_index');

        // participants
        $this->dropIndexIfExists('participants', 'participants_type_created_at_index');

        // files
        $this->dropIndexIfExists('files', 'files_created_at_index');
        $this->dropIndexIfExists('files', 'files_extension_index');

        // user_logs
        $this->dropIndexIfExists('user_logs', 'user_logs_actionable_type_action_status_created_at_index');
    }

    /* ---------- Helpers ---------- */

    private function addIndexIfMissing(string $table, array $columns, string $indexName): void
    {
        if (! $this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
                $t->index($columns, $indexName);
            });
        }
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $t) use ($indexName) {
                $t->dropIndex($indexName); // يسقط بالاسم مباشرة
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $schema = DB::getDatabaseName();
        $res = DB::select(
            'SELECT COUNT(1) AS c
             FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$schema, $table, $indexName]
        );
        return !empty($res) && (int)$res[0]->c > 0;
    }
};
