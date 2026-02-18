<div class="p-6">
    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4 mb-6">
        <div class="flex-1">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Rechercher...') }}" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <select wire:model.live="level" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">{{ __('Tous les niveaux') }}</option>
            <option value="licence">{{ __('Licence') }}</option>
            <option value="master">{{ __('Master') }}</option>
            <option value="doctorat">{{ __('Doctorat') }}</option>
            <option value="dut">{{ __('DUT') }}</option>
            <option value="bts">{{ __('BTS') }}</option>
        </select>
    </div>

    <!-- Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($programs as $program)
        <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800 uppercase">
                        {{ __($program->level) }}
                    </span>
                </div>
                <span class="text-sm text-gray-500">{{ $program->code }}</span>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $program->name }}</h3>
            
            <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $program->description ?? __('Pas de description') }}</p>
            
            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                <span>{{ $program->duration_years }} {{ __('ans') }}</span>
                <span>{{ $program->total_credits }} {{ __('crédits ECTS') }}</span>
            </div>

            @if($program->moodle_category_id)
            <div class="mb-4">
                <span class="text-xs text-green-600">✓ {{ __('Moodle synchronisé') }}</span>
            </div>
            @endif
            
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('programs.show', $program) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    {{ __('Voir détails') }} →
                </a>
                <div class="flex space-x-2">
                    <a href="{{ route('programs.years.index', $program) }}" class="text-green-600 hover:text-green-900" title="{{ __('Gérer les années') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </a>
                    <a href="{{ route('programs.edit', $program) }}" class="text-yellow-600 hover:text-yellow-900" title="{{ __('Modifier') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <button wire:click="deleteProgram({{ $program->id }})" wire:confirm="{{ __('Êtes-vous sûr ?') }}" class="text-red-600 hover:text-red-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-gray-500">
            {{ __('Aucun programme trouvé.') }}
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $programs->links() }}
    </div>
</div>
