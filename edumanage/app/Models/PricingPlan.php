<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PricingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'price_monthly',
        'price_yearly',
        'currency',
        'is_active',
        'features',
        'limits',
        'included_modules',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'is_active' => 'boolean',
        'features' => 'array',
        'limits' => 'array',
        'included_modules' => 'array',
    ];

    public function universities(): HasMany
    {
        return $this->hasMany(University::class);
    }
}
