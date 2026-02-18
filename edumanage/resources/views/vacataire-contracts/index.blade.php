<x-layouts.admin>
    <x-slot name="title">Contrats Vacataires</x-slot>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Contrats Vacataires</h1>
        <a href="{{ route('vacataire-contracts.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nouveau contrat
        </a>
    </div>

    <livewire:vacataires.contract-list />
</x-layouts.admin>
