<x-layouts.admin>
    <x-slot name="header">Détails du contrat</x-slot>

    <div class="w-full">
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-mono">{{ $contract->contract_number }}</p>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $contract->student->full_name }}</h1>
                    <p class="text-gray-500">{{ $contract->student->student_id }} • {{ $contract->student->email }}</p>
                    <div class="flex items-center space-x-3 mt-3">
                        <span class="px-3 py-1 text-sm rounded-full
                            @switch($contract->type)
                                @case('inscription') bg-blue-100 text-blue-800 @break
                                @case('formation') bg-purple-100 text-purple-800 @break
                                @case('stage') bg-orange-100 text-orange-800 @break
                                @case('apprentissage') bg-teal-100 text-teal-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch
                        ">{{ ucfirst($contract->type) }}</span>
                        <span class="px-3 py-1 text-sm rounded-full
                            @switch($contract->status)
                                @case('active') bg-green-100 text-green-800 @break
                                @case('completed') bg-blue-100 text-blue-800 @break
                                @case('cancelled') bg-red-100 text-red-800 @break
                                @case('suspended') bg-orange-100 text-orange-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch
                        ">
                            @switch($contract->status)
                                @case('draft') Brouillon @break
                                @case('active') Actif @break
                                @case('completed') Terminé @break
                                @case('cancelled') Annulé @break
                                @case('suspended') Suspendu @break
                            @endswitch
                        </span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('contracts.edit', $contract) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations du contrat</h2>
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500">Année académique</dt>
                            <dd class="text-gray-900">{{ $contract->academicYear->name }}</dd>
                        </div>
                        @if($contract->programYear)
                        <div>
                            <dt class="text-sm text-gray-500">Formation</dt>
                            <dd class="text-gray-900">{{ $contract->programYear->program->name }} - {{ $contract->programYear->name }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm text-gray-500">Date de début</dt>
                            <dd class="text-gray-900">{{ $contract->start_date->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Date de fin</dt>
                            <dd class="text-gray-900">{{ $contract->end_date->format('d/m/Y') }}</dd>
                        </div>
                        @if($contract->signed_date)
                        <div>
                            <dt class="text-sm text-gray-500">Date de signature</dt>
                            <dd class="text-gray-900">{{ $contract->signed_date->format('d/m/Y') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                @if($contract->special_conditions)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Conditions particulières</h2>
                    <p class="text-gray-600">{{ $contract->special_conditions }}</p>
                </div>
                @endif

                @if($contract->paymentSchedules->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Échéancier de paiement</h2>
                    <div class="space-y-3">
                        @foreach($contract->paymentSchedules->sortBy('installment_number') as $schedule)
                        <div class="flex items-center justify-between p-4 rounded-lg border
                            @if($schedule->status === 'paid') bg-green-50 border-green-200
                            @elseif($schedule->status === 'overdue') bg-red-50 border-red-200
                            @elseif($schedule->status === 'partial') bg-yellow-50 border-yellow-200
                            @else bg-gray-50 border-gray-200 @endif">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold
                                        @if($schedule->status === 'paid') bg-green-200 text-green-800
                                        @elseif($schedule->status === 'overdue') bg-red-200 text-red-800
                                        @else bg-gray-200 text-gray-800 @endif">
                                        {{ $schedule->installment_number }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $schedule->label }}</p>
                                        <p class="text-sm text-gray-500">Échéance: {{ $schedule->due_date->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-lg">{{ number_format($schedule->amount, 2) }} €</p>
                                @if($schedule->amount_paid > 0)
                                <p class="text-sm text-green-600">Payé: {{ number_format($schedule->amount_paid, 2) }} €</p>
                                @endif
                                @if($schedule->remaining_amount > 0)
                                <p class="text-sm {{ $schedule->status === 'overdue' ? 'text-red-600' : 'text-gray-500' }}">
                                    Reste: {{ number_format($schedule->remaining_amount, 2) }} €
                                </p>
                                @endif
                            </div>
                            <div class="ml-4">
                                @if($schedule->status === 'paid')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Payé
                                </span>
                                @elseif($schedule->status === 'overdue')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">En retard</span>
                                @elseif($schedule->status === 'partial')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">Partiel</span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800">En attente</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Historique des paiements</h2>
                        <livewire:contracts.add-payment :contract="$contract" />
                    </div>
                    
                    @if($contract->payments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tranche</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Méthode</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Enregistré par</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($contract->payments as $payment)
                                <tr>
                                    <td class="px-4 py-3 text-sm">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $payment->paymentSchedule?->label ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-green-600">+{{ number_format($payment->amount, 2) }} €</td>
                                    <td class="px-4 py-3 text-sm">
                                        @switch($payment->payment_method)
                                            @case('cash') Espèces @break
                                            @case('bank_transfer') Virement @break
                                            @case('check') Chèque @break
                                            @case('card') Carte @break
                                            @case('mobile_money') Mobile Money @break
                                            @default Autre
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $payment->reference ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $payment->recordedBy?->name ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-4">Aucun paiement enregistré</p>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Résumé financier</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Frais de scolarité</span>
                            <span class="font-medium">{{ number_format($contract->tuition_fees, 2) }} €</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Frais d'inscription</span>
                            <span class="font-medium">{{ number_format($contract->registration_fees, 2) }} €</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between">
                            <span class="text-gray-900 font-semibold">Total</span>
                            <span class="text-lg font-bold">{{ number_format($contract->total_amount, 2) }} €</span>
                        </div>
                        <div class="flex justify-between text-green-600">
                            <span>Payé</span>
                            <span class="font-medium">{{ number_format($contract->amount_paid, 2) }} €</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between">
                            <span class="text-gray-900 font-semibold">Reste à payer</span>
                            <span class="text-xl font-bold {{ $contract->remaining_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($contract->remaining_amount, 2) }} €
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            @php $percent = $contract->total_amount > 0 ? min(100, ($contract->amount_paid / $contract->total_amount) * 100) : 0; @endphp
                            <div class="h-3 rounded-full {{ $percent >= 100 ? 'bg-green-500' : 'bg-indigo-500' }}" style="width: {{ $percent }}%"></div>
                        </div>
                        <p class="text-center text-sm text-gray-500 mt-1">{{ number_format($percent, 0) }}% payé</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Statut paiement</h2>
                    <div class="flex items-center justify-center">
                        <span class="px-4 py-2 text-lg font-semibold rounded-full
                            @switch($contract->payment_status)
                                @case('paid') bg-green-100 text-green-800 @break
                                @case('partial') bg-yellow-100 text-yellow-800 @break
                                @case('overdue') bg-red-100 text-red-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch
                        ">
                            @switch($contract->payment_status)
                                @case('pending') En attente @break
                                @case('partial') Partiellement payé @break
                                @case('paid') Entièrement payé @break
                                @case('overdue') En retard @break
                            @endswitch
                        </span>
                    </div>
                </div>

                @if($contract->student->guarantor_first_name)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Garant</h2>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm text-gray-500">Nom</dt>
                            <dd class="text-gray-900">{{ $contract->student->guarantor_first_name }} {{ $contract->student->guarantor_last_name }}</dd>
                        </div>
                        @if($contract->student->guarantor_relationship)
                        <div>
                            <dt class="text-sm text-gray-500">Relation</dt>
                            <dd class="text-gray-900">{{ $contract->student->guarantor_relationship }}</dd>
                        </div>
                        @endif
                        @if($contract->student->guarantor_phone)
                        <div>
                            <dt class="text-sm text-gray-500">Téléphone</dt>
                            <dd class="text-gray-900">{{ $contract->student->guarantor_phone }}</dd>
                        </div>
                        @endif
                        @if($contract->student->guarantor_email)
                        <div>
                            <dt class="text-sm text-gray-500">Email</dt>
                            <dd class="text-gray-900">{{ $contract->student->guarantor_email }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('contracts.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à la liste
            </a>
        </div>
    </div>
</x-layouts.admin>
