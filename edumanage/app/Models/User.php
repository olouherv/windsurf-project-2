<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'university_id',
        'name',
        'email',
        'password',
        'user_type',
        'phone',
        'locale',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->user_type === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->user_type, ['super_admin', 'admin']);
    }

    public function isTeacher(): bool
    {
        return $this->user_type === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->user_type === 'student';
    }

    public function isSecretary(): bool
    {
        return $this->user_type === 'secretary';
    }

    public function canAccessModule(string $moduleKey): bool
    {
        if ($this->isSuperAdmin()) return true;
        if (!$this->university) return false;
        
        return $this->university->isModuleEnabled($moduleKey);
    }
}
