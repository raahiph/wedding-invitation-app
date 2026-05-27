<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('album_photos', 'hidden')) {
            Schema::table('album_photos', function (Blueprint $table) {
                $table->boolean('hidden')->default(false)->after('sort_order');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('album_photos', 'hidden')) {
            Schema::table('album_photos', function (Blueprint $table) {
                $table->dropColumn('hidden');
            });
        }
    }
};
