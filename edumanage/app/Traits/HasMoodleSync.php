<?php

namespace App\Traits;

trait HasMoodleSync
{
    public function isSyncedWithMoodle(): bool
    {
        $moodleIdField = $this->getMoodleIdField();
        return !empty($this->{$moodleIdField});
    }

    public function getMoodleIdField(): string
    {
        return $this->moodleIdField ?? 'moodle_id';
    }

    public function getMoodleId(): ?int
    {
        $field = $this->getMoodleIdField();
        return $this->{$field};
    }

    public function setMoodleId(?int $moodleId): void
    {
        $field = $this->getMoodleIdField();
        $this->{$field} = $moodleId;
        $this->save();
    }

    public function scopeSyncedWithMoodle($query)
    {
        return $query->whereNotNull($this->getMoodleIdField());
    }

    public function scopeNotSyncedWithMoodle($query)
    {
        return $query->whereNull($this->getMoodleIdField());
    }
}
