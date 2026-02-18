<div>
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
            <h3 class="text-lg font-semibold text-gray-900">ECUs assignés</h3>
            <select wire:model.live="academic_year_id" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @foreach($academicYears as $year)
                <option value="{{ $year->id }}">{{ $year->name }} @if($year->is_current)★@endif</option>
                @endforeach
            </select>
        </div>
        @if($teacher->type !== 'vacataire')
        <button type="button" wire:click="openModal" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Assigner un ECU
        </button>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if($contractEcus->count() > 0)
    <div class="mb-4">
        <h4 class="text-sm font-medium text-gray-500 mb-2">Via contrats vacataires</h4>
        <div class="space-y-2">
            @foreach($contractEcus as $item)
            <a href="{{ route('vacataire-contracts.show', $item['contract']) }}" 
                class="flex items-center justify-between p-4 bg-indigo-50 rounded-lg border border-indigo-200 hover:bg-indigo-100 transition-colors">
                <div class="flex-1">
                    <div class="flex items-center space-x-2">
                        <span class="font-medium text-indigo-900">{{ $item['ecu']->code }}</span>
                        <span class="text-indigo-700">{{ $item['ecu']->name }}</span>
                        <span class="px-2 py-0.5 text-xs bg-indigo-200 text-indigo-800 rounded-full">Contrat</span>
                    </div>
                    <div class="text-sm text-indigo-600 mt-1">
                        {{ $item['ecu']->ue->name ?? 'UE non définie' }} •
                        <span class="uppercase">{{ $item['teaching_type'] === 'all' ? 'Tous types' : $item['teaching_type'] }}</span>
                        • {{ $item['contract']->contract_number }}
                    </div>
                </div>
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($teacher->type !== 'vacataire' && $assignments->count() > 0)
    <div class="space-y-2">
        @if($contractEcus->count() > 0)
        <h4 class="text-sm font-medium text-gray-500 mb-2">Assignations manuelles</h4>
        @endif
        @foreach($assignments as $ecu)
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <span class="font-medium text-gray-900">{{ $ecu->code }}</span>
                    <span class="text-gray-600">{{ $ecu->name }}</span>
                    @if($ecu->pivot->is_responsible)
                    <span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded-full">Responsable</span>
                    @endif
                </div>
                <div class="text-sm text-gray-500 mt-1">
                    {{ $ecu->ue->name ?? 'UE non définie' }} •
                    <span class="uppercase">{{ $ecu->pivot->teaching_type === 'all' ? 'Tous types' : $ecu->pivot->teaching_type }}</span>
                </div>
            </div>
            <button type="button" 
                wire:click="removeAssignment({{ $ecu->id }}, {{ $ecu->pivot->academic_year_id }}, '{{ $ecu->pivot->teaching_type }}')"
                wire:confirm="Voulez-vous vraiment supprimer cette assignation ?"
                class="text-red-500 hover:text-red-700 p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
        @endforeach
    </div>
    @elseif($contractEcus->count() === 0)
    <div class="text-center py-8 text-gray-500">
        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        @if($teacher->type === 'vacataire')
        <p>Aucun ECU pour cette année</p>
        <p class="text-sm mt-1">Créez un contrat vacataire pour assigner un ECU</p>
        @else
        <p>Aucun ECU assigné pour cette année</p>
        @endif
    </div>
    @endif

    @if($showModal)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Assigner un ECU</h3>
            </div>
            
            <form wire:submit.prevent="assign">
                <div class="px-6 py-4 space-y-4">
                    <div class="relative">
                        <label for="ecuSearch" class="block text-sm font-medium text-gray-700 mb-1">ECU *</label>
                        <div class="relative">
                            <input type="text" id="ecuSearch" wire:model.live.debounce.300ms="ecuSearch"
                                placeholder="Rechercher un ECU (code ou nom)..."
                                autocomplete="off"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if($selectedEcu) pr-10 @endif"
                                @if($selectedEcu) readonly @endif>
                            @if($selectedEcu)
                            <button type="button" wire:click="clearEcu" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            @endif
                        </div>
                        
                        @if($showEcuDropdown && count($searchResults) > 0)
                        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            @foreach($searchResults as $ecu)
                            <button type="button" wire:click="selectEcu({{ $ecu->id }})"
                                class="w-full px-4 py-2 text-left hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none border-b border-gray-100 last:border-0">
                                <div class="font-medium text-gray-900">{{ $ecu->code }} - {{ $ecu->name }}</div>
                                <div class="text-sm text-gray-500">{{ $ecu->ue->name ?? 'UE non définie' }}</div>
                            </button>
                            @endforeach
                        </div>
                        @elseif($showEcuDropdown && strlen($ecuSearch) >= 2 && count($searchResults) === 0)
                        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-4 text-center text-gray-500">
                            Aucun ECU trouvé
                        </div>
                        @endif
                        @error('ecu_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="teaching_type" class="block text-sm font-medium text-gray-700 mb-1">Type d'enseignement *</label>
                        <select wire:model="teaching_type" id="teaching_type"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all">Tous (CM, TD, TP)</option>
                            <option value="cm">CM uniquement</option>
                            <option value="td">TD uniquement</option>
                            <option value="tp">TP uniquement</option>
                        </select>
                        @error('teaching_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" wire:model="is_responsible" id="is_responsible"
                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="is_responsible" class="ml-2 block text-sm text-gray-700">
                            Responsable de l'ECU
                        </label>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Assigner
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
