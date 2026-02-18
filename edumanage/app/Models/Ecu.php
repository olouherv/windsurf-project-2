<?php

namespace App\Models;

use App\Traits\HasMoodleSync;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ecu extends Model
{
    use HasFactory, HasMoodleSync;

    protected $moodleIdField = 'moodle_course_id';

    protected $fillable = [
        'ue_id',
        'code',
        'name',
        'credits_ects',
        'coefficient',
        'hours_cm',
        'hours_td',
        'hours_tp',
        'description',
        'objectives',
        'moodle_course_id',
    ];

    protected $casts = [
        'credits_ects' => 'decimal:1',
        'coefficient' => 'decimal:2',
        'hours_cm' => 'integer',
        'hours_td' => 'integer',
        'hours_tp' => 'integer',
    ];

    public function ue(): BelongsTo
    {
        return $this->belongsTo(Ue::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_ecu')
            ->withPivot(['academic_year_id', 'is_responsible', 'teaching_type'])
            ->withTimestamps();
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function getTotalHoursAttribute(): int
    {
        return $this->hours_cm + $this->hours_td + $this->hours_tp;
    }

    public function getResponsibleTeacher(AcademicYear $academicYear): ?Teacher
    {
        return $this->teachers()
            ->wherePivot('academic_year_id', $academicYear->id)
            ->wherePivot('is_responsible', true)
            ->first();
    }

    public function calculateStudentAverage(Student $student, AcademicYear $academicYear): ?float
    {
        $evaluations = $this->evaluations()
            ->where('academic_year_id', $academicYear->id)
            ->where('session', 'normal')
            ->get();

        $totalCoef = 0;
        $weightedSum = 0;

        foreach ($evaluations as $evaluation) {
            $grade = $evaluation->grades()->where('student_id', $student->id)->first();
            if ($grade && $grade->score !== null && !$grade->is_absent) {
                $normalizedScore = ($grade->score / $evaluation->max_score) * 20;
                $weightedSum += $normalizedScore * $evaluation->coefficient;
                $totalCoef += $evaluation->coefficient;
            }
        }

        return $totalCoef > 0 ? round($weightedSum / $totalCoef, 2) : null;
    }
}
