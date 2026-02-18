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
            <!-- Matricule -->
            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Matricule *</label>
                <input type="text" id="employee_id" wire:model="employee_id" value="{{ $employee_id }}" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('employee_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" id="email" wire:model="email" value="{{ $email }}" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Prénom -->
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                <input type="text" id="first_name" wire:model="first_name" value="{{ $first_name }}" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Nom -->
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                <input type="text" id="last_name" wire:model="last_name" value="{{ $last_name }}" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Sexe -->
            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Sexe</label>
                <select id="gender" wire:model="gender" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Sélectionner --</option>
                    <option value="M" @selected($gender === 'M')>Masculin</option>
                    <option value="F" @selected($gender === 'F')>Féminin</option>
                </select>
                @error('gender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Téléphone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input type="text" id="phone" wire:model="phone" value="{{ $phone }}" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Spécialisation -->
            <div>
                <label for="specialization" class="block text-sm font-medium text-gray-700 mb-1">Spécialisation</label>
                <input type="text" id="specialization" wire:model="specialization" value="{{ $specialization }}" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('specialization') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                <select id="type" wire:model="type" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="permanent" @selected($type === 'permanent')>Permanent</option>
                    <option value="temporary" @selected($type === 'temporary')>Temporaire</option>
                    <option value="vacataire" @selected($type === 'vacataire')>Vacataire</option>
                </select>
                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Grade -->
            <div>
                <label for="grade" class="block text-sm font-medium text-gray-700 mb-1">Grade</label>
                <input type="text" id="grade" wire:model="grade" placeholder="Ex: Maître de conférences, Professeur..."
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('grade') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Titre -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                <input type="text" id="title" wire:model="title" placeholder="Ex: Dr., Pr., M., Mme..."
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Statut -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                <select id="status" wire:model="status" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="active" @selected($status === 'active')>Actif</option>
                    <option value="inactive" @selected($status === 'inactive')>Inactif</option>
                    <option value="on_leave" @selected($status === 'on_leave')>En congé</option>
                </select>
                @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Date d'embauche -->
            <div>
                <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">Date d'embauche</label>
                <input type="date" id="hire_date" wire:model="hire_date" value="{{ $hire_date }}" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('hire_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Photo -->
            <div>
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                <input type="file" id="photo" wire:model="photo" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @error('photo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- RIB -->
            <div>
                <label for="rib" class="block text-sm font-medium text-gray-700 mb-1">RIB (numéro)</label>
                <input type="text" id="rib" wire:model="rib" placeholder="Coordonnées bancaires"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('rib') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- IFU -->
            <div>
                <label for="ifu" class="block text-sm font-medium text-gray-700 mb-1">IFU (numéro)</label>
                <input type="text" id="ifu" wire:model="ifu" placeholder="Identifiant Fiscal Unique"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('ifu') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- CV -->
            <div>
                <label for="cv_file" class="block text-sm font-medium text-gray-700 mb-1">CV (PDF, DOC)</label>
                @if($existing_cv)
                <div class="mb-2 flex items-center text-sm text-green-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    CV existant
                </div>
                @endif
                <input type="file" id="cv_file" wire:model="cv_file" accept=".pdf,.doc,.docx"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @error('cv_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-500">Max 5 Mo</p>
            </div>

            <!-- RIB File -->
            <div>
                <label for="rib_file" class="block text-sm font-medium text-gray-700 mb-1">RIB (document)</label>
                @if($existing_rib_file)
                <div class="mb-2 flex items-center text-sm text-green-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Document RIB existant
                </div>
                @endif
                <input type="file" id="rib_file" wire:model="rib_file" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @error('rib_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-500">PDF ou image, max 2 Mo</p>
            </div>

            <!-- IFU File -->
            <div>
                <label for="ifu_file" class="block text-sm font-medium text-gray-700 mb-1">IFU (document)</label>
                @if($existing_ifu_file)
                <div class="mb-2 flex items-center text-sm text-green-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Document IFU existant
                </div>
                @endif
                <input type="file" id="ifu_file" wire:model="ifu_file" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @error('ifu_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-500">PDF ou image, max 2 Mo</p>
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-6 border-t">
            <a href="{{ route('teachers.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                {{ $editMode ? 'Mettre à jour' : 'Créer' }}
            </button>
        </div>
    </form>
</div>
