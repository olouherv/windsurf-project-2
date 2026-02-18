<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <a href="{{ route('programs.show', $program) }}" class="text-gray-500 hover:text-gray-700">{{ $program->name }}</a>
            <span class="text-gray-400">/</span>
            <a href="{{ route('programs.years.index', $program) }}" class="text-gray-500 hover:text-gray-700">Années</a>
            <span class="text-gray-400">/</span>
            <span>Nouvelle année</span>
        </div>
    </x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Ajouter une année de formation</h3>
                <p class="text-sm text-gray-500">Programme: {{ $program->name }}</p>
            </div>
            <livewire:programs.program-year-form :programId="$program->id" />
        </div>
    </div>
</x-layouts.admin>
