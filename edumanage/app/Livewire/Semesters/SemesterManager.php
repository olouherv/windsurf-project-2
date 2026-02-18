<?php

namespace App\Livewire\Semesters;

use App\Models\ProgramYear;
use App\Models\Semester;
use Livewire\Component;

class SemesterManager extends Component
{
    public ProgramYear $programYear;
    
    public bool $showModal = false;
    public ?int $editingSemesterId = null;
    
    public string $name = '';
    public int $semester_number = 1;
    public ?string $start_date = null;
    public ?string $end_date = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'semester_number' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function mount(ProgramYear $programYear)
    {
        $this->programYear = $programYear;
    }

    public function openModal()
    {
        $this->reset(['name', 'semester_number', 'start_date', 'end_date', 'editingSemesterId']);
        $this->semester_number = $this->programYear->semesters()->count() + 1;
        $this->showModal = true;
    }

    public function editSemester(Semester $semester)
    {
        $this->editingSemesterId = $semester->id;
        $this->name = $semester->name;
        $this->semester_number = $semester->semester_number;
        $this->start_date = $semester->start_date?->format('Y-m-d');
        $this->end_date = $semester->end_date?->format('Y-m-d');
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'program_year_id' => $this->programYear->id,
            'name' => $this->name,
            'semester_number' => $this->semester_number,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];

        if ($this->editingSemesterId) {
            Semester::find($this->editingSemesterId)->update($data);
            session()->flash('message', 'Semestre modifié avec succès.');
        } else {
            Semester::create($data);
            session()->flash('message', 'Semestre créé avec succès.');
        }

        $this->showModal = false;
        $this->reset(['name', 'semester_number', 'start_date', 'end_date', 'editingSemesterId']);
        
        return redirect()->route('programs.years.show', [
            'program' => $this->programYear->program_id,
            'year' => $this->programYear->id
        ]);
    }

    public function deleteSemester(Semester $semester)
    {
        if ($semester->ues()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer ce semestre car il contient des UEs.');
            return;
        }
        
        $semester->delete();
        session()->flash('message', 'Semestre supprimé avec succès.');
        
        return redirect()->route('programs.years.show', [
            'program' => $this->programYear->program_id,
            'year' => $this->programYear->id
        ]);
    }

    public function render()
    {
        return view('livewire.semesters.semester-manager', [
            'semesters' => $this->programYear->semesters()->orderBy('semester_number')->get()
        ]);
    }
}
