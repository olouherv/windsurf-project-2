<?php

namespace App\Livewire\Settings;

use App\Models\ModuleSetting;
use Livewire\Component;

class ModuleManager extends Component
{
    public array $modules = [];

    protected array $availableModules = [
        'students' => [
            'name' => 'Étudiants',
            'description' => 'Gestion des étudiants, inscriptions et dossiers',
            'icon' => 'users',
            'required' => true,
        ],
        'teachers' => [
            'name' => 'Enseignants',
            'description' => 'Gestion des enseignants permanents et vacataires',
            'icon' => 'academic-cap',
            'required' => true,
        ],
        'academic_structure' => [
            'name' => 'Structure académique',
            'description' => 'Programmes, années, semestres, UE et ECU',
            'icon' => 'library',
            'required' => true,
        ],
        'contracts' => [
            'name' => 'Contrats & Paiements',
            'description' => 'Contrats étudiants, échéanciers et suivi des paiements',
            'icon' => 'document-text',
            'required' => false,
        ],
        'grades' => [
            'name' => 'Notes & Évaluations',
            'description' => 'Saisie des notes, calcul des moyennes et bulletins',
            'icon' => 'clipboard-check',
            'required' => false,
        ],
        'schedules' => [
            'name' => 'Planification',
            'description' => 'Emplois du temps, salles et équipements',
            'icon' => 'calendar',
            'required' => false,
        ],
        'absences' => [
            'name' => 'Absences & Présences',
            'description' => 'Gestion des absences, retards et feuilles de présence',
            'icon' => 'clipboard-check',
            'required' => false,
        ],
        'enrollments' => [
            'name' => 'Inscriptions Pédagogiques',
            'description' => 'Inscriptions aux formations, parcours et statuts',
            'icon' => 'users',
            'required' => false,
        ],
        'vacataire_contracts' => [
            'name' => 'Contrats Vacataires',
            'description' => 'Contrats vacataires, heures et paiements',
            'icon' => 'document-text',
            'required' => false,
        ],
        'stages' => [
            'name' => 'Stages & Mémoires',
            'description' => 'Gestion des stages, mémoires et encadrements',
            'icon' => 'library',
            'required' => false,
        ],
        'documents' => [
            'name' => 'Documents Officiels',
            'description' => 'Attestations, certificats et documents administratifs',
            'icon' => 'document-text',
            'required' => false,
        ],
        'notifications' => [
            'name' => 'Notifications',
            'description' => 'Alertes et notifications (email / in-app)',
            'icon' => 'link',
            'required' => false,
        ],
        'moodle' => [
            'name' => 'Intégration Moodle',
            'description' => 'Synchronisation avec la plateforme Moodle',
            'icon' => 'link',
            'required' => false,
        ],
    ];

    public function mount()
    {
        $university = auth()->user()->university;
        $inTrial = $university && method_exists($university, 'isInTrial') ? $university->isInTrial() : false;
        
        foreach ($this->availableModules as $key => $module) {
            $setting = ModuleSetting::where('university_id', $university->id)
                ->where('module_key', $key)
                ->first();
            
            $this->modules[$key] = [
                'key' => $key,
                'name' => $module['name'],
                'description' => $module['description'],
                'icon' => $module['icon'],
                'required' => $module['required'],
                'enabled' => $inTrial ? true : ($module['required'] ? true : ($setting?->is_enabled ?? false)),
            ];
        }
    }

    public function toggleModule(string $key)
    {
        if ($this->modules[$key]['required']) {
            return;
        }

        $user = auth()->user();
        if (!$user->isSuperAdmin() && $user->university && method_exists($user->university, 'isTrialExpired') && $user->university->isTrialExpired()) {
            session()->flash('message', "Période d'essai expirée. Seul le superadmin peut activer/désactiver des modules.");
            return;
        }

        $university = auth()->user()->university;
        $newState = !$this->modules[$key]['enabled'];

        ModuleSetting::updateOrCreate(
            [
                'university_id' => $university->id,
                'module_key' => $key,
            ],
            [
                'is_enabled' => $newState,
            ]
        );

        $this->modules[$key]['enabled'] = $newState;

        session()->flash('message', $newState 
            ? "Module \"{$this->modules[$key]['name']}\" activé." 
            : "Module \"{$this->modules[$key]['name']}\" désactivé."
        );

        $this->dispatch('modules-updated');
    }

    public function render()
    {
        return view('livewire.settings.module-manager');
    }
}
