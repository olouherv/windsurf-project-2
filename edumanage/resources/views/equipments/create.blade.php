<x-layouts.admin>
    <x-slot name="header">Nouvel équipement</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Créer un équipement</h3>
                <p class="text-sm text-gray-500">Renseignez les informations</p>
            </div>
            <livewire:equipments.equipment-form />
        </div>
    </div>
</x-layouts.admin>
