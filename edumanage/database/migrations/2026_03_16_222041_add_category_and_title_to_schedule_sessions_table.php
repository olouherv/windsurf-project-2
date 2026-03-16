<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schedule_sessions', function (Blueprint $table) {
            $table->enum('category', ['course', 'activity'])->default('course')->after('id');
            $table->string('title')->nullable()->after('category');
            $table->foreignId('ecu_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedule_sessions', function (Blueprint $table) {
            $table->dropColumn(['category', 'title']);
        });
    }
};
