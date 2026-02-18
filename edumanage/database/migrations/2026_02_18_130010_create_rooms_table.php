<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code');
            $table->string('building')->nullable();
            $table->integer('capacity')->default(30);
            $table->enum('type', ['classroom', 'amphitheater', 'lab', 'computer_room', 'meeting_room', 'other'])->default('classroom');
            $table->json('equipment')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->unique(['university_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
