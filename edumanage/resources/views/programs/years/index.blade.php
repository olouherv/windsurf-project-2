<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <a href="{{ route('programs.show', $program) }}" class="text-gray-500 hover:text-gray-700">{{ $program->name }}</a>
            <span class="text-gray-400">/</span>
            <span>Années de formation</span>
        </div>
    </x-slot>

    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Années de formation - {{ $program->name }}</h2>
            <a href="{{ route('programs.years.create', $program) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajouter une année
            </a>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
            {{ session('success') }}
        </div>
        @endif

        <div class="space-y-4">
            @forelse($program->programYears->sortBy('year_number') as $year)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold">
                                {{ $year->year_number }}
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $year->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $year->semesters->count() }} semestres</p>
                            </div>
                        </div>
                        @if($year->description)
                        <p class="mt-2 text-gray-600 text-sm">{{ $year->description }}</p>
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('programs.years.show', [$program, $year]) }}" class="inline-flex items-center px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Voir
                        </a>
                        <a href="{{ route('programs.years.edit', [$program, $year]) }}" class="inline-flex items-center px-3 py-2 text-sm bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Modifier
                        </a>
                    </div>
                </div>

                @if($year->semesters->count() > 0)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($year->semesters->sortBy('semester_number') as $semester)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">{{ $semester->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $semester->ues->count() }} UEs - {{ $semester->ues->sum('credits_ects') }} crédits</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune année de formation</h3>
                <p class="mt-1 text-sm text-gray-500">Commencez par créer une année de formation pour ce programme.</p>
                <div class="mt-6">
                    <a href="{{ route('programs.years.create', $program) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter une année
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        <div class="mt-6">
            <a href="{{ route('programs.show', $program) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour au programme
            </a>
        </div>
    </div>
</x-layouts.admin>
