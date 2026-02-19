<x-layouts.admin>
    <x-slot name="header">Modifier le plan</x-slot>

    <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Modifier le plan</h3>
                <p class="text-sm text-gray-500">{{ $plan->name }} ({{ $plan->key }})</p>
            </div>

            <form method="POST" action="{{ route('admin.pricing-plans.update', $plan) }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700">Clé</label>
                    <input type="text" name="key" value="{{ old('key', $plan->key) }}" class="mt-1 w-full border-gray-300 rounded-md" required />
                    @error('key') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" name="name" value="{{ old('name', $plan->name) }}" class="mt-1 w-full border-gray-300 rounded-md" required />
                    @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prix mensuel</label>
                        <input type="number" step="0.01" min="0" name="price_monthly" value="{{ old('price_monthly', $plan->price_monthly) }}" class="mt-1 w-full border-gray-300 rounded-md" required />
                        @error('price_monthly') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prix annuel</label>
                        <input type="number" step="0.01" min="0" name="price_yearly" value="{{ old('price_yearly', $plan->price_yearly) }}" class="mt-1 w-full border-gray-300 rounded-md" required />
                        @error('price_yearly') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Devise</label>
                        <input type="text" name="currency" value="{{ old('currency', $plan->currency) }}" class="mt-1 w-full border-gray-300 rounded-md" required />
                        @error('currency') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" {{ old('is_active', $plan->is_active) ? 'checked' : '' }} />
                    <span class="ml-2 text-sm text-gray-700">Actif</span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Modules inclus</label>
                    @php
                        $selected = old('included_modules', $plan->included_modules ?? []);
                    @endphp
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach($modules as $key => $info)
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="included_modules[]" value="{{ $key }}" class="rounded border-gray-300" {{ in_array($key, $selected, true) ? 'checked' : '' }} />
                                <span>{{ $info['name'] ?? $key }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <a href="{{ route('admin.pricing-plans.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Annuler</a>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
