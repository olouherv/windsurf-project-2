<?php

namespace App\Livewire\Vacataires;

use App\Models\AcademicYear;
use App\Models\Ecu;
use App\Models\Teacher;
use App\Models\VacataireContract;
use Livewire\Component;

class ContractForm extends Component
{
    public ?VacataireContract $contract = null;
    public bool $editMode = false;

    public ?int $teacher_id = null;
    public string $teacherSearch = '';
    public bool $showTeacherDropdown = false;
    public ?Teacher $selectedTeacher = null;

    public ?int $ecu_id = null;
    public string $ecuSearch = '';
    public bool $showEcuDropdown = false;
    public ?Ecu $selectedEcu = null;

    public string $teaching_type = 'all';
    public ?int $academic_year_id = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public int $total_hours_planned = 0;
    public float $hourly_rate = 35.00;
    public string $status = 'draft';
    public ?string $notes = '';

    public function updatedTeacherSearch($value): void
    {
        $this->showTeacherDropdown = strlen($value) >= 2;
        if (!$value) {
            $this->teacher_id = null;
            $this->selectedTeacher = null;
        }
    }

    public function selectTeacher(int $id): void
    {
        $teacher = Teacher::find($id);
        if ($teacher) {
            $this->teacher_id = $teacher->id;
            $this->selectedTeacher = $teacher;
            $this->teacherSearch = $teacher->full_name . ' (' . $teacher->employee_id . ')';
            $this->showTeacherDropdown = false;
        }
    }

    public function clearTeacher(): void
    {
        $this->teacher_id = null;
        $this->selectedTeacher = null;
        $this->teacherSearch = '';
        $this->showTeacherDropdown = false;
        $this->clearEcu();
    }

    public function updatedEcuSearch($value): void
    {
        $this->showEcuDropdown = strlen($value) >= 2;
        if (!$value) {
            $this->ecu_id = null;
            $this->selectedEcu = null;
            $this->total_hours_planned = 0;
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
            $this->updateHoursFromEcu();
        }
    }

    public function clearEcu(): void
    {
        $this->ecu_id = null;
        $this->selectedEcu = null;
        $this->ecuSearch = '';
        $this->showEcuDropdown = false;
        $this->total_hours_planned = 0;
    }

    public function updatedTeachingType(): void
    {
        $this->updateHoursFromEcu();
    }

    protected function updateHoursFromEcu(): void
    {
        if (!$this->selectedEcu) {
            return;
        }

        $this->total_hours_planned = match($this->teaching_type) {
            'cm' => $this->selectedEcu->hours_cm ?? 0,
            'td' => $this->selectedEcu->hours_td ?? 0,
            'tp' => $this->selectedEcu->hours_tp ?? 0,
            'all' => $this->selectedEcu->total_hours ?? 0,
        };
    }

    public function updatedAcademicYearId($value): void
    {
        if ($value) {
            $academicYear = AcademicYear::find($value);
            if ($academicYear) {
                $this->start_date = $academicYear->start_date?->format('Y-m-d');
                $this->end_date = $academicYear->end_date?->format('Y-m-d');
            }
        }
    }

    protected function rules(): array
    {
        return [
            'teacher_id' => 'required|exists:teachers,id',
            'ecu_id' => 'required|exists:ecus,id',
            'teaching_type' => 'required|in:cm,td,tp,all',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_hours_planned' => 'required|integer|min:1',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,completed,cancelled',
            'notes' => 'nullable|string',
        ];
    }

    public function mount(?VacataireContract $vacataireContract = null, ?Teacher $teacher = null): void
    {
        if ($vacataireContract && $vacataireContract->exists) {
            $this->contract = $vacataireContract;
            $this->editMode = true;
            $this->teacher_id = $vacataireContract->teacher_id;
            $this->selectedTeacher = $vacataireContract->teacher;
            $this->teacherSearch = $vacataireContract->teacher->full_name . ' (' . $vacataireContract->teacher->employee_id . ')';
            $this->academic_year_id = $vacataireContract->academic_year_id;
            
            if ($vacataireContract->ecu) {
                $this->ecu_id = $vacataireContract->ecu_id;
                $this->selectedEcu = $vacataireContract->ecu;
                $this->ecuSearch = $vacataireContract->ecu->code . ' - ' . $vacataireContract->ecu->name;
            }
            $this->teaching_type = $vacataireContract->teaching_type ?? 'all';
            
            $this->start_date = $vacataireContract->start_date?->format('Y-m-d');
            $this->end_date = $vacataireContract->end_date?->format('Y-m-d');
            $this->total_hours_planned = $vacataireContract->total_hours_planned;
            $this->hourly_rate = $vacataireContract->hourly_rate;
            $this->status = $vacataireContract->status;
            $this->notes = $vacataireContract->notes ?? '';
        } elseif ($teacher && $teacher->exists) {
            $this->teacher_id = $teacher->id;
            $this->selectedTeacher = $teacher;
            $this->teacherSearch = $teacher->full_name . ' (' . $teacher->employee_id . ')';
        }

        if (!$this->academic_year_id) {
            $currentYear = AcademicYear::where('is_current', true)->first();
            if ($currentYear) {
                $this->academic_year_id = $currentYear->id;
                if (!$this->start_date) {
                    $this->start_date = $currentYear->start_date?->format('Y-m-d');
                }
                if (!$this->end_date) {
                    $this->end_date = $currentYear->end_date?->format('Y-m-d');
                }
            }
        }
    }

    protected function generateContractNumber(): string
    {
        $year = date('Y');
        $count = VacataireContract::whereYear('created_at', $year)->count() + 1;
        return sprintf('VAC-%s-%05d', $year, $count);
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->editMode && $this->contract) {
            $this->contract->update($validated);
            session()->flash('success', __('Contrat vacataire mis à jour avec succès.'));
            $contract = $this->contract;
        } else {
            $validated['contract_number'] = $this->generateContractNumber();
            $contract = VacataireContract::create($validated);
            session()->flash('success', __('Contrat vacataire créé avec succès.'));
        }

        return redirect()->route('vacataire-contracts.show', $contract);
    }

    public function render()
    {
        $teacherResults = [];
        if ($this->showTeacherDropdown && strlen($this->teacherSearch) >= 2) {
            $teacherResults = Teacher::where('type', 'vacataire')
                ->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->teacherSearch . '%')
                      ->orWhere('last_name', 'like', '%' . $this->teacherSearch . '%')
                      ->orWhere('employee_id', 'like', '%' . $this->teacherSearch . '%');
                })->limit(10)->get();
        }

        $ecuResults = [];
        if ($this->showEcuDropdown && strlen($this->ecuSearch) >= 2) {
            $ecuResults = Ecu::with('ue')
                ->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->ecuSearch . '%')
                      ->orWhere('name', 'like', '%' . $this->ecuSearch . '%');
                })->limit(10)->get();
        }

        return view('livewire.vacataires.contract-form', [
            'teacherResults' => $teacherResults,
            'ecuResults' => $ecuResults,
            'academicYears' => AcademicYear::orderBy('start_date', 'desc')->get(),
        ]);
    }
}
