<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sessions individuelles générées à partir des créneaux récurrents ou ajoutées manuellement
        Schema::create('schedule_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('ecu_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_group_id')->nullable()->constrained()->onDelete('set null');
            
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('type', ['cm', 'td', 'tp'])->default('cm');
            $table->enum('status', ['planned', 'completed', 'cancelled', 'rescheduled'])->default('planned');
            
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['ecu_id', 'academic_year_id', 'session_date']);
            $table->index(['teacher_id', 'session_date']);
        });

        // Modifier la table attendances pour lier aux sessions
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('schedule_session_id')->nullable()->after('schedule_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['schedule_session_id']);
            $table->dropColumn('schedule_session_id');
        });
        Schema::dropIfExists('schedule_sessions');
    }
};
