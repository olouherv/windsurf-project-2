<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->string('code');
            $table->string('name');
            $table->decimal('credits_ects', 4, 1)->default(0);
            $table->decimal('coefficient', 4, 2)->default(1);
            $table->text('description')->nullable();
            $table->boolean('is_optional')->default(false);
            $table->timestamps();

            $table->unique(['semester_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ues');
    }
};
