<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Semestres / Périodes</h3>
        <button wire:click="openModal" class="inline-flex items-center px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter un semestre
        </button>
    </div>

    @if(session('message'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
        {{ session('message') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
        {{ session('error') }}
    </div>
    @endif

    @if($semesters->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($semesters as $semester)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="font-medium text-gray-900">{{ $semester->name }}</h4>
                    <p class="text-sm text-gray-500">Période {{ $semester->semester_number }}</p>
                </div>
                <div class="flex space-x-1">
                    <button wire:click="editSemester({{ $semester->id }})" class="p-1 text-gray-400 hover:text-indigo-600" title="Modifier">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    @if($semester->ues->count() == 0)
                    <button wire:click="deleteSemester({{ $semester->id }})" wire:confirm="Supprimer ce semestre ?" class="p-1 text-gray-400 hover:text-red-600" title="Supprimer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
            
            <div class="mt-3 text-sm text-gray-600">
                <div class="flex items-center space-x-4">
                    <span>{{ $semester->ues->count() }} UE(s)</span>
                    <span>{{ number_format($semester->ues->sum('credits_ects'), 1) }} ECTS</span>
                </div>
                @if($semester->start_date || $semester->end_date)
                <div class="mt-1 text-xs text-gray-400">
                    @if($semester->start_date){{ $semester->start_date->format('d/m/Y') }}@endif
                    @if($semester->start_date && $semester->end_date) - @endif
                    @if($semester->end_date){{ $semester->end_date->format('d/m/Y') }}@endif
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-gray-50 rounded-lg p-6 text-center">
        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-gray-500">Aucun semestre défini</p>
        <p class="text-sm text-gray-400 mt-1">Cliquez sur "Ajouter un semestre" pour commencer</p>
    </div>
    @endif

    @if($showModal)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ $editingSemesterId ? 'Modifier le semestre' : 'Nouveau semestre' }}
                </h3>
            </div>
            
            <form wire:submit.prevent="save">
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                        <input type="text" wire:model="name" id="name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ex: Semestre 1, Trimestre 1...">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="semester_number" class="block text-sm font-medium text-gray-700 mb-1">Numéro de période *</label>
                        <input type="number" wire:model="semester_number" id="semester_number" min="1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('semester_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                            <input type="date" wire:model="start_date" id="start_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                            <input type="date" wire:model="end_date" id="end_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('end_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        {{ $editingSemesterId ? 'Enregistrer' : 'Créer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
