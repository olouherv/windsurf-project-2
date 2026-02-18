<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->string('module_key');
            $table->boolean('is_enabled')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['university_id', 'module_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_settings');
    }
};
