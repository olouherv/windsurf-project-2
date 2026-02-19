<x-layouts.admin>
    <x-slot name="header">Salles</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Gestion des salles</h3>
                    <p class="text-sm text-gray-500">Créer, modifier et consulter les disponibilités</p>
                </div>
                <a href="{{ route('rooms.create') }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouvelle salle
                </a>
            </div>
            <div class="p-6">
                @livewire('rooms.room-list')
            </div>
        </div>
    </div>
</x-layouts.admin>
