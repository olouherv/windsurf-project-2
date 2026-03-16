<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Room;
use App\Models\University;

class SeedScheduleData extends Command
{
    protected $signature = 'seed:schedule-data {--university=}';
    protected $description = 'Créer des salles de test pour la planification';

    public function handle()
    {
        $universityId = $this->option('university') ?? University::first()?->id;
        
        if (!$universityId) {
            $this->error('Aucune université trouvée. Créez d\'abord une université.');
            return 1;
        }

        $this->info('Création de salles de test...');

        $salles = [
            ['code' => 'A101', 'name' => 'Amphi A101', 'capacity' => 200, 'type' => 'amphitheater'],
            ['code' => 'A102', 'name' => 'Amphi A102', 'capacity' => 150, 'type' => 'amphitheater'],
            ['code' => 'B201', 'name' => 'Salle TD B201', 'capacity' => 40, 'type' => 'classroom'],
            ['code' => 'B202', 'name' => 'Salle TD B202', 'capacity' => 40, 'type' => 'classroom'],
            ['code' => 'B203', 'name' => 'Salle TD B203', 'capacity' => 35, 'type' => 'classroom'],
            ['code' => 'C301', 'name' => 'Labo Info C301', 'capacity' => 30, 'type' => 'computer_room'],
            ['code' => 'C302', 'name' => 'Labo Info C302', 'capacity' => 30, 'type' => 'computer_room'],
            ['code' => 'D401', 'name' => 'Labo Physique D401', 'capacity' => 25, 'type' => 'lab'],
            ['code' => 'D402', 'name' => 'Labo Chimie D402', 'capacity' => 25, 'type' => 'lab'],
            ['code' => 'E501', 'name' => 'Salle Réunion E501', 'capacity' => 15, 'type' => 'meeting_room'],
        ];

        foreach ($salles as $salle) {
            Room::updateOrCreate(
                [
                    'university_id' => $universityId,
                    'code' => $salle['code']
                ],
                [
                    'name' => $salle['name'],
                    'capacity' => $salle['capacity'],
                    'type' => $salle['type'],
                    'is_available' => true,
                    'building' => substr($salle['code'], 0, 1),
                ]
            );
            $this->info("✓ Salle créée: {$salle['code']} - {$salle['name']}");
        }

        $count = count($salles);
        $this->info("\n✓ {$count} salles créées avec succès!");
        $this->info("\nVous pouvez maintenant utiliser le calendrier de planification.");
        
        return 0;
    }
}
