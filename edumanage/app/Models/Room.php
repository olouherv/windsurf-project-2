<?php

namespace App\Models;

use App\Traits\BelongsToUniversity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Room extends Model
{
    use HasFactory, BelongsToUniversity;

    protected $fillable = [
        'university_id',
        'name',
        'code',
        'building',
        'capacity',
        'type',
        'equipment',
        'is_available',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'equipment' => 'array',
        'is_available' => 'boolean',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function isAvailableAt(string $dayOfWeek, string $startTime, string $endTime, ?int $excludeScheduleId = null, ?int $academicYearId = null): bool
    {
        $query = $this->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeScheduleId) {
            $query->where('id', '!=', $excludeScheduleId);
        }

        return !$query->exists();
    }

    public function isAvailableOnDate(string $date, string $startTime, string $endTime, ?int $excludeScheduleId = null, ?int $academicYearId = null): bool
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $query = $this->schedules()
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->where(function ($q) use ($date, $dayOfWeek) {
                $q->where(function ($q1) use ($date) {
                    $q1->where('category', 'activity')
                        ->whereDate('scheduled_date', $date);
                })->orWhere(function ($q2) use ($date, $dayOfWeek) {
                    $q2->where('category', 'course')
                        ->where('is_recurring', true)
                        ->where('day_of_week', $dayOfWeek)
                        ->where(function ($q3) use ($date) {
                            $q3->whereNull('start_date')->orWhereDate('start_date', '<=', $date);
                        })
                        ->where(function ($q4) use ($date) {
                            $q4->whereNull('end_date')->orWhereDate('end_date', '>=', $date);
                        });
                });
            })
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeScheduleId) {
            $query->where('id', '!=', $excludeScheduleId);
        }

        return !$query->exists();
    }
}
