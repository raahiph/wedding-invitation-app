<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->string('nickname', 120)->nullable()->after('name');
        });

        Schema::table('rsvps', function (Blueprint $table) {
            $table->dropColumn('full_name');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn('nickname');
        });

        Schema::table('rsvps', function (Blueprint $table) {
            $table->string('full_name', 120)->nullable()->after('guest_id');
        });
    }
};
