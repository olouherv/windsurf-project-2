<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('university_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->enum('user_type', ['super_admin', 'admin', 'secretary', 'teacher', 'student'])->default('student')->after('email');
            $table->string('phone')->nullable()->after('user_type');
            $table->string('locale')->default('fr')->after('phone');
            $table->boolean('is_active')->default(true)->after('locale');
            $table->softDeletes();

            $table->index('university_id');
            $table->index('user_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['university_id']);
            $table->dropColumn(['university_id', 'user_type', 'phone', 'locale', 'is_active']);
            $table->dropSoftDeletes();
        });
    }
};
