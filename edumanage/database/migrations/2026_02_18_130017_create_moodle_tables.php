<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moodle_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->string('moodle_url');
            $table->text('moodle_token');
            $table->boolean('is_active')->default(false);
            $table->boolean('sync_students')->default(true);
            $table->boolean('sync_teachers')->default(true);
            $table->boolean('sync_courses')->default(true);
            $table->boolean('sync_cohorts')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();

            $table->unique('university_id');
        });

        Schema::create('moodle_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->enum('sync_type', ['students', 'teachers', 'courses', 'cohorts', 'grades', 'all']);
            $table->enum('direction', ['to_moodle', 'from_moodle', 'bidirectional']);
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->integer('records_processed')->default(0);
            $table->integer('records_synced')->default(0);
            $table->integer('records_failed')->default(0);
            $table->json('errors')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['university_id', 'sync_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moodle_sync_logs');
        Schema::dropIfExists('moodle_configs');
    }
};
