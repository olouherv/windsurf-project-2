<x-layouts.admin>
    <x-slot name="header">Détails de l'enseignant</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center">
                        @if($teacher->photo)
                            <img src="{{ Storage::url($teacher->photo) }}" alt="{{ $teacher->full_name }}" class="w-24 h-24 rounded-full object-cover">
                        @else
                            <span class="text-3xl font-bold text-indigo-600">{{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $teacher->first_name }} {{ $teacher->last_name }}</h1>
                        <p class="text-gray-500">{{ $teacher->employee_id }}</p>
                        <div class="flex items-center space-x-2 mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $teacher->type === 'permanent' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $teacher->type === 'temporary' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $teacher->type === 'visiting' ? 'bg-purple-100 text-purple-800' : '' }}">
                                {{ $teacher->type === 'permanent' ? 'Permanent' : ($teacher->type === 'temporary' ? 'Temporaire' : 'Vacataire') }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $teacher->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $teacher->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $teacher->status === 'on_leave' ? 'bg-orange-100 text-orange-800' : '' }}">
                                {{ $teacher->status === 'active' ? 'Actif' : ($teacher->status === 'on_leave' ? 'En congé' : 'Inactif') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('teachers.edit', $teacher) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                    <a href="{{ route('teachers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations personnelles</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Sexe</dt>
                        <dd class="text-gray-900">{{ $teacher->gender === 'M' ? 'Masculin' : ($teacher->gender === 'F' ? 'Féminin' : '-') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-gray-900">{{ $teacher->email }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Téléphone</dt>
                        <dd class="text-gray-900">{{ $teacher->phone ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Grade</dt>
                        <dd class="text-gray-900">{{ $teacher->grade ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Titre</dt>
                        <dd class="text-gray-900">{{ $teacher->title ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Spécialisation</dt>
                        <dd class="text-gray-900">{{ $teacher->specialization ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Date d'embauche</dt>
                        <dd class="text-gray-900">{{ $teacher->hire_date ? $teacher->hire_date->format('d/m/Y') : '-' }}</dd>
                    </div>
                    @if($teacher->rib)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">RIB</dt>
                        <dd class="text-gray-900 font-mono text-sm">{{ $teacher->rib }}</dd>
                    </div>
                    @endif
                    @if($teacher->ifu)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">IFU</dt>
                        <dd class="text-gray-900 font-mono text-sm">{{ $teacher->ifu }}</dd>
                    </div>
                    @endif
                </dl>

                @if($teacher->cv_file || $teacher->rib_file || $teacher->ifu_file)
                <div class="mt-6 pt-4 border-t">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Documents</h3>
                    <div class="flex flex-wrap gap-2">
                        @if($teacher->cv_file)
                        <a href="{{ Storage::url($teacher->cv_file) }}" target="_blank" 
                            class="inline-flex items-center px-3 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            CV
                        </a>
                        @endif
                        @if($teacher->rib_file)
                        <a href="{{ Storage::url($teacher->rib_file) }}" target="_blank" 
                            class="inline-flex items-center px-3 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            RIB
                        </a>
                        @endif
                        @if($teacher->ifu_file)
                        <a href="{{ Storage::url($teacher->ifu_file) }}" target="_blank" 
                            class="inline-flex items-center px-3 py-2 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            IFU
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <livewire:teachers.ecu-assignment :teacher="$teacher" />
            </div>
        </div>

        @if($teacher->type === 'vacataire')
        <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Contrats vacataires</h2>
                <a href="{{ route('vacataire-contracts.create', $teacher) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nouveau contrat
                </a>
            </div>
            @if($teacher->vacataireContracts && $teacher->vacataireContracts->count() > 0)
            <div class="space-y-3">
                @foreach($teacher->vacataireContracts->sortByDesc('created_at') as $contract)
                <a href="{{ route('vacataire-contracts.show', $contract) }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-mono font-medium text-gray-900">{{ $contract->contract_number }}</span>
                            <span class="ml-2 text-sm text-gray-500">{{ $contract->academicYear->name }}</span>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full
                            @switch($contract->status)
                                @case('active') bg-green-100 text-green-800 @break
                                @case('completed') bg-blue-100 text-blue-800 @break
                                @case('cancelled') bg-red-100 text-red-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch
                        ">
                            @switch($contract->status)
                                @case('draft') Brouillon @break
                                @case('active') Actif @break
                                @case('completed') Terminé @break
                                @case('cancelled') Annulé @break
                            @endswitch
                        </span>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        {{ $contract->hours_completed }} / {{ $contract->total_hours_planned }} heures • {{ number_format($contract->total_amount, 2) }} €
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">Aucun contrat vacataire</p>
            @endif
        </div>
        @endif
    </div>
</x-layouts.admin>
