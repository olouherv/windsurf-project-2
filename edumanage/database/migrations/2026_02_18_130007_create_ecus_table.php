<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ue_id')->constrained()->onDelete('cascade');
            $table->string('code');
            $table->string('name');
            $table->decimal('credits_ects', 4, 1)->default(0);
            $table->decimal('coefficient', 4, 2)->default(1);
            $table->integer('hours_cm')->default(0);
            $table->integer('hours_td')->default(0);
            $table->integer('hours_tp')->default(0);
            $table->text('description')->nullable();
            $table->text('objectives')->nullable();
            $table->unsignedBigInteger('moodle_course_id')->nullable()->index();
            $table->timestamps();

            $table->unique(['ue_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecus');
    }
};
