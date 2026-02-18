<div class="p-6">
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-500">Étudiants inscrits</div>
        </div>
        <div class="bg-blue-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['graded'] }}</div>
            <div class="text-sm text-gray-500">Notes saisies</div>
        </div>
        <div class="bg-green-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['average'] ? number_format($stats['average'], 2) : '-' }}</div>
            <div class="text-sm text-gray-500">Moyenne</div>
        </div>
        <div class="bg-red-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $stats['absent'] }}</div>
            <div class="text-sm text-gray-500">Absents</div>
        </div>
    </div>

    <div class="mb-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher un étudiant..."
            class="w-full md:w-1/3 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    @if(count($filteredGrades) > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom complet</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Note /{{ $evaluation->max_score }}</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Absent</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commentaire</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($filteredGrades as $studentId => $data)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-mono text-gray-600">{{ $data['student_number'] }}</td>
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $data['student_name'] }}</td>
                    <td class="px-4 py-3">
                        <input type="number" 
                            wire:model="grades.{{ $studentId }}.score" 
                            step="0.25" 
                            min="0" 
                            max="{{ $evaluation->max_score }}"
                            @if($data['is_absent']) disabled @endif
                            class="w-20 text-center rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 
                                @if($data['is_absent']) bg-gray-100 @endif
                                @if($data['score'] !== null && !$data['is_absent'])
                                    @if($data['score'] >= ($evaluation->max_score / 2)) border-green-300 bg-green-50 @else border-red-300 bg-red-50 @endif
                                @endif">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="checkbox" 
                            wire:model.live="grades.{{ $studentId }}.is_absent"
                            class="h-5 w-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    </td>
                    <td class="px-4 py-3">
                        <input type="text" 
                            wire:model="grades.{{ $studentId }}.comment" 
                            placeholder="Optionnel..."
                            class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex justify-end space-x-3 pt-6 border-t">
        <a href="{{ route('evaluations.show', $evaluation) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            Annuler
        </a>
        <button type="button" wire:click="saveGrades" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Enregistrer
        </button>
        @if(!$evaluation->is_published)
        <button type="button" wire:click="publishGrades" wire:confirm="Voulez-vous publier les notes ? Les étudiants pourront les consulter."
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Publier les notes
        </button>
        @endif
    </div>
    @else
    <div class="text-center py-12 text-gray-500">
        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
        </svg>
        <p class="text-lg font-medium">Aucun étudiant inscrit</p>
        <p class="text-sm mt-1">Inscrivez d'abord des étudiants à cet ECU pour pouvoir saisir des notes.</p>
    </div>
    @endif
</div>
