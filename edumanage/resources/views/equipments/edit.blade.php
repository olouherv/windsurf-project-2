<x-layouts.admin>
    <x-slot name="header">Modifier l’équipement</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Modifier un équipement</h3>
                <p class="text-sm text-gray-500">Mettre à jour les informations</p>
            </div>
            <livewire:equipments.equipment-form :equipmentId="$equipment->id" />
        </div>
    </div>
</x-layouts.admin>
