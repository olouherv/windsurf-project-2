<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vacataire_contracts', function (Blueprint $table) {
            $table->foreignId('university_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        DB::statement('UPDATE vacataire_contracts SET university_id = (SELECT university_id FROM teachers WHERE teachers.id = vacataire_contracts.teacher_id) WHERE university_id IS NULL');
    }

    public function down(): void
    {
        Schema::table('vacataire_contracts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('university_id');
        });
    }
};
