<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_ecu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('ecu_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->boolean('is_responsible')->default(false);
            $table->enum('teaching_type', ['cm', 'td', 'tp', 'all'])->default('all');
            $table->timestamps();

            $table->unique(['teacher_id', 'ecu_id', 'academic_year_id', 'teaching_type'], 'teacher_ecu_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_ecu');
    }
};
