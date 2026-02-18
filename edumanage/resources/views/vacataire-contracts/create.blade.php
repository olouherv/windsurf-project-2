<x-layouts.admin>
    <x-slot name="title">Nouveau contrat vacataire</x-slot>

    <div class="mb-6">
        <a href="{{ route('vacataire-contracts.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-900">Nouveau contrat vacataire</h1>
        </div>
        <livewire:vacataires.contract-form :teacher="$teacher ?? null" />
    </div>
</x-layouts.admin>
