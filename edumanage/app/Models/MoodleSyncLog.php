<?php

namespace App\Models;

use App\Traits\BelongsToUniversity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodleSyncLog extends Model
{
    use HasFactory, BelongsToUniversity;

    protected $fillable = [
        'university_id',
        'sync_type',
        'direction',
        'status',
        'records_processed',
        'records_synced',
        'records_failed',
        'errors',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'errors' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function start(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    public function complete(int $processed, int $synced, int $failed, ?array $errors = null): void
    {
        $this->update([
            'status' => $failed > 0 && $synced === 0 ? 'failed' : 'completed',
            'records_processed' => $processed,
            'records_synced' => $synced,
            'records_failed' => $failed,
            'errors' => $errors,
            'completed_at' => now(),
        ]);
    }

    public function fail(array $errors): void
    {
        $this->update([
            'status' => 'failed',
            'errors' => $errors,
            'completed_at' => now(),
        ]);
    }

    public function getDurationAttribute(): ?int
    {
        if (!$this->started_at || !$this->completed_at) return null;
        return $this->completed_at->diffInSeconds($this->started_at);
    }
}
