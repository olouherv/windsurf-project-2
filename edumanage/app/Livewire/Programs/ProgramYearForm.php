<?php

namespace App\Livewire\Programs;

use App\Models\Program;
use App\Models\ProgramYear;
use Livewire\Component;

class ProgramYearForm extends Component
{
    public int $programId;
    public ?int $yearId = null;
    public bool $editMode = false;

    public string $name = '';
    public int $year_number = 1;
    public ?string $description = '';
    public float $tuition_fees = 0;
    public float $registration_fees = 0;
    public int $default_installments = 1;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'year_number' => 'required|integer|min:1|max:8',
            'description' => 'nullable|string',
            'tuition_fees' => 'required|numeric|min:0',
            'registration_fees' => 'required|numeric|min:0',
            'default_installments' => 'required|integer|min:1|max:12',
        ];
    }

    public function mount(int $programId, ?int $yearId = null): void
    {
        $this->programId = $programId;
        
        if ($yearId) {
            $year = ProgramYear::findOrFail($yearId);
            $this->yearId = $year->id;
            $this->editMode = true;
            $this->name = $year->name ?? '';
            $this->year_number = $year->year_number ?? 1;
            $this->description = $year->description ?? '';
            $this->tuition_fees = $year->tuition_fees ?? 0;
            $this->registration_fees = $year->registration_fees ?? 0;
            $this->default_installments = $year->default_installments ?? 1;
        }
    }

    public function save()
    {
        $validated = $this->validate();
        $validated['program_id'] = $this->programId;

        if ($this->editMode && $this->yearId) {
            $year = ProgramYear::findOrFail($this->yearId);
            $year->update($validated);
            session()->flash('success', __('Année de formation mise à jour avec succès.'));
        } else {
            ProgramYear::create($validated);
            session()->flash('success', __('Année de formation créée avec succès.'));
        }

        return redirect()->route('programs.years.index', $this->programId);
    }

    public function render()
    {
        return view('livewire.programs.program-year-form');
    }
}
