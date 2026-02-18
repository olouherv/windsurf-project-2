<x-layouts.admin :title="__('Nouvel enseignant')">
    <x-slot name="header">Ajouter un enseignant</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Informations de l'enseignant</h3>
        </div>
        <livewire:teachers.teacher-form />
    </div>
</x-layouts.admin>
