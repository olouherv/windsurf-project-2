<x-layouts.admin>
    <x-slot name="header">Plans & Tarifs</x-slot>

    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Plans & Tarifs</h2>
                <p class="text-sm text-gray-500">Vue Super Admin : gestion des offres et tarifs</p>
            </div>
            <a href="{{ route('admin.pricing-plans.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau plan
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clé</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mensuel</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Annuel</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Devise</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actif</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($plans as $plan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-mono text-gray-700">{{ $plan->key }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $plan->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ number_format($plan->price_monthly, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ number_format($plan->price_yearly, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $plan->currency }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $plan->is_active ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <a href="{{ route('admin.pricing-plans.edit', $plan) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                    <form method="POST" action="{{ route('admin.pricing-plans.destroy', $plan) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-3 text-red-600 hover:text-red-900" onclick="return confirm('Supprimer ce plan ?')">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">Aucun plan pour le moment</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
