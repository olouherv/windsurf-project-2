<?php

namespace App\Models;

use App\Traits\HasMoodleSync;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentGroup extends Model
{
    use HasFactory, HasMoodleSync;

    protected $moodleIdField = 'moodle_cohort_id';

    protected $fillable = [
        'program_year_id',
        'academic_year_id',
        'name',
        'type',
        'max_students',
        'moodle_cohort_id',
    ];

    protected $casts = [
        'max_students' => 'integer',
    ];

    public function programYear(): BelongsTo
    {
        return $this->belongsTo(ProgramYear::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_group_student')
            ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }

    public function isFull(): bool
    {
        return $this->max_students && $this->student_count >= $this->max_students;
    }
}
