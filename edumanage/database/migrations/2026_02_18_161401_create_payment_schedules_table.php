<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_contract_id')->constrained()->onDelete('cascade');
            $table->integer('installment_number');
            $table->string('label')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_contract_id', 'installment_number']);
        });

        Schema::table('contract_payments', function (Blueprint $table) {
            $table->foreignId('payment_schedule_id')->nullable()->after('student_contract_id')
                ->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('contract_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_schedule_id');
        });
        Schema::dropIfExists('payment_schedules');
    }
};
