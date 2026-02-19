<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\University;
use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\ProgramYear;
use App\Models\Semester;
use App\Models\Ue;
use App\Models\Ecu;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Room;
use App\Models\ModuleSetting;
use App\Models\PricingPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = ['super_admin', 'admin', 'secretary', 'teacher', 'student'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create Super Admin (no university)
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@edumanage.com',
            'password' => Hash::make('password'),
            'user_type' => 'super_admin',
            'locale' => 'fr',
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super_admin');

        // Create Demo University
        $university = University::create([
            'name' => 'Université Demo',
            'code' => 'UDEMO',
            'email' => 'contact@univ-demo.edu',
            'phone' => '+33 1 23 45 67 89',
            'address' => '123 Rue de l\'Université, 75001 Paris',
            'trial_ends_at' => now()->addDays(14),
            'is_active' => true,
        ]);

        $starterPlan = PricingPlan::firstOrCreate([
            'key' => 'starter',
        ], [
            'name' => 'Starter',
            'subtitle' => 'Essentiel pour démarrer',
            'description' => 'Pour les petites universités qui souhaitent digitaliser la gestion quotidienne.',
            'price_monthly' => 49,
            'price_yearly' => 490,
            'currency' => 'EUR',
            'is_active' => true,
            'features' => [
                'Gestion étudiants & enseignants',
                'Structure académique',
                'Planification de base',
                'Notifications',
            ],
            'included_modules' => ['students', 'teachers', 'academic_structure', 'schedules', 'notifications'],
        ]);

        $proPlan = PricingPlan::firstOrCreate([
            'key' => 'pro',
        ], [
            'name' => 'Pro',
            'subtitle' => 'Pour une gestion complète',
            'description' => 'Inclut les modules avancés et l’automatisation des processus clés.',
            'price_monthly' => 129,
            'price_yearly' => 1290,
            'currency' => 'EUR',
            'is_active' => true,
            'features' => [
                'Notes & évaluations',
                'Documents officiels (PDF)',
                'Stages & mémoires',
                'Contrats & paiements',
            ],
            'included_modules' => ['students', 'teachers', 'academic_structure', 'grades', 'schedules', 'documents', 'stages', 'contracts', 'vacataire_contracts', 'enrollments', 'notifications'],
        ]);

        PricingPlan::firstOrCreate([
            'key' => 'entreprise',
        ], [
            'name' => 'Entreprise',
            'subtitle' => 'Sur mesure',
            'description' => 'Pour les grandes universités : fonctionnalités avancées, intégrations et accompagnement.',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'currency' => 'EUR',
            'is_active' => true,
            'features' => [
                'Intégrations (Moodle, SSO) selon besoin',
                'Support prioritaire',
                'Accompagnement & paramétrage',
                'Modules sur mesure',
            ],
            'included_modules' => array_keys(ModuleSetting::MODULES),
        ]);

        $university->update([
            'pricing_plan_id' => $starterPlan->id,
            'plan_key' => $starterPlan->key,
            'plan_started_at' => now(),
        ]);

        // Enable default modules
        $defaultModules = ['students', 'teachers', 'academic_structure', 'grades', 'schedules', 'documents', 'notifications'];
        foreach ($defaultModules as $module) {
            ModuleSetting::create([
                'university_id' => $university->id,
                'module_key' => $module,
                'is_enabled' => true,
            ]);
        }

        // Create Admin for University
        $admin = User::create([
            'university_id' => $university->id,
            'name' => 'Admin Université',
            'email' => 'admin@univ-demo.edu',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'locale' => 'fr',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create Academic Year
        $academicYear = AcademicYear::create([
            'university_id' => $university->id,
            'name' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_current' => true,
        ]);

        // Create Program: Licence Informatique
        $program = Program::create([
            'university_id' => $university->id,
            'name' => 'Licence Informatique',
            'code' => 'L-INFO',
            'level' => 'licence',
            'duration_years' => 3,
            'description' => 'Formation en informatique générale',
            'is_active' => true,
        ]);

        // Create Program Years (L1, L2, L3)
        for ($i = 1; $i <= 3; $i++) {
            $programYear = ProgramYear::create([
                'program_id' => $program->id,
                'name' => "L$i",
                'year_number' => $i,
            ]);

            // Create 2 semesters per year
            for ($s = 1; $s <= 2; $s++) {
                $semesterNum = ($i - 1) * 2 + $s;
                $semester = Semester::create([
                    'program_year_id' => $programYear->id,
                    'academic_year_id' => $academicYear->id,
                    'name' => "Semestre $semesterNum",
                    'semester_number' => $semesterNum,
                ]);

                // Create 3 UEs per semester
                for ($u = 1; $u <= 3; $u++) {
                    $ue = Ue::create([
                        'semester_id' => $semester->id,
                        'code' => "UE{$semesterNum}{$u}",
                        'name' => "Unité d'enseignement {$semesterNum}.{$u}",
                        'credits_ects' => 10,
                        'coefficient' => 1,
                    ]);

                    // Create 2 ECUs per UE
                    for ($e = 1; $e <= 2; $e++) {
                        Ecu::create([
                            'ue_id' => $ue->id,
                            'code' => "EC{$semesterNum}{$u}{$e}",
                            'name' => "Matière {$semesterNum}.{$u}.{$e}",
                            'credits_ects' => 5,
                            'coefficient' => 1,
                            'hours_cm' => 20,
                            'hours_td' => 15,
                            'hours_tp' => 10,
                        ]);
                    }
                }
            }
        }

        // Create Rooms
        $rooms = [
            ['name' => 'Amphi A', 'code' => 'AMP-A', 'capacity' => 200, 'type' => 'amphitheater'],
            ['name' => 'Salle 101', 'code' => 'S101', 'capacity' => 30, 'type' => 'classroom'],
            ['name' => 'Salle Info 1', 'code' => 'INFO1', 'capacity' => 25, 'type' => 'computer_room'],
            ['name' => 'Labo Physique', 'code' => 'LAB-P', 'capacity' => 20, 'type' => 'lab'],
        ];
        foreach ($rooms as $room) {
            Room::create(array_merge($room, ['university_id' => $university->id]));
        }

        // Create Sample Teachers
        $teachers = [
            ['first_name' => 'Jean', 'last_name' => 'Dupont', 'type' => 'permanent', 'specialization' => 'Algorithmique'],
            ['first_name' => 'Marie', 'last_name' => 'Martin', 'type' => 'permanent', 'specialization' => 'Base de données'],
            ['first_name' => 'Pierre', 'last_name' => 'Bernard', 'type' => 'vacataire', 'specialization' => 'Réseaux'],
        ];
        foreach ($teachers as $i => $t) {
            Teacher::create([
                'university_id' => $university->id,
                'employee_id' => 'ENS' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'first_name' => $t['first_name'],
                'last_name' => $t['last_name'],
                'email' => strtolower($t['first_name'] . '.' . $t['last_name']) . '@univ-demo.edu',
                'type' => $t['type'],
                'specialization' => $t['specialization'],
                'status' => 'active',
                'hire_date' => now()->subYears(rand(1, 10)),
            ]);
        }

        // Create Sample Students
        $firstNames = ['Alice', 'Bob', 'Claire', 'David', 'Emma', 'François', 'Géraldine', 'Hugo', 'Isabelle', 'Julien'];
        $lastNames = ['Leroy', 'Moreau', 'Petit', 'Roux', 'Simon', 'Thomas', 'Vidal', 'Weber', 'Xavier', 'Yves'];
        
        for ($i = 0; $i < 20; $i++) {
            Student::create([
                'university_id' => $university->id,
                'student_id' => 'ETU' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'first_name' => $firstNames[array_rand($firstNames)],
                'last_name' => $lastNames[array_rand($lastNames)],
                'email' => 'etudiant' . ($i + 1) . '@univ-demo.edu',
                'gender' => rand(0, 1) ? 'male' : 'female',
                'status' => 'active',
                'enrollment_date' => now()->subMonths(rand(1, 24)),
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Super Admin: superadmin@edumanage.com / password');
        $this->command->info('Admin: admin@univ-demo.edu / password');
    }
}
