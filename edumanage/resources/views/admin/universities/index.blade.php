<x-layouts.admin>
    <x-slot name="header">Universités</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Gestion Globale — Universités</h3>
                <p class="text-sm text-gray-500">Vue Super Admin : universités, modules activés/désactivés, effectifs (V1)</p>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Université</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Admin</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Offre</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inscription</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Démo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Effectifs</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modules</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($universities as $u)
                                @php
                                    $enabledKeys = $u->moduleSettings->where('is_enabled', true)->pluck('module_key')->values()->all();
                                    $adminUser = $u->users->firstWhere('user_type', 'admin');
                                @endphp
                                <tr class="hover:bg-gray-50 align-top">
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-semibold text-gray-900">{{ $u->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $u->code }} • {{ $u->email }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        @if($adminUser)
                                            <div class="font-medium">{{ $adminUser->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $adminUser->email }}</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <form method="POST" action="{{ route('admin.universities.plan.set', $u) }}" class="flex items-center gap-2">
                                            @csrf
                                            <select name="pricing_plan_id" class="border-gray-300 rounded-md text-sm">
                                                <option value="">Aucun</option>
                                                @foreach($plans as $p)
                                                    <option value="{{ $p->id }}" {{ (int) $u->pricing_plan_id === (int) $p->id ? 'selected' : '' }}>
                                                        {{ $p->name }} ({{ $p->currency }} {{ number_format($p->price_monthly, 2) }}/mo)
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="px-3 py-1 rounded-md bg-indigo-600 text-white text-xs hover:bg-indigo-700">OK</button>
                                        </form>
                                        @if($u->plan_key)
                                            <div class="text-xs text-gray-500 mt-1">Clé: {{ $u->plan_key }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $u->created_at?->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        @if($u->trial_ends_at)
                                            @if($u->trial_ends_at->isFuture())
                                                <span class="text-gray-700">{{ $u->trialRemainingHuman() }} restant</span>
                                            @else
                                                <span class="text-red-600">Expirée</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <div>Étudiants: {{ $u->students_count }}</div>
                                        <div>Enseignants: {{ $u->teachers_count }}</div>
                                        <div>Salles: {{ $u->rooms_count }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($modules as $key => $info)
                                                @php
                                                    $isEnabled = in_array($key, $enabledKeys, true);
                                                    $isRequired = $info['required'] ?? false;
                                                @endphp
                                                <form method="POST" action="{{ route('admin.universities.modules.toggle', ['university' => $u->id, 'moduleKey' => $key]) }}">
                                                    @csrf
                                                    <button type="submit" {{ $isRequired ? 'disabled' : '' }} class="px-2 py-1 rounded-full text-xs {{ $isEnabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} {{ $isRequired ? 'opacity-60 cursor-not-allowed' : 'hover:opacity-80' }}" title="{{ $isRequired ? 'Module obligatoire' : 'Cliquer pour activer/désactiver' }}">
                                                        {{ $info['name'] ?? $key }}
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-sm text-gray-500">
                    {{ $universities->count() }} université(s)
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
