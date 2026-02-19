<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

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
        'trial_ends_at',
        'pricing_plan_id',
        'plan_key',
        'plan_started_at',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'enabled_modules' => 'array',
        'trial_ends_at' => 'datetime',
        'plan_started_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function pricingPlan(): BelongsTo
    {
        return $this->belongsTo(PricingPlan::class);
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
        if ($this->isInTrial()) {
            return true;
        }

        if (ModuleSetting::isRequired($moduleKey)) {
            return true;
        }

        $planModules = $this->pricingPlan?->included_modules;
        if (is_array($planModules)) {
            return in_array($moduleKey, $planModules, true);
        }

        $setting = $this->moduleSettings()->where('module_key', $moduleKey)->first();
        return $setting ? $setting->is_enabled : false;
    }

    public function trialEndsAt(): ?Carbon
    {
        return $this->trial_ends_at;
    }

    public function isInTrial(): bool
    {
        return (bool) $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function isTrialExpired(): bool
    {
        return (bool) $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    public function trialRemainingHuman(): ?string
    {
        if (!$this->trial_ends_at) {
            return null;
        }

        if ($this->trial_ends_at->isPast()) {
            return null;
        }

        return now()->diffForHumans($this->trial_ends_at, [
            'parts' => 2,
            'short' => true,
            'syntax' => Carbon::DIFF_ABSOLUTE,
        ]);
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
