<?php

namespace App\Livewire\Schedules;

use App\Models\AcademicYear;
use App\Models\Ecu;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\ScheduleSession;
use App\Models\StudentGroup;
use App\Models\Teacher;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class ScheduleCalendar extends Component
{
    public int $year;
    public int $month;
    public ?string $selectedDate = null;
    public ?int $academicYearId = null;

    // Filtres
    public ?string $filterCategory = null;
    public ?int $filterEcuId = null;
    public ?int $filterTeacherId = null;
    public ?int $filterRoomId = null;

    // Modal ajout/modification
    public bool $showSessionModal = false;
    public bool $editMode = false;
    public ?int $sessionId = null;

    // Champs du formulaire
    public string $category = 'course'; // 'course' ou 'activity'
    public ?string $title = null; // pour les activités
    public ?int $ecuId = null;
    public ?int $teacherId = null;
    public ?int $roomId = null;
    public ?int $studentGroupId = null;
    public string $sessionDate = '';
    public string $startTime = '08:00';
    public string $endTime = '12:00';
    public string $type = 'cm';
    public ?string $notes = null;

    // Répétition
    public bool $isRecurring = false;
    public string $repeatUntil = 'end_of_hours'; // 'end_of_hours', 'date', 'count'
    public ?string $repeatEndDate = null;
    public int $repeatCount = 10;
    public array $repeatDays = []; // jours de répétition (0=Dim, 1=Lun, etc.)

    // Détails du jour sélectionné
    public bool $showDayDetails = false;

    protected $listeners = ['refreshCalendar' => '$refresh'];

    public function updatedSessionDate() { $this->reset('teacherId', 'roomId'); }
    public function updatedStartTime() { $this->reset('teacherId', 'roomId'); }
    public function updatedEndTime() { $this->reset('teacherId', 'roomId'); }

    public function mount(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
        
        $currentYear = AcademicYear::where('university_id', auth()->user()->university_id)
            ->where('is_current', true)
            ->first();
        $this->academicYearId = $currentYear?->id;
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
        $this->selectedDate = null;
        $this->showDayDetails = false;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year = $date->year;
        $this->month = $date->month;
        $this->selectedDate = null;
        $this->showDayDetails = false;
    }

    public function goToToday(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
        $this->selectedDate = now()->toDateString();
        $this->showDayDetails = true;
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->showDayDetails = true;
    }

    public function openAddSession(?string $date = null): void
    {
        $this->resetSessionForm();
        $this->sessionDate = Carbon::parse($date ?? $this->selectedDate ?? now())->toDateString();
        $this->repeatDays = [Carbon::parse($this->sessionDate)->dayOfWeek];
        $this->showSessionModal = true;
        $this->editMode = false;
    }

    public function openEditSession(int $sessionId): void
    {
        $session = ScheduleSession::findOrFail($sessionId);

        $this->sessionId = $session->id;
        $this->ecuId = $session->ecu_id;
        $this->teacherId = $session->teacher_id;
        $this->roomId = $session->room_id;
        $this->studentGroupId = $session->student_group_id;

        $this->sessionDate = Carbon::parse($session->session_date)->toDateString();

        $this->startTime = $session->start_time;
        $this->endTime = $session->end_time;
        $this->type = $session->type;
        $this->notes = $session->notes;
        $this->isRecurring = false;

        $this->showSessionModal = true;
        $this->editMode = true;
    }

    public function closeSessionModal(): void
    {
        $this->showSessionModal = false;
        $this->resetSessionForm();
    }

    protected function resetSessionForm(): void
    {
        $this->category = 'course';
        $this->title = null;
        $this->ecuId = null;
        $this->teacherId = null;
        $this->roomId = null;
        $this->studentGroupId = null;
        $this->sessionDate = '';
        $this->startTime = '08:00';
        $this->endTime = '12:00';
        $this->type = 'cm';
        $this->notes = null;
        $this->isRecurring = false;
        $this->repeatUntil = 'end_of_hours';
        $this->repeatEndDate = null;
        $this->repeatCount = 10;
        $this->repeatDays = [];
        $this->sessionId = null;
    }

        public function saveSession(): void
    {
        $data = $this->validate([
            'category' => 'required|in:course,activity',
            'title' => 'required_if:category,activity|nullable|string|max:255',
            'ecuId' => 'required_if:category,course|nullable|exists:ecus,id',
            'sessionDate' => 'required|date',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
            'type' => 'required|in:cm,td,tp',
            'teacherId' => 'nullable|exists:teachers,id',
            'roomId' => 'nullable|exists:rooms,id',
            'studentGroupId' => 'nullable|exists:student_groups,id',
        ]);

        // Conflit ECU (seulement pour les cours)
        if ($this->category === 'course' && $this->ecuId) {
            $conflitEcu = ScheduleSession::where('ecu_id', $this->ecuId)
                ->where('session_date', $this->sessionDate)
                ->where('start_time', '<', $this->endTime)
                ->where('end_time', '>', $this->startTime)
                ->when($this->sessionId, fn ($q) => $q->where('id', '!=', $this->sessionId))
                ->exists();
            if ($conflitEcu) {
                throw ValidationException::withMessages([
                    'ecuId' => 'Impossible : Cette matière est déjà planifiée à ce créneau.'
                ]);
            }
        }
        // Conflit Salle
        if ($this->roomId) {
            $conflitSalle = ScheduleSession::where('room_id', $this->roomId)
                ->where('session_date', $this->sessionDate)
                ->where('start_time', '<', $this->endTime)
                ->where('end_time', '>', $this->startTime)
                ->when($this->sessionId, fn ($q) => $q->where('id', '!=', $this->sessionId))
                ->exists();
            if ($conflitSalle) {
                throw ValidationException::withMessages([
                    'roomId' => 'Impossible : Cette salle est déjà occupée à ce créneau.'
                ]);
            }
        }
        // Conflit Enseignant
        if ($this->teacherId) {
            $conflitEns = ScheduleSession::where('teacher_id', $this->teacherId)
                ->where('session_date', $this->sessionDate)
                ->where('start_time', '<', $this->endTime)
                ->where('end_time', '>', $this->startTime)
                ->when($this->sessionId, fn ($q) => $q->where('id', '!=', $this->sessionId))
                ->exists();
            if ($conflitEns) {
                throw ValidationException::withMessages([
                    'teacherId' => 'Impossible : Cet enseignant a déjà une séance à ce créneau.'
                ]);
            }
        }

        if ($this->editMode && $this->sessionId) {
            // Mode édition - mise à jour simple
            ScheduleSession::whereKey($this->sessionId)->update([
                'category' => $this->category,
                'title' => $this->title,
                'ecu_id' => $this->ecuId,
                'teacher_id' => $this->teacherId,
                'room_id' => $this->roomId,
                'student_group_id' => $this->studentGroupId,
                'session_date' => Carbon::parse($this->sessionDate)->toDateString(),
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
                'type' => $this->type,
                'notes' => $this->notes,
            ]);
            session()->flash('success', 'Séance mise à jour.');
        } else {
            // Mode création
            if ($this->isRecurring && !empty($this->repeatDays)) {
                $count = $this->createRecurringSessions();
                session()->flash('success', "{$count} séances créées.");
            } else {
                $this->createSingleSession($this->sessionDate);
                session()->flash('success', 'Séance créée.');
            }
        }

        $this->closeSessionModal();
    }

    protected function createSingleSession(string $date): ScheduleSession
    {
        return ScheduleSession::create([
            'category' => $this->category,
            'title' => $this->title,
            'ecu_id' => $this->ecuId,
            'teacher_id' => $this->teacherId,
            'room_id' => $this->roomId,
            'student_group_id' => $this->studentGroupId,
            'academic_year_id' => $this->academicYearId,
            'session_date' => Carbon::parse($date)->toDateString(),
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'type' => $this->type,
            'status' => 'planned',
            'notes' => $this->notes,
        ]);
    }

    protected function createRecurringSessions(): int
    {
        $ecu = Ecu::find($this->ecuId);
        if (!$ecu) return 0;

        // Calcul de la durée d'une séance
        $start = Carbon::parse($this->startTime);
        $end = Carbon::parse($this->endTime);
        $sessionDuration = $end->diffInMinutes($start) / 60;

        // Masse horaire totale selon le type
        $totalHours = match($this->type) {
            'cm' => $ecu->cm_hours ?? 0,
            'td' => $ecu->td_hours ?? 0,
            'tp' => $ecu->tp_hours ?? 0,
            default => 0,
        };

        // Heures déjà planifiées
        $plannedHours = ScheduleSession::where('ecu_id', $this->ecuId)
            ->where('academic_year_id', $this->academicYearId)
            ->where('type', $this->type)
            ->get()
            ->sum(function ($s) {
                $st = Carbon::parse($s->start_time);
                $et = Carbon::parse($s->end_time);
                return $et->diffInMinutes($st) / 60;
            });

        $remainingHours = max(0, $totalHours - $plannedHours);
        
        // Déterminer la date de fin
        $startDate = Carbon::parse($this->sessionDate);
        $maxEndDate = Carbon::parse($this->sessionDate)->addMonths(6); // Limite à 6 mois
        
        if ($this->repeatUntil === 'date' && $this->repeatEndDate) {
            $maxEndDate = Carbon::parse($this->repeatEndDate);
        }

        $count = 0;
        $hoursCreated = 0;
        $maxSessions = $this->repeatUntil === 'count' ? $this->repeatCount : 100;
        
        $current = $startDate->copy();
        
        while ($current->lessThanOrEqualTo($maxEndDate) && $count < $maxSessions) {
            // Vérifier si le jour correspond aux jours de répétition
            if (in_array($current->dayOfWeek, $this->repeatDays)) {
                // Vérifier si on n'a pas dépassé la masse horaire
                if ($this->repeatUntil === 'end_of_hours' && $hoursCreated >= $remainingHours) {
                    break;
                }

                // Vérifier si la séance n'existe pas déjà
                $exists = ScheduleSession::where('ecu_id', $this->ecuId)
                    ->where('academic_year_id', $this->academicYearId)
                    ->where('session_date', $current->toDateString())
                    ->where('start_time', $this->startTime)
                    ->exists();

                if (!$exists) {
                    $this->createSingleSession($current->toDateString());
                    $count++;
                    $hoursCreated += $sessionDuration;
                }
            }
            $current->addDay();
        }

        return $count;
    }

    public function deleteSession(int $sessionId): void
    {
        ScheduleSession::whereKey($sessionId)->delete();
        session()->flash('success', 'Séance supprimée.');
    }

    public function markAsCompleted(int $sessionId): void
    {
        $session = ScheduleSession::findOrFail($sessionId);
        $session->markAsCompleted();
        session()->flash('success', 'Séance marquée comme effectuée.');
    }

    public function cancelSession(int $sessionId): void
    {
        $session = ScheduleSession::findOrFail($sessionId);
        $session->cancel();
        session()->flash('success', 'Séance annulée.');
    }

    public function getCalendarDaysProperty(): array
    {
        $firstOfMonth = Carbon::create($this->year, $this->month, 1);
        $lastOfMonth = $firstOfMonth->copy()->endOfMonth();
        
        // Commencer au lundi précédent
        $start = $firstOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        // Finir au dimanche suivant
        $end = $lastOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $days = [];
        $current = $start->copy();

        while ($current->lessThanOrEqualTo($end)) {
            $days[] = [
                'date' => $current->format('Y-m-d'),
                'day' => $current->day,
                'isCurrentMonth' => $current->month === $this->month,
                'isToday' => $current->isToday(),
                'isSelected' => $current->format('Y-m-d') === $this->selectedDate,
                'dayOfWeek' => $current->dayOfWeek,
            ];
            $current->addDay();
        }

        return $days;
    }

    public function getSessionsForMonthProperty(): Collection
    {
        $firstOfMonth = Carbon::create($this->year, $this->month, 1);
        $start = $firstOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $end = $firstOfMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $query = ScheduleSession::with(['ecu', 'teacher', 'room'])
            ->where('academic_year_id', $this->academicYearId)
            ->whereBetween('session_date', [$start, $end]);

        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }
        if ($this->filterEcuId) {
            $query->where('ecu_id', $this->filterEcuId);
        }
        if ($this->filterTeacherId) {
            $query->where('teacher_id', $this->filterTeacherId);
        }
        if ($this->filterRoomId) {
            $query->where('room_id', $this->filterRoomId);
        }

        return $query->orderBy('session_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn($s) => $s->session_date->format('Y-m-d'));
    }

    public function getSelectedDaySessionsProperty(): Collection
    {
        if (!$this->selectedDate) {
            return collect();
        }

        return ScheduleSession::with(['ecu', 'teacher', 'room', 'studentGroup'])
            ->where('academic_year_id', $this->academicYearId)
            ->where('session_date', $this->selectedDate)
            ->orderBy('start_time')
            ->get();
    }

    public function getAvailableRoomsProperty(): Collection
    {
        $date = $this->sessionDate ?: $this->selectedDate;

        if (!$date || !$this->startTime || !$this->endTime) {
            return $this->rooms;
        }

        $universityId = auth()->user()->university_id;
        $occupiedRoomIds = ScheduleSession::where('academic_year_id', $this->academicYearId)
            ->where('session_date', $date)
            ->where(function ($q) {
                $q->where('start_time', '<', $this->endTime)
                  ->where('end_time', '>', $this->startTime);
            })
            ->when($this->sessionId, fn($q) => $q->where('id', '!=', $this->sessionId))
            ->whereNotNull('room_id')
            ->pluck('room_id');

        return Room::where('university_id', $universityId)
            ->where('is_available', true)
            ->whereNotIn('id', $occupiedRoomIds)
            ->orderBy('code')
            ->get();
    }

    public function getAvailableTeachersProperty(): Collection
    {
        $date = $this->sessionDate ?: $this->selectedDate;

        if (!$date || !$this->startTime || !$this->endTime) {
            return $this->teachers;
        }

        $occupiedTeacherIds = ScheduleSession::where('academic_year_id', $this->academicYearId)
            ->where('session_date', $date)
            ->where(function ($q) {
                $q->where('start_time', '<', $this->endTime)
                  ->where('end_time', '>', $this->startTime);
            })
            ->when($this->sessionId, fn($q) => $q->where('id', '!=', $this->sessionId))
            ->whereNotNull('teacher_id')
            ->pluck('teacher_id');

        return Teacher::where('university_id', auth()->user()->university_id)
            ->whereNotIn('id', $occupiedTeacherIds)
            ->orderBy('last_name')
            ->get();
    }

    public function getEcusProperty(): Collection
    {
        $universityId = auth()->user()->university_id;
        return Ecu::whereHas('ue.semester.programYear.program', function ($q) use ($universityId) {
            $q->where('university_id', $universityId);
        })->with('ue.semester.programYear.program')->orderBy('code')->get();
    }

    public function getTeachersProperty(): Collection
    {
        return Teacher::where('university_id', auth()->user()->university_id)
            ->orderBy('last_name')
            ->get();
    }

    public function getRoomsProperty(): Collection
    {
        return Room::where('university_id', auth()->user()->university_id)
            ->where('is_available', true)
            ->orderBy('code')
            ->get();
    }

    public function getStudentGroupsProperty(): Collection
    {
        return StudentGroup::where('academic_year_id', $this->academicYearId)
            ->with('programYear.program')
            ->orderBy('name')
            ->get();
    }

    public function getAcademicYearsProperty(): Collection
    {
        return AcademicYear::where('university_id', auth()->user()->university_id)
            ->orderByDesc('start_date')
            ->get();
    }

    public function getSelectedEcuHoursSummaryProperty(): ?array
    {
        if (!$this->ecuId || !$this->academicYearId) {
            return null;
        }

        $ecu = Ecu::find($this->ecuId);
        if (!$ecu) return null;

        $sessions = ScheduleSession::where('ecu_id', $this->ecuId)
            ->where('academic_year_id', $this->academicYearId)
            ->get();

        $summary = [
            'cm' => ['total' => $ecu->hours_cm ?? 0, 'planned' => 0],
            'td' => ['total' => $ecu->hours_td ?? 0, 'planned' => 0],
            'tp' => ['total' => $ecu->hours_tp ?? 0, 'planned' => 0],
        ];

        foreach ($sessions as $session) {
            $duration = Carbon::parse($session->end_time)->diffInMinutes(Carbon::parse($session->start_time)) / 60;
            if (isset($summary[$session->type])) {
                $summary[$session->type]['planned'] += $duration;
            }
        }

        foreach ($summary as $type => &$data) {
            $data['remaining'] = max(0, $data['total'] - $data['planned']);
        }

        return $summary;
    }

    public function exportPdf()
    {
        $data = [
            'year' => $this->year,
            'month' => $this->month,
            'monthName' => Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y'),
            'calendarDays' => $this->calendarDays,
            'sessionsForMonth' => $this->sessionsForMonth,
            'academicYear' => AcademicYear::find($this->academicYearId),
        ];

        $pdf = Pdf::loadView('schedules.calendar-pdf', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'calendrier-' . $this->year . '-' . str_pad($this->month, 2, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function render()
    {
        return view('livewire.schedules.schedule-calendar', [
            'calendarDays' => $this->calendarDays,
            'sessionsForMonth' => $this->sessionsForMonth,
            'selectedDaySessions' => $this->selectedDaySessions,
            'ecus' => $this->ecus,
            'teachers' => $this->teachers,
            'rooms' => $this->rooms,
            'studentGroups' => $this->studentGroups,
            'academicYears' => $this->academicYears,
            'availableRooms' => $this->availableRooms,
            'availableTeachers' => $this->availableTeachers,
            'ecuHoursSummary' => $this->selectedEcuHoursSummary,
        ]);
    }
}
