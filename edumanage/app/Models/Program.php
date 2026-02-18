<?php

namespace App\Models;

use App\Traits\BelongsToUniversity;
use App\Traits\HasMoodleSync;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes, BelongsToUniversity, HasMoodleSync;

    protected $moodleIdField = 'moodle_category_id';

    protected $fillable = [
        'university_id',
        'name',
        'code',
        'level',
        'duration_years',
        'description',
        'is_active',
        'moodle_category_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_years' => 'integer',
    ];

    public function programYears(): HasMany
    {
        return $this->hasMany(ProgramYear::class);
    }

    public function getTotalStudentsAttribute(): int
    {
        return $this->programYears()
            ->withCount('enrollments')
            ->get()
            ->sum('enrollments_count');
    }
}
