<div class="p-6">
    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <ul class="list-disc list-inside text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="relative">
                <label for="ecuSearch" class="block text-sm font-medium text-gray-700 mb-1">ECU *</label>
                @if($editMode && $selectedEcu)
                <div class="flex items-center justify-between p-3 bg-gray-100 rounded-lg">
                    <div>
                        <span class="font-medium">{{ $selectedEcu->code }}</span>
                        <span class="text-gray-600 ml-2">{{ $selectedEcu->name }}</span>
                    </div>
                </div>
                @else
                <div class="relative">
                    <input type="text" id="ecuSearch" wire:model.live.debounce.300ms="ecuSearch"
                        placeholder="Rechercher un ECU..."
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
                
                @if($showEcuDropdown && count($ecuResults) > 0)
                <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    @foreach($ecuResults as $ecu)
                    <button type="button" wire:click="selectEcu({{ $ecu->id }})"
                        class="w-full px-4 py-3 text-left hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none border-b border-gray-100 last:border-0">
                        <div class="font-medium text-gray-900">{{ $ecu->code }} - {{ $ecu->name }}</div>
                        <div class="text-sm text-gray-500">{{ $ecu->ue->name ?? '' }}</div>
                    </button>
                    @endforeach
                </div>
                @elseif($showEcuDropdown && strlen($ecuSearch) >= 2 && count($ecuResults) === 0)
                <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-4 text-center text-gray-500">
                    Aucun ECU trouvé
                </div>
                @endif
                @endif
                @error('ecu_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-1">Année académique *</label>
                <select id="academic_year_id" wire:model="academic_year_id"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Sélectionner une année</option>
                    @foreach($academicYears as $year)
                    <option value="{{ $year->id }}">{{ $year->name }} @if($year->is_current)(actuelle)@endif</option>
                    @endforeach
                </select>
                @error('academic_year_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom de l'évaluation *</label>
                <input type="text" id="name" wire:model="name" placeholder="Ex: Examen final, CC1..."
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                <select id="type" wire:model="type"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="exam">Examen</option>
                    <option value="cc">Contrôle continu</option>
                    <option value="tp">TP</option>
                    <option value="project">Projet</option>
                    <option value="oral">Oral</option>
                </select>
                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="session" class="block text-sm font-medium text-gray-700 mb-1">Session *</label>
                <select id="session" wire:model="session"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="normal">Session normale</option>
                    <option value="rattrapage">Rattrapage</option>
                </select>
                @error('session') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="date" wire:model="date"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="coefficient" class="block text-sm font-medium text-gray-700 mb-1">Coefficient *</label>
                <input type="number" id="coefficient" wire:model="coefficient" step="0.1" min="0.1" max="10"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('coefficient') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="max_score" class="block text-sm font-medium text-gray-700 mb-1">Note maximale *</label>
                <input type="number" id="max_score" wire:model="max_score" step="1" min="1" max="100"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('max_score') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" wire:model="description" rows="3"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Instructions, barème, etc."></textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-6 border-t">
            <a href="{{ route('evaluations.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                {{ $editMode ? 'Mettre à jour' : 'Créer l\'évaluation' }}
            </button>
        </div>
    </form>
</div>
