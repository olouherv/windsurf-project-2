<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\University;
use App\Models\AcademicYear;
use App\Models\Room;
use App\Models\Teacher;
use App\Models\Ecu;
use App\Models\ScheduleSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Schedules\ScheduleCalendar;

class ScheduleCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $university;
    protected $academicYear;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->university = University::factory()->create();
        $this->user = User::factory()->create(['university_id' => $this->university->id]);
        $this->academicYear = AcademicYear::factory()->create([
            'university_id' => $this->university->id,
            'is_current' => true
        ]);
    }

    /** @test */
    public function it_loads_available_rooms()
    {
        $room = Room::factory()->create([
            'university_id' => $this->university->id,
            'is_active' => true
        ]);

        Livewire::actingAs($this->user)
            ->test(ScheduleCalendar::class)
            ->assertSet('academicYearId', $this->academicYear->id)
            ->call('openAddSession', now()->format('Y-m-d'))
            ->assertSee($room->code);
    }

    /** @test */
    public function it_shows_ecu_hours_summary_when_ecu_selected()
    {
        $ecu = Ecu::factory()->create([
            'cm_hours' => 40,
            'td_hours' => 20,
            'tp_hours' => 10
        ]);

        Livewire::actingAs($this->user)
            ->test(ScheduleCalendar::class)
            ->set('ecuId', $ecu->id)
            ->assertSet('ecuHoursSummary.cm.total', 40)
            ->assertSet('ecuHoursSummary.td.total', 20)
            ->assertSet('ecuHoursSummary.tp.total', 10);
    }

    /** @test */
    public function it_displays_sessions_for_selected_date()
    {
        $ecu = Ecu::factory()->create();
        $session = ScheduleSession::factory()->create([
            'ecu_id' => $ecu->id,
            'academic_year_id' => $this->academicYear->id,
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '12:00'
        ]);

        Livewire::actingAs($this->user)
            ->test(ScheduleCalendar::class)
            ->call('selectDate', now()->format('Y-m-d'))
            ->assertSee($ecu->code);
    }
}
