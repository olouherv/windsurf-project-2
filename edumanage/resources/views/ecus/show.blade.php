<x-layouts.admin>
    <x-slot name="header">Détails de l'ECU</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ $ecu->code }}</p>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $ecu->name }}</h1>
                    <p class="text-gray-500 mt-1">
                        {{ $ecu->ue->semester->programYear->program->name }} > {{ $ecu->ue->name }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('ecus.edit', $ecu) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                    <form action="{{ route('ecus.destroy', $ecu) }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cet ECU ?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Crédits ECTS</dt>
                        <dd class="text-gray-900 font-medium">{{ $ecu->credits_ects }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Coefficient</dt>
                        <dd class="text-gray-900 font-medium">{{ $ecu->coefficient }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Total heures</dt>
                        <dd class="text-gray-900 font-medium">{{ $ecu->hours_cm + $ecu->hours_td + $ecu->hours_tp }}h</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Répartition horaire</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Cours Magistraux (CM)</span>
                        <span class="font-medium text-gray-900">{{ $ecu->hours_cm }}h</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $ecu->hours_cm + $ecu->hours_td + $ecu->hours_tp > 0 ? ($ecu->hours_cm / ($ecu->hours_cm + $ecu->hours_td + $ecu->hours_tp)) * 100 : 0 }}%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Travaux Dirigés (TD)</span>
                        <span class="font-medium text-gray-900">{{ $ecu->hours_td }}h</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $ecu->hours_cm + $ecu->hours_td + $ecu->hours_tp > 0 ? ($ecu->hours_td / ($ecu->hours_cm + $ecu->hours_td + $ecu->hours_tp)) * 100 : 0 }}%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Travaux Pratiques (TP)</span>
                        <span class="font-medium text-gray-900">{{ $ecu->hours_tp }}h</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $ecu->hours_cm + $ecu->hours_td + $ecu->hours_tp > 0 ? ($ecu->hours_tp / ($ecu->hours_cm + $ecu->hours_td + $ecu->hours_tp)) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        @if($ecu->description)
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Description</h2>
            <p class="text-gray-600">{{ $ecu->description }}</p>
        </div>
        @endif

        @if($ecu->objectives)
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Objectifs pédagogiques</h2>
            <p class="text-gray-600">{{ $ecu->objectives }}</p>
        </div>
        @endif

        @if($ecu->teachers->count() > 0)
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Enseignants</h2>
            <div class="space-y-2">
                @foreach($ecu->teachers as $teacher)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <span class="text-indigo-600 font-medium">{{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $teacher->first_name }} {{ $teacher->last_name }}</p>
                            <p class="text-sm text-gray-500">{{ $teacher->pivot->is_responsible ? 'Responsable' : 'Intervenant' }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('ues.show', $ecu->ue) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à l'UE
            </a>
        </div>
    </div>
</x-layouts.admin>
