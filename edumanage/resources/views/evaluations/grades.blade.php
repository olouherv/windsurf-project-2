<x-layouts.admin>
    <x-slot name="title">Notes - {{ $evaluation->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('evaluations.show', $evaluation) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour aux détails
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Saisie des notes</h1>
                    <p class="text-sm text-gray-500">{{ $evaluation->name }} - {{ $evaluation->ecu->code }}</p>
                </div>
                <span class="text-sm text-gray-500">Note max: {{ $evaluation->max_score }}</span>
            </div>
        </div>
        <livewire:evaluations.grade-entry :evaluation="$evaluation" />
    </div>
</x-layouts.admin>
