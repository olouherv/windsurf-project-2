<?php

namespace App\Livewire\Equipments;

use App\Models\Equipment;
use Livewire\Component;

class EquipmentList extends Component
{
    public string $search = '';

    public function delete(int $id): void
    {
        Equipment::whereKey($id)->delete();
        session()->flash('success', __('Équipement supprimé.'));
    }

    public function render()
    {
        $universityId = auth()->user()->university_id;

        $equipments = Equipment::where('university_id', $universityId)
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->get();

        return view('livewire.equipments.equipment-list', [
            'equipments' => $equipments,
        ]);
    }
}
