<?php

namespace App\Livewire\Schedules;

use App\Models\AcademicYear;
use App\Models\Ecu;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\ScheduleSession;
use App\Models\Teacher;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class SessionManager extends Component
{
    use WithPagination;

    public ?int $academicYearId = null;
    public ?int $ecuId = null;
    public ?int $scheduleId = null;
    public string $search = '';
    public string $statusFilter = '';

    // Modal ajout manuel
    public bool $showAddModal = false;
    public ?string $sessionDate = null;
    public ?string $startTime = null;
    public ?string $endTime = null;
    public ?int $teacherId = null;
    public ?int $roomId = null;
    public string $type = 'cm';
    public ?string $notes = null;

    // Modal détails
    public bool $showDetailsModal = false;
    public ?ScheduleSession $selectedSession = null;

    public function mount(): void
    {
        $currentYear = AcademicYear::where('university_id', auth()->user()->university_id)
            ->where('is_current', true)
            ->first();
        $this->academicYearId = $currentYear?->id;
    }

    public function updatedAcademicYearId(): void
    {
        $this->reset(['ecuId', 'scheduleId']);
        $this->resetPage();
    }

    public function updatedEcuId(): void
    {
        $this->reset(['scheduleId']);
        $this->resetPage();
    }

    public function openAddModal(): void
    {
        $this->reset(['sessionDate', 'startTime', 'endTime', 'teacherId', 'roomId', 'type', 'notes']);
        $this->sessionDate = now()->format('Y-m-d');
        $this->showAddModal = true;
    }

    public function closeAddModal(): void
    {
        $this->showAddModal = false;
    }

    public function saveSession(): void
    {
        $this->validate([
            'ecuId' => 'required|exists:ecus,id',
            'sessionDate' => 'required|date',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
            'type' => 'required|in:cm,td,tp',
        ]);

        ScheduleSession::create([
            'schedule_id' => $this->scheduleId,
            'ecu_id' => $this->ecuId,
            'teacher_id' => $this->teacherId,
            'room_id' => $this->roomId,
            'academic_year_id' => $this->academicYearId,
            'session_date' => $this->sessionDate,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'type' => $this->type,
            'status' => 'planned',
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Séance ajoutée avec succès.');
        $this->closeAddModal();
    }

    public function openDetails(int $sessionId): void
    {
        $this->selectedSession = ScheduleSession::with(['ecu', 'teacher', 'room', 'attendances.student'])->find($sessionId);
        $this->showDetailsModal = true;
    }

    public function closeDetails(): void
    {
        $this->showDetailsModal = false;
        $this->selectedSession = null;
    }

    public function markAsCompleted(int $sessionId): void
    {
        $session = ScheduleSession::findOrFail($sessionId);
        $session->markAsCompleted();
        session()->flash('success', 'Séance marquée comme effectuée.');
    }

    public function cancelSession(int $sessionId, string $reason = ''): void
    {
        $session = ScheduleSession::findOrFail($sessionId);
        $session->cancel($reason);
        session()->flash('success', 'Séance annulée.');
    }

    public function deleteSession(int $sessionId): void
    {
        ScheduleSession::whereKey($sessionId)->delete();
        session()->flash('success', 'Séance supprimée.');
    }

    public function getAcademicYearsProperty()
    {
        return AcademicYear::where('university_id', auth()->user()->university_id)
            ->orderByDesc('start_date')
            ->get();
    }

    public function getEcusProperty()
    {
        $universityId = auth()->user()->university_id;
        return Ecu::whereHas('ue.semester.programYear.program', function ($q) use ($universityId) {
            $q->where('university_id', $universityId);
        })->orderBy('code')->get();
    }

    public function getSchedulesProperty()
    {
        if (!$this->ecuId || !$this->academicYearId) {
            return collect();
        }

        return Schedule::where('ecu_id', $this->ecuId)
            ->where('academic_year_id', $this->academicYearId)
            ->with('teacher')
            ->get();
    }

    public function getTeachersProperty()
    {
        return Teacher::where('university_id', auth()->user()->university_id)
            ->orderBy('last_name')
            ->get();
    }

    public function getRoomsProperty()
    {
        return Room::where('university_id', auth()->user()->university_id)
            ->orderBy('code')
            ->get();
    }

    public function getSessionsProperty()
    {
        $query = ScheduleSession::with(['ecu', 'teacher', 'room', 'schedule'])
            ->where('academic_year_id', $this->academicYearId);

        if ($this->ecuId) {
            $query->where('ecu_id', $this->ecuId);
        }

        if ($this->scheduleId) {
            $query->where('schedule_id', $this->scheduleId);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('ecu', function ($eq) {
                    $eq->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('code', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('teacher', function ($tq) {
                    $tq->where('first_name', 'like', '%' . $this->search . '%')
                       ->orWhere('last_name', 'like', '%' . $this->search . '%');
                });
            });
        }

        return $query->orderBy('session_date')->orderBy('start_time')->paginate(20);
    }

    public function getHoursSummaryProperty(): array
    {
        if (!$this->ecuId || !$this->academicYearId) {
            return ['planned' => 0, 'completed' => 0, 'remaining' => 0, 'total' => 0];
        }

        $ecu = Ecu::find($this->ecuId);
        if (!$ecu) {
            return ['planned' => 0, 'completed' => 0, 'remaining' => 0, 'total' => 0];
        }

        $sessions = ScheduleSession::where('ecu_id', $this->ecuId)
            ->where('academic_year_id', $this->academicYearId)
            ->get();

        $plannedHours = 0;
        $completedHours = 0;

        foreach ($sessions as $session) {
            $duration = $session->duration_in_hours;
            $plannedHours += $duration;
            if ($session->status === 'completed') {
                $completedHours += $duration;
            }
        }

        $totalHours = $ecu->total_hours;

        return [
            'planned' => round($plannedHours, 1),
            'completed' => round($completedHours, 1),
            'remaining' => max(0, round($totalHours - $completedHours, 1)),
            'total' => $totalHours,
        ];
    }

    public function render()
    {
        return view('livewire.schedules.session-manager', [
            'academicYears' => $this->academicYears,
            'ecus' => $this->ecus,
            'schedules' => $this->schedules,
            'teachers' => $this->teachers,
            'rooms' => $this->rooms,
            'sessions' => $this->sessions,
            'hoursSummary' => $this->hoursSummary,
        ]);
    }
}
