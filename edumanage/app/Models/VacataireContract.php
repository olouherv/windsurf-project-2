<?php

namespace App\Models;

use App\Traits\BelongsToUniversity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VacataireContract extends Model
{
    use HasFactory, BelongsToUniversity;

    protected $fillable = [
        'university_id',
        'teacher_id',
        'academic_year_id',
        'ecu_id',
        'teaching_type',
        'contract_number',
        'start_date',
        'end_date',
        'total_hours_planned',
        'hours_completed',
        'hourly_rate',
        'amount_paid',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_hours_planned' => 'integer',
        'hours_completed' => 'integer',
        'hourly_rate' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function ecu(): BelongsTo
    {
        return $this->belongsTo(Ecu::class);
    }

    public function hours(): HasMany
    {
        return $this->hasMany(VacataireHour::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->total_hours_planned * $this->hourly_rate;
    }

    public function getRemainingHoursAttribute(): int
    {
        return $this->total_hours_planned - $this->hours_completed;
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->total_amount - $this->amount_paid;
    }

    public function updateHoursCompleted(): void
    {
        $this->hours_completed = $this->hours()->where('is_validated', true)->sum('hours');
        $this->save();
    }
}
