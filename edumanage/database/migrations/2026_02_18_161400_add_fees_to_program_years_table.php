<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_years', function (Blueprint $table) {
            $table->decimal('tuition_fees', 10, 2)->default(0)->after('description');
            $table->decimal('registration_fees', 10, 2)->default(0)->after('tuition_fees');
            $table->integer('default_installments')->default(1)->after('registration_fees');
        });
    }

    public function down(): void
    {
        Schema::table('program_years', function (Blueprint $table) {
            $table->dropColumn(['tuition_fees', 'registration_fees', 'default_installments']);
        });
    }
};
