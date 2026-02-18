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
        Schema::table('vacataire_contracts', function (Blueprint $table) {
            $table->foreignId('ecu_id')->nullable()->after('academic_year_id')->constrained()->onDelete('set null');
            $table->enum('teaching_type', ['cm', 'td', 'tp', 'all'])->default('all')->after('ecu_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacataire_contracts', function (Blueprint $table) {
            $table->dropForeign(['ecu_id']);
            $table->dropColumn(['ecu_id', 'teaching_type']);
        });
    }
};
