<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEcuEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'ecu_id',
        'academic_year_id',
        'status',
        'final_grade',
        'credits_acquired',
    ];

    protected $casts = [
        'final_grade' => 'decimal:2',
        'credits_acquired' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function ecu(): BelongsTo
    {
        return $this->belongsTo(Ecu::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function calculateFinalGrade(): ?float
    {
        $evaluations = Evaluation::where('ecu_id', $this->ecu_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->with(['grades' => fn($q) => $q->where('student_id', $this->student_id)])
            ->get();

        $totalCoef = 0;
        $weightedSum = 0;

        foreach ($evaluations as $evaluation) {
            $grade = $evaluation->grades->first();
            if ($grade && $grade->score !== null && !$grade->is_absent) {
                $normalizedScore = ($grade->score / $evaluation->max_score) * 20;
                $weightedSum += $normalizedScore * $evaluation->coefficient;
                $totalCoef += $evaluation->coefficient;
            }
        }

        if ($totalCoef === 0) return null;

        return round($weightedSum / $totalCoef, 2);
    }

    public function updateStatus(): void
    {
        $this->final_grade = $this->calculateFinalGrade();
        
        if ($this->final_grade !== null) {
            if ($this->final_grade >= 10) {
                $this->status = 'validated';
                $this->credits_acquired = true;
            } else {
                $this->status = 'failed';
                $this->credits_acquired = false;
            }
        }
        
        $this->save();
    }
}
