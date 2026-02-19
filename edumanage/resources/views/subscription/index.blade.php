<x-layouts.admin :title="__('Abonnement')">
    <x-slot name="header">{{ __('Abonnement / Offre') }}</x-slot>

    <div class="w-full space-y-6">
        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900">Offre actuelle</h3>
            <div class="mt-2 text-sm text-gray-700">
                <div><span class="text-gray-500">Université:</span> {{ $university->name }}</div>
                <div><span class="text-gray-500">Démo:</span> {{ $university->isInTrial() ? 'Oui' : 'Non' }}</div>
                <div><span class="text-gray-500">Plan:</span> {{ $university->pricingPlan?->name ?? 'Aucun' }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900">Changer d'offre</h3>
            <p class="text-sm text-gray-500 mt-1">Choisis un plan et envoie une demande. Le superadmin validera le paiement et appliquera le plan.</p>

            <form method="POST" action="{{ route('subscription.request-change') }}" class="mt-4 flex flex-col md:flex-row gap-2 items-start md:items-end">
                @csrf
                <div class="w-full md:w-96">
                    <label class="block text-sm font-medium text-gray-700">Plan</label>
                    <select name="pricing_plan_id" class="mt-1 w-full border-gray-300 rounded-md" required>
                        <option value="">--</option>
                        @foreach($plans as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->currency }} {{ number_format($p->price_monthly, 2) }}/mo)</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Envoyer la demande</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900">Historique des demandes / paiements</h3>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payments as $pay)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $pay->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $pay->pricingPlan?->name ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $pay->currency }} {{ number_format((float) $pay->amount, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $pay->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">Aucune demande</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
