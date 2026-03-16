<div>
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sélection de la séance</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Filière</label>
                <select wire:model.live="programId" class="w-full border-gray-300 rounded-lg text-sm">
                    <option value="">-- Sélectionner --</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Niveau</label>
                <select wire:model.live="programYearId" class="w-full border-gray-300 rounded-lg text-sm" {{ !$programId ? 'disabled' : '' }}>
                    <option value="">-- Sélectionner --</option>
                    @foreach($programYears as $py)
                        <option value="{{ $py->id }}">{{ $py->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ECU / Matière</label>
                <select wire:model.live="ecuId" class="w-full border-gray-300 rounded-lg text-sm" {{ !$programYearId ? 'disabled' : '' }}>
                    <option value="">-- Sélectionner --</option>
                    @foreach($ecus as $ecu)
                        <option value="{{ $ecu->id }}">{{ $ecu->code }} - {{ $ecu->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Séance</label>
                <select wire:model.live="scheduleId" class="w-full border-gray-300 rounded-lg text-sm" {{ !$ecuId ? 'disabled' : '' }}>
                    <option value="">-- Sélectionner --</option>
                    @foreach($schedules as $schedule)
                        <option value="{{ $schedule->id }}">
                            {{ $schedule->day_name }} {{ $schedule->start_time }}-{{ $schedule->end_time }}
                            ({{ strtoupper($schedule->type) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de la séance</label>
                <input type="date" wire:model.live="sessionDate" class="w-full border-gray-300 rounded-lg text-sm" {{ !$scheduleId ? 'disabled' : '' }}>
            </div>
        </div>
    </div>

    @if($hoursSummary)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Masse horaire : {{ $selectedEcu->code }} - {{ $selectedEcu->name }}
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-sm text-blue-600 font-medium">CM</div>
                    <div class="text-xl font-bold text-blue-700">{{ $hoursSummary['cm']['completed'] }}h / {{ $hoursSummary['cm']['planned'] }}h</div>
                    <div class="text-xs text-blue-500">Reste {{ $hoursSummary['cm']['remaining'] }}h</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-sm text-green-600 font-medium">TD</div>
                    <div class="text-xl font-bold text-green-700">{{ $hoursSummary['td']['completed'] }}h / {{ $hoursSummary['td']['planned'] }}h</div>
                    <div class="text-xs text-green-500">Reste {{ $hoursSummary['td']['remaining'] }}h</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="text-sm text-purple-600 font-medium">TP</div>
                    <div class="text-xl font-bold text-purple-700">{{ $hoursSummary['tp']['completed'] }}h / {{ $hoursSummary['tp']['planned'] }}h</div>
                    <div class="text-xs text-purple-500">Reste {{ $hoursSummary['tp']['remaining'] }}h</div>
                </div>
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-sm text-indigo-600 font-medium">Total</div>
                    <div class="text-xl font-bold text-indigo-700">{{ $hoursSummary['total']['completed'] }}h / {{ $hoursSummary['total']['planned'] }}h</div>
                    <div class="text-xs text-indigo-500">Reste {{ $hoursSummary['total']['remaining'] }}h</div>
                    @php
                        $progress = $hoursSummary['total']['planned'] > 0 
                            ? round(($hoursSummary['total']['completed'] / $hoursSummary['total']['planned']) * 100) 
                            : 0;
                    @endphp
                    <div class="mt-2 bg-indigo-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(count($attendances) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-lg font-semibold text-gray-900">{{ count($attendances) }} étudiant(s)</span>
                    <div class="flex items-center space-x-2 text-sm">
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded">{{ $statistics['present'] }} présents</span>
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded">{{ $statistics['absent'] }} absents</span>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded">{{ $statistics['late'] }} retards</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">{{ $statistics['excused'] }} excusés</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button wire:click="$set('showMarkAllModal', true)" class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Marquer tous
                    </button>
                    <button wire:click="saveAttendances" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enregistrer
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N°</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matricule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom complet</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Détails</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attendances as $index => $attendance)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $attendance['student_matricule'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $attendance['student_name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <button wire:click="updateStatus({{ $attendance['student_id'] }}, 'present')"
                                            class="px-2 py-1 text-xs rounded {{ $attendance['status'] === 'present' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                            P
                                        </button>
                                        <button wire:click="updateStatus({{ $attendance['student_id'] }}, 'absent')"
                                            class="px-2 py-1 text-xs rounded {{ $attendance['status'] === 'absent' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                            A
                                        </button>
                                        <button wire:click="updateStatus({{ $attendance['student_id'] }}, 'late')"
                                            class="px-2 py-1 text-xs rounded {{ $attendance['status'] === 'late' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                                            R
                                        </button>
                                        <button wire:click="updateStatus({{ $attendance['student_id'] }}, 'excused')"
                                            class="px-2 py-1 text-xs rounded {{ $attendance['status'] === 'excused' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                                            E
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($attendance['status'] === 'late')
                                        <input type="number" wire:model.lazy="attendances.{{ $attendance['student_id'] }}.late_minutes" 
                                            placeholder="Minutes" min="0" max="120"
                                            class="w-20 border-gray-300 rounded text-xs">
                                        <span class="text-xs text-gray-500">min</span>
                                    @elseif($attendance['status'] === 'excused')
                                        <input type="text" wire:model.lazy="attendances.{{ $attendance['student_id'] }}.excuse_reason" 
                                            placeholder="Motif..." 
                                            class="w-32 border-gray-300 rounded text-xs">
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($scheduleId && $sessionDate && $programYearId)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-yellow-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-gray-700 font-medium mb-2">Aucun étudiant trouvé</p>
            <p class="text-gray-500 text-sm">Aucun étudiant n'est inscrit pour cette année académique et ce niveau.<br>Vérifiez que les inscriptions ont été effectuées.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <p class="text-gray-500">Sélectionnez une année académique, une filière, un niveau, une matière et une séance pour gérer les présences.</p>
        </div>
    @endif

    @if($showMarkAllModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Marquer tous les étudiants</h3>
                <div class="space-y-3">
                    <button wire:click="markAllAs('present')" class="w-full px-4 py-3 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-left">
                        <span class="font-medium">Tous présents</span>
                    </button>
                    <button wire:click="markAllAs('absent')" class="w-full px-4 py-3 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-left">
                        <span class="font-medium">Tous absents</span>
                    </button>
                    <button wire:click="markAllAs('late')" class="w-full px-4 py-3 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition text-left">
                        <span class="font-medium">Tous en retard</span>
                    </button>
                </div>
                <button wire:click="$set('showMarkAllModal', false)" class="mt-4 w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Annuler
                </button>
            </div>
        </div>
    @endif
</div>
