<x-layouts.admin :title="__('Modifier programme')">
    <x-slot name="header">Modifier le programme: {{ $program->name }}</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Informations du programme</h3>
        </div>
        <livewire:programs.program-form :programId="$program->id" />
    </div>
</x-layouts.admin>
