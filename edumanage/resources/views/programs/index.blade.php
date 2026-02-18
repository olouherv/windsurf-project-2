<x-layouts.admin :title="__('Programmes')">
    <x-slot name="header">{{ __('Gestion des programmes') }}</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Liste des programmes') }}</h3>
            <a href="{{ route('programs.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                {{ __('Ajouter un programme') }}
            </a>
        </div>
        <livewire:programs.program-list />
    </div>
</x-layouts.admin>
