<x-layouts.admin>
    <x-slot name="header">Paiements</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Paiements (Super Admin)</h3>
                <p class="text-sm text-gray-500">Suivi des demandes de changement de plan et validations</p>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Université</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payments as $p)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <div class="font-medium">{{ $p->university?->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">Demandeur: {{ $p->requestedBy?->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $p->pricingPlan?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $p->currency }} {{ number_format((float) $p->amount, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $p->status }}</td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        @if($p->status !== 'paid')
                                            <form method="POST" action="{{ route('admin.payments.mark-paid', $p) }}" class="flex items-center justify-end gap-2">
                                                @csrf
                                                <input type="text" name="provider" placeholder="provider" class="border-gray-300 rounded-md text-xs" />
                                                <input type="text" name="reference" placeholder="ref" class="border-gray-300 rounded-md text-xs" />
                                                <button type="submit" class="px-3 py-1 rounded-md bg-indigo-600 text-white text-xs hover:bg-indigo-700">Marquer payé</button>
                                            </form>
                                        @else
                                            <span class="text-green-700">Payé</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
