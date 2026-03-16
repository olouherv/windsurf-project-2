<?php

namespace App\Livewire\Deliberations;

use App\Models\DeliberationSetting;
use Livewire\Component;

class DeliberationSettings extends Component
{
    // Critères UE
    public float $ue_validation_average = 10.00;
    public bool $ue_allow_compensation = true;
    public float $ue_compensation_min = 8.00;

    // Critères Semestre
    public float $semester_validation_average = 10.00;
    public int $semester_min_ue_validated_percent = 100;
    public bool $semester_allow_compensation = true;
    public int $semester_max_ue_failed = 2;

    // Critères Année
    public float $year_validation_average = 10.00;
    public bool $year_require_all_semesters = true;
    public int $year_max_credits_failed = 12;

    // Passage conditionnel
    public bool $allow_conditional_pass = true;
    public int $conditional_max_credits_debt = 6;

    // Mentions
    public float $mention_passable_min = 10.00;
    public float $mention_assez_bien_min = 12.00;
    public float $mention_bien_min = 14.00;
    public float $mention_tres_bien_min = 16.00;

    public function mount(): void
    {
        $settings = DeliberationSetting::getOrCreateForUniversity(auth()->user()->university_id);

        $this->ue_validation_average = (float) $settings->ue_validation_average;
        $this->ue_allow_compensation = $settings->ue_allow_compensation;
        $this->ue_compensation_min = (float) $settings->ue_compensation_min;

        $this->semester_validation_average = (float) $settings->semester_validation_average;
        $this->semester_min_ue_validated_percent = $settings->semester_min_ue_validated_percent;
        $this->semester_allow_compensation = $settings->semester_allow_compensation;
        $this->semester_max_ue_failed = $settings->semester_max_ue_failed;

        $this->year_validation_average = (float) $settings->year_validation_average;
        $this->year_require_all_semesters = $settings->year_require_all_semesters;
        $this->year_max_credits_failed = $settings->year_max_credits_failed;

        $this->allow_conditional_pass = $settings->allow_conditional_pass;
        $this->conditional_max_credits_debt = $settings->conditional_max_credits_debt;

        $this->mention_passable_min = (float) $settings->mention_passable_min;
        $this->mention_assez_bien_min = (float) $settings->mention_assez_bien_min;
        $this->mention_bien_min = (float) $settings->mention_bien_min;
        $this->mention_tres_bien_min = (float) $settings->mention_tres_bien_min;
    }

    public function save(): void
    {
        $this->validate([
            'ue_validation_average' => 'required|numeric|min:0|max:20',
            'ue_compensation_min' => 'required|numeric|min:0|max:20',
            'semester_validation_average' => 'required|numeric|min:0|max:20',
            'semester_min_ue_validated_percent' => 'required|integer|min:0|max:100',
            'semester_max_ue_failed' => 'required|integer|min:0',
            'year_validation_average' => 'required|numeric|min:0|max:20',
            'year_max_credits_failed' => 'required|integer|min:0',
            'conditional_max_credits_debt' => 'required|integer|min:0',
            'mention_passable_min' => 'required|numeric|min:0|max:20',
            'mention_assez_bien_min' => 'required|numeric|min:0|max:20',
            'mention_bien_min' => 'required|numeric|min:0|max:20',
            'mention_tres_bien_min' => 'required|numeric|min:0|max:20',
        ]);

        DeliberationSetting::updateOrCreate(
            ['university_id' => auth()->user()->university_id],
            [
                'ue_validation_average' => $this->ue_validation_average,
                'ue_allow_compensation' => $this->ue_allow_compensation,
                'ue_compensation_min' => $this->ue_compensation_min,
                'semester_validation_average' => $this->semester_validation_average,
                'semester_min_ue_validated_percent' => $this->semester_min_ue_validated_percent,
                'semester_allow_compensation' => $this->semester_allow_compensation,
                'semester_max_ue_failed' => $this->semester_max_ue_failed,
                'year_validation_average' => $this->year_validation_average,
                'year_require_all_semesters' => $this->year_require_all_semesters,
                'year_max_credits_failed' => $this->year_max_credits_failed,
                'allow_conditional_pass' => $this->allow_conditional_pass,
                'conditional_max_credits_debt' => $this->conditional_max_credits_debt,
                'mention_passable_min' => $this->mention_passable_min,
                'mention_assez_bien_min' => $this->mention_assez_bien_min,
                'mention_bien_min' => $this->mention_bien_min,
                'mention_tres_bien_min' => $this->mention_tres_bien_min,
            ]
        );

        session()->flash('success', 'Paramètres de délibération enregistrés.');
    }

    public function render()
    {
        return view('livewire.deliberations.deliberation-settings');
    }
}
