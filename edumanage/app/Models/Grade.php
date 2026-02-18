<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'evaluation_id',
        'score',
        'is_absent',
        'is_excused',
        'comments',
        'graded_by',
        'graded_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'is_absent' => 'boolean',
        'is_excused' => 'boolean',
        'graded_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function getNormalizedScoreAttribute(): ?float
    {
        if ($this->score === null || $this->is_absent) return null;
        return round(($this->score / $this->evaluation->max_score) * 20, 2);
    }

    public function isPassing(): bool
    {
        return $this->normalized_score !== null && $this->normalized_score >= 10;
    }
}
