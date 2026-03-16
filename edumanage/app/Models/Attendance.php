<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'student_id',
        'session_date',
        'status',
        'late_minutes',
        'excuse_reason',
        'marked_by',
        'marked_at',
        'notes',
    ];

    protected $casts = [
        'session_date' => 'date',
        'marked_at' => 'datetime',
        'late_minutes' => 'integer',
    ];

    public const STATUSES = [
        'present' => 'Présent',
        'absent' => 'Absent',
        'late' => 'En retard',
        'excused' => 'Excusé',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function markedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isPresent(): bool
    {
        return $this->status === 'present';
    }

    public function isAbsent(): bool
    {
        return in_array($this->status, ['absent', 'excused']);
    }
}
