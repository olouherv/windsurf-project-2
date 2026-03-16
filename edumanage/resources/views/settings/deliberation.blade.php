<x-layouts.admin :title="__('Paramètres de délibération')">
    <x-slot name="header">{{ __('Paramètres de délibération') }}</x-slot>

    <div class="mb-4">
        <a href="{{ route('deliberations.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
            ← Retour aux délibérations
        </a>
    </div>

    @livewire('deliberations.deliberation-settings')
</x-layouts.admin>
