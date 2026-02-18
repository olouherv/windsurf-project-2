<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('student_id')->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->string('nationality')->nullable();
            $table->enum('status', ['active', 'inactive', 'graduated', 'suspended'])->default('active');
            $table->date('enrollment_date')->nullable();
            $table->unsignedBigInteger('moodle_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['university_id', 'student_id']);
            $table->unique(['university_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
