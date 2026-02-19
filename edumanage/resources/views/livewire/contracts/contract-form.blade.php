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
                <label for="studentSearch" class="block text-sm font-medium text-gray-700 mb-1">Étudiant *</label>
                @if($editMode && $selectedStudent)
                <div class="flex items-center justify-between p-3 bg-gray-100 rounded-lg">
                    <div>
                        <span class="font-medium">{{ $selectedStudent->full_name }}</span>
                        <span class="text-gray-500 text-sm ml-2">({{ $selectedStudent->student_id }})</span>
                    </div>
                </div>
                @else
                <div class="relative">
                    <input type="text" id="studentSearch" wire:model.live.debounce.300ms="studentSearch"
                        placeholder="Tapez le nom ou matricule de l'étudiant..."
                        autocomplete="off"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if($selectedStudent) pr-10 @endif"
                        @if($selectedStudent) readonly @endif>
                    @if($selectedStudent)
                    <button type="button" wire:click="clearStudent" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    @endif
                </div>
                
                @if($showStudentDropdown && count($searchResults) > 0)
                <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    @foreach($searchResults as $student)
                    <button type="button" wire:click="selectStudent({{ $student->id }})"
                        class="w-full px-4 py-3 text-left hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none border-b border-gray-100 last:border-0">
                        <div class="font-medium text-gray-900">{{ $student->full_name }}</div>
                        <div class="text-sm text-gray-500">{{ $student->student_id }} • {{ $student->email }}</div>
                    </button>
                    @endforeach
                </div>
                @elseif($showStudentDropdown && strlen($studentSearch) >= 2 && count($searchResults) === 0)
                <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-4 text-center text-gray-500">
                    Aucun étudiant trouvé
                </div>
                @endif
                @endif
                @error('student_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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
                <label for="program_year_id" class="block text-sm font-medium text-gray-700 mb-1">Année de formation</label>
                <select id="program_year_id" wire:model.live="program_year_id"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Aucune</option>
                    @foreach($programYears as $py)
                    <option value="{{ $py->id }}">{{ $py->program?->name ?? '-' }} - {{ $py->name }} @if($py->total_fees > 0)({{ number_format($py->total_fees, 0) }} €)@endif</option>
                    @endforeach
                </select>
                @error('program_year_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-500">Les frais seront remplis automatiquement</p>
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type de contrat *</label>
                <select id="type" wire:model="type"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="inscription">Inscription</option>
                    <option value="formation">Formation</option>
                    <option value="stage">Stage</option>
                    <option value="apprentissage">Apprentissage</option>
                    <option value="autre">Autre</option>
                </select>
                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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
                <label for="tuition_fees" class="block text-sm font-medium text-gray-700 mb-1">Frais de scolarité (€) *</label>
                <input type="number" id="tuition_fees" wire:model="tuition_fees" step="0.01" min="0"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('tuition_fees') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="registration_fees" class="block text-sm font-medium text-gray-700 mb-1">Frais d'inscription (€) *</label>
                <input type="number" id="registration_fees" wire:model="registration_fees" step="0.01" min="0"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('registration_fees') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="installments" class="block text-sm font-medium text-gray-700 mb-1">Nombre de tranches *</label>
                <select id="installments" wire:model="installments"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}">{{ $i }} {{ $i > 1 ? 'tranches' : 'tranche (paiement unique)' }}</option>
                    @endfor
                </select>
                @error('installments') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                <select id="status" wire:model="status"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="draft">Brouillon</option>
                    <option value="active">Actif</option>
                    <option value="completed">Terminé</option>
                    <option value="cancelled">Annulé</option>
                    <option value="suspended">Suspendu</option>
                </select>
                @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="special_conditions" class="block text-sm font-medium text-gray-700 mb-1">Conditions particulières</label>
                <textarea id="special_conditions" wire:model="special_conditions" rows="2"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('special_conditions') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes internes</label>
                <textarea id="notes" wire:model="notes" rows="2"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <span class="text-gray-700 font-medium">Total:</span>
                <span class="text-2xl font-bold text-indigo-600">{{ number_format($tuition_fees + $registration_fees, 2) }} €</span>
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-6 border-t">
            <a href="{{ route('contracts.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                {{ $editMode ? 'Mettre à jour' : 'Créer le contrat' }}
            </button>
        </div>
    </form>
</div>
