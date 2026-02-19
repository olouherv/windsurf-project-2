<div class="relative">
    <input type="hidden" name="{{ $inputName }}" value="{{ $selectedId }}" />

    <div class="flex gap-2">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            class="mt-1 w-full border-gray-300 rounded-md"
            placeholder="Saisir le nom ou matricule..."
            autocomplete="off"
        />
        @if($selectedId)
            <button type="button" wire:click="clear" class="mt-1 px-3 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">X</button>
        @endif
    </div>

    @if($showDropdown)
        <div class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-sm max-h-60 overflow-auto">
            @forelse($results as $s)
                <button type="button" wire:click="select({{ $s->id }})" class="w-full text-left px-3 py-2 hover:bg-gray-50">
                    <div class="text-sm text-gray-900 font-medium">{{ $s->full_name }}</div>
                    <div class="text-xs text-gray-500">{{ $s->student_id }}</div>
                </button>
            @empty
                <div class="px-3 py-2 text-sm text-gray-500">Aucun résultat</div>
            @endforelse
        </div>
    @endif
</div>
