<div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Historique des présences</h3>
            <select wire:model.live="academicYearId" class="border-gray-300 rounded-lg text-sm">
                <option value="">Toutes les années</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $statistics['total'] }}</div>
                <div class="text-sm text-gray-500">Séances</div>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $statistics['present'] }}</div>
                <div class="text-sm text-green-600">Présences</div>
            </div>
            <div class="bg-red-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $statistics['absent'] }}</div>
                <div class="text-sm text-red-600">Absences</div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $statistics['late'] }}</div>
                <div class="text-sm text-yellow-600">Retards</div>
            </div>
            <div class="bg-indigo-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-indigo-600">{{ $statistics['rate'] }}%</div>
                <div class="text-sm text-indigo-600">Taux présence</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enseignant</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Détails</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $attendance->session_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $attendance->schedule?->ecu?->code ?? '-' }} - {{ $attendance->schedule?->ecu?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $attendance->schedule?->start_time ?? '-' }} - {{ $attendance->schedule?->end_time ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $attendance->schedule?->teacher?->full_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @switch($attendance->status)
                                    @case('present')
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Présent</span>
                                        @break
                                    @case('absent')
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Absent</span>
                                        @break
                                    @case('late')
                                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Retard</span>
                                        @break
                                    @case('excused')
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Excusé</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($attendance->status === 'late' && $attendance->late_minutes)
                                    {{ $attendance->late_minutes }} min
                                @elseif($attendance->status === 'excused' && $attendance->excuse_reason)
                                    {{ Str::limit($attendance->excuse_reason, 30) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Aucune présence enregistrée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
