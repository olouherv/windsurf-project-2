<div>
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="save">
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Critères de validation des UE</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Moyenne de validation</label>
                        <div class="flex items-center">
                            <input type="number" step="0.5" min="0" max="20" wire:model="ue_validation_average" class="w-full border-gray-300 rounded-lg">
                            <span class="ml-2 text-gray-500">/20</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Note minimale pour valider une UE directement</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Autoriser la compensation</label>
                        <label class="flex items-center mt-2">
                            <input type="checkbox" wire:model="ue_allow_compensation" class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-600">Oui, autoriser</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Note minimale pour compensation</label>
                        <div class="flex items-center">
                            <input type="number" step="0.5" min="0" max="20" wire:model="ue_compensation_min" class="w-full border-gray-300 rounded-lg">
                            <span class="ml-2 text-gray-500">/20</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">En dessous, l'UE ne peut pas être compensée</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Critères de validation du semestre</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Moyenne de validation</label>
                        <div class="flex items-center">
                            <input type="number" step="0.5" min="0" max="20" wire:model="semester_validation_average" class="w-full border-gray-300 rounded-lg">
                            <span class="ml-2 text-gray-500">/20</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">% UE à valider</label>
                        <div class="flex items-center">
                            <input type="number" min="0" max="100" wire:model="semester_min_ue_validated_percent" class="w-full border-gray-300 rounded-lg">
                            <span class="ml-2 text-gray-500">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Compensation semestrielle</label>
                        <label class="flex items-center mt-2">
                            <input type="checkbox" wire:model="semester_allow_compensation" class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-600">Autoriser</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max UE non validées</label>
                        <input type="number" min="0" wire:model="semester_max_ue_failed" class="w-full border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Pour bénéficier de la compensation</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Critères de validation de l'année</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Moyenne de validation</label>
                        <div class="flex items-center">
                            <input type="number" step="0.5" min="0" max="20" wire:model="year_validation_average" class="w-full border-gray-300 rounded-lg">
                            <span class="ml-2 text-gray-500">/20</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tous les semestres requis</label>
                        <label class="flex items-center mt-2">
                            <input type="checkbox" wire:model="year_require_all_semesters" class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-600">Oui</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max crédits non validés</label>
                        <input type="number" min="0" wire:model="year_max_credits_failed" class="w-full border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Pour validation par compensation</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Passage conditionnel</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Autoriser le passage conditionnel</label>
                        <label class="flex items-center mt-2">
                            <input type="checkbox" wire:model="allow_conditional_pass" class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-600">Oui, autoriser</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">L'étudiant passe avec une dette de crédits</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max crédits en dette</label>
                        <input type="number" min="0" wire:model="conditional_max_credits_debt" class="w-full border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Nombre max de crédits pouvant être reportés</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mentions</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Passable</label>
                        <div class="flex items-center">
                            <input type="number" step="0.5" min="0" max="20" wire:model="mention_passable_min" class="w-full border-gray-300 rounded-lg">
                            <span class="ml-2 text-gray-500">/20</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assez Bien</label>
                        <div class="flex items-center">
                            <input type="number" step="0.5" min="0" max="20" wire:model="mention_assez_bien_min" class="w-full border-gray-300 rounded-lg">
                            <span class="ml-2 text-gray-500">/20</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bien</label>
                        <div class="flex items-center">
                            <input type="number" step="0.5" min="0" max="20" wire:model="mention_bien_min" class="w-full border-gray-300 rounded-lg">
                            <span class="ml-2 text-gray-500">/20</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Très Bien</label>
                        <div class="flex items-center">
                            <input type="number" step="0.5" min="0" max="20" wire:model="mention_tres_bien_min" class="w-full border-gray-300 rounded-lg">
                            <span class="ml-2 text-gray-500">/20</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                    Enregistrer les paramètres
                </button>
            </div>
        </div>
    </form>
</div>
