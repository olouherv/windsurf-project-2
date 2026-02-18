<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class MoodleConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'moodle_url',
        'moodle_token',
        'is_active',
        'sync_students',
        'sync_teachers',
        'sync_courses',
        'sync_cohorts',
        'last_sync_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sync_students' => 'boolean',
        'sync_teachers' => 'boolean',
        'sync_courses' => 'boolean',
        'sync_cohorts' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    protected $hidden = ['moodle_token'];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function setMoodleTokenAttribute($value): void
    {
        $this->attributes['moodle_token'] = Crypt::encryptString($value);
    }

    public function getMoodleTokenAttribute($value): ?string
    {
        if (!$value) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getApiUrl(string $function): string
    {
        return rtrim($this->moodle_url, '/') . '/webservice/rest/server.php?' . http_build_query([
            'wstoken' => $this->moodle_token,
            'wsfunction' => $function,
            'moodlewsrestformat' => 'json',
        ]);
    }

    public function syncLogs()
    {
        return $this->hasMany(MoodleSyncLog::class, 'university_id', 'university_id');
    }
}
