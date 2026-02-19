<div>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Recherche</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Code, nom, bâtiment..."
                class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($rooms->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bâtiment</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacité</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rooms as $room)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-mono text-gray-700">{{ $room->code }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('rooms.show', $room) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                    {{ $room->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $room->building ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $room->capacity }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('rooms.edit', $room) }}" class="text-indigo-600 hover:text-indigo-800">Modifier</a>
                                    <button type="button" wire:click="delete({{ $room->id }})" wire:confirm="Supprimer cette salle ?" class="text-red-600 hover:text-red-800">Supprimer</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-sm text-gray-500">
            {{ $rooms->count() }} salle(s)
        </div>
    @else
        <div class="text-center py-10 text-gray-500">
            <p>Aucune salle</p>
        </div>
    @endif
</div>
