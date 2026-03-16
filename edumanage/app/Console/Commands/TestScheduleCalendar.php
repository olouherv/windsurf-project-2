<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Room;
use App\Models\Teacher;
use App\Models\Ecu;
use App\Models\ScheduleSession;
use App\Models\AcademicYear;

class TestScheduleCalendar extends Command
{
    protected $signature = 'test:calendar';
    protected $description = 'Test calendar data availability';

    public function handle()
    {
        $this->info('=== Test Calendrier de Planification ===');
        
        // Test 1: Salles disponibles
        $rooms = Room::where('is_available', true)->count();
        $this->info("✓ Salles disponibles: {$rooms}");
        
        // Test 2: Enseignants
        $teachers = Teacher::count();
        $this->info("✓ Enseignants: {$teachers}");
        
        // Test 3: ECUs avec masse horaire
        $ecus = Ecu::whereNotNull('hours_cm')->orWhereNotNull('hours_td')->orWhereNotNull('hours_tp')->count();
        $this->info("✓ ECUs avec masse horaire: {$ecus}");
        
        // Test 4: Séances planifiées
        $sessions = ScheduleSession::count();
        $this->info("✓ Séances planifiées: {$sessions}");
        
        // Test 5: Année académique courante
        $currentYear = AcademicYear::where('is_current', true)->first();
        if ($currentYear) {
            $this->info("✓ Année académique courante: {$currentYear->name}");
        } else {
            $this->error("✗ Aucune année académique courante définie");
        }
        
        // Test 6: Exemple de disponibilité salle
        $date = now()->format('Y-m-d');
        $startTime = '08:00';
        $endTime = '12:00';
        
        $occupiedRoomIds = ScheduleSession::where('session_date', $date)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->whereNotNull('room_id')
            ->pluck('room_id');
            
        $availableRooms = Room::where('is_available', true)
            ->whereNotIn('id', $occupiedRoomIds)
            ->count();
            
        $this->info("✓ Salles disponibles aujourd'hui 8h-12h: {$availableRooms}");
        
        $this->info("\n=== Diagnostic terminé ===");
    }
}
