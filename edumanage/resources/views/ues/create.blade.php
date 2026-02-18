<x-layouts.admin>
    <x-slot name="header">Ajouter une UE</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Nouvelle Unité d'Enseignement</h3>
                <p class="text-sm text-gray-500">
                    {{ $semester->programYear->program->name }} > {{ $semester->programYear->name }} > {{ $semester->name }}
                </p>
            </div>
            <livewire:ues.ue-form :semesterId="$semester->id" />
        </div>
    </div>
</x-layouts.admin>
