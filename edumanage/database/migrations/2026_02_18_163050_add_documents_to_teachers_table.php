<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('cv_file')->nullable()->after('ifu');
            $table->string('rib_file')->nullable()->after('cv_file');
            $table->string('ifu_file')->nullable()->after('rib_file');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['cv_file', 'rib_file', 'ifu_file']);
        });
    }
};
