<?php

namespace App\Models;

use App\Traits\BelongsToUniversity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    use HasFactory, BelongsToUniversity;

    protected $fillable = [
        'university_id',
        'ecu_id',
        'academic_year_id',
        'name',
        'type',
        'session',
        'coefficient',
        'max_score',
        'date',
        'description',
        'is_published',
    ];

    protected $casts = [
        'coefficient' => 'decimal:2',
        'max_score' => 'decimal:2',
        'date' => 'date',
        'is_published' => 'boolean',
    ];

    public function ecu(): BelongsTo
    {
        return $this->belongsTo(Ecu::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function getAverageScoreAttribute(): ?float
    {
        $avg = $this->grades()->whereNotNull('score')->where('is_absent', false)->avg('score');
        return $avg ? round($avg, 2) : null;
    }

    public function getPassRateAttribute(): float
    {
        $total = $this->grades()->whereNotNull('score')->where('is_absent', false)->count();
        if ($total === 0) return 0;

        $passed = $this->grades()
            ->whereNotNull('score')
            ->where('is_absent', false)
            ->whereRaw('score >= (max_score * 0.5)', ['max_score' => $this->max_score])
            ->count();

        return round(($passed / $total) * 100, 1);
    }
}
