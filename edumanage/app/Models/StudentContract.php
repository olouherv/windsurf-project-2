<?php

namespace App\Models;

use App\Traits\BelongsToUniversity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentContract extends Model
{
    use HasFactory, SoftDeletes, BelongsToUniversity;

    protected $fillable = [
        'university_id',
        'student_id',
        'academic_year_id',
        'program_year_id',
        'contract_number',
        'type',
        'start_date',
        'end_date',
        'tuition_fees',
        'registration_fees',
        'amount_paid',
        'payment_status',
        'status',
        'signed_date',
        'signed_by_student',
        'signed_by_guarantor',
        'signed_by_admin',
        'special_conditions',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_date' => 'date',
        'tuition_fees' => 'decimal:2',
        'registration_fees' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function programYear(): BelongsTo
    {
        return $this->belongsTo(ProgramYear::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ContractPayment::class);
    }

    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    public function generatePaymentSchedule(int $installments = null): void
    {
        $installments = $installments ?? $this->programYear?->default_installments ?? 1;
        
        $this->paymentSchedules()->delete();
        
        $amountPerInstallment = round($this->total_amount / $installments, 2);
        $remainder = $this->total_amount - ($amountPerInstallment * $installments);
        
        $startDate = $this->start_date ?? now();
        $monthsBetween = max(1, ceil(12 / $installments));
        
        for ($i = 1; $i <= $installments; $i++) {
            $amount = $amountPerInstallment;
            if ($i === $installments) {
                $amount += $remainder;
            }
            
            $dueDate = $startDate->copy()->addMonths(($i - 1) * $monthsBetween);
            
            $this->paymentSchedules()->create([
                'installment_number' => $i,
                'label' => "Tranche {$i}/{$installments}",
                'amount' => $amount,
                'due_date' => $dueDate,
            ]);
        }
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->total_amount - $this->amount_paid;
    }

    public function isFullyPaid(): bool
    {
        return $this->amount_paid >= $this->total_amount;
    }

    public function updatePaymentStatus(): void
    {
        if ($this->amount_paid >= $this->total_amount) {
            $this->payment_status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->payment_status = 'partial';
        } elseif ($this->end_date && $this->end_date->isPast()) {
            $this->payment_status = 'overdue';
        } else {
            $this->payment_status = 'pending';
        }
        $this->save();
    }
}
