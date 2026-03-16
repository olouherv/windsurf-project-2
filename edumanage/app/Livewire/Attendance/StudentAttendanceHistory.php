<?php

namespace App\Livewire\Attendance;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class StudentAttendanceHistory extends Component
{
    use WithPagination;

    public Student $student;
    public ?int $academicYearId = null;

    public function mount(Student $student): void
    {
        $this->student = $student;
        
        $currentYear = AcademicYear::where('university_id', auth()->user()->university_id)
            ->where('is_current', true)
            ->first();
        
        $this->academicYearId = $currentYear?->id;
    }

    public function updatedAcademicYearId(): void
    {
        $this->resetPage();
    }

    public function getAttendancesProperty()
    {
        return Attendance::where('student_id', $this->student->id)
            ->when($this->academicYearId, function ($q) {
                $q->whereHas('schedule', function ($sq) {
                    $sq->where('academic_year_id', $this->academicYearId);
                });
            })
            ->with(['schedule.ecu', 'schedule.teacher'])
            ->orderByDesc('session_date')
            ->paginate(20);
    }

    public function getStatisticsProperty(): array
    {
        $query = Attendance::where('student_id', $this->student->id);
        
        if ($this->academicYearId) {
            $query->whereHas('schedule', function ($sq) {
                $sq->where('academic_year_id', $this->academicYearId);
            });
        }

        $total = $query->count();
        $present = (clone $query)->where('status', 'present')->count();
        $absent = (clone $query)->where('status', 'absent')->count();
        $late = (clone $query)->where('status', 'late')->count();
        $excused = (clone $query)->where('status', 'excused')->count();

        $rate = $total > 0 ? round(($present + $late) / $total * 100, 1) : 0;

        return compact('total', 'present', 'absent', 'late', 'excused', 'rate');
    }

    public function getAcademicYearsProperty()
    {
        return AcademicYear::where('university_id', auth()->user()->university_id)
            ->orderByDesc('start_date')
            ->get();
    }

    public function render()
    {
        return view('livewire.attendance.student-attendance-history', [
            'attendances' => $this->attendances,
            'statistics' => $this->statistics,
            'academicYears' => $this->academicYears,
        ]);
    }
}
