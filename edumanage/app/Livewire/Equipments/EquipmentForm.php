<?php

namespace App\Livewire\Equipments;

use App\Models\Equipment;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EquipmentForm extends Component
{
    public ?int $equipmentId = null;
    public bool $editMode = false;

    public string $name = '';
    public ?string $code = '';
    public ?string $description = '';
    public bool $is_active = true;

    protected function rules(): array
    {
        $equipmentId = $this->equipmentId;
        $universityId = auth()->user()->university_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('equipments', 'code')->where('university_id', $universityId)->ignore($equipmentId),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function mount(?int $equipmentId = null): void
    {
        if ($equipmentId) {
            $equipment = Equipment::whereKey($equipmentId)->first();
            if ($equipment) {
                $this->equipmentId = $equipment->id;
                $this->editMode = true;
                $this->name = $equipment->name;
                $this->code = $equipment->code;
                $this->description = $equipment->description;
                $this->is_active = (bool) $equipment->is_active;
            }
        }
    }

    public function save()
    {
        $data = $this->validate();

        $payload = [
            'university_id' => auth()->user()->university_id,
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'],
            'is_active' => $data['is_active'],
        ];

        if ($this->editMode && $this->equipmentId) {
            Equipment::whereKey($this->equipmentId)->update($payload);
            session()->flash('success', __('Équipement mis à jour.'));
        } else {
            Equipment::create($payload);
            session()->flash('success', __('Équipement créé.'));
        }

        return $this->redirect(route('equipments.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.equipments.equipment-form');
    }
}
