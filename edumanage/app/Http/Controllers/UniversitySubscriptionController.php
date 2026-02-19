<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PricingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversitySubscriptionController
{
    public function index(): View
    {
        $university = auth()->user()->university;

        $plans = PricingPlan::query()
            ->where('is_active', true)
            ->orderBy('price_monthly')
            ->get();

        $payments = Payment::query()
            ->where('university_id', $university->id)
            ->with('pricingPlan')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('subscription.index', compact('university', 'plans', 'payments'));
    }

    public function requestChange(Request $request): RedirectResponse
    {
        $university = auth()->user()->university;

        $data = $request->validate([
            'pricing_plan_id' => ['required', 'exists:pricing_plans,id'],
        ]);

        $plan = PricingPlan::findOrFail($data['pricing_plan_id']);

        Payment::create([
            'university_id' => $university->id,
            'pricing_plan_id' => $plan->id,
            'requested_by_user_id' => auth()->id(),
            'amount' => (float) $plan->price_monthly,
            'currency' => $plan->currency,
            'status' => 'pending',
            'notes' => 'Demande de changement de plan',
        ]);

        return back()->with('success', 'Demande envoyée. Un paiement est en attente de validation.');
    }
}
