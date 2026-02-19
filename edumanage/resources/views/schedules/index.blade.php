<x-layouts.admin>
    <x-slot name="header">Planification</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Emplois du temps</h3>
                <p class="text-sm text-gray-500">Créer et gérer les séances (CM/TD/TP)</p>
            </div>
            <div class="p-6">
                @livewire('schedules.schedule-manager')
            </div>
        </div>
    </div>
</x-layouts.admin>
