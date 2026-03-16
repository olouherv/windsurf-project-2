<x-layouts.admin :title="__('Délibérations')">
    <x-slot name="header">{{ __('Délibérations') }}</x-slot>

    @livewire('deliberations.deliberation-manager')
</x-layouts.admin>
