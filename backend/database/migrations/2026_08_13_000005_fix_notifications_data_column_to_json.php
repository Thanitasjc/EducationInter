<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Filament filters notifications with data->format (JSON path).
 * On PostgreSQL that requires json/jsonb, not text.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications') || ! Schema::hasColumn('notifications', 'data')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            $type = DB::selectOne(
                "select data_type from information_schema.columns where table_name = 'notifications' and column_name = 'data'"
            )?->data_type;

            if (in_array($type, ['json', 'jsonb'], true)) {
                return;
            }

            DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE jsonb USING data::jsonb');

            return;
        }

        if ($driver === 'mysql') {
            try {
                DB::statement('ALTER TABLE notifications MODIFY data JSON NOT NULL');
            } catch (\Throwable) {
                // Already JSON or unsupported.
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifications') || ! Schema::hasColumn('notifications', 'data')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE text USING data::text');
        }
    }
};
