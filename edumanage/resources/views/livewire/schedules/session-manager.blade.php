<div>
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Année académique</label>
                <select wire:model.live="academicYearId" class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="">-- Sélectionner --</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ECU / Matière</label>
                <select wire:model.live="ecuId" class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="">Tous les ECU</option>
                    @foreach($ecus as $ecu)
                        <option value="{{ $ecu->id }}">{{ $ecu->code }} - {{ $ecu->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Créneau récurrent</label>
                <select wire:model.live="scheduleId" class="w-full border-gray-300 rounded-lg text-sm" {{ !$ecuId ? 'disabled' : '' }}>
                    <option value="">Toutes les séances</option>
                    @foreach($schedules as $schedule)
                        <option value="{{ $schedule->id }}">
                            {{ ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'][$schedule->day_of_week] }}
                            {{ $schedule->start_time }}-{{ $schedule->end_time }}
                            ({{ strtoupper($schedule->type) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select wire:model.live="statusFilter" class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="">Tous</option>
                    <option value="planned">Planifiées</option>
                    <option value="completed">Effectuées</option>
                    <option value="cancelled">Annulées</option>
                </select>
            </div>
        </div>
    </div>

    @if($ecuId && $hoursSummary['total'] > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Masse horaire</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-gray-700">{{ $hoursSummary['total'] }}h</div>
                    <div class="text-sm text-gray-500">Volume total</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-700">{{ $hoursSummary['planned'] }}h</div>
                    <div class="text-sm text-blue-500">Planifiées</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-700">{{ $hoursSummary['completed'] }}h</div>
                    <div class="text-sm text-green-500">Effectuées</div>
                </div>
                <div class="bg-orange-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-orange-700">{{ $hoursSummary['remaining'] }}h</div>
                    <div class="text-sm text-orange-500">Restantes</div>
                </div>
            </div>
            @php
                $progress = $hoursSummary['total'] > 0 ? round(($hoursSummary['completed'] / $hoursSummary['total']) * 100) : 0;
            @endphp
            <div class="mt-4">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Progression</span>
                    <span>{{ $progress }}%</span>
                </div>
                <div class="bg-gray-200 rounded-full h-3">
                    <div class="bg-green-500 h-3 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher..." 
                    class="border-gray-300 rounded-lg text-sm w-64">
            </div>
            <button wire:click="openAddModal" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition" {{ !$ecuId ? 'disabled' : '' }}>
                + Ajouter une séance
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ECU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enseignant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salle</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $session->session_date->format('d/m/Y') }}
                                <span class="text-gray-400 text-xs">({{ $session->session_date->translatedFormat('l') }})</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $session->start_time }} - {{ $session->end_time }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $session->ecu?->code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded {{ $session->type === 'cm' ? 'bg-blue-100 text-blue-700' : ($session->type === 'td' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700') }}">
                                    {{ strtoupper($session->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $session->teacher?->full_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $session->room?->code ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @switch($session->status)
                                    @case('planned')
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Planifiée</span>
                                        @break
                                    @case('completed')
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Effectuée</span>
                                        @break
                                    @case('cancelled')
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Annulée</span>
                                        @break
                                    @case('rescheduled')
                                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Reportée</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end space-x-2">
                                    @if($session->status === 'planned')
                                        <button wire:click="markAsCompleted({{ $session->id }})" class="text-green-600 hover:text-green-900" title="Marquer effectuée">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <button wire:click="cancelSession({{ $session->id }})" class="text-red-600 hover:text-red-900" title="Annuler">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    @endif
                                    <button wire:click="openDetails({{ $session->id }})" class="text-indigo-600 hover:text-indigo-900" title="Détails">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="deleteSession({{ $session->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer cette séance ?" class="text-gray-400 hover:text-red-600" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                Aucune séance trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>

    @if($showAddModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ajouter une séance</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" wire:model="sessionDate" class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Heure début</label>
                            <input type="time" wire:model="startTime" class="w-full border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Heure fin</label>
                            <input type="time" wire:model="endTime" class="w-full border-gray-300 rounded-lg">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select wire:model="type" class="w-full border-gray-300 rounded-lg">
                            <option value="cm">CM</option>
                            <option value="td">TD</option>
                            <option value="tp">TP</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Enseignant</label>
                        <select wire:model="teacherId" class="w-full border-gray-300 rounded-lg">
                            <option value="">-- Sélectionner --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Salle</label>
                        <select wire:model="roomId" class="w-full border-gray-300 rounded-lg">
                            <option value="">-- Sélectionner --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->code }} - {{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea wire:model="notes" rows="2" class="w-full border-gray-300 rounded-lg"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button wire:click="closeAddModal" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button wire:click="saveSession" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
