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
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom de l'année *</label>
                <input type="text" id="name" wire:model="name" placeholder="ex: Licence 1"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="year_number" class="block text-sm font-medium text-gray-700 mb-1">Numéro d'année *</label>
                <input type="number" id="year_number" wire:model="year_number" min="1" max="8"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('year_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" wire:model="description" rows="3"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-medium text-gray-900 mb-4">Frais de formation</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="tuition_fees" class="block text-sm font-medium text-gray-700 mb-1">Frais de scolarité (€)</label>
                    <input type="number" id="tuition_fees" wire:model="tuition_fees" step="0.01" min="0"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('tuition_fees') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="registration_fees" class="block text-sm font-medium text-gray-700 mb-1">Frais d'inscription (€)</label>
                    <input type="number" id="registration_fees" wire:model="registration_fees" step="0.01" min="0"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('registration_fees') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="default_installments" class="block text-sm font-medium text-gray-700 mb-1">Tranches par défaut</label>
                    <select id="default_installments" wire:model="default_installments"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">{{ $i }} {{ $i > 1 ? 'tranches' : 'tranche' }}</option>
                        @endfor
                    </select>
                    @error('default_installments') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <p class="mt-2 text-sm text-gray-500">Total: <span class="font-semibold">{{ number_format($tuition_fees + $registration_fees, 2) }} €</span></p>
        </div>

        <div class="flex justify-end space-x-3 pt-6 border-t">
            <a href="{{ route('programs.years.index', $programId) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                {{ $editMode ? 'Mettre à jour' : 'Créer' }}
            </button>
        </div>
    </form>
</div>
