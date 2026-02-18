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

class Teacher extends Model
{
    use HasFactory, SoftDeletes, BelongsToUniversity, HasMoodleSync;

    protected $fillable = [
        'university_id',
        'user_id',
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'gender',
        'address',
        'photo',
        'type',
        'specialization',
        'grade',
        'title',
        'rib',
        'ifu',
        'cv_file',
        'rib_file',
        'ifu_file',
        'status',
        'hire_date',
        'moodle_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ecus(): BelongsToMany
    {
        return $this->belongsToMany(Ecu::class, 'teacher_ecu')
            ->withPivot(['academic_year_id', 'is_responsible', 'teaching_type'])
            ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function vacataireContracts(): HasMany
    {
        return $this->hasMany(VacataireContract::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isVacataire(): bool
    {
        return $this->type === 'vacataire';
    }

    public function getCurrentContract(AcademicYear $academicYear): ?VacataireContract
    {
        return $this->vacataireContracts()
            ->where('academic_year_id', $academicYear->id)
            ->where('status', 'active')
            ->first();
    }

    public function getTotalHoursForYear(AcademicYear $academicYear): int
    {
        return $this->schedules()
            ->where('academic_year_id', $academicYear->id)
            ->count() * 2;
    }
}
