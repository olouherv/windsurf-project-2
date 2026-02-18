<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_year_id')->nullable()->constrained()->onDelete('set null');
            $table->string('contract_number')->unique();
            $table->enum('type', ['inscription', 'formation', 'stage', 'apprentissage', 'autre'])->default('inscription');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('tuition_fees', 10, 2)->default(0);
            $table->decimal('registration_fees', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->storedAs('tuition_fees + registration_fees');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled', 'suspended'])->default('draft');
            $table->date('signed_date')->nullable();
            $table->string('signed_by_student')->nullable();
            $table->string('signed_by_guarantor')->nullable();
            $table->string('signed_by_admin')->nullable();
            $table->text('special_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contract_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_contract_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'card', 'mobile_money', 'other'])->default('cash');
            $table->string('reference')->nullable();
            $table->string('receipt_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_payments');
        Schema::dropIfExists('student_contracts');
    }
};
