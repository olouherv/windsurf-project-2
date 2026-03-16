<?php

namespace App\Livewire\Students;

use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\ProgramYear;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class StudentsByProgram extends Component
{
    use WithPagination;

    public ?int $academicYearId = null;
    public ?int $programId = null;
    public ?int $programYearId = null;
    public string $search = '';

    public function mount(): void
    {
        $universityId = auth()->user()->university_id;
        
        $currentYear = AcademicYear::where('university_id', $universityId)
            ->where('is_current', true)
            ->first();
        
        $this->academicYearId = $currentYear?->id;
    }

    public function updatedAcademicYearId(): void
    {
        $this->programYearId = null;
        $this->resetPage();
    }

    public function updatedProgramId(): void
    {
        $this->programYearId = null;
        $this->resetPage();
    }

    public function updatedProgramYearId(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function getStudentsProperty()
    {
        $universityId = auth()->user()->university_id;

        $query = Student::where('university_id', $universityId)
            ->with(['user', 'currentEnrollment.programYear.program']);

        if ($this->academicYearId && $this->programYearId) {
            $query->whereHas('enrollments', function ($q) {
                $q->where('academic_year_id', $this->academicYearId)
                  ->where('program_year_id', $this->programYearId);
            });
        } elseif ($this->academicYearId && $this->programId) {
            $query->whereHas('enrollments', function ($q) {
                $q->where('academic_year_id', $this->academicYearId)
                  ->whereHas('programYear', function ($pq) {
                      $pq->where('program_id', $this->programId);
                  });
            });
        } elseif ($this->academicYearId) {
            $query->whereHas('enrollments', function ($q) {
                $q->where('academic_year_id', $this->academicYearId);
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('student_id', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('last_name')->orderBy('first_name')->paginate(25);
    }

    public function getAcademicYearsProperty()
    {
        return AcademicYear::where('university_id', auth()->user()->university_id)
            ->orderByDesc('start_date')
            ->get();
    }

    public function getProgramsProperty()
    {
        return Program::where('university_id', auth()->user()->university_id)
            ->orderBy('name')
            ->get();
    }

    public function getProgramYearsProperty()
    {
        if (!$this->programId) {
            return collect();
        }

        return ProgramYear::where('program_id', $this->programId)
            ->orderBy('year_number')
            ->get();
    }

    public function getStudentCountProperty(): int
    {
        return $this->students->total();
    }

    public function render()
    {
        return view('livewire.students.students-by-program', [
            'students' => $this->students,
            'academicYears' => $this->academicYears,
            'programs' => $this->programs,
            'programYears' => $this->programYears,
            'studentCount' => $this->studentCount,
        ]);
    }
}
