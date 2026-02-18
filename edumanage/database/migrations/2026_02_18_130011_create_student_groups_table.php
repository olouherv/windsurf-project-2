<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['td', 'tp', 'promotion'])->default('promotion');
            $table->integer('max_students')->nullable();
            $table->unsignedBigInteger('moodle_cohort_id')->nullable()->index();
            $table->timestamps();

            $table->unique(['program_year_id', 'academic_year_id', 'name'], 'student_group_unique');
        });

        Schema::create('student_group_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['student_group_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_group_student');
        Schema::dropIfExists('student_groups');
    }
};
