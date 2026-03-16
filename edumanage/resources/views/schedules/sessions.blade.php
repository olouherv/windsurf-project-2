<x-layouts.admin :title="__('Gestion des séances')">
    <x-slot name="header">{{ __('Séances de cours') }}</x-slot>

    @livewire('schedules.session-manager')
</x-layouts.admin>
