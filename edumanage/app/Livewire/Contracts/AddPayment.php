<?php

namespace App\Livewire\Contracts;

use App\Models\ContractPayment;
use App\Models\StudentContract;
use Livewire\Component;

class AddPayment extends Component
{
    public StudentContract $contract;
    public bool $showModal = false;

    public ?int $payment_schedule_id = null;
    public float $amount = 0;
    public ?string $payment_date = null;
    public string $payment_method = 'cash';
    public ?string $reference = '';
    public ?string $receipt_number = '';
    public ?string $notes = '';

    protected function rules(): array
    {
        return [
            'payment_schedule_id' => 'nullable|exists:payment_schedules,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check,card,mobile_money,other',
            'reference' => 'nullable|string|max:100',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ];
    }

    public function updatedPaymentScheduleId($value): void
    {
        if ($value) {
            $schedule = $this->contract->paymentSchedules()->find($value);
            if ($schedule) {
                $this->amount = max(0, $schedule->remaining_amount);
            }
        } else {
            $this->amount = max(0, $this->contract->fresh()->remaining_amount);
        }
    }

    public function mount(StudentContract $contract): void
    {
        $this->contract = $contract;
        $this->payment_date = now()->format('Y-m-d');
        $this->amount = max(0, $this->contract->remaining_amount);
    }

    public function openModal(): void
    {
        $this->reset(['payment_schedule_id', 'amount', 'payment_method', 'reference', 'receipt_number', 'notes']);
        $this->payment_date = now()->format('Y-m-d');
        $this->amount = max(0, $this->contract->fresh()->remaining_amount);
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        ContractPayment::create([
            'student_contract_id' => $this->contract->id,
            'payment_schedule_id' => $this->payment_schedule_id,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'payment_method' => $this->payment_method,
            'reference' => $this->reference,
            'receipt_number' => $this->receipt_number,
            'notes' => $this->notes,
            'recorded_by' => auth()->id(),
        ]);

        $this->showModal = false;
        session()->flash('success', __('Paiement enregistré avec succès.'));
        
        return redirect()->route('contracts.show', $this->contract);
    }

    public function render()
    {
        return view('livewire.contracts.add-payment');
    }
}
