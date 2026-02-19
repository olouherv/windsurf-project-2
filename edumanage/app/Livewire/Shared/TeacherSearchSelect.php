<?php

namespace App\Livewire\Shared;

use App\Models\Teacher;
use Livewire\Component;

class TeacherSearchSelect extends Component
{
    public string $inputName = 'supervisor_teacher_id';
    public string $nameInput = 'supervisor_teacher_name';
    public bool $allowCreate = false;

    public ?int $selectedId = null;
    public ?string $selectedName = null;

    public string $search = '';
    public bool $showDropdown = false;

    public function mount(
        string $inputName = 'supervisor_teacher_id',
        ?int $initialId = null,
        bool $allowCreate = false,
        string $nameInput = 'supervisor_teacher_name'
    ): void {
        $this->inputName = $inputName;
        $this->allowCreate = $allowCreate;
        $this->nameInput = $nameInput;
        $this->selectedId = $initialId;

        if ($initialId) {
            $teacher = Teacher::find($initialId);
            if ($teacher) {
                $this->selectedName = $teacher->full_name;
                $this->search = $teacher->full_name;
            }
        }
    }

    public function updatedSearch(string $value): void
    {
        $this->showDropdown = strlen($value) >= 2;
        if (!$value) {
            $this->selectedId = null;
            $this->selectedName = null;
        } else {
            if ($this->allowCreate) {
                $this->selectedId = null;
                $this->selectedName = $value;
            }
        }
    }

    public function select(int $id): void
    {
        $teacher = Teacher::find($id);
        if ($teacher) {
            $this->selectedId = $teacher->id;
            $this->selectedName = $teacher->full_name;
            $this->search = $teacher->full_name;
            $this->showDropdown = false;
        }
    }

    public function useTyped(): void
    {
        if (!$this->allowCreate) {
            return;
        }

        $this->selectedId = null;
        $this->selectedName = $this->search;
        $this->showDropdown = false;
    }

    public function clear(): void
    {
        $this->selectedId = null;
        $this->selectedName = null;
        $this->search = '';
        $this->showDropdown = false;
    }

    public function render()
    {
        $universityId = auth()->user()->university_id;

        $results = [];
        if ($this->showDropdown && strlen($this->search) >= 2) {
            $results = Teacher::where('university_id', $universityId)
                ->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('employee_id', 'like', '%' . $this->search . '%');
                })
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(10)
                ->get();
        }

        return view('livewire.shared.teacher-search-select', [
            'results' => $results,
        ]);
    }
}
