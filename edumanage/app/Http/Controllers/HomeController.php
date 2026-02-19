<?php

namespace App\Http\Controllers;

use App\Models\PricingPlan;
use Illuminate\View\View;

class HomeController
{
    public function __invoke(): View
    {
        $plans = PricingPlan::query()
            ->where('is_active', true)
            ->orderBy('price_monthly')
            ->get();

        return view('welcome', compact('plans'));
    }
}
