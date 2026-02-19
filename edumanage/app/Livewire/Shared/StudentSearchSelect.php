<?php

namespace App\Livewire\Shared;

use App\Models\Student;
use Livewire\Component;

class StudentSearchSelect extends Component
{
    public string $inputName = 'student_id';
    public ?int $selectedId = null;

    public string $search = '';
    public bool $showDropdown = false;

    public function mount(string $inputName = 'student_id', ?int $initialId = null): void
    {
        $this->inputName = $inputName;
        $this->selectedId = $initialId;

        if ($initialId) {
            $student = Student::find($initialId);
            if ($student) {
                $this->search = $student->full_name . ' (' . $student->student_id . ')';
            }
        }
    }

    public function updatedSearch(string $value): void
    {
        $this->showDropdown = strlen($value) >= 2;
        if (!$value) {
            $this->selectedId = null;
        }

        if (strlen($value) >= 2) {
            $universityId = auth()->user()->university_id;
            $exact = Student::where('university_id', $universityId)
                ->where('student_id', $value)
                ->first();

            if ($exact) {
                $this->select($exact->id);
            }
        }
    }

    public function select(int $id): void
    {
        $student = Student::find($id);
        if ($student) {
            $this->selectedId = $student->id;
            $this->search = $student->full_name . ' (' . $student->student_id . ')';
            $this->showDropdown = false;
        }
    }

    public function clear(): void
    {
        $this->selectedId = null;
        $this->search = '';
        $this->showDropdown = false;
    }

    public function render()
    {
        $universityId = auth()->user()->university_id;

        $results = [];
        if ($this->showDropdown && strlen($this->search) >= 2) {
            $results = Student::where('university_id', $universityId)
                ->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('student_id', 'like', '%' . $this->search . '%');
                })
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(10)
                ->get();
        }

        return view('livewire.shared.student-search-select', [
            'results' => $results,
        ]);
    }
}
