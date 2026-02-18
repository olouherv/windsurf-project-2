<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <a href="{{ route('programs.show', $program) }}" class="text-gray-500 hover:text-gray-700">{{ $program->name }}</a>
            <span class="text-gray-400">/</span>
            <a href="{{ route('programs.years.index', $program) }}" class="text-gray-500 hover:text-gray-700">Années</a>
            <span class="text-gray-400">/</span>
            <span>{{ $year->name }}</span>
        </div>
    </x-slot>

    <div class="w-full">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $year->name }}</h2>
                <p class="text-gray-500">Année {{ $year->year_number }} - {{ $program->name }}</p>
            </div>
            <a href="{{ route('programs.years.edit', [$program, $year]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            @livewire('enrollments.student-enrollment-manager', ['programYear' => $year])
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            @livewire('semesters.semester-manager', ['programYear' => $year])
        </div>

        @foreach($year->semesters->sortBy('semester_number') as $semester)
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $semester->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $semester->ues->count() }} UEs - {{ number_format($semester->ues->sum('credits_ects'), 1) }} crédits ECTS</p>
                </div>
                <a href="{{ route('ues.create', $semester) }}" class="inline-flex items-center px-3 py-2 text-sm bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter UE
                </a>
            </div>
            
            @if($semester->ues->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($semester->ues as $ue)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-mono text-gray-500">{{ $ue->code }}</span>
                                @if($ue->is_optional)
                                <span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded">Optionnelle</span>
                                @endif
                            </div>
                            <h4 class="font-medium text-gray-900">{{ $ue->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $ue->credits_ects }} ECTS - Coef. {{ $ue->coefficient }}</p>
                            
                            @if($ue->ecus->count() > 0)
                            <div class="mt-2 pl-4 border-l-2 border-gray-200">
                                @foreach($ue->ecus as $ecu)
                                <div class="py-1 flex justify-between items-center">
                                    <div>
                                        <span class="text-sm text-gray-600">{{ $ecu->name }}</span>
                                        <span class="text-xs text-gray-400 ml-2">({{ $ecu->code }})</span>
                                    </div>
                                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                                        <span>{{ $ecu->hours_cm + $ecu->hours_td + $ecu->hours_tp }}h</span>
                                        <a href="{{ route('ecus.edit', $ecu) }}" class="text-indigo-600 hover:text-indigo-800">Modifier</a>
                                        <form action="{{ route('ecus.destroy', $ecu) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cet ECU ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('ues.show', $ue) }}" class="text-gray-400 hover:text-gray-600" title="Voir">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('ues.edit', $ue) }}" class="text-gray-400 hover:text-gray-600" title="Modifier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('ues.destroy', $ue) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette UE et ses ECUs ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600" title="Supprimer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-8 text-center text-gray-500">
                Aucune UE dans ce semestre
            </div>
            @endif
        </div>
        @endforeach

        <div class="mt-6">
            <a href="{{ route('programs.years.index', $program) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour aux années
            </a>
        </div>
    </div>
</x-layouts.admin>
