<div class="p-6">
    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-center mb-2">
            <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium text-red-800">{{ __('Veuillez corriger les erreurs suivantes :') }}</span>
        </div>
        <ul class="list-disc list-inside text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Matricule -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Matricule') }} *</label>
                <input type="text" wire:model="student_id" value="{{ $student_id }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('student_id') border-red-500 @enderror">
                @error('student_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Prénom -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Prénom') }} *</label>
                <input type="text" wire:model="first_name" value="{{ $first_name }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('first_name') border-red-500 @enderror">
                @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Nom -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nom') }} *</label>
                <input type="text" wire:model="last_name" value="{{ $last_name }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('last_name') border-red-500 @enderror">
                @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }} *</label>
                <input type="email" wire:model="email" value="{{ $email }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Téléphone -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Téléphone') }}</label>
                <input type="text" wire:model="phone" value="{{ $phone }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Date de naissance -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Date de naissance') }}</label>
                <input type="date" wire:model="birth_date" value="{{ $birth_date }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Lieu de naissance -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Lieu de naissance') }}</label>
                <input type="text" wire:model="birth_place" value="{{ $birth_place }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Genre -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Genre') }}</label>
                <select wire:model="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="" @selected($gender === '')>{{ __('Sélectionner') }}</option>
                    <option value="male" @selected($gender === 'male')>{{ __('Masculin') }}</option>
                    <option value="female" @selected($gender === 'female')>{{ __('Féminin') }}</option>
                    <option value="other" @selected($gender === 'other')>{{ __('Autre') }}</option>
                </select>
            </div>

            <!-- Nationalité -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nationalité') }}</label>
                <input type="text" wire:model="nationality" value="{{ $nationality }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Statut -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Statut') }} *</label>
                <select wire:model="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="active" @selected($status === 'active')>{{ __('Actif') }}</option>
                    <option value="inactive" @selected($status === 'inactive')>{{ __('Inactif') }}</option>
                    <option value="graduated" @selected($status === 'graduated')>{{ __('Diplômé') }}</option>
                    <option value="suspended" @selected($status === 'suspended')>{{ __('Suspendu') }}</option>
                </select>
            </div>

            <!-- Adresse -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Adresse') }}</label>
                <textarea wire:model="address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ $address }}</textarea>
            </div>

            <!-- Photo -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Photo') }}</label>
                <input type="file" wire:model="photo" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                @error('photo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('students.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                {{ __('Annuler') }}
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $editMode ? __('Mettre à jour') : __('Créer') }}</span>
                <span wire:loading>Chargement...</span>
            </button>
        </div>
    </form>
</div>
