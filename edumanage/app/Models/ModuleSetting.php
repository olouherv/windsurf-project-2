<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'module_key',
        'is_enabled',
        'settings',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'settings' => 'array',
    ];

    public const MODULES = [
        'students' => ['name' => 'Étudiants', 'required' => true, 'default' => true],
        'teachers' => ['name' => 'Enseignants', 'required' => true, 'default' => true],
        'academic_structure' => ['name' => 'Structure Académique', 'required' => true, 'default' => true],
        'grades' => ['name' => 'Notes & Évaluations', 'required' => true, 'default' => true],
        'schedules' => ['name' => 'Planification', 'required' => false, 'default' => true],
        'absences' => ['name' => 'Absences & Présences', 'required' => false, 'default' => false],
        'enrollments' => ['name' => 'Inscriptions Pédagogiques', 'required' => false, 'default' => false],
        'vacataire_contracts' => ['name' => 'Contrats Vacataires', 'required' => false, 'default' => false],
        'stages' => ['name' => 'Stages & Mémoires', 'required' => false, 'default' => false],
        'documents' => ['name' => 'Documents Officiels', 'required' => false, 'default' => true],
        'notifications' => ['name' => 'Notifications', 'required' => false, 'default' => true],
        'moodle' => ['name' => 'Intégration Moodle', 'required' => false, 'default' => false],
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public static function getModuleInfo(string $key): ?array
    {
        return self::MODULES[$key] ?? null;
    }

    public static function isRequired(string $key): bool
    {
        return self::MODULES[$key]['required'] ?? false;
    }
}
