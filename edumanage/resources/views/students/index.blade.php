<x-layouts.admin :title="__('Étudiants')">
    <x-slot name="header">{{ __('Gestion des étudiants') }}</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Liste des étudiants') }}</h3>
            <a href="{{ route('students.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                {{ __('Ajouter un étudiant') }}
            </a>
        </div>
        <livewire:students.student-list />
    </div>
</x-layouts.admin>
