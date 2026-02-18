<x-layouts.admin>
    <x-slot name="header">Modifier le contrat</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Modifier le contrat {{ $contract->contract_number }}</h3>
                <p class="text-sm text-gray-500">{{ $contract->student->full_name }}</p>
            </div>
            <livewire:contracts.contract-form :contract="$contract" />
        </div>
    </div>
</x-layouts.admin>
