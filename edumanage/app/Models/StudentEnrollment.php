<?php

namespace App\Models;

use App\Traits\BelongsToUniversity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEnrollment extends Model
{
    use HasFactory, BelongsToUniversity;

    protected $fillable = [
        'university_id',
        'student_id',
        'program_year_id',
        'academic_year_id',
        'status',
        'enrollment_date',
        'validation_date',
        'notes',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'validation_date' => 'date',
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

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'enrolled' => 'Inscrit',
            'validated' => 'Validé',
            'failed' => 'Ajourné',
            'abandoned' => 'Abandonné',
            'transferred' => 'Transféré',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'enrolled' => 'blue',
            'validated' => 'green',
            'failed' => 'red',
            'abandoned' => 'gray',
            'transferred' => 'yellow',
            default => 'gray',
        };
    }
}
