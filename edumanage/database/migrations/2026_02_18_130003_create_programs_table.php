<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code');
            $table->enum('level', ['licence', 'master', 'doctorat', 'dut', 'bts', 'other']);
            $table->integer('duration_years')->default(3);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('moodle_category_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['university_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
