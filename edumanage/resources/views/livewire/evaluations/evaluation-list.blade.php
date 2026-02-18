<div>
    <div class="mb-6 flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher..."
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <select wire:model.live="academicYearId" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @foreach($academicYears as $year)
            <option value="{{ $year->id }}">{{ $year->name }} @if($year->is_current)★@endif</option>
            @endforeach
        </select>
        <select wire:model.live="type" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Tous les types</option>
            <option value="exam">Examen</option>
            <option value="cc">Contrôle continu</option>
            <option value="tp">TP</option>
            <option value="project">Projet</option>
            <option value="oral">Oral</option>
        </select>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Évaluation</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ECU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($evaluations as $evaluation)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $evaluation->name }}</div>
                        <div class="text-xs text-gray-500">Coef: {{ $evaluation->coefficient }} • Max: {{ $evaluation->max_score }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $evaluation->ecu->code }}</div>
                        <div class="text-xs text-gray-500">{{ $evaluation->ecu->ue->name ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full
                            @switch($evaluation->type)
                                @case('exam') bg-red-100 text-red-800 @break
                                @case('cc') bg-blue-100 text-blue-800 @break
                                @case('tp') bg-green-100 text-green-800 @break
                                @case('project') bg-purple-100 text-purple-800 @break
                                @case('oral') bg-yellow-100 text-yellow-800 @break
                            @endswitch
                        ">
                            @switch($evaluation->type)
                                @case('exam') Examen @break
                                @case('cc') CC @break
                                @case('tp') TP @break
                                @case('project') Projet @break
                                @case('oral') Oral @break
                            @endswitch
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $evaluation->date ? $evaluation->date->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="text-sm font-medium text-gray-900">{{ $evaluation->grades_count }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($evaluation->is_published)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Publiée</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Brouillon</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        <a href="{{ route('evaluations.grades', $evaluation) }}" class="text-green-600 hover:text-green-900 mr-3" title="Saisir les notes">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </a>
                        <a href="{{ route('evaluations.show', $evaluation) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('evaluations.edit', $evaluation) }}" class="text-yellow-600 hover:text-yellow-900">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        Aucune évaluation trouvée
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $evaluations->links() }}
    </div>
</div>
