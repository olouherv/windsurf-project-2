<x-layouts.admin>
    <x-slot name="title">Contrat {{ $vacataireContract->contract_number }}</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('vacataire-contracts.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour à la liste
        </a>
        <a href="{{ route('vacataire-contracts.edit', $vacataireContract) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Modifier
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $vacataireContract->contract_number }}</h1>
                        <p class="text-gray-500">{{ $vacataireContract->academicYear->name }}</p>
                    </div>
                    <span class="px-3 py-1 text-sm rounded-full
                        @switch($vacataireContract->status)
                            @case('active') bg-green-100 text-green-800 @break
                            @case('completed') bg-blue-100 text-blue-800 @break
                            @case('cancelled') bg-red-100 text-red-800 @break
                            @default bg-gray-100 text-gray-800
                        @endswitch
                    ">
                        @switch($vacataireContract->status)
                            @case('draft') Brouillon @break
                            @case('active') Actif @break
                            @case('completed') Terminé @break
                            @case('cancelled') Annulé @break
                        @endswitch
                    </span>
                </div>

                <div class="border-t pt-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Enseignant</h2>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                            <span class="text-indigo-600 font-bold text-lg">{{ substr($vacataireContract->teacher->first_name, 0, 1) }}{{ substr($vacataireContract->teacher->last_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <a href="{{ route('teachers.show', $vacataireContract->teacher) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                {{ $vacataireContract->teacher->full_name }}
                            </a>
                            <p class="text-sm text-gray-500">{{ $vacataireContract->teacher->employee_id }} • {{ $vacataireContract->teacher->email }}</p>
                        </div>
                    </div>
                </div>

                @if($vacataireContract->ecu)
                <div class="mt-6 pt-4 border-t">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">ECU concerné</h2>
                    <div class="p-4 bg-indigo-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-mono font-bold text-indigo-900">{{ $vacataireContract->ecu->code }}</span>
                                <span class="text-gray-700 ml-2">{{ $vacataireContract->ecu->name }}</span>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded bg-indigo-200 text-indigo-800 uppercase">
                                {{ $vacataireContract->teaching_type === 'all' ? 'Tous types' : $vacataireContract->teaching_type }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ $vacataireContract->ecu->ue->name ?? '' }}</p>
                        <div class="mt-2 text-xs text-gray-500">
                            Volume horaire : CM {{ $vacataireContract->ecu->hours_cm }}h, TD {{ $vacataireContract->ecu->hours_td }}h, TP {{ $vacataireContract->ecu->hours_tp }}h
                        </div>
                    </div>
                </div>
                @endif

                <dl class="grid grid-cols-2 gap-4 mt-6 pt-4 border-t">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Période</dt>
                        <dd class="text-gray-900">{{ $vacataireContract->start_date->format('d/m/Y') }} - {{ $vacataireContract->end_date->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Taux horaire</dt>
                        <dd class="text-gray-900">{{ number_format($vacataireContract->hourly_rate, 2) }} €/h</dd>
                    </div>
                </dl>

                @if($vacataireContract->notes)
                <div class="mt-6 pt-4 border-t">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Notes</h3>
                    <p class="text-gray-700">{{ $vacataireContract->notes }}</p>
                </div>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Heures déclarées</h2>
                    @if($vacataireContract->status === 'active')
                    <livewire:vacataires.add-hours :contract="$vacataireContract" />
                    @endif
                </div>
                
                @if($vacataireContract->hours->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ECU</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Heures</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($vacataireContract->hours->sortByDesc('date') as $hour)
                            <tr>
                                <td class="px-4 py-3 text-sm">{{ $hour->date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm">{{ $hour->ecu?->code ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="uppercase px-2 py-0.5 text-xs rounded bg-gray-100">{{ $hour->type }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm font-medium">{{ $hour->hours }}h</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($hour->is_validated)
                                    <span class="inline-flex items-center text-green-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Validé
                                    </span>
                                    @else
                                    <span class="text-yellow-600">En attente</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-gray-500 text-center py-4">Aucune heure déclarée</p>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Résumé financier</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Heures prévues</span>
                        <span class="font-medium">{{ $vacataireContract->total_hours_planned }}h</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Heures effectuées</span>
                        <span class="font-medium text-indigo-600">{{ $vacataireContract->hours_completed }}h</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Heures restantes</span>
                        <span class="font-medium">{{ $vacataireContract->remaining_hours }}h</span>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-3">
                        @php $progress = $vacataireContract->total_hours_planned > 0 ? ($vacataireContract->hours_completed / $vacataireContract->total_hours_planned) * 100 : 0; @endphp
                        <div class="bg-indigo-600 h-3 rounded-full" style="width: {{ min($progress, 100) }}%"></div>
                    </div>

                    <div class="pt-4 border-t space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Montant total</span>
                            <span class="font-bold text-lg">{{ number_format($vacataireContract->total_amount, 2) }} €</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Déjà payé</span>
                            <span class="font-medium text-green-600">{{ number_format($vacataireContract->amount_paid, 2) }} €</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t">
                            <span class="text-gray-900 font-medium">Reste à payer</span>
                            <span class="font-bold text-lg text-red-600">{{ number_format($vacataireContract->remaining_amount, 2) }} €</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50 rounded-lg p-4">
                <h3 class="font-medium text-indigo-900 mb-2">Calcul automatique</h3>
                <p class="text-sm text-indigo-700">
                    Le montant est calculé automatiquement : <br>
                    <strong>{{ $vacataireContract->total_hours_planned }}h × {{ number_format($vacataireContract->hourly_rate, 2) }} €</strong>
                </p>
            </div>
        </div>
    </div>
</x-layouts.admin>
