<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ecu;
use App\Models\ScheduleSession;
use App\Models\AcademicYear;
use Carbon\Carbon;

class TestEcuHours extends Command
{
    protected $signature = 'test:ecu-hours {ecu_id?}';
    protected $description = 'Tester le calcul de la masse horaire d\'un ECU';

    public function handle()
    {
        $ecuId = $this->argument('ecu_id');
        
        if (!$ecuId) {
            $ecu = Ecu::whereNotNull('hours_cm')
                ->orWhereNotNull('hours_td')
                ->orWhereNotNull('hours_tp')
                ->first();
            
            if (!$ecu) {
                $this->error('Aucun ECU avec masse horaire trouvé');
                return 1;
            }
            $ecuId = $ecu->id;
        } else {
            $ecu = Ecu::find($ecuId);
        }

        if (!$ecu) {
            $this->error("ECU #{$ecuId} non trouvé");
            return 1;
        }

        $this->info("=== Test Masse Horaire ECU ===");
        $this->info("ECU: {$ecu->code} - {$ecu->name}");
        $this->newLine();

        $this->info("Masse horaire définie:");
        $this->info("  CM: " . ($ecu->hours_cm ?? 0) . "h");
        $this->info("  TD: " . ($ecu->hours_td ?? 0) . "h");
        $this->info("  TP: " . ($ecu->hours_tp ?? 0) . "h");
        $this->newLine();

        $academicYear = AcademicYear::where('is_current', true)->first();
        if (!$academicYear) {
            $this->warn("Aucune année académique courante");
            return 0;
        }

        $sessions = ScheduleSession::where('ecu_id', $ecu->id)
            ->where('academic_year_id', $academicYear->id)
            ->get();

        $this->info("Séances planifiées: " . $sessions->count());

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
            $this->info("  - {$session->session_date} {$session->start_time}-{$session->end_time} ({$session->type}): {$duration}h");
        }

        $this->newLine();
        $this->info("Résumé:");
        foreach ($summary as $type => $data) {
            $remaining = max(0, $data['total'] - $data['planned']);
            $this->info("  " . strtoupper($type) . ": {$data['total']}h total, {$data['planned']}h planifié, {$remaining}h restant");
        }

        return 0;
    }
}
