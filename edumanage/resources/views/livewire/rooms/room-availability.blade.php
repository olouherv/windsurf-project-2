<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Année académique</label>
            <select wire:model.live="academic_year_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}">{{ $year->name }} @if($year->is_current)★@endif</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
            <input type="date" wire:model="date_of_check" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Début</label>
                <input type="time" wire:model="start_time" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fin</label>
                <input type="time" wire:model="end_time" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="button" wire:click="checkAvailability" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
            Vérifier disponibilité
        </button>
    </div>

    @if($availabilityResult !== null)
        <div class="p-4 rounded-lg border text-sm {{ $availabilityResult ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' }}">
            {{ $availabilityResult ? 'Salle disponible sur ce créneau.' : 'Salle occupée sur ce créneau.' }}
        </div>
    @endif

    <div>
        <h4 class="text-md font-semibold text-gray-900 mb-3">Séances planifiées</h4>

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
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    @if(($s->category ?? 'course') === 'activity')
                                        {{ $s->title ?? 'Activité' }}
                                    @else
                                        {{ $s->ecu?->name ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 uppercase">{{ $s->type }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-sm text-gray-500">Aucune séance pour cette année académique.</div>
        @endif
    </div>
</div>
