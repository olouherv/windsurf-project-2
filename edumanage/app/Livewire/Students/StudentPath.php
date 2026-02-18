<?php

namespace App\Livewire\Students;

use App\Models\Student;
use App\Models\StudentEnrollment;
use Livewire\Component;

class StudentPath extends Component
{
    public Student $student;

    public function mount(Student $student): void
    {
        $this->student = $student;
    }

    public function render()
    {
        $enrollments = StudentEnrollment::where('student_id', $this->student->id)
            ->with(['programYear.program', 'academicYear'])
            ->orderBy('academic_year_id', 'desc')
            ->get();

        return view('livewire.students.student-path', [
            'enrollments' => $enrollments,
        ]);
    }
}
