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
                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code *</label>
                <input type="text" id="code" wire:model="code" placeholder="ex: UE-INF101"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                <input type="text" id="name" wire:model="name" placeholder="ex: Fondamentaux Informatiques"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="credits_ects" class="block text-sm font-medium text-gray-700 mb-1">Crédits ECTS *</label>
                <input type="number" id="credits_ects" wire:model="credits_ects" min="0.5" max="30" step="0.5"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('credits_ects') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="coefficient" class="block text-sm font-medium text-gray-700 mb-1">Coefficient *</label>
                <input type="number" id="coefficient" wire:model="coefficient" min="0.1" max="10" step="0.1"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('coefficient') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" wire:model="description" rows="3"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="flex items-center mt-2">
                    <input type="checkbox" id="is_optional" wire:model="is_optional" 
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_optional" class="ml-2 text-sm text-gray-700">UE optionnelle</label>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-6 border-t">
            <button type="button" onclick="history.back()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Annuler
            </button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                {{ $editMode ? 'Mettre à jour' : 'Créer' }}
            </button>
        </div>
    </form>
</div>
