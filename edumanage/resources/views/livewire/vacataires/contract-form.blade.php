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
                <label for="teacherSearch" class="block text-sm font-medium text-gray-700 mb-1">Enseignant vacataire *</label>
                @if($editMode && $selectedTeacher)
                <div class="flex items-center justify-between p-3 bg-gray-100 rounded-lg">
                    <div>
                        <span class="font-medium">{{ $selectedTeacher->full_name }}</span>
                        <span class="text-gray-500 text-sm ml-2">({{ $selectedTeacher->employee_id }})</span>
                    </div>
                </div>
                @else
                <div class="relative">
                    <input type="text" id="teacherSearch" wire:model.live.debounce.300ms="teacherSearch"
                        placeholder="Tapez le nom ou matricule de l'enseignant..."
                        autocomplete="off"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if($selectedTeacher) pr-10 @endif"
                        @if($selectedTeacher) readonly @endif>
                    @if($selectedTeacher)
                    <button type="button" wire:click="clearTeacher" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    @endif
                </div>
                
                @if($showTeacherDropdown && count($teacherResults) > 0)
                <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    @foreach($teacherResults as $teacher)
                    <button type="button" wire:click="selectTeacher({{ $teacher->id }})"
                        class="w-full px-4 py-3 text-left hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none border-b border-gray-100 last:border-0">
                        <div class="font-medium text-gray-900">{{ $teacher->full_name }}</div>
                        <div class="text-sm text-gray-500">{{ $teacher->employee_id }} • {{ $teacher->email }}</div>
                    </button>
                    @endforeach
                </div>
                @elseif($showTeacherDropdown && strlen($teacherSearch) >= 2 && count($teacherResults) === 0)
                <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-4 text-center text-gray-500">
                    Aucun enseignant vacataire trouvé
                </div>
                @endif
                @endif
                @error('teacher_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="relative">
                <label for="ecuSearch" class="block text-sm font-medium text-gray-700 mb-1">ECU concerné *</label>
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
                
                @if($showEcuDropdown && count($ecuResults) > 0)
                <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    @foreach($ecuResults as $ecu)
                    <button type="button" wire:click="selectEcu({{ $ecu->id }})"
                        class="w-full px-4 py-3 text-left hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none border-b border-gray-100 last:border-0">
                        <div class="font-medium text-gray-900">{{ $ecu->code }} - {{ $ecu->name }}</div>
                        <div class="text-sm text-gray-500">
                            {{ $ecu->ue->name ?? '' }} • CM: {{ $ecu->hours_cm }}h, TD: {{ $ecu->hours_td }}h, TP: {{ $ecu->hours_tp }}h
                        </div>
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
                <label for="teaching_type" class="block text-sm font-medium text-gray-700 mb-1">Type d'enseignement *</label>
                <select id="teaching_type" wire:model.live="teaching_type"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="all">Tous (CM + TD + TP)</option>
                    <option value="cm">CM uniquement</option>
                    <option value="td">TD uniquement</option>
                    <option value="tp">TP uniquement</option>
                </select>
                @error('teaching_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                @if($selectedEcu)
                <p class="mt-1 text-xs text-gray-500">
                    Heures disponibles : CM {{ $selectedEcu->hours_cm }}h, TD {{ $selectedEcu->hours_td }}h, TP {{ $selectedEcu->hours_tp }}h
                </p>
                @endif
            </div>

            <div>
                <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-1">Année académique *</label>
                <select id="academic_year_id" wire:model.live="academic_year_id"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Sélectionner une année</option>
                    @foreach($academicYears as $year)
                    <option value="{{ $year->id }}">{{ $year->name }} @if($year->is_current)(actuelle)@endif</option>
                    @endforeach
                </select>
                @error('academic_year_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-500">Les dates seront remplies automatiquement</p>
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de début *</label>
                <input type="date" id="start_date" wire:model="start_date"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin *</label>
                <input type="date" id="end_date" wire:model="end_date"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="total_hours_planned" class="block text-sm font-medium text-gray-700 mb-1">Heures prévues</label>
                <div class="flex items-center">
                    <input type="number" id="total_hours_planned" wire:model="total_hours_planned" min="1" readonly
                        class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <span class="ml-2 text-gray-500">h</span>
                </div>
                @error('total_hours_planned') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-500">Rempli automatiquement selon l'ECU et le type</p>
            </div>

            <div>
                <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-1">Taux horaire (€) *</label>
                <input type="number" id="hourly_rate" wire:model="hourly_rate" step="0.01" min="0"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('hourly_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                <select id="status" wire:model="status"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="draft">Brouillon</option>
                    <option value="active">Actif</option>
                    <option value="completed">Terminé</option>
                    <option value="cancelled">Annulé</option>
                </select>
                @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Montant total estimé</label>
                <div class="p-3 bg-indigo-50 rounded-lg text-indigo-900 font-bold text-lg">
                    {{ number_format($total_hours_planned * $hourly_rate, 2) }} €
                </div>
            </div>

            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea id="notes" wire:model="notes" rows="3"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-6 border-t">
            <a href="{{ route('vacataire-contracts.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                {{ $editMode ? 'Mettre à jour' : 'Créer le contrat' }}
            </button>
        </div>
    </form>
</div>
