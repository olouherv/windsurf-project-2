<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite: to change FK/nullability we recreate the table.
        DB::statement('PRAGMA foreign_keys=OFF;');

        Schema::create('schedules__new', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ecu_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();

            $table->string('category')->default('course');
            $table->string('title')->nullable();
            $table->date('scheduled_date')->nullable();

            $table->enum('type', ['cm', 'td', 'tp'])->default('cm');
            $table->tinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_recurring')->default(true);

            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Copy existing data (new columns get defaults/null)
        DB::statement(
            "INSERT INTO schedules__new (id, ecu_id, teacher_id, room_id, student_group_id, academic_year_id, type, day_of_week, start_time, end_time, start_date, end_date, is_recurring, notes, created_at, updated_at)
             SELECT id, ecu_id, teacher_id, room_id, student_group_id, academic_year_id, type, day_of_week, start_time, end_time, start_date, end_date, is_recurring, notes, created_at, updated_at
             FROM schedules"
        );

        Schema::drop('schedules');
        Schema::rename('schedules__new', 'schedules');

        DB::statement('PRAGMA foreign_keys=ON;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Best effort rollback: recreate the old schema with ecu_id NOT NULL.
        DB::statement('PRAGMA foreign_keys=OFF;');

        Schema::create('schedules__old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['cm', 'td', 'tp'])->default('cm');
            $table->tinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_recurring')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        DB::statement(
            "INSERT INTO schedules__old (id, ecu_id, teacher_id, room_id, student_group_id, academic_year_id, type, day_of_week, start_time, end_time, start_date, end_date, is_recurring, notes, created_at, updated_at)
             SELECT id, ecu_id, teacher_id, room_id, student_group_id, academic_year_id, type, day_of_week, start_time, end_time, start_date, end_date, is_recurring, notes, created_at, updated_at
             FROM schedules
             WHERE ecu_id IS NOT NULL"
        );

        Schema::drop('schedules');
        Schema::rename('schedules__old', 'schedules');

        DB::statement('PRAGMA foreign_keys=ON;');
    }
};
