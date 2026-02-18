<div class="p-6">
    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-center mb-2">
            <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium text-red-800">Veuillez corriger les erreurs suivantes :</span>
        </div>
        <ul class="list-disc list-inside text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Code -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code *</label>
                <input type="text" id="code" wire:model="code" value="{{ $code }}" placeholder="ex: INFO-L3"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Niveau -->
            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Niveau *</label>
                <select id="level" wire:model="level" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="bts" @selected($level === 'bts')>BTS</option>
                    <option value="dut" @selected($level === 'dut')>DUT</option>
                    <option value="licence" @selected($level === 'licence')>Licence</option>
                    <option value="master" @selected($level === 'master')>Master</option>
                    <option value="doctorat" @selected($level === 'doctorat')>Doctorat</option>
                </select>
                @error('level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Nom -->
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom du programme *</label>
                <input type="text" id="name" wire:model="name" value="{{ $name }}" placeholder="ex: Licence Informatique"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" wire:model="description" rows="3"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $description }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Durée -->
            <div>
                <label for="duration_years" class="block text-sm font-medium text-gray-700 mb-1">Durée (années) *</label>
                <input type="number" id="duration_years" wire:model="duration_years" value="{{ $duration_years }}" min="1" max="8"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('duration_years') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Crédits -->
            <div>
                <label for="total_credits" class="block text-sm font-medium text-gray-700 mb-1">Total crédits *</label>
                <input type="number" id="total_credits" wire:model="total_credits" value="{{ $total_credits }}" min="1"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('total_credits') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Statut -->
            <div>
                <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                <div class="flex items-center mt-2">
                    <input type="checkbox" id="is_active" wire:model="is_active" 
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        @checked($is_active)>
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Programme actif</label>
                </div>
                @error('is_active') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-6 border-t">
            <a href="{{ route('programs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                {{ $editMode ? 'Mettre à jour' : 'Créer' }}
            </button>
        </div>
    </form>
</div>
