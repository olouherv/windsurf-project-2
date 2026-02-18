<x-layouts.admin :title="__('Enseignants')">
    <x-slot name="header">{{ __('Gestion des enseignants') }}</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Liste des enseignants') }}</h3>
            <a href="{{ route('teachers.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                {{ __('Ajouter un enseignant') }}
            </a>
        </div>
        <livewire:teachers.teacher-list />
    </div>
</x-layouts.admin>
