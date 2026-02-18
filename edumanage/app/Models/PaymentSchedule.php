<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_contract_id',
        'installment_number',
        'label',
        'amount',
        'due_date',
        'amount_paid',
        'status',
        'paid_date',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(StudentContract::class, 'student_contract_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ContractPayment::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->amount - $this->amount_paid);
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'paid' && $this->due_date->isPast();
    }

    public function updateStatus(): void
    {
        $this->amount_paid = $this->payments()->sum('amount');
        
        if ($this->amount_paid >= $this->amount) {
            $this->status = 'paid';
            $this->paid_date = $this->paid_date ?? now();
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partial';
        } elseif ($this->due_date && $this->due_date->isPast()) {
            $this->status = 'overdue';
        } else {
            $this->status = 'pending';
        }
        
        $this->save();
    }
}
