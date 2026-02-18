<?php

namespace App\Livewire\Teachers;

use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;

class TeacherList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public string $status = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = ['search', 'type', 'status'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteTeacher(int $id): void
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();
        
        session()->flash('success', __('Enseignant supprimé avec succès.'));
    }

    public function render()
    {
        $teachers = Teacher::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('employee_id', 'like', "%{$this->search}%");
                });
            })
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.teachers.teacher-list', compact('teachers'));
    }
}
