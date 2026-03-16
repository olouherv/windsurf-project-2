<?php

namespace App\Livewire\Attendance;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Ecu;
use App\Models\Program;
use App\Models\ProgramYear;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Carbon\Carbon;
use Livewire\Component;

class AttendanceManager extends Component
{
    public ?int $academicYearId = null;
    public ?int $programId = null;
    public ?int $programYearId = null;
    public ?int $ecuId = null;
    public ?int $scheduleId = null;
    public ?string $sessionDate = null;

    public array $attendances = [];
    public bool $showMarkAllModal = false;
    public string $markAllStatus = 'present';

    protected $listeners = ['refreshAttendance' => '$refresh'];

    public function mount(): void
    {
        $universityId = auth()->user()->university_id;
        
        $currentYear = AcademicYear::where('university_id', $universityId)
            ->where('is_current', true)
            ->first();
        
        $this->academicYearId = $currentYear?->id;
        $this->sessionDate = now()->format('Y-m-d');
    }

    public function updatedAcademicYearId(): void
    {
        $this->reset(['programYearId', 'ecuId', 'scheduleId', 'attendances']);
    }

    public function updatedProgramId(): void
    {
        $this->reset(['programYearId', 'ecuId', 'scheduleId', 'attendances']);
    }

    public function updatedProgramYearId(): void
    {
        $this->reset(['ecuId', 'scheduleId', 'attendances']);
    }

    public function updatedEcuId(): void
    {
        $this->reset(['scheduleId', 'attendances']);
    }

    public function updatedScheduleId(): void
    {
        $this->loadAttendances();
    }

    public function updatedSessionDate(): void
    {
        $this->loadAttendances();
    }

    public function loadAttendances(): void
    {
        if (!$this->scheduleId || !$this->sessionDate || !$this->programYearId) {
            $this->attendances = [];
            return;
        }

        $schedule = Schedule::find($this->scheduleId);
        if (!$schedule) {
            $this->attendances = [];
            return;
        }

        // Chercher les étudiants inscrits pour cette année académique et ce niveau
        $students = Student::where('university_id', auth()->user()->university_id)
            ->where(function ($query) {
                // Via enrollments (inscriptions financières)
                $query->whereHas('enrollments', function ($q) {
                    $q->where('academic_year_id', $this->academicYearId)
                      ->where('program_year_id', $this->programYearId);
                })
                // Ou via pedagogicEnrollments (inscriptions pédagogiques)
                ->orWhereHas('pedagogicEnrollments', function ($q) {
                    $q->where('academic_year_id', $this->academicYearId)
                      ->where('program_year_id', $this->programYearId);
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $existingAttendances = Attendance::where('schedule_id', $this->scheduleId)
            ->where('session_date', $this->sessionDate)
            ->get()
            ->keyBy('student_id');

        $this->attendances = [];
        foreach ($students as $student) {
            $existing = $existingAttendances->get($student->id);
            $this->attendances[$student->id] = [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'student_matricule' => $student->student_id,
                'status' => $existing?->status ?? 'present',
                'late_minutes' => $existing?->late_minutes ?? null,
                'excuse_reason' => $existing?->excuse_reason ?? '',
                'notes' => $existing?->notes ?? '',
                'id' => $existing?->id,
            ];
        }
    }

    public function updateStatus(int $studentId, string $status): void
    {
        if (isset($this->attendances[$studentId])) {
            $this->attendances[$studentId]['status'] = $status;
            if ($status !== 'late') {
                $this->attendances[$studentId]['late_minutes'] = null;
            }
            if ($status !== 'excused') {
                $this->attendances[$studentId]['excuse_reason'] = '';
            }
        }
    }

    public function markAllAs(string $status): void
    {
        foreach ($this->attendances as $studentId => $attendance) {
            $this->attendances[$studentId]['status'] = $status;
            if ($status !== 'late') {
                $this->attendances[$studentId]['late_minutes'] = null;
            }
            if ($status !== 'excused') {
                $this->attendances[$studentId]['excuse_reason'] = '';
            }
        }
        $this->showMarkAllModal = false;
    }

    public function saveAttendances(): void
    {
        if (!$this->scheduleId || !$this->sessionDate) {
            session()->flash('error', 'Veuillez sélectionner une séance et une date.');
            return;
        }

        $userId = auth()->id();
        $now = now();

        foreach ($this->attendances as $studentId => $data) {
            Attendance::updateOrCreate(
                [
                    'schedule_id' => $this->scheduleId,
                    'student_id' => $studentId,
                    'session_date' => $this->sessionDate,
                ],
                [
                    'status' => $data['status'],
                    'late_minutes' => $data['status'] === 'late' ? ($data['late_minutes'] ?? 0) : null,
                    'excuse_reason' => $data['status'] === 'excused' ? ($data['excuse_reason'] ?? '') : null,
                    'notes' => $data['notes'] ?? null,
                    'marked_by' => $userId,
                    'marked_at' => $now,
                ]
            );
        }

        session()->flash('success', 'Présences enregistrées avec succès.');
        $this->loadAttendances();
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

    public function getEcusProperty()
    {
        if (!$this->programYearId) {
            return collect();
        }

        return Ecu::whereHas('ue.semester', function ($q) {
            $q->where('program_year_id', $this->programYearId);
        })->orderBy('code')->get();
    }

    public function getSchedulesProperty()
    {
        if (!$this->ecuId || !$this->academicYearId) {
            return collect();
        }

        return Schedule::where('ecu_id', $this->ecuId)
            ->where('academic_year_id', $this->academicYearId)
            ->with(['teacher', 'room'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    public function getSelectedEcuProperty()
    {
        if (!$this->ecuId) {
            return null;
        }
        return Ecu::find($this->ecuId);
    }

    public function getHoursSummaryProperty(): ?array
    {
        if (!$this->selectedEcu || !$this->academicYearId) {
            return null;
        }
        return $this->selectedEcu->getHoursSummary($this->academicYearId);
    }

    public function getStatisticsProperty(): array
    {
        if (empty($this->attendances)) {
            return ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0, 'total' => 0];
        }

        $stats = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
        foreach ($this->attendances as $attendance) {
            $stats[$attendance['status']] = ($stats[$attendance['status']] ?? 0) + 1;
        }
        $stats['total'] = count($this->attendances);

        return $stats;
    }

    public function render()
    {
        return view('livewire.attendance.attendance-manager', [
            'academicYears' => $this->academicYears,
            'programs' => $this->programs,
            'programYears' => $this->programYears,
            'ecus' => $this->ecus,
            'schedules' => $this->schedules,
            'statistics' => $this->statistics,
            'selectedEcu' => $this->selectedEcu,
            'hoursSummary' => $this->hoursSummary,
        ]);
    }
}
