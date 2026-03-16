<x-layouts.admin :title="__('Étudiants par filière')">
    <x-slot name="header">{{ __('Liste des étudiants par filière') }}</x-slot>

    @livewire('students.students-by-program')
</x-layouts.admin>
