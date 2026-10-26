<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teamKey = $columnNames['team_foreign_key'] ?? 'sekolah_id';
        $modelKey = $columnNames['model_morph_key'] ?? 'model_id';
        $rolePivotKey = $columnNames['role_pivot_key'] ?? 'role_id';
        $permissionPivotKey = $columnNames['permission_pivot_key'] ?? 'permission_id';

        $this->makeRoleTeamNullable($tableNames['roles'], $teamKey);

        $this->makePivotTeamNullable(
            table: $tableNames['model_has_roles'],
            teamKey: $teamKey,
            pivotKey: $rolePivotKey,
            modelKey: $modelKey,
            uniqueName: 'model_has_roles_'.$teamKey.'_unique',
        );

        $this->makePivotTeamNullable(
            table: $tableNames['model_has_permissions'],
            teamKey: $teamKey,
            pivotKey: $permissionPivotKey,
            modelKey: $modelKey,
            uniqueName: 'model_has_permissions_'.$teamKey.'_unique',
        );
    }

    public function down(): void
    {
        // Sengaja tidak dikembalikan ke primary key lama.
        // Role global memakai pivot team NULL; mengembalikannya akan menghapus
        // kemampuan role global yang dibutuhkan web dan mobile.
    }

    private function makeRoleTeamNullable(string $table, string $teamKey): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        DB::statement("ALTER TABLE `{$table}` MODIFY `{$teamKey}` BIGINT UNSIGNED NULL");
    }

    private function makePivotTeamNullable(
        string $table,
        string $teamKey,
        string $pivotKey,
        string $modelKey,
        string $uniqueName,
    ): void {
        if (! Schema::hasTable($table)) {
            return;
        }

        $indexes = collect(Schema::getIndexes($table));

        // Primary key MySQL tidak bisa memuat kolom NULL. Ganti dengan unique
        // index agar role global (team NULL) dan role per sekolah bisa hidup bersama.
        if ($indexes->contains(fn (array $index) => ($index['primary'] ?? false) === true)) {
            DB::statement("ALTER TABLE `{$table}` DROP PRIMARY KEY");
        }

        DB::statement("ALTER TABLE `{$table}` MODIFY `{$teamKey}` BIGINT UNSIGNED NULL");

        $hasUnique = $indexes->contains(fn (array $index) => ($index['name'] ?? null) === $uniqueName);

        if (! $hasUnique) {
            Schema::table($table, function (Blueprint $blueprint) use ($teamKey, $pivotKey, $modelKey, $uniqueName) {
                $blueprint->unique([$teamKey, $pivotKey, $modelKey, 'model_type'], $uniqueName);
            });
        }
    }
};
