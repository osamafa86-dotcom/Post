<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add indexes for better query performance
        $this->addIndexIfMissing('user_logs', ['actionable_id'], 'user_logs_actionable_id_index');
        $this->addIndexIfMissing('user_logs', ['actionable_type', 'actionable_id'], 'user_logs_actionable_type_id_index');
        $this->addIndexIfMissing('user_logs', ['created_at'], 'user_logs_created_at_index');
        $this->addIndexIfMissing('user_logs', ['user_id', 'action_status'], 'user_logs_user_id_action_status_index');
    }

    public function down(): void
    {
        $this->dropIndexIfExists('user_logs', 'user_logs_actionable_id_index');
        $this->dropIndexIfExists('user_logs', 'user_logs_actionable_type_id_index');
        $this->dropIndexIfExists('user_logs', 'user_logs_created_at_index');
        $this->dropIndexIfExists('user_logs', 'user_logs_user_id_action_status_index');
    }

    /* ---------- Helpers ---------- */

    private function addIndexIfMissing(string $table, array $columns, string $indexName): void
    {
        if (!$this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
                $t->index($columns, $indexName);
            });
        }
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $t) use ($indexName) {
                $t->dropIndex($indexName);
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
