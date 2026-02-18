<?php

namespace App\Livewire\Ecus;

use App\Models\Ecu;
use App\Models\Ue;
use Livewire\Component;

class EcuForm extends Component
{
    public int $ueId;
    public ?int $ecuId = null;
    public bool $editMode = false;

    public string $code = '';
    public string $name = '';
    public float $credits_ects = 1;
    public float $coefficient = 1;
    public int $hours_cm = 0;
    public int $hours_td = 0;
    public int $hours_tp = 0;
    public ?string $description = '';
    public ?string $objectives = '';

    protected function rules(): array
    {
        $ecuId = $this->ecuId;
        
        return [
            'code' => "required|string|max:20|unique:ecus,code,{$ecuId}",
            'name' => 'required|string|max:200',
            'credits_ects' => 'required|numeric|min:0.5|max:30',
            'coefficient' => 'required|numeric|min:0.1|max:10',
            'hours_cm' => 'required|integer|min:0',
            'hours_td' => 'required|integer|min:0',
            'hours_tp' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
        ];
    }

    public function mount(int $ueId, ?int $ecuId = null): void
    {
        $this->ueId = $ueId;
        
        if ($ecuId) {
            $ecu = Ecu::findOrFail($ecuId);
            $this->ecuId = $ecu->id;
            $this->editMode = true;
            $this->code = $ecu->code ?? '';
            $this->name = $ecu->name ?? '';
            $this->credits_ects = $ecu->credits_ects ?? 1;
            $this->coefficient = $ecu->coefficient ?? 1;
            $this->hours_cm = $ecu->hours_cm ?? 0;
            $this->hours_td = $ecu->hours_td ?? 0;
            $this->hours_tp = $ecu->hours_tp ?? 0;
            $this->description = $ecu->description ?? '';
            $this->objectives = $ecu->objectives ?? '';
        }
    }

    public function save()
    {
        $validated = $this->validate();
        $validated['ue_id'] = $this->ueId;

        if ($this->editMode && $this->ecuId) {
            $ecu = Ecu::findOrFail($this->ecuId);
            $ecu->update($validated);
            session()->flash('success', __('ECU mise à jour avec succès.'));
        } else {
            Ecu::create($validated);
            session()->flash('success', __('ECU créée avec succès.'));
        }

        return redirect()->route('ues.show', $this->ueId);
    }

    public function render()
    {
        return view('livewire.ecus.ecu-form');
    }
}
