<div x-data="{ showFilters: false }">
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Calendrier principal -->
        <div class="flex-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <!-- En-tête du calendrier -->
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button wire:click="previousMonth" class="p-2 hover:bg-gray-100 rounded-lg">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <h2 class="text-xl font-semibold text-gray-900">
                                {{ Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}
                            </h2>
                            <button wire:click="nextMonth" class="p-2 hover:bg-gray-100 rounded-lg">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            <button wire:click="goToToday" class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Aujourd'hui
                            </button>
                        </div>
                        <div class="flex items-center space-x-2">
                            <select wire:model.live="academicYearId" class="border-gray-300 rounded-lg text-sm">
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </select>
                            <button @click="showFilters = !showFilters" class="p-2 hover:bg-gray-100 rounded-lg" title="Filtres">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                            </button>
                            <button wire:click="exportPdf" class="p-2 hover:bg-gray-100 rounded-lg" title="Exporter en PDF">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </button>
                            <button wire:click="openAddSession" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                                + Ajouter
                            </button>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <div x-show="showFilters" x-collapse class="mt-4 pt-4 border-t border-gray-100">
                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                                <select wire:model.live="filterCategory" class="w-full border-gray-300 rounded-lg text-sm">
                                    <option value="">Tous</option>
                                    <option value="course">Cours</option>
                                    <option value="activity">Activités</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">ECU</label>
                                <select wire:model.live="filterEcuId" class="w-full border-gray-300 rounded-lg text-sm">
                                    <option value="">Tous</option>
                                    @foreach($ecus as $ecu)
                                        <option value="{{ $ecu->id }}">{{ $ecu->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Enseignant</label>
                                <select wire:model.live="filterTeacherId" class="w-full border-gray-300 rounded-lg text-sm">
                                    <option value="">Tous</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Salle</label>
                                <select wire:model.live="filterRoomId" class="w-full border-gray-300 rounded-lg text-sm">
                                    <option value="">Toutes</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jours de la semaine -->
                <div class="grid grid-cols-7 border-b border-gray-100">
                    @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $day)
                        <div class="py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ $day }}</div>
                    @endforeach
                </div>

                <!-- Grille du calendrier -->
                <div class="grid grid-cols-7">
                    @foreach($calendarDays as $day)
                        @php
                            $daySessions = $sessionsForMonth[$day['date']] ?? collect();
                        @endphp
                        <div 
                            wire:click="selectDate('{{ $day['date'] }}')"
                            class="min-h-[100px] p-1 border-b border-r border-gray-100 cursor-pointer transition-colors
                                {{ !$day['isCurrentMonth'] ? 'bg-gray-50' : '' }}
                                {{ $day['isSelected'] ? 'bg-indigo-50 ring-2 ring-indigo-500 ring-inset' : 'hover:bg-gray-50' }}
                                {{ $day['isToday'] ? 'bg-yellow-50' : '' }}"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium {{ $day['isToday'] ? 'bg-indigo-600 text-white w-6 h-6 rounded-full flex items-center justify-center' : ($day['isCurrentMonth'] ? 'text-gray-900' : 'text-gray-400') }}">
                                    {{ $day['day'] }}
                                </span>
                                @if($daySessions->count() > 0)
                                    <span class="text-xs text-gray-500">{{ $daySessions->count() }}</span>
                                @endif
                            </div>
                            <div class="space-y-0.5 overflow-hidden max-h-[70px]">
                                @foreach($daySessions->take(3) as $session)
                                    <div class="text-xs px-1 py-0.5 rounded truncate
                                        {{ $session->type === 'cm' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $session->type === 'td' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $session->type === 'tp' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $session->status === 'cancelled' ? 'line-through opacity-50' : '' }}
                                        {{ $session->status === 'completed' ? 'opacity-75' : '' }}"
                                        title="{{ $session->ecu?->code }} - {{ $session->start_time }}"
                                    >
                                        {{ substr($session->start_time, 0, 5) }} {{ $session->ecu?->code }}
                                    </div>
                                @endforeach
                                @if($daySessions->count() > 3)
                                    <div class="text-xs text-gray-400 pl-1">+{{ $daySessions->count() - 3 }} autres</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Panneau latéral - Détails du jour -->
        @if($showDayDetails && $selectedDate)
            <div class="w-full lg:w-96 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ Carbon\Carbon::parse($selectedDate)->translatedFormat('l d F') }}
                            </h3>
                            <p class="text-sm text-gray-500">{{ $selectedDaySessions->count() }} séance(s)</p>
                        </div>
                        <button wire:click="openAddSession('{{ $selectedDate }}')" class="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-4 space-y-3 max-h-[calc(100vh-300px)] overflow-y-auto">
                    @forelse($selectedDaySessions as $session)
                        <div wire:key="session-{{ $session->id }}" class="p-3 rounded-lg border {{ $session->status === 'cancelled' ? 'border-red-200 bg-red-50' : ($session->status === 'completed' ? 'border-green-200 bg-green-50' : 'border-gray-200') }}">
                                                    <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        @if($session->category === 'activity')
                                            <span class="px-2 py-0.5 text-xs font-medium rounded bg-amber-100 text-amber-700">
                                                ACTIVITÉ
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-medium rounded
                                                {{ $session->type === 'cm' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $session->type === 'td' ? 'bg-green-100 text-green-700' : '' }}
                                                {{ $session->type === 'tp' ? 'bg-purple-100 text-purple-700' : '' }}">
                                                {{ strtoupper($session->type) }}
                                            </span>
                                        @endif
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }}
                                        </span>
                                    </div>
                                    @if($session->category === 'activity')
                                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $session->title }}</p>
                                    @else
                                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $session->ecu?->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $session->ecu?->code }}</p>
                                    @endif
                                    @if($session->teacher)
                                        <p class="text-xs text-gray-600 mt-1">
                                            <span class="font-medium">Enseignant:</span> {{ $session->teacher->full_name }}
                                        </p>
                                    @endif
                                    @if($session->room)
                                        <p class="text-xs text-gray-600">
                                            <span class="font-medium">Salle:</span> {{ $session->room->code }}
                                        </p>
                                    @endif
                                    @if($session->studentGroup)
                                        <p class="text-xs text-gray-600">
                                            <span class="font-medium">Groupe:</span> {{ $session->studentGroup->name }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex flex-col space-y-1">
                                    @if($session->status === 'planned')
                                        <button wire:click="markAsCompleted({{ $session->id }})" class="p-1 text-green-600 hover:bg-green-100 rounded" title="Marquer effectuée">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <button wire:click="cancelSession({{ $session->id }})" class="p-1 text-red-600 hover:bg-red-100 rounded" title="Annuler">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    @endif
                                    <button wire:click="openEditSession({{ $session->id }})" class="p-1 text-gray-600 hover:bg-gray-100 rounded" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="deleteSession({{ $session->id }})" wire:confirm="Supprimer cette séance ?" class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-100 rounded" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm">Aucune séance ce jour</p>
                            <button wire:click="openAddSession('{{ $selectedDate }}')" class="mt-2 text-indigo-600 text-sm hover:underline">
                                Planifier une séance
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    <!-- Modal ajout/modification de séance -->
    @if($showSessionModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $editMode ? 'Modifier la séance' : 'Planifier une séance' }}
                        </h3>
                        <button wire:click="closeSessionModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Catégorie -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de planification *</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" wire:model.live="category" value="course" class="border-gray-300 text-indigo-600">
                                <span class="ml-2 text-sm text-gray-700">Cours (CM/TD/TP)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" wire:model.live="category" value="activity" class="border-gray-300 text-indigo-600">
                                <span class="ml-2 text-sm text-gray-700">Activité</span>
                            </label>
                        </div>
                    </div>

                    <!-- ECU (si cours) -->
                    @if($category === 'course')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ECU / Matière *</label>
                            <select wire:model.live="ecuId" class="w-full border-gray-300 rounded-lg">
                                <option value="">-- Sélectionner --</option>
                                @foreach($ecus as $ecu)
                                    <option value="{{ $ecu->id }}">
                                        {{ $ecu->code }} - {{ $ecu->name }}
                                        ({{ $ecu->ue?->semester?->programYear?->program?->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('ecuId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @else
                        <!-- Titre (si activité) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Titre de l'activité *</label>
                            <input type="text" wire:model="title" class="w-full border-gray-300 rounded-lg" placeholder="Ex: Réunion pédagogique, Conférence, Examen...">
                            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Résumé masse horaire si ECU sélectionné -->
                    @if($ecuHoursSummary)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Masse horaire de l'ECU</h4>
                            <div class="grid grid-cols-3 gap-3 text-center text-sm">
                                @foreach(['cm' => 'CM', 'td' => 'TD', 'tp' => 'TP'] as $key => $label)
                                    <div class="bg-white rounded p-2 {{ $type === $key ? 'ring-2 ring-indigo-500' : '' }}">
                                        <div class="font-medium {{ $type === $key ? 'text-indigo-600' : 'text-gray-700' }}">{{ $label }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $ecuHoursSummary[$key]['planned'] }}/{{ $ecuHoursSummary[$key]['total'] }}h
                                        </div>
                                        @if($ecuHoursSummary[$key]['remaining'] > 0)
                                            <div class="text-xs text-green-600">Reste: {{ $ecuHoursSummary[$key]['remaining'] }}h</div>
                                        @else
                                            <div class="text-xs text-orange-600">Complet</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Date et horaires -->
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                            <input type="date" wire:model.live.debounce.300ms="sessionDate" class="w-full border-gray-300 rounded-lg">
                            @error('sessionDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Heure début *</label>
                            <input type="time" wire:model.live.debounce.300ms="startTime" class="w-full border-gray-300 rounded-lg">
                            @error('startTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Heure fin *</label>
                            <input type="time" wire:model.live.debounce.300ms="endTime" class="w-full border-gray-300 rounded-lg">
                            @error('endTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Type (seulement pour les cours) -->
                    @if($category === 'course')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de séance *</label>
                            <div class="flex space-x-4">
                                @foreach(['cm' => 'CM (Cours)', 'td' => 'TD (Travaux Dirigés)', 'tp' => 'TP (Travaux Pratiques)'] as $value => $label)
                                    <label class="flex items-center">
                                        <input type="radio" wire:model="type" value="{{ $value }}" class="border-gray-300 text-indigo-600">
                                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Enseignant et Salle -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Enseignant
                                <span class="text-xs text-green-600">({{ $availableTeachers->count() }} disponibles)</span>
                            </label>
                            <select wire:model="teacherId" class="w-full border-gray-300 rounded-lg">
                                <option value="">-- Sélectionner --</option>
                                @foreach($availableTeachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Salle
                                <span class="text-xs text-green-600">({{ $availableRooms->count() }} disponibles)</span>
                            </label>
                            <select wire:model="roomId" class="w-full border-gray-300 rounded-lg">
                                <option value="">-- Sélectionner --</option>
                                @foreach($availableRooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->code }} - {{ $room->name }} ({{ $room->capacity }} places)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Groupe (seulement pour les cours) -->
                    @if($category === 'course')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Groupe d'étudiants</label>
                            <select wire:model="studentGroupId" class="w-full border-gray-300 rounded-lg">
                                <option value="">-- Tous les étudiants --</option>
                                @foreach($studentGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }} ({{ $group->programYear?->program?->name }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Répétition (seulement en création) -->
                    @if(!$editMode)
                        <div class="border-t border-gray-100 pt-4">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model.live="isRecurring" class="rounded border-gray-300 text-indigo-600">
                                <span class="ml-2 text-sm font-medium text-gray-700">Répéter cette séance</span>
                            </label>

                            @if($isRecurring)
                                <div class="mt-4 pl-6 space-y-4 bg-gray-50 rounded-lg p-4">
                                    <!-- Jours de répétition -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Jours de répétition</label>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach([1 => 'Lun', 2 => 'Mar', 3 => 'Mer', 4 => 'Jeu', 5 => 'Ven', 6 => 'Sam', 0 => 'Dim'] as $dayNum => $dayName)
                                                <label class="flex items-center px-3 py-1 rounded-lg border cursor-pointer {{ in_array($dayNum, $repeatDays) ? 'bg-indigo-100 border-indigo-500 text-indigo-700' : 'bg-white border-gray-300 text-gray-700' }}">
                                                    <input type="checkbox" wire:model="repeatDays" value="{{ $dayNum }}" class="sr-only">
                                                    <span class="text-sm">{{ $dayName }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Condition de fin -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Répéter jusqu'à</label>
                                        <div class="space-y-2">
                                            <label class="flex items-center">
                                                <input type="radio" wire:model.live="repeatUntil" value="end_of_hours" class="border-gray-300 text-indigo-600">
                                                <span class="ml-2 text-sm text-gray-700">Fin de la masse horaire (automatique)</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" wire:model.live="repeatUntil" value="date" class="border-gray-300 text-indigo-600">
                                                <span class="ml-2 text-sm text-gray-700">Date précise</span>
                                            </label>
                                            @if($repeatUntil === 'date')
                                                <input type="date" wire:model="repeatEndDate" class="ml-6 border-gray-300 rounded-lg text-sm">
                                            @endif
                                            <label class="flex items-center">
                                                <input type="radio" wire:model.live="repeatUntil" value="count" class="border-gray-300 text-indigo-600">
                                                <span class="ml-2 text-sm text-gray-700">Nombre de séances</span>
                                            </label>
                                            @if($repeatUntil === 'count')
                                                <input type="number" wire:model="repeatCount" min="1" max="50" class="ml-6 w-20 border-gray-300 rounded-lg text-sm">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea wire:model="notes" rows="2" class="w-full border-gray-300 rounded-lg" placeholder="Notes optionnelles..."></textarea>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-100 flex justify-end space-x-3">
                    <button wire:click="closeSessionModal" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button wire:click="saveSession" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        {{ $editMode ? 'Enregistrer' : ($isRecurring ? 'Créer les séances' : 'Créer') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
