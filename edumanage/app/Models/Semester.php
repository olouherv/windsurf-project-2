<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_year_id',
        'academic_year_id',
        'name',
        'semester_number',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'semester_number' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function programYear(): BelongsTo
    {
        return $this->belongsTo(ProgramYear::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function ues(): HasMany
    {
        return $this->hasMany(Ue::class);
    }

    public function getTotalCreditsAttribute(): float
    {
        return $this->ues()->sum('credits_ects');
    }
}
