<?php

namespace App\Models;

use App\Traits\BelongsToUniversity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliberationSetting extends Model
{
    use HasFactory, BelongsToUniversity;

    protected $fillable = [
        'university_id',
        // Critères UE
        'ue_validation_average',
        'ue_allow_compensation',
        'ue_compensation_min',
        // Critères Semestre
        'semester_validation_average',
        'semester_min_ue_validated_percent',
        'semester_allow_compensation',
        'semester_max_ue_failed',
        // Critères Année
        'year_validation_average',
        'year_require_all_semesters',
        'year_max_credits_failed',
        // Passage conditionnel
        'allow_conditional_pass',
        'conditional_max_credits_debt',
        // Mentions
        'mention_passable_min',
        'mention_assez_bien_min',
        'mention_bien_min',
        'mention_tres_bien_min',
    ];

    protected $casts = [
        'ue_validation_average' => 'decimal:2',
        'ue_allow_compensation' => 'boolean',
        'ue_compensation_min' => 'decimal:2',
        'semester_validation_average' => 'decimal:2',
        'semester_min_ue_validated_percent' => 'integer',
        'semester_allow_compensation' => 'boolean',
        'semester_max_ue_failed' => 'integer',
        'year_validation_average' => 'decimal:2',
        'year_require_all_semesters' => 'boolean',
        'year_max_credits_failed' => 'integer',
        'allow_conditional_pass' => 'boolean',
        'conditional_max_credits_debt' => 'integer',
        'mention_passable_min' => 'decimal:2',
        'mention_assez_bien_min' => 'decimal:2',
        'mention_bien_min' => 'decimal:2',
        'mention_tres_bien_min' => 'decimal:2',
    ];

    public static function getOrCreateForUniversity(int $universityId): self
    {
        return self::firstOrCreate(
            ['university_id' => $universityId],
            [
                'ue_validation_average' => 10.00,
                'ue_allow_compensation' => true,
                'ue_compensation_min' => 8.00,
                'semester_validation_average' => 10.00,
                'semester_min_ue_validated_percent' => 100,
                'semester_allow_compensation' => true,
                'semester_max_ue_failed' => 2,
                'year_validation_average' => 10.00,
                'year_require_all_semesters' => true,
                'year_max_credits_failed' => 12,
                'allow_conditional_pass' => true,
                'conditional_max_credits_debt' => 6,
                'mention_passable_min' => 10.00,
                'mention_assez_bien_min' => 12.00,
                'mention_bien_min' => 14.00,
                'mention_tres_bien_min' => 16.00,
            ]
        );
    }

    public function getMention(float $average): ?string
    {
        if ($average >= $this->mention_tres_bien_min) {
            return 'Très Bien';
        } elseif ($average >= $this->mention_bien_min) {
            return 'Bien';
        } elseif ($average >= $this->mention_assez_bien_min) {
            return 'Assez Bien';
        } elseif ($average >= $this->mention_passable_min) {
            return 'Passable';
        }
        return null;
    }
}
