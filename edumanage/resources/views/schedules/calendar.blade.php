<x-layouts.admin :title="__('Calendrier de planification')">
    <x-slot name="header">{{ __('Calendrier de planification') }}</x-slot>

    @livewire('schedules.schedule-calendar')
</x-layouts.admin>
