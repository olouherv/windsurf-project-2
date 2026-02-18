<x-layouts.admin>
    <x-slot name="header">Ajouter un ECU</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Nouvel Élément Constitutif</h3>
                <p class="text-sm text-gray-500">
                    UE: {{ $ue->name }} ({{ $ue->code }})
                </p>
            </div>
            <livewire:ecus.ecu-form :ueId="$ue->id" />
        </div>
    </div>
</x-layouts.admin>
