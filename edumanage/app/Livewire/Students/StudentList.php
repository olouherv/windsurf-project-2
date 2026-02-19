<?php

namespace App\Livewire\Students;

use App\Models\AcademicYear;
use App\Models\ProgramYear;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Livewire\Component;
use Livewire\WithPagination;

class StudentList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public array $selectedStudentIds = [];
    public bool $showEnrollModal = false;
    public ?int $bulkAcademicYearId = null;
    public ?int $bulkProgramYearId = null;

    protected $queryString = ['search', 'status'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteStudent(int $id): void
    {
        $student = Student::findOrFail($id);
        $student->delete();
        
        session()->flash('success', __('Étudiant supprimé avec succès.'));
    }

    public function toggleStudentSelection(int $id): void
    {
        if (in_array($id, $this->selectedStudentIds, true)) {
            $this->selectedStudentIds = array_values(array_diff($this->selectedStudentIds, [$id]));
            return;
        }

        $this->selectedStudentIds[] = $id;
        $this->selectedStudentIds = array_values(array_unique($this->selectedStudentIds));
    }

    public function toggleSelectPage(array $ids): void
    {
        $allSelected = count(array_diff($ids, $this->selectedStudentIds)) === 0;
        if ($allSelected) {
            $this->selectedStudentIds = array_values(array_diff($this->selectedStudentIds, $ids));
            return;
        }

        $this->selectedStudentIds = array_values(array_unique(array_merge($this->selectedStudentIds, $ids)));
    }

    public function openEnrollModal(): void
    {
        if (count($this->selectedStudentIds) === 0) {
            return;
        }

        $universityId = auth()->user()->university_id;
        $currentYear = AcademicYear::where('university_id', $universityId)
            ->where('is_current', true)
            ->first();
        $this->bulkAcademicYearId = $currentYear?->id;
        $this->bulkProgramYearId = null;
        $this->showEnrollModal = true;
    }

    public function closeEnrollModal(): void
    {
        $this->showEnrollModal = false;
    }

    public function bulkEnroll(): void
    {
        if (count($this->selectedStudentIds) === 0) {
            return;
        }

        if (!$this->bulkAcademicYearId || !$this->bulkProgramYearId) {
            return;
        }

        $universityId = auth()->user()->university_id;

        $alreadyEnrolledIds = StudentEnrollment::query()
            ->where('academic_year_id', $this->bulkAcademicYearId)
            ->whereIn('student_id', $this->selectedStudentIds)
            ->where('status', '!=', 'abandoned')
            ->pluck('student_id')
            ->all();

        $alreadyEnrolledIds = array_map('intval', $alreadyEnrolledIds);
        $toEnrollIds = array_values(array_diff($this->selectedStudentIds, $alreadyEnrolledIds));

        foreach ($toEnrollIds as $studentId) {
            StudentEnrollment::create([
                'university_id' => $universityId,
                'student_id' => $studentId,
                'program_year_id' => $this->bulkProgramYearId,
                'academic_year_id' => $this->bulkAcademicYearId,
                'status' => 'enrolled',
                'enrollment_date' => now(),
            ]);
        }

        $createdCount = count($toEnrollIds);
        $alreadyCount = count($alreadyEnrolledIds);
        $this->selectedStudentIds = [];
        $this->closeEnrollModal();

        $message = $createdCount . ' étudiant(s) inscrit(s) avec succès.';
        if ($alreadyCount > 0) {
            $message .= ' ' . $alreadyCount . " déjà inscrit(s) pour l'année sélectionnée (ignoré).";
        }

        session()->flash('success', $message);
        $this->resetPage();
        $this->dispatch('$refresh');
    }

    public function render()
    {
        $universityId = auth()->user()->university_id;

        $students = Student::query()
            ->where('university_id', $universityId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('student_id', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        $academicYears = AcademicYear::where('university_id', $universityId)
            ->orderBy('start_date', 'desc')
            ->get();

        $programYears = ProgramYear::query()
            ->whereHas('program', fn ($q) => $q->where('university_id', $universityId))
            ->with('program')
            ->get();

        return view('livewire.students.student-list', compact('students', 'academicYears', 'programYears'));
    }
}
