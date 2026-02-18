<div>
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
            <h3 class="text-lg font-semibold text-gray-900">Étudiants inscrits</h3>
            <select wire:model.live="academicYearId" class="text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach($academicYears as $year)
                <option value="{{ $year->id }}">{{ $year->name }} @if($year->is_current)★@endif</option>
                @endforeach
            </select>
        </div>
        <button type="button" wire:click="openModal" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Inscrire des étudiants
        </button>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if($enrollments->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom complet</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date inscription</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($enrollments as $enrollment)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-mono text-gray-600">{{ $enrollment->student->student_id }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('students.show', $enrollment->student) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            {{ $enrollment->student->full_name }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">
                        {{ $enrollment->enrollment_date?->format('d/m/Y') ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <select wire:change="updateStatus({{ $enrollment->id }}, $event.target.value)" 
                            class="text-xs rounded-lg border-gray-300 
                                @if($enrollment->status === 'validated') bg-green-50 text-green-800 border-green-200
                                @elseif($enrollment->status === 'failed') bg-red-50 text-red-800 border-red-200
                                @elseif($enrollment->status === 'abandoned') bg-gray-50 text-gray-800 border-gray-200
                                @else bg-blue-50 text-blue-800 border-blue-200 @endif">
                            <option value="enrolled" @selected($enrollment->status === 'enrolled')>Inscrit</option>
                            <option value="validated" @selected($enrollment->status === 'validated')>Validé</option>
                            <option value="failed" @selected($enrollment->status === 'failed')>Ajourné</option>
                            <option value="abandoned" @selected($enrollment->status === 'abandoned')>Abandonné</option>
                            <option value="transferred" @selected($enrollment->status === 'transferred')>Transféré</option>
                        </select>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button type="button" wire:click="removeEnrollment({{ $enrollment->id }})" 
                            wire:confirm="Supprimer cette inscription ?"
                            class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4 text-sm text-gray-500">
        {{ $enrollments->count() }} étudiant(s) inscrit(s)
    </div>
    @else
    <div class="text-center py-8 text-gray-500">
        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
        </svg>
        <p>Aucun étudiant inscrit pour cette année académique</p>
        <p class="text-sm mt-1">Cliquez sur "Inscrire des étudiants" pour commencer</p>
    </div>
    @endif

    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Inscrire des étudiants</h3>
                    
                    <div class="mb-4">
                        <input type="text" wire:model.live.debounce.300ms="studentSearch" 
                            placeholder="Rechercher par nom ou matricule..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    @if(strlen($studentSearch) >= 2)
                        @if($searchResults->count() > 0)
                        <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100">
                            @foreach($searchResults as $student)
                            <label class="flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" 
                                    wire:click="toggleStudent({{ $student->id }})"
                                    @checked(in_array($student->id, $selectedStudents))
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $student->student_id }}</div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4 text-gray-500 text-sm">
                            Aucun étudiant trouvé
                        </div>
                        @endif
                    @else
                    <div class="text-center py-4 text-gray-500 text-sm">
                        Tapez au moins 2 caractères pour rechercher
                    </div>
                    @endif

                    @if(count($selectedStudents) > 0)
                    <div class="mt-4 p-3 bg-indigo-50 rounded-lg">
                        <span class="text-sm font-medium text-indigo-700">{{ count($selectedStudents) }} étudiant(s) sélectionné(s)</span>
                    </div>
                    @endif
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" wire:click="enrollStudents"
                        @disabled(count($selectedStudents) === 0)
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-white font-medium hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Inscrire
                    </button>
                    <button type="button" wire:click="closeModal"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 font-medium hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
