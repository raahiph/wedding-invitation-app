<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE photos DROP COLUMN is_pro');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE photos ADD COLUMN is_pro TINYINT(1) NOT NULL DEFAULT 0 AFTER approved');
    }
};
