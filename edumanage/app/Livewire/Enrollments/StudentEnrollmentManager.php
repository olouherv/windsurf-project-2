<?php

namespace App\Livewire\Enrollments;

use App\Models\AcademicYear;
use App\Models\ProgramYear;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Livewire\Component;

class StudentEnrollmentManager extends Component
{
    public ProgramYear $programYear;
    public ?int $academicYearId = null;

    public bool $showModal = false;
    public string $studentSearch = '';
    public array $selectedStudents = [];

    protected $listeners = ['refreshEnrollments' => '$refresh'];

    public function mount(ProgramYear $programYear): void
    {
        $this->programYear = $programYear;
        $currentYear = AcademicYear::where('is_current', true)->first();
        $this->academicYearId = $currentYear?->id;
    }

    public function openModal(): void
    {
        $this->showModal = true;
        $this->studentSearch = '';
        $this->selectedStudents = [];
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->studentSearch = '';
        $this->selectedStudents = [];
    }

    public function toggleStudent(int $studentId): void
    {
        if (in_array($studentId, $this->selectedStudents)) {
            $this->selectedStudents = array_diff($this->selectedStudents, [$studentId]);
        } else {
            $this->selectedStudents[] = $studentId;
        }
    }

    public function enrollStudents(): void
    {
        if (empty($this->selectedStudents) || !$this->academicYearId) {
            return;
        }

        foreach ($this->selectedStudents as $studentId) {
            StudentEnrollment::firstOrCreate(
                [
                    'student_id' => $studentId,
                    'program_year_id' => $this->programYear->id,
                    'academic_year_id' => $this->academicYearId,
                ],
                [
                    'university_id' => auth()->user()->university_id,
                    'status' => 'enrolled',
                    'enrollment_date' => now(),
                ]
            );
        }

        $this->closeModal();
        session()->flash('success', count($this->selectedStudents) . ' étudiant(s) inscrit(s) avec succès.');
    }

    public function updateStatus(int $enrollmentId, string $status): void
    {
        $enrollment = StudentEnrollment::find($enrollmentId);
        if ($enrollment) {
            $enrollment->update([
                'status' => $status,
                'validation_date' => $status === 'validated' ? now() : null,
            ]);
        }
    }

    public function removeEnrollment(int $enrollmentId): void
    {
        StudentEnrollment::destroy($enrollmentId);
        session()->flash('success', 'Inscription supprimée.');
    }

    public function render()
    {
        $enrollments = StudentEnrollment::where('program_year_id', $this->programYear->id)
            ->when($this->academicYearId, fn($q) => $q->where('academic_year_id', $this->academicYearId))
            ->with(['student', 'academicYear'])
            ->orderBy('created_at', 'desc')
            ->get();

        $searchResults = collect();
        if ($this->showModal && strlen($this->studentSearch) >= 2) {
            $enrolledIds = $enrollments->pluck('student_id')->toArray();
            $searchResults = Student::where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->studentSearch . '%')
                      ->orWhere('last_name', 'like', '%' . $this->studentSearch . '%')
                      ->orWhere('student_id', 'like', '%' . $this->studentSearch . '%');
                })
                ->whereNotIn('id', $enrolledIds)
                ->limit(20)
                ->get();
        }

        return view('livewire.enrollments.student-enrollment-manager', [
            'enrollments' => $enrollments,
            'searchResults' => $searchResults,
            'academicYears' => AcademicYear::orderBy('start_date', 'desc')->get(),
        ]);
    }
}
