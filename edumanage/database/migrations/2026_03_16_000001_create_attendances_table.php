<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('session_date');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->integer('late_minutes')->nullable();
            $table->text('excuse_reason')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('marked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['schedule_id', 'student_id', 'session_date'], 'attendance_unique');
            $table->index(['student_id', 'session_date']);
            $table->index(['schedule_id', 'session_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
