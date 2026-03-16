<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliberationUeResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'deliberation_result_id',
        'ue_id',
        'average',
        'credits',
        'is_validated',
        'is_compensated',
    ];

    protected $casts = [
        'average' => 'decimal:2',
        'credits' => 'integer',
        'is_validated' => 'boolean',
        'is_compensated' => 'boolean',
    ];

    public function deliberationResult(): BelongsTo
    {
        return $this->belongsTo(DeliberationResult::class);
    }

    public function ue(): BelongsTo
    {
        return $this->belongsTo(Ue::class);
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_validated && $this->is_compensated) {
            return 'Validée (compensation)';
        } elseif ($this->is_validated) {
            return 'Validée';
        }
        return 'Non validée';
    }
}
