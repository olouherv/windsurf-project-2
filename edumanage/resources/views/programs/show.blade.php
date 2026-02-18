<x-layouts.admin>
    <x-slot name="header">Détails du programme</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            {{ strtoupper($program->level) }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $program->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $program->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $program->name }}</h1>
                    <p class="text-gray-500">Code: {{ $program->code }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('programs.years.index', $program) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Gérer les années
                    </a>
                    <a href="{{ route('programs.edit', $program) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                    <a href="{{ route('programs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <p class="text-3xl font-bold text-indigo-600">{{ $program->duration_years }}</p>
                <p class="text-gray-500">Années</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <p class="text-3xl font-bold text-indigo-600">{{ $program->total_credits ?? 0 }}</p>
                <p class="text-gray-500">Crédits ECTS</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <p class="text-3xl font-bold text-indigo-600">{{ $program->programYears?->count() ?? 0 }}</p>
                <p class="text-gray-500">Années de formation</p>
            </div>
        </div>

        @if($program->description)
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Description</h2>
            <p class="text-gray-600">{{ $program->description }}</p>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Années de formation</h2>
            @if($program->programYears && $program->programYears->count() > 0)
                <div class="space-y-3">
                    @foreach($program->programYears as $year)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $year->name }}</p>
                            <p class="text-sm text-gray-500">{{ $year->ues?->count() ?? 0 }} UEs - {{ $year->credits ?? 0 }} crédits</p>
                        </div>
                        <span class="text-sm text-gray-500">Année {{ $year->year_number }}</span>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Aucune année de formation définie</p>
            @endif
        </div>
    </div>
</x-layouts.admin>
