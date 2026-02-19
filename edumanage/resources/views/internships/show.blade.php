<x-layouts.admin>
    <x-slot name="header">Détails du stage</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $internship->company_name }}</h1>
                    <p class="text-gray-500">{{ $internship->student?->full_name ?? '-' }} • {{ $internship->academicYear?->name ?? '-' }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('internships.edit', $internship) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Modifier</a>
                    <form method="POST" action="{{ route('internships.destroy', $internship) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50" onclick="return confirm('Supprimer ce stage ?')">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm text-gray-500">Entreprise</dt>
                    <dd class="text-gray-900">{{ $internship->company_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Encadrant</dt>
                    <dd class="text-gray-900">{{ $internship->supervisor?->full_name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Période</dt>
                    <dd class="text-gray-900">{{ $internship->start_date?->format('d/m/Y') ?? '-' }} - {{ $internship->end_date?->format('d/m/Y') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Statut</dt>
                    <dd class="text-gray-900">{{ $internship->status }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm text-gray-500">Sujet</dt>
                    <dd class="text-gray-900">{{ $internship->topic ?? '-' }}</dd>
                </div>
                @if($internship->notes)
                    <div class="md:col-span-2">
                        <dt class="text-sm text-gray-500">Notes</dt>
                        <dd class="text-gray-900">{{ $internship->notes }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="mt-6">
            <a href="{{ route('internships.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">Retour à la liste</a>
        </div>
    </div>
</x-layouts.admin>
