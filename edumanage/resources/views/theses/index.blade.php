<x-layouts.admin>
    <x-slot name="header">Mémoires</x-slot>

    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Mémoires</h2>
            <a href="{{ route('theses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Nouveau mémoire
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Soutenance</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($theses as $t)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <div class="font-medium">{{ $t->student?->full_name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $t->student?->student_id ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $t->title }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $t->defense_date?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $t->status }}</td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <a href="{{ route('theses.show', $t) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                    <a href="{{ route('theses.edit', $t) }}" class="ml-3 text-gray-600 hover:text-gray-900">Modifier</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Aucun mémoire</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $theses->links() }}
        </div>
    </div>
</x-layouts.admin>
