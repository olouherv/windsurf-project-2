<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacataire_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('contract_number')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_hours_planned');
            $table->integer('hours_completed')->default(0);
            $table->decimal('hourly_rate', 8, 2);
            $table->decimal('total_amount', 10, 2)->storedAs('total_hours_planned * hourly_rate');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('vacataire_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacataire_contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('ecu_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->decimal('hours', 4, 2);
            $table->enum('type', ['cm', 'td', 'tp'])->default('cm');
            $table->text('description')->nullable();
            $table->boolean('is_validated')->default(false);
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacataire_hours');
        Schema::dropIfExists('vacataire_contracts');
    }
};
