<?php

namespace App\Livewire\Schedules;

use App\Models\AcademicYear;
use App\Models\Ecu;
use App\Models\Equipment;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\StudentGroup;
use App\Models\Teacher;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ScheduleManager extends Component
{
    public ?int $academic_year_id = null;

    public string $search = '';

    public bool $showModal = false;
    public bool $editMode = false;
    public ?int $scheduleId = null;

    public ?int $ecu_id = null;
    public ?int $teacher_id = null;
    public ?int $room_id = null;
    public ?int $student_group_id = null;
    public string $category = 'course';
    public ?string $title = null;
    public ?string $scheduled_date = null;
    public string $type = 'cm';

    /**
     * @var array<int>
     */
    public array $equipment_ids = [];

    /**
     * @var array<int, array{day_of_week:int,start_time:?string,end_time:?string}>
     */
    public array $timeSlots = [];

    public ?string $start_date = null;
    public ?string $end_date = null;
    public bool $is_recurring = true;
    public ?string $notes = '';

    public function mount(): void
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $this->academic_year_id = $currentYear?->id;
    }

    protected function rules(): array
    {
        return [
            'academic_year_id' => ['required', Rule::exists('academic_years', 'id')],
            'category' => ['required', Rule::in(['course', 'activity'])],
            'title' => ['nullable', 'string', 'max:255'],
            'scheduled_date' => ['nullable', 'date'],
            'ecu_id' => [
                Rule::requiredIf(fn() => $this->category === 'course'),
                'nullable',
                Rule::exists('ecus', 'id'),
            ],
            'teacher_id' => ['nullable', Rule::exists('teachers', 'id')],
            'room_id' => ['nullable', Rule::exists('rooms', 'id')],
            'student_group_id' => ['nullable', Rule::exists('student_groups', 'id')],
            'type' => ['required', Rule::in(['cm', 'td', 'tp'])],
            'timeSlots' => ['required', 'array', 'min:1'],
            'timeSlots.*.day_of_week' => ['required', 'integer', 'min:0', 'max:6'],
            'timeSlots.*.start_time' => ['required', 'date_format:H:i'],
            'timeSlots.*.end_time' => ['required', 'date_format:H:i'],
            'start_date' => [Rule::requiredIf(fn() => $this->category === 'course'), 'nullable', 'date'],
            'end_date' => [Rule::requiredIf(fn() => $this->category === 'course'), 'nullable', 'date', 'after_or_equal:start_date'],
            'is_recurring' => ['boolean'],
            'notes' => ['nullable', 'string'],
            'equipment_ids' => ['array'],
            'equipment_ids.*' => [Rule::exists('equipments', 'id')],
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editMode = false;
    }

    public function openEdit(int $id): void
    {
        $schedule = Schedule::with(['ecu', 'teacher', 'room', 'studentGroup', 'equipments'])->findOrFail($id);

        $this->scheduleId = $schedule->id;
        $this->ecu_id = $schedule->ecu_id;
        $this->teacher_id = $schedule->teacher_id;
        $this->room_id = $schedule->room_id;
        $this->student_group_id = $schedule->student_group_id;
        $this->academic_year_id = $schedule->academic_year_id;
        $this->category = $schedule->category ?? 'course';
        $this->title = $schedule->title;
        $this->scheduled_date = $schedule->scheduled_date?->format('Y-m-d');
        $this->type = $schedule->type;
        $this->timeSlots = [[
            'day_of_week' => (int) $schedule->day_of_week,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
        ]];
        $this->start_date = $schedule->start_date?->format('Y-m-d');
        $this->end_date = $schedule->end_date?->format('Y-m-d');
        $this->is_recurring = (bool)$schedule->is_recurring;
        $this->notes = $schedule->notes;
        $this->equipment_ids = $schedule->equipments->pluck('id')->values()->all();

        $this->showModal = true;
        $this->editMode = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    protected function resetForm(): void
    {
        $this->reset([
            'scheduleId',
            'ecu_id',
            'teacher_id',
            'room_id',
            'student_group_id',
            'category',
            'title',
            'scheduled_date',
            'type',
            'equipment_ids',
            'timeSlots',
            'start_date',
            'end_date',
            'is_recurring',
            'notes',
        ]);

        $this->category = 'course';
        $this->type = 'cm';
        $this->is_recurring = true;
        $this->notes = '';
        $this->equipment_ids = [];

        $this->timeSlots = [[
            'day_of_week' => 1,
            'start_time' => null,
            'end_time' => null,
        ]];
    }

    public function addTimeSlot(): void
    {
        if ($this->editMode) {
            return;
        }

        $this->timeSlots[] = [
            'day_of_week' => 1,
            'start_time' => null,
            'end_time' => null,
        ];
    }

    public function removeTimeSlot(int $index): void
    {
        if ($this->editMode) {
            return;
        }

        if (count($this->timeSlots) <= 1) {
            return;
        }

        unset($this->timeSlots[$index]);
        $this->timeSlots = array_values($this->timeSlots);
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($data['category'] === 'activity') {
            if (empty($data['title'])) {
                throw ValidationException::withMessages([
                    'title' => __('Le titre est requis pour une activité.'),
                ]);
            }
            if (empty($data['scheduled_date'])) {
                throw ValidationException::withMessages([
                    'scheduled_date' => __('La date est requise pour une activité.'),
                ]);
            }

            $forcedDayOfWeek = Carbon::parse($data['scheduled_date'])->dayOfWeek;
            $data['timeSlots'] = collect($data['timeSlots'])
                ->map(function ($slot) use ($forcedDayOfWeek) {
                    $slot['day_of_week'] = $forcedDayOfWeek;
                    return $slot;
                })
                ->all();
        }

        foreach ($data['timeSlots'] as $i => $slot) {
            if (!empty($slot['start_time']) && !empty($slot['end_time']) && $slot['end_time'] <= $slot['start_time']) {
                throw ValidationException::withMessages([
                    "timeSlots.{$i}.end_time" => __('L\'heure de fin doit être après l\'heure de début.'),
                ]);
            }
        }

        if ($this->editMode && $this->scheduleId) {
            $slot = $data['timeSlots'][0] ?? null;
            if (!$slot) {
                throw ValidationException::withMessages([
                    'timeSlots' => __('Veuillez ajouter au moins un créneau.'),
                ]);
            }

            if ($data['room_id']) {
                $room = Room::find($data['room_id']);
                if ($room) {
                    if ($data['category'] === 'activity') {
                        if (!$room->isAvailableOnDate($data['scheduled_date'], $slot['start_time'], $slot['end_time'], $this->scheduleId, $data['academic_year_id'])) {
                            throw ValidationException::withMessages([
                                'room_id' => __('Cette salle est indisponible pour le créneau sélectionné.'),
                            ]);
                        }
                    } else {
                        $conflict = $this->roomHasConflictsForRecurringCourse($room, (int) $slot['day_of_week'], $slot['start_time'], $slot['end_time'], $data['start_date'], $data['end_date'], $data['academic_year_id'], $this->scheduleId);
                        if ($conflict) {
                            throw ValidationException::withMessages([
                                'room_id' => __('Cette salle est indisponible pour au moins une occurrence sur la période.'),
                            ]);
                        }
                    }
                }
            }

            $payload = [
                'academic_year_id' => $data['academic_year_id'],
                'ecu_id' => $data['ecu_id'],
                'teacher_id' => $data['teacher_id'],
                'room_id' => $data['room_id'],
                'student_group_id' => $data['student_group_id'],
                'category' => $data['category'],
                'title' => $data['category'] === 'activity' ? $data['title'] : null,
                'scheduled_date' => $data['category'] === 'activity' ? $data['scheduled_date'] : null,
                'type' => $data['type'],
                'day_of_week' => $slot['day_of_week'],
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'start_date' => $data['category'] === 'course' ? $data['start_date'] : null,
                'end_date' => $data['category'] === 'course' ? $data['end_date'] : null,
                'is_recurring' => $data['category'] === 'course',
                'notes' => $data['notes'],
            ];

            $schedule = Schedule::findOrFail($this->scheduleId);
            $schedule->update($payload);
            $schedule->equipments()->sync($data['equipment_ids'] ?? []);
            session()->flash('success', __('Séance mise à jour.'));
        } else {
            foreach ($data['timeSlots'] as $slot) {
                if ($data['room_id']) {
                    $room = Room::find($data['room_id']);
                    if ($room) {
                        if ($data['category'] === 'activity') {
                            if (!$room->isAvailableOnDate($data['scheduled_date'], $slot['start_time'], $slot['end_time'], null, $data['academic_year_id'])) {
                                throw ValidationException::withMessages([
                                    'room_id' => __('Cette salle est indisponible pour au moins un des créneaux sélectionnés.'),
                                ]);
                            }
                        } else {
                            $conflict = $this->roomHasConflictsForRecurringCourse($room, (int) $slot['day_of_week'], $slot['start_time'], $slot['end_time'], $data['start_date'], $data['end_date'], $data['academic_year_id'], null);
                            if ($conflict) {
                                throw ValidationException::withMessages([
                                    'room_id' => __('Cette salle est indisponible pour au moins une occurrence sur la période.'),
                                ]);
                            }
                        }
                    }
                }

                $schedule = Schedule::create([
                    'academic_year_id' => $data['academic_year_id'],
                    'ecu_id' => $data['ecu_id'],
                    'teacher_id' => $data['teacher_id'],
                    'room_id' => $data['room_id'],
                    'student_group_id' => $data['student_group_id'],
                    'category' => $data['category'],
                    'title' => $data['category'] === 'activity' ? $data['title'] : null,
                    'scheduled_date' => $data['category'] === 'activity' ? $data['scheduled_date'] : null,
                    'type' => $data['type'],
                    'day_of_week' => $slot['day_of_week'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'start_date' => $data['category'] === 'course' ? $data['start_date'] : null,
                    'end_date' => $data['category'] === 'course' ? $data['end_date'] : null,
                    'is_recurring' => $data['category'] === 'course',
                    'notes' => $data['notes'],
                ]);

                $schedule->equipments()->sync($data['equipment_ids'] ?? []);
            }

            session()->flash('success', __('Séances créées.'));
        }

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        Schedule::whereKey($id)->delete();
        session()->flash('success', __('Séance supprimée.'));
    }

    public function render()
    {
        $universityId = auth()->user()->university_id;

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        $ecus = Ecu::whereHas('ue.semester.programYear.program', function ($q) use ($universityId) {
                $q->where('university_id', $universityId);
            })
            ->orderBy('code')
            ->get();

        $teachers = Teacher::where('university_id', $universityId)->orderBy('last_name')->get();
        $rooms = Room::where('university_id', $universityId)->orderBy('code')->get();

        $equipments = Equipment::where('university_id', $universityId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $studentGroups = StudentGroup::when($this->academic_year_id, function ($q) {
                $q->where('academic_year_id', $this->academic_year_id);
            })
            ->with('programYear.program')
            ->orderBy('name')
            ->get();

        $schedules = Schedule::with(['ecu.ue.semester.programYear.program', 'teacher', 'room', 'studentGroup.programYear.program', 'academicYear'])
            ->when($this->academic_year_id, fn($q) => $q->where('academic_year_id', $this->academic_year_id))
            ->when($this->search, function ($q) {
                $q->whereHas('ecu', function ($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%');
                })->orWhereHas('teacher', function ($q2) {
                    $q2->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('livewire.schedules.schedule-manager', [
            'academicYears' => $academicYears,
            'ecus' => $ecus,
            'teachers' => $teachers,
            'rooms' => $rooms,
            'equipments' => $equipments,
            'studentGroups' => $studentGroups,
            'schedules' => $schedules,
        ]);
    }

    protected function roomHasConflictsForRecurringCourse(Room $room, int $dayOfWeek, string $startTime, string $endTime, ?string $startDate, ?string $endDate, int $academicYearId, ?int $excludeScheduleId): bool
    {
        if (!$startDate || !$endDate) {
            return true;
        }

        $current = Carbon::parse($startDate);
        $last = Carbon::parse($endDate);

        while ($current->lessThanOrEqualTo($last)) {
            if ($current->dayOfWeek === $dayOfWeek) {
                if (!$room->isAvailableOnDate($current->format('Y-m-d'), $startTime, $endTime, $excludeScheduleId, $academicYearId)) {
                    return true;
                }
            }
            $current->addDay();
        }

        return false;
    }
}
