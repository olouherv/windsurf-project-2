<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('title')->nullable()->after('grade');
            $table->string('rib')->nullable()->after('phone');
            $table->string('ifu')->nullable()->after('rib');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['title', 'rib', 'ifu']);
        });
    }
};
