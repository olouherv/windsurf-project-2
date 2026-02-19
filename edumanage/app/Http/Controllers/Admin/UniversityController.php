<?php

namespace App\Http\Controllers\Admin;

use App\Models\ModuleSetting;
use App\Models\PricingPlan;
use App\Models\University;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversityController
{
    public function index(): View
    {
        $plans = PricingPlan::query()->orderByDesc('is_active')->orderBy('name')->get();

        $universities = University::query()
            ->with(['moduleSettings', 'pricingPlan'])
            ->with(['users' => fn ($q) => $q->orderBy('created_at')])
            ->withCount(['students', 'teachers', 'rooms'])
            ->orderByDesc('created_at')
            ->get();

        $modules = ModuleSetting::MODULES;

        return view('admin.universities.index', compact('universities', 'modules', 'plans'));
    }

    public function toggleModule(Request $request, University $university, string $moduleKey): RedirectResponse
    {
        if (ModuleSetting::isRequired($moduleKey)) {
            return back()->with('error', 'Ce module est obligatoire et ne peut pas être désactivé.');
        }

        if (!array_key_exists($moduleKey, ModuleSetting::MODULES)) {
            return back()->with('error', 'Module inconnu.');
        }

        $setting = ModuleSetting::query()
            ->where('university_id', $university->id)
            ->where('module_key', $moduleKey)
            ->first();

        $newState = !($setting?->is_enabled ?? false);

        ModuleSetting::updateOrCreate(
            ['university_id' => $university->id, 'module_key' => $moduleKey],
            ['is_enabled' => $newState]
        );

        return back()->with('success', $newState
            ? 'Module activé pour cette université.'
            : 'Module désactivé pour cette université.'
        );
    }

    public function setPlan(Request $request, University $university): RedirectResponse
    {
        $data = $request->validate([
            'pricing_plan_id' => ['nullable', 'exists:pricing_plans,id'],
        ]);

        $planId = $data['pricing_plan_id'] ?? null;
        $plan = $planId ? PricingPlan::find($planId) : null;

        $university->pricing_plan_id = $plan?->id;
        $university->plan_key = $plan?->key;
        $university->plan_started_at = $plan ? now() : null;
        $university->save();

        return back()->with('success', 'Offre mise à jour pour cette université.');
    }
}
