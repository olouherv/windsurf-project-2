<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ecu;
use App\Models\Ue;

class CreateTestEcu extends Command
{
    protected $signature = 'create:test-ecu';
    protected $description = 'Créer un ECU de test avec masse horaire';

    public function handle()
    {
        $ue = Ue::first();
        
        if (!$ue) {
            $this->error('Aucune UE trouvée. Créez d\'abord une UE.');
            return 1;
        }

        $ecu = Ecu::updateOrCreate(
            ['code' => 'TEST-ECU'],
            [
                'ue_id' => $ue->id,
                'name' => 'ECU de Test pour Planification',
                'hours_cm' => 40,
                'hours_td' => 20,
                'hours_tp' => 10,
                'coefficient' => 1,
            ]
        );

        $this->info("✓ ECU créé avec succès!");
        $this->info("  ID: {$ecu->id}");
        $this->info("  Code: {$ecu->code}");
        $this->info("  Nom: {$ecu->name}");
        $this->info("  CM: {$ecu->hours_cm}h");
        $this->info("  TD: {$ecu->hours_td}h");
        $this->info("  TP: {$ecu->hours_tp}h");
        $this->newLine();
        $this->info("Vous pouvez maintenant tester avec: php artisan test:ecu-hours {$ecu->id}");

        return 0;
    }
}
