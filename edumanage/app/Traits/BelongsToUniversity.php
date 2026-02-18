<?php

namespace App\Traits;

use App\Models\University;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToUniversity
{
    protected static function bootBelongsToUniversity(): void
    {
        static::creating(function ($model) {
            if (empty($model->university_id) && auth()->check() && auth()->user()->university_id) {
                $model->university_id = auth()->user()->university_id;
            }
        });

        static::addGlobalScope('university', function (Builder $builder) {
            if (auth()->check() && auth()->user()->university_id && auth()->user()->user_type !== 'super_admin') {
                $builder->where($builder->getModel()->getTable() . '.university_id', auth()->user()->university_id);
            }
        });
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function scopeForUniversity(Builder $query, int $universityId): Builder
    {
        return $query->where('university_id', $universityId);
    }
}
