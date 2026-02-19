<?php

namespace App\Http\Controllers\Admin;

use App\Models\ModuleSetting;
use App\Models\PricingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingPlanController
{
    public function index(): View
    {
        $plans = PricingPlan::query()->orderByDesc('is_active')->orderBy('name')->get();

        return view('admin.pricing-plans.index', compact('plans'));
    }

    public function create(): View
    {
        $modules = ModuleSetting::MODULES;

        return view('admin.pricing-plans.create', compact('modules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:64', 'unique:pricing_plans,key'],
            'name' => ['required', 'string', 'max:255'],
            'price_monthly' => ['required', 'numeric', 'min:0'],
            'price_yearly' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:8'],
            'is_active' => ['sometimes', 'boolean'],
            'included_modules' => ['sometimes', 'array'],
            'included_modules.*' => ['string', 'max:64'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['included_modules'] = array_values($data['included_modules'] ?? []);

        PricingPlan::create($data);

        return redirect()->route('admin.pricing-plans.index')->with('success', 'Plan créé.');
    }

    public function edit(PricingPlan $pricingPlan): View
    {
        $modules = ModuleSetting::MODULES;

        return view('admin.pricing-plans.edit', ['plan' => $pricingPlan, 'modules' => $modules]);
    }

    public function update(Request $request, PricingPlan $pricingPlan): RedirectResponse
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:64', 'unique:pricing_plans,key,' . $pricingPlan->id],
            'name' => ['required', 'string', 'max:255'],
            'price_monthly' => ['required', 'numeric', 'min:0'],
            'price_yearly' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:8'],
            'is_active' => ['sometimes', 'boolean'],
            'included_modules' => ['sometimes', 'array'],
            'included_modules.*' => ['string', 'max:64'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['included_modules'] = array_values($data['included_modules'] ?? []);

        $pricingPlan->update($data);

        return redirect()->route('admin.pricing-plans.index')->with('success', 'Plan mis à jour.');
    }

    public function destroy(PricingPlan $pricingPlan): RedirectResponse
    {
        $pricingPlan->delete();

        return redirect()->route('admin.pricing-plans.index')->with('success', 'Plan supprimé.');
    }
}
