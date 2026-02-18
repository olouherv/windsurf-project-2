<?php

namespace App\Models;

use App\Traits\BelongsToUniversity;
use App\Traits\HasMoodleSync;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes, BelongsToUniversity, HasMoodleSync;

    protected $fillable = [
        'university_id',
        'user_id',
        'student_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'birth_place',
        'gender',
        'address',
        'photo',
        'nationality',
        'status',
        'enrollment_date',
        'moodle_id',
        'guarantor_first_name',
        'guarantor_last_name',
        'guarantor_relationship',
        'guarantor_phone',
        'guarantor_email',
        'guarantor_address',
        'guarantor_profession',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'enrollment_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function pedagogicEnrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function currentEnrollment(): ?StudentEnrollment
    {
        return $this->pedagogicEnrollments()
            ->whereHas('academicYear', fn($q) => $q->where('is_current', true))
            ->first();
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function studentGroups(): BelongsToMany
    {
        return $this->belongsToMany(StudentGroup::class, 'student_group_student')
            ->withTimestamps();
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(StudentContract::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getCurrentEnrollment(): ?Enrollment
    {
        return $this->enrollments()
            ->whereHas('academicYear', fn($q) => $q->where('is_current', true))
            ->where('status', 'confirmed')
            ->first();
    }

    public function isEnrolledIn(ProgramYear $programYear, AcademicYear $academicYear): bool
    {
        return $this->enrollments()
            ->where('program_year_id', $programYear->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('status', 'confirmed')
            ->exists();
    }
}
