<x-layouts.admin>
    <x-slot name="header">Nouveau contrat</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Créer un contrat étudiant</h3>
                <p class="text-sm text-gray-500">Remplissez les informations du contrat</p>
            </div>
            <livewire:contracts.contract-form :student="$student ?? null" />
        </div>
    </div>
</x-layouts.admin>
