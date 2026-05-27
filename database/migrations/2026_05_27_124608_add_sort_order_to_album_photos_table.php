<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('album_photos', 'sort_order')) {
            Schema::table('album_photos', function (Blueprint $table) {
                $table->unsignedSmallInteger('sort_order')->default(0)->after('is_group_cover');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('album_photos', 'sort_order')) {
            Schema::table('album_photos', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};
