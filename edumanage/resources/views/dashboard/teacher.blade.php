<x-layouts.admin :title="__('Espace Enseignant')">
    <x-slot name="header">{{ __('Mon espace enseignant') }}</x-slot>

    @if($teacher)
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-sm p-6 mb-6 text-white">
        <h2 class="text-2xl font-bold">{{ __('Bienvenue') }}, {{ $teacher->full_name }}</h2>
        <p class="text-green-100 mt-1">{{ $teacher->specialization ?? __('Enseignant') }} - {{ __($teacher->type) }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Mes ECUs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Mes cours cette année') }}</h3>
            </div>
            <div class="p-6">
                @forelse($ecus as $ecu)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div>
                        <p class="font-medium text-gray-900">{{ $ecu->name }}</p>
                        <p class="text-sm text-gray-500">{{ $ecu->code }} - {{ $ecu->ue->name ?? '' }}</p>
                    </div>
                    <div class="text-right text-sm text-gray-500">
                        <p>{{ $ecu->total_hours }}h</p>
                        <p class="text-xs">CM: {{ $ecu->hours_cm }}h | TD: {{ $ecu->hours_td }}h | TP: {{ $ecu->hours_tp }}h</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">{{ __('Aucun cours assigné.') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Emploi du temps -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Mon emploi du temps') }}</h3>
            </div>
            <div class="p-6">
                @forelse($schedules as $schedule)
                <div class="flex items-center py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="w-16 text-center">
                        <p class="text-sm font-semibold text-indigo-600">{{ $schedule->day_name }}</p>
                    </div>
                    <div class="flex-1 ml-4">
                        <p class="font-medium text-gray-900">{{ $schedule->ecu->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                            @if($schedule->room) | {{ $schedule->room->name }} @endif
                        </p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 uppercase">{{ $schedule->type }}</span>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">{{ __('Aucun cours planifié.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
        <p class="text-yellow-800">{{ __('Votre profil enseignant n\'est pas encore configuré.') }}</p>
    </div>
    @endif
</x-layouts.admin>
