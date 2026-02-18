<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ue extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester_id',
        'code',
        'name',
        'credits_ects',
        'coefficient',
        'description',
        'is_optional',
    ];

    protected $casts = [
        'credits_ects' => 'decimal:1',
        'coefficient' => 'decimal:2',
        'is_optional' => 'boolean',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function ecus(): HasMany
    {
        return $this->hasMany(Ecu::class);
    }

    public function getTotalHoursAttribute(): int
    {
        return $this->ecus()->selectRaw('SUM(hours_cm + hours_td + hours_tp) as total')->value('total') ?? 0;
    }

    public function calculateStudentAverage(Student $student, AcademicYear $academicYear): ?float
    {
        $ecus = $this->ecus;
        $totalCoef = 0;
        $weightedSum = 0;

        foreach ($ecus as $ecu) {
            $average = $ecu->calculateStudentAverage($student, $academicYear);
            if ($average !== null) {
                $weightedSum += $average * $ecu->coefficient;
                $totalCoef += $ecu->coefficient;
            }
        }

        return $totalCoef > 0 ? round($weightedSum / $totalCoef, 2) : null;
    }
}
