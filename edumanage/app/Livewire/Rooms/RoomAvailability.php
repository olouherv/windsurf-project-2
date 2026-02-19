<?php

namespace App\Livewire\Rooms;

use App\Models\AcademicYear;
use App\Models\Room;
use App\Models\Schedule;
use Livewire\Component;

class RoomAvailability extends Component
{
    public Room $room;

    public ?int $academic_year_id = null;
    public ?string $date_of_check = null;
    public ?string $start_time = null;
    public ?string $end_time = null;

    public ?bool $availabilityResult = null;

    public function mount(Room $room, ?int $academicYearId = null): void
    {
        $this->room = $room;

        if ($academicYearId) {
            $this->academic_year_id = $academicYearId;
        } else {
            $currentYear = AcademicYear::where('is_current', true)->first();
            $this->academic_year_id = $currentYear?->id;
        }

        $this->date_of_check = now()->format('Y-m-d');
    }

    public function checkAvailability(): void
    {
        $this->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'date_of_check' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $this->availabilityResult = $this->room->isAvailableOnDate(
            $this->date_of_check,
            $this->start_time,
            $this->end_time,
            null,
            $this->academic_year_id
        );
    }

    public function render()
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        $schedules = Schedule::with(['ecu'])
            ->where('room_id', $this->room->id)
            ->when($this->academic_year_id, fn($q) => $q->where('academic_year_id', $this->academic_year_id))
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('livewire.rooms.room-availability', [
            'academicYears' => $academicYears,
            'schedules' => $schedules,
        ]);
    }
}
