<?php

namespace App\Models;

use App\Traits\BelongsToUniversity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deliberation extends Model
{
    use HasFactory, BelongsToUniversity;

    protected $fillable = [
        'university_id',
        'academic_year_id',
        'program_year_id',
        'semester_id',
        'type',
        'session',
        'deliberation_date',
        'status',
        'president_id',
        'jury_members',
        'notes',
        'validated_at',
        'validated_by',
        'published_at',
    ];

    protected $casts = [
        'deliberation_date' => 'date',
        'validated_at' => 'datetime',
        'published_at' => 'datetime',
        'jury_members' => 'array',
    ];

    public const TYPES = [
        'semester' => 'Semestrielle',
        'annual' => 'Annuelle',
    ];

    public const SESSIONS = [
        'normal' => 'Session normale',
        'rattrapage' => 'Session de rattrapage',
    ];

    public const STATUSES = [
        'draft' => 'Brouillon',
        'in_progress' => 'En cours',
        'validated' => 'Validée',
        'published' => 'Publiée',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function programYear(): BelongsTo
    {
        return $this->belongsTo(ProgramYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function president(): BelongsTo
    {
        return $this->belongsTo(User::class, 'president_id');
    }

    public function validatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function results(): HasMany
    {
        return $this->hasMany(DeliberationResult::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getSessionLabelAttribute(): string
    {
        return self::SESSIONS[$this->session] ?? $this->session;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function validate(): void
    {
        $this->update([
            'status' => 'validated',
            'validated_at' => now(),
            'validated_by' => auth()->id(),
        ]);
    }

    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
