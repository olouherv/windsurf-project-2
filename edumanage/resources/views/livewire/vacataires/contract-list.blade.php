<div>
    <div class="mb-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Année académique</label>
                <select wire:model.live="academicYearId" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-medium">
                    <option value="">Toutes les années</option>
                    @foreach($academicYears as $year)
                    <option value="{{ $year->id }}">{{ $year->name }} @if($year->is_current)★@endif</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 max-w-lg">
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher par enseignant ou n° contrat..."
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <select wire:model.live="status" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="">Tous les statuts</option>
                <option value="draft">Brouillon</option>
                <option value="active">Actif</option>
                <option value="completed">Terminé</option>
                <option value="cancelled">Annulé</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Contrat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enseignant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ECU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($contracts as $contract)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-mono text-gray-900">{{ $contract->contract_number }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $contract->teacher->full_name }}</div>
                        <div class="text-sm text-gray-500">{{ $contract->teacher->employee_id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($contract->ecu)
                        <div class="text-sm font-medium text-gray-900">{{ $contract->ecu->code }}</div>
                        <div class="text-xs text-gray-500">
                            <span class="uppercase">{{ $contract->teaching_type === 'all' ? 'Tous' : $contract->teaching_type }}</span>
                        </div>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $contract->hours_completed }} / {{ $contract->total_hours_planned }} h</div>
                        <div class="w-24 bg-gray-200 rounded-full h-2 mt-1">
                            @php $progress = $contract->total_hours_planned > 0 ? ($contract->hours_completed / $contract->total_hours_planned) * 100 : 0; @endphp
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min($progress, 100) }}%"></div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ number_format($contract->total_amount, 2) }} €</div>
                        <div class="text-xs text-gray-500">{{ number_format($contract->hourly_rate, 2) }} €/h</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full
                            @switch($contract->status)
                                @case('active') bg-green-100 text-green-800 @break
                                @case('completed') bg-blue-100 text-blue-800 @break
                                @case('cancelled') bg-red-100 text-red-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch
                        ">
                            @switch($contract->status)
                                @case('draft') Brouillon @break
                                @case('active') Actif @break
                                @case('completed') Terminé @break
                                @case('cancelled') Annulé @break
                            @endswitch
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('vacataire-contracts.show', $contract) }}" class="text-gray-400 hover:text-indigo-600" title="Voir">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('vacataire-contracts.edit', $contract) }}" class="text-gray-400 hover:text-yellow-600" title="Modifier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>Aucun contrat vacataire trouvé</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $contracts->links() }}
    </div>
</div>
