<div>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Année académique</label>
                <select wire:model.live="academic_year_id" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name }} @if($year->is_current)★@endif</option>
                    @endforeach
                </select>
                @error('academic_year_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Recherche</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="ECU ou enseignant..."
                    class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>

        <button type="button" wire:click="openCreate" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle séance
        </button>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($schedules->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date / Période</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jour</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horaire</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cours / Activité</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enseignant</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salle</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Groupe</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($schedules as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @if(($s->category ?? 'course') === 'activity')
                                    {{ $s->scheduled_date?->format('d/m/Y') ?? '-' }}
                                @else
                                    {{ $s->start_date?->format('d/m/Y') ?? '-' }}
                                    @if($s->end_date)
                                        → {{ $s->end_date->format('d/m/Y') }}
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $s->day_name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ substr($s->start_time, 0, 5) }} - {{ substr($s->end_time, 0, 5) }}</td>
                            <td class="px-4 py-3">
                                @if(($s->category ?? 'course') === 'activity')
                                    <div class="text-sm font-medium text-gray-900">{{ $s->title ?? 'Activité' }}</div>
                                    <div class="text-xs text-gray-500">Activité</div>
                                @else
                                    <div class="text-sm font-medium text-gray-900">{{ $s->ecu?->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $s->ecu?->code ?? '' }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 uppercase">{{ $s->type }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $s->teacher?->full_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $s->room?->code ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $s->studentGroup?->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button type="button" wire:click="openEdit({{ $s->id }})" class="text-indigo-600 hover:text-indigo-800">
                                        Modifier
                                    </button>
                                    <button type="button" wire:click="delete({{ $s->id }})" wire:confirm="Supprimer cette séance ?" class="text-red-600 hover:text-red-800">
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-sm text-gray-500">
            {{ $schedules->count() }} séance(s)
        </div>
    @else
        <div class="text-center py-10 text-gray-500">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p>Aucune séance planifiée</p>
            <p class="text-sm mt-1">Cliquez sur "Nouvelle séance" pour commencer</p>
        </div>
    @endif

    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $editMode ? 'Modifier la séance' : 'Nouvelle séance' }}</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie *</label>
                                <select wire:model.live="category" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="course">Cours</option>
                                    <option value="activity">Activité (réunion, évènement...)</option>
                                </select>
                                @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    ECU @if($category === 'course')* @endif
                                </label>
                                <select wire:model="ecu_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @if($category === 'activity') disabled @endif>
                                    <option value="">Sélectionner</option>
                                    @foreach($ecus as $ecu)
                                        <option value="{{ $ecu->id }}">{{ $ecu->code }} - {{ $ecu->name }}</option>
                                    @endforeach
                                </select>
                                @error('ecu_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            @if($category === 'activity')
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Titre *</label>
                                <input type="text" wire:model="title" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                                <input type="date" wire:model="scheduled_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('scheduled_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                <select wire:model="type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="cm">CM</option>
                                    <option value="td">TD</option>
                                    <option value="tp">TP</option>
                                </select>
                                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700">Créneaux *</label>

                                    @if(!$editMode)
                                    <button type="button" wire:click="addTimeSlot" class="text-sm text-indigo-600 hover:text-indigo-800">
                                        + Ajouter un créneau
                                    </button>
                                    @endif
                                </div>

                                @error('timeSlots')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <div class="space-y-3">
                                    @foreach($timeSlots as $index => $slot)
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 p-3 border border-gray-200 rounded-lg">
                                        <div class="md:col-span-4">
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Jour</label>
                                            <select wire:model="timeSlots.{{ $index }}.day_of_week" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="1">Lundi</option>
                                                <option value="2">Mardi</option>
                                                <option value="3">Mercredi</option>
                                                <option value="4">Jeudi</option>
                                                <option value="5">Vendredi</option>
                                                <option value="6">Samedi</option>
                                                <option value="0">Dimanche</option>
                                            </select>
                                            @error('timeSlots.' . $index . '.day_of_week') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Début</label>
                                            <input type="time" wire:model="timeSlots.{{ $index }}.start_time" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @error('timeSlots.' . $index . '.start_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Fin</label>
                                            <input type="time" wire:model="timeSlots.{{ $index }}.end_time" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @error('timeSlots.' . $index . '.end_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="md:col-span-2 flex items-end justify-end">
                                            @if(!$editMode)
                                            <button type="button" wire:click="removeTimeSlot({{ $index }})" class="text-red-600 hover:text-red-800 text-sm">
                                                Supprimer
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Enseignant</label>
                                <select wire:model="teacher_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Aucun</option>
                                    @foreach($teachers as $t)
                                        <option value="{{ $t->id }}">{{ $t->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('teacher_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Salle</label>
                                <select wire:model="room_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Aucune</option>
                                    @foreach($rooms as $r)
                                        <option value="{{ $r->id }}">{{ $r->code }} - {{ $r->name }}</option>
                                    @endforeach
                                </select>
                                @error('room_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Groupe</label>
                                <select wire:model="student_group_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Aucun</option>
                                    @foreach($studentGroups as $g)
                                        <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->programYear->program->name }} - {{ $g->programYear->name }})</option>
                                    @endforeach
                                </select>
                                @error('student_group_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <input type="date" wire:model="start_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @if($category === 'activity') disabled @endif>
                                        @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <input type="date" wire:model="end_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @if($category === 'activity') disabled @endif>
                                        @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model.live="is_recurring" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" @if($category === 'activity') disabled @endif>
                                    <span class="ml-2 text-sm text-gray-700">Séance récurrente</span>
                                </label>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Équipements</label>
                                <select wire:model="equipment_ids" multiple class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($equipments as $eq)
                                        <option value="{{ $eq->id }}">{{ $eq->name }}@if($eq->code) ({{ $eq->code }})@endif</option>
                                    @endforeach
                                </select>
                                @error('equipment_ids') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea wire:model="notes" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" wire:click="save" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-white font-medium hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            {{ $editMode ? 'Enregistrer' : 'Créer' }}
                        </button>
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 font-medium hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
