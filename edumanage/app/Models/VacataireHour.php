<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacataireHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacataire_contract_id',
        'ecu_id',
        'date',
        'hours',
        'type',
        'description',
        'is_validated',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => 'decimal:2',
        'is_validated' => 'boolean',
        'validated_at' => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(VacataireContract::class, 'vacataire_contract_id');
    }

    public function ecu(): BelongsTo
    {
        return $this->belongsTo(Ecu::class);
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function validate(User $user): void
    {
        $this->update([
            'is_validated' => true,
            'validated_by' => $user->id,
            'validated_at' => now(),
        ]);

        $this->contract->updateHoursCompleted();
    }
}
