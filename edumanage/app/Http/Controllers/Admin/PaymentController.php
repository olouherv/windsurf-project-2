<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Models\PricingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController
{
    public function index(): View
    {
        $payments = Payment::query()
            ->with(['university', 'pricingPlan', 'requestedBy'])
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.payments.index', compact('payments'));
    }

    public function markPaid(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate([
            'provider' => ['nullable', 'string', 'max:64'],
            'reference' => ['nullable', 'string', 'max:128'],
        ]);

        if ($payment->status === 'paid') {
            return back()->with('success', 'Paiement déjà marqué comme payé.');
        }

        $payment->status = 'paid';
        $payment->paid_at = now();
        $payment->provider = $data['provider'] ?? $payment->provider;
        $payment->reference = $data['reference'] ?? $payment->reference;
        $payment->save();

        $plan = $payment->pricingPlan;

        if ($plan instanceof PricingPlan) {
            $u = $payment->university;
            $u->pricing_plan_id = $plan->id;
            $u->plan_key = $plan->key;
            $u->plan_started_at = now();
            $u->trial_ends_at = null;
            $u->save();
        }

        return back()->with('success', 'Paiement validé et plan appliqué.');
    }
}
