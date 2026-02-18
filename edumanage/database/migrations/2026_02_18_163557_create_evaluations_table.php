<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('student_ecu_enrollments');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('evaluations');
        
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->foreignId('ecu_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['exam', 'cc', 'tp', 'project', 'oral'])->default('exam');
            $table->enum('session', ['normal', 'rattrapage'])->default('normal');
            $table->date('date')->nullable();
            $table->decimal('coefficient', 5, 2)->default(1);
            $table->decimal('max_score', 5, 2)->default(20);
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['ecu_id', 'academic_year_id']);
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('is_absent')->default(false);
            $table->boolean('is_excused')->default(false);
            $table->text('comment')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->unique(['evaluation_id', 'student_id']);
        });

        Schema::create('student_ecu_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('ecu_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['enrolled', 'validated', 'failed', 'exempted'])->default('enrolled');
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->boolean('credits_acquired')->default(false);
            $table->timestamps();

            $table->unique(['student_id', 'ecu_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_ecu_enrollments');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('evaluations');
    }
};
