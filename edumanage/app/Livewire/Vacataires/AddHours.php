<?php

namespace App\Livewire\Vacataires;

use App\Models\Ecu;
use App\Models\VacataireContract;
use App\Models\VacataireHour;
use Livewire\Component;

class AddHours extends Component
{
    public VacataireContract $contract;
    public bool $showModal = false;

    public ?int $ecu_id = null;
    public ?string $date = null;
    public float $hours = 2;
    public string $type = 'cm';
    public ?string $description = '';

    protected function rules(): array
    {
        return [
            'ecu_id' => 'nullable|exists:ecus,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.5|max:10',
            'type' => 'required|in:cm,td,tp',
            'description' => 'nullable|string',
        ];
    }

    public function mount(VacataireContract $contract): void
    {
        $this->contract = $contract;
        $this->date = now()->format('Y-m-d');
    }

    public function openModal(): void
    {
        $this->reset(['ecu_id', 'hours', 'type', 'description']);
        $this->date = now()->format('Y-m-d');
        $this->hours = 2;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        VacataireHour::create([
            'vacataire_contract_id' => $this->contract->id,
            'ecu_id' => $this->ecu_id,
            'date' => $this->date,
            'hours' => $this->hours,
            'type' => $this->type,
            'description' => $this->description,
        ]);

        $this->showModal = false;
        session()->flash('success', __('Heures ajoutées avec succès.'));

        return redirect()->route('vacataire-contracts.show', $this->contract);
    }

    public function render()
    {
        $ecus = Ecu::whereHas('teachers', function ($q) {
            $q->where('teacher_id', $this->contract->teacher_id)
              ->where('academic_year_id', $this->contract->academic_year_id);
        })->with('ue')->get();

        return view('livewire.vacataires.add-hours', [
            'ecus' => $ecus,
        ]);
    }
}
