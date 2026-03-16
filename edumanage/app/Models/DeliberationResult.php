<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliberationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'deliberation_id',
        'student_id',
        'semester_average',
        'year_average',
        'credits_validated',
        'credits_total',
        'decision',
        'mention',
        'rank',
        'jury_observation',
        'conditions',
    ];

    protected $casts = [
        'semester_average' => 'decimal:2',
        'year_average' => 'decimal:2',
        'credits_validated' => 'integer',
        'credits_total' => 'integer',
        'rank' => 'integer',
    ];

    public const DECISIONS = [
        'validated' => 'Admis',
        'validated_compensated' => 'Admis par compensation',
        'conditional' => 'Passage conditionnel',
        'retake' => 'Rattrapage',
        'repeat' => 'Redoublement',
        'exclusion' => 'Exclusion',
        'pending' => 'En attente',
    ];

    public const DECISION_COLORS = [
        'validated' => 'green',
        'validated_compensated' => 'blue',
        'conditional' => 'yellow',
        'retake' => 'orange',
        'repeat' => 'red',
        'exclusion' => 'gray',
        'pending' => 'gray',
    ];

    public function deliberation(): BelongsTo
    {
        return $this->belongsTo(Deliberation::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function ueResults(): HasMany
    {
        return $this->hasMany(DeliberationUeResult::class);
    }

    public function getDecisionLabelAttribute(): string
    {
        return self::DECISIONS[$this->decision] ?? $this->decision;
    }

    public function getDecisionColorAttribute(): string
    {
        return self::DECISION_COLORS[$this->decision] ?? 'gray';
    }

    public function isAdmitted(): bool
    {
        return in_array($this->decision, ['validated', 'validated_compensated', 'conditional']);
    }

    public function getCreditsFailedAttribute(): int
    {
        return $this->credits_total - $this->credits_validated;
    }

    public function getValidationRateAttribute(): float
    {
        return $this->credits_total > 0 
            ? round(($this->credits_validated / $this->credits_total) * 100, 1) 
            : 0;
    }
}
