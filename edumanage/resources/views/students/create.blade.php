<x-layouts.admin :title="__('Nouvel étudiant')">
    <x-slot name="header">{{ __('Ajouter un étudiant') }}</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Informations de l\'étudiant') }}</h3>
        </div>
        <livewire:students.student-form />
    </div>
</x-layouts.admin>
