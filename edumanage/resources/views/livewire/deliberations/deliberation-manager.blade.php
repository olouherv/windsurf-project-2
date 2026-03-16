<div>
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Année académique</label>
                <select wire:model.live="academicYearId" class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="">Toutes</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filière</label>
                <select wire:model.live="programId" class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="">Toutes</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Niveau</label>
                <select wire:model.live="programYearId" class="w-full border-gray-300 rounded-lg text-sm" {{ !$programId ? 'disabled' : '' }}>
                    <option value="">Tous</option>
                    @foreach($programYears as $py)
                        <option value="{{ $py->id }}">{{ $py->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select wire:model.live="typeFilter" class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="">Tous</option>
                    <option value="semester">Semestrielle</option>
                    <option value="annual">Annuelle</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Délibérations</h3>
            <div class="flex items-center space-x-2">
                <a href="{{ route('settings.deliberation') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition">
                    Paramètres
                </a>
                <button wire:click="openCreateModal" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    + Nouvelle délibération
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Année</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Filière / Niveau</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Session</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($deliberations as $delib)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $delib->deliberation_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $delib->academicYear?->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $delib->programYear?->program?->name }} - {{ $delib->programYear?->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 text-xs rounded {{ $delib->type === 'semester' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ $delib->type_label }}
                                    @if($delib->semester)
                                        - {{ $delib->semester->name }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $delib->session_label }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @switch($delib->status)
                                    @case('draft')
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Brouillon</span>
                                        @break
                                    @case('in_progress')
                                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">En cours</span>
                                        @break
                                    @case('validated')
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Validée</span>
                                        @break
                                    @case('published')
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Publiée</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end space-x-2">
                                    @if($delib->status === 'draft')
                                        <button wire:click="calculateResults({{ $delib->id }})" class="text-indigo-600 hover:text-indigo-900" title="Calculer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    @endif
                                    @if($delib->status === 'in_progress')
                                        <button wire:click="validateDeliberation({{ $delib->id }})" class="text-green-600 hover:text-green-900" title="Valider">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    @endif
                                    @if($delib->status === 'validated')
                                        <button wire:click="publishDeliberation({{ $delib->id }})" class="text-blue-600 hover:text-blue-900" title="Publier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    @endif
                                    <button wire:click="openDetails({{ $delib->id }})" class="text-gray-600 hover:text-gray-900" title="Voir résultats">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </button>
                                    @if($delib->status === 'draft')
                                        <button wire:click="deleteDeliberation({{ $delib->id }})" wire:confirm="Supprimer cette délibération ?" class="text-red-600 hover:text-red-900" title="Supprimer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Aucune délibération trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deliberations->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $deliberations->links() }}
            </div>
        @endif
    </div>

    @if($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Nouvelle délibération</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de délibération</label>
                        <select wire:model.live="deliberationType" class="w-full border-gray-300 rounded-lg">
                            <option value="semester">Semestrielle</option>
                            <option value="annual">Annuelle</option>
                        </select>
                    </div>
                    @if($deliberationType === 'semester')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Semestre</label>
                            <select wire:model="semesterId" class="w-full border-gray-300 rounded-lg" {{ !$programYearId ? 'disabled' : '' }}>
                                <option value="">-- Sélectionner --</option>
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}">{{ $sem->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Session</label>
                        <select wire:model="session" class="w-full border-gray-300 rounded-lg">
                            <option value="normal">Session normale</option>
                            <option value="rattrapage">Session de rattrapage</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de délibération</label>
                        <input type="date" wire:model="deliberationDate" class="w-full border-gray-300 rounded-lg">
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button wire:click="closeCreateModal" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button wire:click="createDeliberation" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Créer
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showDetailsModal && $selectedDeliberation)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto py-8">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-6xl mx-4">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Résultats de délibération
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ $selectedDeliberation->programYear?->program?->name }} - {{ $selectedDeliberation->programYear?->name }}
                            @if($selectedDeliberation->semester)
                                | {{ $selectedDeliberation->semester->name }}
                            @endif
                            | {{ $selectedDeliberation->session_label }}
                        </p>
                    </div>
                    <button wire:click="closeDetails" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 max-h-[70vh] overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rang</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Moyenne</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Crédits</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Décision</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mention</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($selectedDeliberation->results->sortBy('rank') as $result)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $result->rank ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ $result->student?->student_id }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ $result->student?->full_name }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium {{ ($result->semester_average ?? $result->year_average ?? 0) >= 10 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($result->semester_average ?? $result->year_average ?? 0, 2) }}/20
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                        <span class="{{ $result->credits_validated === $result->credits_total ? 'text-green-600' : 'text-orange-600' }}">
                                            {{ $result->credits_validated }}/{{ $result->credits_total }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $result->decision_color }}-100 text-{{ $result->decision_color }}-700">
                                            {{ $result->decision_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $result->mention ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
