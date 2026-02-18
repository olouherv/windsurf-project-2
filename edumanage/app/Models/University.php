<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class University extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'logo',
        'website',
        'settings',
        'enabled_modules',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'enabled_modules' => 'array',
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function moduleSettings(): HasMany
    {
        return $this->hasMany(ModuleSetting::class);
    }

    public function moodleConfig()
    {
        return $this->hasOne(MoodleConfig::class);
    }

    public function isModuleEnabled(string $moduleKey): bool
    {
        $setting = $this->moduleSettings()->where('module_key', $moduleKey)->first();
        return $setting ? $setting->is_enabled : false;
    }

    public function enableModule(string $moduleKey, array $settings = []): void
    {
        $this->moduleSettings()->updateOrCreate(
            ['module_key' => $moduleKey],
            ['is_enabled' => true, 'settings' => $settings]
        );
    }

    public function disableModule(string $moduleKey): void
    {
        $this->moduleSettings()->where('module_key', $moduleKey)->update(['is_enabled' => false]);
    }

    public function getCurrentAcademicYear(): ?AcademicYear
    {
        return $this->academicYears()->where('is_current', true)->first();
    }
}
