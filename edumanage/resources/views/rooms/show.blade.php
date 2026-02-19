<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <a href="{{ route('rooms.index') }}" class="text-gray-500 hover:text-gray-700">Salles</a>
            <span class="text-gray-400">/</span>
            <span>{{ $room->code }}</span>
        </div>
    </x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $room->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $room->code }} @if($room->building)• {{ $room->building }}@endif</p>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('rooms.edit', $room) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        Modifier
                    </a>
                </div>
            </div>
            <div class="p-6">
                @livewire('rooms.room-availability', ['room' => $room, 'academicYearId' => $academicYearId])
            </div>
        </div>
    </div>
</x-layouts.admin>
