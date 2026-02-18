<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'name',
        'year_number',
        'description',
        'tuition_fees',
        'registration_fees',
        'default_installments',
    ];

    protected $casts = [
        'year_number' => 'integer',
        'tuition_fees' => 'decimal:2',
        'registration_fees' => 'decimal:2',
        'default_installments' => 'integer',
    ];

    public function getTotalFeesAttribute(): float
    {
        return $this->tuition_fees + $this->registration_fees;
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    public function studentGroups(): HasMany
    {
        return $this->hasMany(StudentGroup::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function studentEnrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function getStudentsForAcademicYear(int $academicYearId)
    {
        return Student::whereHas('pedagogicEnrollments', function ($query) use ($academicYearId) {
            $query->where('program_year_id', $this->id)
                  ->where('academic_year_id', $academicYearId)
                  ->where('status', '!=', 'abandoned');
        })->orderBy('last_name')->get();
    }

    public function getFullNameAttribute(): string
    {
        return $this->program->name . ' - ' . $this->name;
    }
}
