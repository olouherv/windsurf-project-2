<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'category',
        'title',
        'ecu_id',
        'teacher_id',
        'room_id',
        'academic_year_id',
        'student_group_id',
        'session_date',
        'start_time',
        'end_time',
        'type',
        'status',
        'notes',
        'cancellation_reason',
    ];

    protected $casts = [
        'session_date' => 'date:Y-m-d',
    ];

    public const STATUSES = [
        'planned' => 'Planifiée',
        'completed' => 'Effectuée',
        'cancelled' => 'Annulée',
        'rescheduled' => 'Reportée',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function ecu(): BelongsTo
    {
        return $this->belongsTo(Ecu::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function studentGroup(): BelongsTo
    {
        return $this->belongsTo(StudentGroup::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function getDurationInHoursAttribute(): float
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        return $end->diffInMinutes($start) / 60;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);
    }
}
