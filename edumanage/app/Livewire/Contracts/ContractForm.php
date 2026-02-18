<?php

namespace App\Livewire\Contracts;

use App\Models\AcademicYear;
use App\Models\ProgramYear;
use App\Models\Student;
use App\Models\StudentContract;
use Livewire\Component;

class ContractForm extends Component
{
    public ?StudentContract $contract = null;
    public bool $editMode = false;

    public ?int $student_id = null;
    public string $studentSearch = '';
    public bool $showStudentDropdown = false;
    public ?Student $selectedStudent = null;
    public ?int $academic_year_id = null;
    public ?int $program_year_id = null;
    public string $type = 'inscription';
    public ?string $start_date = null;
    public ?string $end_date = null;
    public float $tuition_fees = 0;
    public float $registration_fees = 0;
    public int $installments = 1;
    public string $status = 'draft';
    public ?string $special_conditions = '';
    public ?string $notes = '';

    public function updatedStudentSearch($value): void
    {
        $this->showStudentDropdown = strlen($value) >= 2;
        if (!$value) {
            $this->student_id = null;
            $this->selectedStudent = null;
        }
    }

    public function selectStudent(int $id): void
    {
        $student = Student::find($id);
        if ($student) {
            $this->student_id = $student->id;
            $this->selectedStudent = $student;
            $this->studentSearch = $student->full_name . ' (' . $student->student_id . ')';
            $this->showStudentDropdown = false;
        }
    }

    public function clearStudent(): void
    {
        $this->student_id = null;
        $this->selectedStudent = null;
        $this->studentSearch = '';
        $this->showStudentDropdown = false;
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

    public function updatedProgramYearId($value): void
    {
        if ($value) {
            $programYear = ProgramYear::find($value);
            if ($programYear) {
                $this->tuition_fees = $programYear->tuition_fees ?? 0;
                $this->registration_fees = $programYear->registration_fees ?? 0;
                $this->installments = $programYear->default_installments ?? 1;
            }
        }
    }

    protected function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'program_year_id' => 'nullable|exists:program_years,id',
            'type' => 'required|in:inscription,formation,stage,apprentissage,autre',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'tuition_fees' => 'required|numeric|min:0',
            'registration_fees' => 'required|numeric|min:0',
            'installments' => 'required|integer|min:1|max:12',
            'status' => 'required|in:draft,active,completed,cancelled,suspended',
            'special_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    public function mount(?StudentContract $contract = null, ?Student $student = null): void
    {
        if ($contract && $contract->exists) {
            $this->contract = $contract;
            $this->editMode = true;
            $this->student_id = $contract->student_id;
            $this->academic_year_id = $contract->academic_year_id;
            $this->program_year_id = $contract->program_year_id;
            $this->type = $contract->type;
            $this->start_date = $contract->start_date?->format('Y-m-d');
            $this->end_date = $contract->end_date?->format('Y-m-d');
            $this->tuition_fees = $contract->tuition_fees;
            $this->registration_fees = $contract->registration_fees;
            $this->status = $contract->status;
            $this->installments = $contract->paymentSchedules()->count() ?: 1;
            $this->special_conditions = $contract->special_conditions ?? '';
            $this->notes = $contract->notes ?? '';
        } elseif ($student && $student->exists) {
            $this->student_id = $student->id;
            $this->selectedStudent = $student;
            $this->studentSearch = $student->full_name . ' (' . $student->student_id . ')';
        }

        if ($this->student_id && !$this->selectedStudent) {
            $this->selectedStudent = Student::find($this->student_id);
            if ($this->selectedStudent) {
                $this->studentSearch = $this->selectedStudent->full_name . ' (' . $this->selectedStudent->student_id . ')';
            }
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
        $count = StudentContract::whereYear('created_at', $year)->count() + 1;
        return sprintf('CTR-%s-%05d', $year, $count);
    }

    public function save()
    {
        $validated = $this->validate();

        $student = Student::find($this->student_id);
        $validated['university_id'] = $student->university_id;

        if ($this->editMode && $this->contract) {
            $this->contract->update($validated);
            $this->contract->generatePaymentSchedule($this->installments);
            session()->flash('success', __('Contrat mis à jour avec succès.'));
            $contract = $this->contract;
        } else {
            $validated['contract_number'] = $this->generateContractNumber();
            $contract = StudentContract::create($validated);
            $contract->generatePaymentSchedule($this->installments);
            session()->flash('success', __('Contrat créé avec succès.'));
        }

        return redirect()->route('contracts.show', $contract);
    }

    public function render()
    {
        $searchResults = [];
        if ($this->showStudentDropdown && strlen($this->studentSearch) >= 2) {
            $searchResults = Student::where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->studentSearch . '%')
                  ->orWhere('last_name', 'like', '%' . $this->studentSearch . '%')
                  ->orWhere('student_id', 'like', '%' . $this->studentSearch . '%');
            })->limit(10)->get();
        }

        return view('livewire.contracts.contract-form', [
            'searchResults' => $searchResults,
            'academicYears' => AcademicYear::orderBy('start_date', 'desc')->get(),
            'programYears' => ProgramYear::with('program')->get(),
        ]);
    }
}
