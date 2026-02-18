<x-layouts.admin :title="__('Modifier enseignant')">
    <x-slot name="header">Modifier l'enseignant: {{ $teacher->first_name }} {{ $teacher->last_name }}</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Informations de l'enseignant</h3>
        </div>
        <livewire:teachers.teacher-form :teacherId="$teacher->id" />
    </div>
</x-layouts.admin>
