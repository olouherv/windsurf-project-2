<?php

namespace App\Livewire\Teachers;

use App\Models\AcademicYear;
use App\Models\Ecu;
use App\Models\Teacher;
use App\Models\VacataireContract;
use Livewire\Component;

class EcuAssignment extends Component
{
    public Teacher $teacher;
    public bool $showModal = false;

    public ?int $academic_year_id = null;
    public ?int $ecu_id = null;
    public bool $is_responsible = false;
    public string $teaching_type = 'all';

    public string $ecuSearch = '';
    public bool $showEcuDropdown = false;
    public ?Ecu $selectedEcu = null;

    protected function rules(): array
    {
        return [
            'academic_year_id' => 'required|exists:academic_years,id',
            'ecu_id' => 'required|exists:ecus,id',
            'is_responsible' => 'boolean',
            'teaching_type' => 'required|in:cm,td,tp,all',
        ];
    }

    public function mount(Teacher $teacher): void
    {
        $this->teacher = $teacher;
        $currentYear = AcademicYear::where('is_current', true)->first();
        $this->academic_year_id = $currentYear?->id;
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

    public function openModal(): void
    {
        $this->reset(['ecu_id', 'is_responsible', 'teaching_type', 'ecuSearch', 'selectedEcu']);
        $this->showModal = true;
    }

    public function assign()
    {
        $this->validate();

        $exists = $this->teacher->ecus()
            ->wherePivot('academic_year_id', $this->academic_year_id)
            ->wherePivot('ecu_id', $this->ecu_id)
            ->wherePivot('teaching_type', $this->teaching_type)
            ->exists();

        if ($exists) {
            $this->addError('ecu_id', 'Cet enseignant est déjà assigné à cet ECU pour ce type d\'enseignement.');
            return;
        }

        $this->teacher->ecus()->attach($this->ecu_id, [
            'academic_year_id' => $this->academic_year_id,
            'is_responsible' => $this->is_responsible,
            'teaching_type' => $this->teaching_type,
        ]);

        $this->showModal = false;
        session()->flash('success', __('ECU assigné avec succès.'));

        $this->dispatch('ecu-assigned');
    }

    public function removeAssignment(int $ecuId, int $academicYearId, string $teachingType): void
    {
        $this->teacher->ecus()
            ->wherePivot('academic_year_id', $academicYearId)
            ->wherePivot('teaching_type', $teachingType)
            ->detach($ecuId);

        session()->flash('success', __('Assignation supprimée.'));
        $this->dispatch('ecu-assigned');
    }

    public function render()
    {
        $searchResults = [];
        if ($this->showEcuDropdown && strlen($this->ecuSearch) >= 2) {
            $searchResults = Ecu::with('ue')
                ->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->ecuSearch . '%')
                      ->orWhere('name', 'like', '%' . $this->ecuSearch . '%');
                })->limit(10)->get();
        }

        $assignments = $this->teacher->ecus()
            ->with('ue')
            ->wherePivot('academic_year_id', $this->academic_year_id)
            ->get();

        $contractEcus = VacataireContract::where('teacher_id', $this->teacher->id)
            ->where('academic_year_id', $this->academic_year_id)
            ->whereNotNull('ecu_id')
            ->with('ecu.ue')
            ->get()
            ->map(function ($contract) {
                return [
                    'ecu' => $contract->ecu,
                    'teaching_type' => $contract->teaching_type,
                    'contract' => $contract,
                ];
            });

        return view('livewire.teachers.ecu-assignment', [
            'searchResults' => $searchResults,
            'assignments' => $assignments,
            'contractEcus' => $contractEcus,
            'academicYears' => AcademicYear::orderBy('start_date', 'desc')->get(),
        ]);
    }
}
