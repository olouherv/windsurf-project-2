<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('guarantor_first_name')->nullable()->after('moodle_id');
            $table->string('guarantor_last_name')->nullable()->after('guarantor_first_name');
            $table->string('guarantor_relationship')->nullable()->after('guarantor_last_name');
            $table->string('guarantor_phone')->nullable()->after('guarantor_relationship');
            $table->string('guarantor_email')->nullable()->after('guarantor_phone');
            $table->text('guarantor_address')->nullable()->after('guarantor_email');
            $table->string('guarantor_profession')->nullable()->after('guarantor_address');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'guarantor_first_name',
                'guarantor_last_name',
                'guarantor_relationship',
                'guarantor_phone',
                'guarantor_email',
                'guarantor_address',
                'guarantor_profession',
            ]);
        });
    }
};
