<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('answer_options')) {
            return;
        }

        match (Schema::getConnection()->getDriverName()) {
            'pgsql' => DB::statement('ALTER TABLE answer_options ALTER COLUMN text TYPE text'),
            'mysql', 'mariadb' => DB::statement('ALTER TABLE answer_options MODIFY `text` TEXT'),
            default => null, // SQLite: нет ALTER COLUMN … TYPE; длинные строки уже допустимы
        };
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('answer_options')) {
            return;
        }

        match (Schema::getConnection()->getDriverName()) {
            'pgsql' => DB::statement('ALTER TABLE answer_options ALTER COLUMN text TYPE varchar(255)'),
            'mysql', 'mariadb' => DB::statement('ALTER TABLE answer_options MODIFY `text` VARCHAR(255)'),
            default => null,
        };
    }
};
