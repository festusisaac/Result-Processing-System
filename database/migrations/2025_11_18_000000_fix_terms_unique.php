<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration needs to be defensive because SQLite index names
        // may differ depending on how the previous migrations were applied.
        // We'll inspect the sqlite_master table for any existing indexes and
        // drop them explicitly before adding our composite unique index.
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            // possible old index names
            $possible = [
                'terms_term_name_unique',
                'terms_term_name_session_unique',
                'unique_term_name_per_session',
            ];

            $indexes = $connection->select("SELECT name FROM sqlite_master WHERE type='index' AND tbl_name='terms'");
            $existing = array_map(fn($r) => $r->name, $indexes);

            foreach ($possible as $name) {
                if (in_array($name, $existing, true)) {
                    $connection->statement('DROP INDEX IF EXISTS "' . $name . '"');
                }
            }

            // Finally add the composite unique index
            $connection->statement('CREATE UNIQUE INDEX IF NOT EXISTS "unique_term_name_per_session" ON "terms" ("term_name", "session_id")');
        } else {
            Schema::table('terms', function (Blueprint $table) {
                try {
                    $table->dropUnique(['term_name']);
                } catch (\Throwable $e) {
                    // ignore if doesn't exist
                }

                $table->unique(['term_name', 'session_id'], 'terms_term_name_session_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            try {
                $table->dropUnique('terms_term_name_session_unique');
            } catch (\Throwable $e) {
                // ignore
            }

            $table->unique('term_name');
        });
    }
};
