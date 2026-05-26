<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('album_photos', 'is_group_cover')) {
            Schema::table('album_photos', function (Blueprint $table) {
                $table->boolean('is_group_cover')->default(false)->after('group_key');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('album_photos', 'is_group_cover')) {
            Schema::table('album_photos', function (Blueprint $table) {
                $table->dropColumn('is_group_cover');
            });
        }
    }
};
