<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_contract_id',
        'payment_schedule_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference',
        'receipt_number',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::created(function (ContractPayment $payment) {
            $contract = $payment->contract;
            $contract->amount_paid = $contract->payments()->sum('amount');
            $contract->updatePaymentStatus();
            
            if ($payment->paymentSchedule) {
                $payment->paymentSchedule->updateStatus();
            }
        });

        static::deleted(function (ContractPayment $payment) {
            $contract = $payment->contract;
            $contract->amount_paid = $contract->payments()->sum('amount');
            $contract->updatePaymentStatus();
            
            if ($payment->paymentSchedule) {
                $payment->paymentSchedule->updateStatus();
            }
        });
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(StudentContract::class, 'student_contract_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function paymentSchedule(): BelongsTo
    {
        return $this->belongsTo(PaymentSchedule::class);
    }
}
