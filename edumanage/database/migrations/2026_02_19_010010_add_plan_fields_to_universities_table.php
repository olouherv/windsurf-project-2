<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->foreignId('pricing_plan_id')->nullable()->after('trial_ends_at')->constrained('pricing_plans');
            $table->string('plan_key')->nullable()->after('pricing_plan_id');
            $table->timestamp('plan_started_at')->nullable()->after('plan_key');
        });
    }

    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pricing_plan_id');
            $table->dropColumn(['plan_key', 'plan_started_at']);
        });
    }
};
