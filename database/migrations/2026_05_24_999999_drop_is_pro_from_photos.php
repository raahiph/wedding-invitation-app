<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('photos', 'is_pro')) {
            DB::statement('ALTER TABLE photos DROP COLUMN is_pro');
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('photos', 'is_pro')) {
            DB::statement('ALTER TABLE photos ADD COLUMN is_pro TINYINT(1) NOT NULL DEFAULT 0 AFTER approved');
        }
    }
};
