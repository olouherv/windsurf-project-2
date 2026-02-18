<?php

namespace App\Livewire\Programs;

use App\Models\Program;
use Livewire\Component;

class ProgramForm extends Component
{
    public ?int $programId = null;
    public bool $editMode = false;

    public string $code = '';
    public string $name = '';
    public ?string $description = '';
    public string $level = 'licence';
    public int $duration_years = 3;
    public int $total_credits = 180;
    public bool $is_active = true;

    protected function rules(): array
    {
        $programId = $this->programId;
        $universityId = auth()->user()->university_id;

        return [
            'code' => "required|string|max:20|unique:programs,code,{$programId},id,university_id,{$universityId}",
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'level' => 'required|in:licence,master,doctorat,dut,bts',
            'duration_years' => 'required|integer|min:1|max:8',
            'total_credits' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ];
    }

    public function mount($programId = null): void
    {
        if ($programId) {
            $program = Program::find($programId);
            
            if ($program) {
                $this->programId = $program->id;
                $this->editMode = true;
                $this->code = $program->code ?? '';
                $this->name = $program->name ?? '';
                $this->description = $program->description ?? '';
                $this->level = $program->level ?? 'licence';
                $this->duration_years = $program->duration_years ?? 3;
                $this->total_credits = $program->total_credits ?? 180;
                $this->is_active = $program->is_active ?? true;
            }
        }
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->editMode && $this->programId) {
            $program = Program::findOrFail($this->programId);
            $program->update($validated);
            session()->flash('success', __('Programme mis à jour avec succès.'));
        } else {
            $validated['university_id'] = auth()->user()->university_id;
            Program::create($validated);
            session()->flash('success', __('Programme créé avec succès.'));
        }

        return redirect()->route('programs.index');
    }

    public function render()
    {
        return view('livewire.programs.program-form');
    }
}
