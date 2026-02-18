<?php

namespace App\Livewire\Ues;

use App\Models\Semester;
use App\Models\Ue;
use Livewire\Component;

class UeForm extends Component
{
    public int $semesterId;
    public ?int $ueId = null;
    public bool $editMode = false;

    public string $code = '';
    public string $name = '';
    public float $credits_ects = 3;
    public float $coefficient = 1;
    public ?string $description = '';
    public bool $is_optional = false;

    protected function rules(): array
    {
        $ueId = $this->ueId;
        
        return [
            'code' => "required|string|max:20|unique:ues,code,{$ueId}",
            'name' => 'required|string|max:200',
            'credits_ects' => 'required|numeric|min:0.5|max:30',
            'coefficient' => 'required|numeric|min:0.1|max:10',
            'description' => 'nullable|string',
            'is_optional' => 'boolean',
        ];
    }

    public function mount(int $semesterId, ?int $ueId = null): void
    {
        $this->semesterId = $semesterId;
        
        if ($ueId) {
            $ue = Ue::findOrFail($ueId);
            $this->ueId = $ue->id;
            $this->editMode = true;
            $this->code = $ue->code ?? '';
            $this->name = $ue->name ?? '';
            $this->credits_ects = $ue->credits_ects ?? 3;
            $this->coefficient = $ue->coefficient ?? 1;
            $this->description = $ue->description ?? '';
            $this->is_optional = $ue->is_optional ?? false;
        }
    }

    public function save()
    {
        $validated = $this->validate();
        $validated['semester_id'] = $this->semesterId;

        if ($this->editMode && $this->ueId) {
            $ue = Ue::findOrFail($this->ueId);
            $ue->update($validated);
            session()->flash('success', __('UE mise à jour avec succès.'));
        } else {
            Ue::create($validated);
            session()->flash('success', __('UE créée avec succès.'));
        }

        $semester = Semester::with('programYear')->find($this->semesterId);
        return redirect()->route('programs.years.show', [
            'program' => $semester->programYear->program_id,
            'year' => $semester->programYear->id
        ]);
    }

    public function render()
    {
        return view('livewire.ues.ue-form');
    }
}
