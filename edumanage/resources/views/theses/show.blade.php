<x-layouts.admin>
    <x-slot name="header">Détails du mémoire</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $thesis->title }}</h1>
                    <p class="text-gray-500">{{ $thesis->student?->full_name ?? '-' }} • {{ $thesis->academicYear?->name ?? '-' }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('theses.edit', $thesis) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Modifier</a>
                    <form method="POST" action="{{ route('theses.destroy', $thesis) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50" onclick="return confirm('Supprimer ce mémoire ?')">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm text-gray-500">Encadrant</dt>
                    <dd class="text-gray-900">{{ $thesis->supervisor?->full_name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Statut</dt>
                    <dd class="text-gray-900">{{ $thesis->status }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Soumission</dt>
                    <dd class="text-gray-900">{{ $thesis->submission_date?->format('d/m/Y') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Soutenance</dt>
                    <dd class="text-gray-900">{{ $thesis->defense_date?->format('d/m/Y') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Note</dt>
                    <dd class="text-gray-900">{{ $thesis->grade !== null ? number_format((float) $thesis->grade, 2) . '/20' : '-' }}</dd>
                </div>
                @if($thesis->abstract)
                    <div class="md:col-span-2">
                        <dt class="text-sm text-gray-500">Résumé</dt>
                        <dd class="text-gray-900">{{ $thesis->abstract }}</dd>
                    </div>
                @endif
                @if($thesis->notes)
                    <div class="md:col-span-2">
                        <dt class="text-sm text-gray-500">Notes</dt>
                        <dd class="text-gray-900">{{ $thesis->notes }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="mt-6">
            <a href="{{ route('theses.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">Retour à la liste</a>
        </div>
    </div>
</x-layouts.admin>
