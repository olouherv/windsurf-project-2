<x-layouts.admin>
    <x-slot name="header">Modifier l'UE</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Modifier {{ $ue->name }}</h3>
                <p class="text-sm text-gray-500">
                    {{ $ue->semester->programYear->program->name }} > {{ $ue->semester->programYear->name }} > {{ $ue->semester->name }}
                </p>
            </div>
            <livewire:ues.ue-form :semesterId="$ue->semester_id" :ueId="$ue->id" />
        </div>
    </div>
</x-layouts.admin>
