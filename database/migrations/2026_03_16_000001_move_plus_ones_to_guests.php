<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->tinyInteger('plus_ones')->default(0)->after('attends_ceremony');
        });

        Schema::table('rsvps', function (Blueprint $table) {
            $table->dropColumn('plus_ones');
        });
    }

    public function down(): void
    {
        Schema::table('rsvps', function (Blueprint $table) {
            $table->tinyInteger('plus_ones')->default(0)->after('attending');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn('plus_ones');
        });
    }
};
