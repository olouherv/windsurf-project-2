<x-layouts.admin>
    <x-slot name="title">Modifier {{ $evaluation->name }}</x-slot>

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
            <h1 class="text-xl font-semibold text-gray-900">Modifier l'évaluation</h1>
        </div>
        <livewire:evaluations.evaluation-form :evaluation="$evaluation" />
    </div>
</x-layouts.admin>
