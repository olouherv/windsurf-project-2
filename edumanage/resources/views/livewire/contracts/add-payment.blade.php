<div>
    <button wire:click="openModal" class="inline-flex items-center px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Ajouter un paiement
    </button>

    @if($showModal)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Nouveau paiement</h3>
                <p class="text-sm text-gray-500">Reste à payer: {{ number_format($contract->remaining_amount, 2) }} €</p>
            </div>
            
            <form wire:submit.prevent="save">
                <div class="px-6 py-4 space-y-4">
                    @if($contract->paymentSchedules->count() > 0)
                    <div>
                        <label for="payment_schedule_id" class="block text-sm font-medium text-gray-700 mb-1">Tranche de paiement</label>
                        <select wire:model.live="payment_schedule_id" id="payment_schedule_id"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Paiement général --</option>
                            @foreach($contract->paymentSchedules->sortBy('installment_number') as $schedule)
                            @if($schedule->remaining_amount > 0)
                            <option value="{{ $schedule->id }}">
                                {{ $schedule->label }} - Reste: {{ number_format($schedule->remaining_amount, 2) }} € (échéance: {{ $schedule->due_date->format('d/m/Y') }})
                            </option>
                            @endif
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Sélectionnez une tranche pour cibler le paiement</p>
                    </div>
                    @endif

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Montant (€) *</label>
                        <input type="number" wire:model="amount" id="amount" step="0.01" min="0.01"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Date de paiement *</label>
                        <input type="date" wire:model="payment_date" id="payment_date"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('payment_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement *</label>
                        <select wire:model="payment_method" id="payment_method"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="cash">Espèces</option>
                            <option value="bank_transfer">Virement bancaire</option>
                            <option value="check">Chèque</option>
                            <option value="card">Carte bancaire</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="other">Autre</option>
                        </select>
                        @error('payment_method') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">Référence</label>
                            <input type="text" wire:model="reference" id="reference"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="receipt_number" class="block text-sm font-medium text-gray-700 mb-1">N° Reçu</label>
                            <input type="text" wire:model="receipt_number" id="receipt_number"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea wire:model="notes" id="notes" rows="2"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                        Enregistrer le paiement
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
