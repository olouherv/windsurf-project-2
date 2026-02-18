<?php

namespace App\Livewire\Evaluations;

use App\Models\AcademicYear;
use App\Models\Ecu;
use App\Models\Evaluation;
use Livewire\Component;

class EvaluationForm extends Component
{
    public ?Evaluation $evaluation = null;
    public bool $editMode = false;

    public ?int $ecu_id = null;
    public string $ecuSearch = '';
    public bool $showEcuDropdown = false;
    public ?Ecu $selectedEcu = null;

    public ?int $academic_year_id = null;
    public string $name = '';
    public string $type = 'exam';
    public string $session = 'normal';
    public ?string $date = null;
    public float $coefficient = 1;
    public float $max_score = 20;
    public ?string $description = '';

    protected function rules(): array
    {
        return [
            'ecu_id' => 'required|exists:ecus,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:exam,cc,tp,project,oral',
            'session' => 'required|in:normal,rattrapage',
            'date' => 'nullable|date',
            'coefficient' => 'required|numeric|min:0.1|max:10',
            'max_score' => 'required|numeric|min:1|max:100',
            'description' => 'nullable|string',
        ];
    }

    public function mount(?Evaluation $evaluation = null, ?Ecu $ecu = null): void
    {
        if ($evaluation && $evaluation->exists) {
            $this->evaluation = $evaluation;
            $this->editMode = true;
            $this->ecu_id = $evaluation->ecu_id;
            $this->selectedEcu = $evaluation->ecu;
            $this->ecuSearch = $evaluation->ecu->code . ' - ' . $evaluation->ecu->name;
            $this->academic_year_id = $evaluation->academic_year_id;
            $this->name = $evaluation->name;
            $this->type = $evaluation->type;
            $this->session = $evaluation->session;
            $this->date = $evaluation->date?->format('Y-m-d');
            $this->coefficient = $evaluation->coefficient;
            $this->max_score = $evaluation->max_score;
            $this->description = $evaluation->description ?? '';
        } elseif ($ecu && $ecu->exists) {
            $this->ecu_id = $ecu->id;
            $this->selectedEcu = $ecu;
            $this->ecuSearch = $ecu->code . ' - ' . $ecu->name;
        }

        if (!$this->academic_year_id) {
            $currentYear = AcademicYear::where('is_current', true)->first();
            $this->academic_year_id = $currentYear?->id;
        }
    }

    public function updatedEcuSearch($value): void
    {
        $this->showEcuDropdown = strlen($value) >= 2;
        if (!$value) {
            $this->ecu_id = null;
            $this->selectedEcu = null;
        }
    }

    public function selectEcu(int $id): void
    {
        $ecu = Ecu::with('ue')->find($id);
        if ($ecu) {
            $this->ecu_id = $ecu->id;
            $this->selectedEcu = $ecu;
            $this->ecuSearch = $ecu->code . ' - ' . $ecu->name;
            $this->showEcuDropdown = false;
        }
    }

    public function clearEcu(): void
    {
        $this->ecu_id = null;
        $this->selectedEcu = null;
        $this->ecuSearch = '';
        $this->showEcuDropdown = false;
    }

    public function save()
    {
        $validated = $this->validate();
        $validated['university_id'] = auth()->user()->university_id;

        if ($this->editMode && $this->evaluation) {
            $this->evaluation->update($validated);
            session()->flash('success', __('Évaluation mise à jour avec succès.'));
            $evaluation = $this->evaluation;
        } else {
            $evaluation = Evaluation::create($validated);
            session()->flash('success', __('Évaluation créée avec succès.'));
        }

        return redirect()->route('evaluations.show', $evaluation);
    }

    public function render()
    {
        $ecuResults = [];
        if ($this->showEcuDropdown && strlen($this->ecuSearch) >= 2) {
            $ecuResults = Ecu::with('ue')
                ->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->ecuSearch . '%')
                      ->orWhere('name', 'like', '%' . $this->ecuSearch . '%');
                })->limit(10)->get();
        }

        return view('livewire.evaluations.evaluation-form', [
            'ecuResults' => $ecuResults,
            'academicYears' => AcademicYear::orderBy('start_date', 'desc')->get(),
        ]);
    }
}
