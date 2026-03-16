<x-layouts.admin :title="__('Gestion des présences')">
    <x-slot name="header">{{ __('Absences & Présences') }}</x-slot>

    @livewire('attendance.attendance-manager')
</x-layouts.admin>
