<x-layouts.admin>
    <x-slot name="header">Modifier la salle</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Modifier une salle</h3>
                <p class="text-sm text-gray-500">Mettre à jour les informations</p>
            </div>
            <livewire:rooms.room-form :roomId="$room->id" />
        </div>
    </div>
</x-layouts.admin>
