<?php

namespace App\Livewire\Students;

use App\Models\AcademicYear;
use App\Models\ProgramYear;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class StudentForm extends Component
{
    use WithFileUploads;

    public ?int $studentId = null;
    public bool $editMode = false;

    public string $student_id = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public ?string $phone = '';
    public ?string $birth_date = null;
    public ?string $birth_place = '';
    public ?string $gender = '';
    public ?string $address = '';
    public ?string $nationality = '';
    public string $status = 'active';
    public $photo = null;

    public ?int $program_year_id = null;
    public ?int $academic_year_id = null;
    public bool $create_contract = false;

    protected function rules(): array
    {
        $studentId = $this->studentId;
        $universityId = auth()->user()->university_id;

        return [
            'student_id' => "required|string|max:50|unique:students,student_id,{$studentId},id,university_id,{$universityId}",
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => "required|email|unique:students,email,{$studentId},id,university_id,{$universityId}",
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'nationality' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,graduated,suspended',
            'photo' => 'nullable|image|max:2048',
            'program_year_id' => $this->editMode ? 'nullable' : 'nullable|exists:program_years,id',
            'academic_year_id' => $this->editMode ? 'nullable' : 'nullable|exists:academic_years,id',
            'create_contract' => $this->editMode ? 'nullable' : 'boolean',
        ];
    }

    public function mount($studentId = null): void
    {
        if ($studentId) {
            $student = Student::find($studentId);
            
            if ($student) {
                $this->studentId = $student->id;
                $this->editMode = true;
                $this->student_id = $student->student_id ?? '';
                $this->first_name = $student->first_name ?? '';
                $this->last_name = $student->last_name ?? '';
                $this->email = $student->email ?? '';
                $this->phone = $student->phone ?? '';
                $this->birth_date = $student->birth_date?->format('Y-m-d');
                $this->birth_place = $student->birth_place ?? '';
                $this->gender = $student->gender ?? '';
                $this->address = $student->address ?? '';
                $this->nationality = $student->nationality ?? '';
                $this->status = $student->status ?? 'active';

                $currentEnrollment = $student->currentEnrollment;
                $this->program_year_id = $currentEnrollment?->program_year_id;
                $this->academic_year_id = $currentEnrollment?->academic_year_id;
            }
        } else {
            $currentYear = AcademicYear::where('is_current', true)->first();
            $this->academic_year_id = $currentYear?->id;
        }
    }

    public function save()
    {
        \Log::info('StudentForm save() called', [
            'editMode' => $this->editMode,
            'studentId' => $this->studentId,
            'first_name' => $this->first_name
        ]);

        try {
            $validated = $this->validate();
            
            \Log::info('Validation passed', $validated);

            $programYearId = $validated['program_year_id'] ?? null;
            $academicYearId = $validated['academic_year_id'] ?? null;
            $createContract = (bool)($validated['create_contract'] ?? false);

            unset($validated['program_year_id'], $validated['academic_year_id'], $validated['create_contract']);

            if ($this->photo) {
                $validated['photo'] = $this->photo->store('students', 'public');
            }

            $student = DB::transaction(function () use ($validated, $programYearId, $academicYearId) {
                if ($this->editMode && $this->studentId) {
                    $student = Student::findOrFail($this->studentId);
                    $student->update($validated);
                    \Log::info('Student updated', ['id' => $this->studentId]);
                    session()->flash('success', __('Étudiant mis à jour avec succès.'));
                    return $student;
                }

                $validated['university_id'] = auth()->user()->university_id;
                $validated['enrollment_date'] = now();
                $student = Student::create($validated);
                \Log::info('Student created', ['id' => $student->id]);

                if ($programYearId && $academicYearId) {
                    StudentEnrollment::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'program_year_id' => $programYearId,
                            'academic_year_id' => $academicYearId,
                        ],
                        [
                            'university_id' => auth()->user()->university_id,
                            'status' => 'enrolled',
                            'enrollment_date' => now(),
                        ]
                    );
                }

                session()->flash('success', __('Étudiant créé avec succès.'));
                return $student;
            });

            if (!$this->editMode && $createContract) {
                return $this->redirect(route('contracts.create', $student), navigate: true);
            }

            return $this->redirect(route('students.index'), navigate: true);
        } catch (\Exception $e) {
            \Log::error('StudentForm save error', ['error' => $e->getMessage()]);
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $universityId = auth()->user()->university_id;

        return view('livewire.students.student-form', [
            'academicYears' => AcademicYear::orderBy('start_date', 'desc')->get(),
            'programYears' => ProgramYear::with('program')
                ->whereHas('program', fn($q) => $q->where('university_id', $universityId))
                ->orderBy('year_number')
                ->get(),
        ]);
    }
}
