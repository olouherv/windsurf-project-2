<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'program_year_id',
        'academic_year_id',
        'enrollment_date',
        'status',
        'tuition_fee',
        'amount_paid',
        'notes',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'tuition_fee' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function programYear(): BelongsTo
    {
        return $this->belongsTo(ProgramYear::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function getRemainingBalanceAttribute(): float
    {
        return ($this->tuition_fee ?? 0) - $this->amount_paid;
    }

    public function isPaid(): bool
    {
        return $this->remaining_balance <= 0;
    }
}
