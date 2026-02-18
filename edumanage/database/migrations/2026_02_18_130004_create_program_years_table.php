<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('year_number');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['program_id', 'year_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_years');
    }
};
