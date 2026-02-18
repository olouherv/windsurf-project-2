<?php

namespace App\Livewire\Programs;

use App\Models\Program;
use Livewire\Component;
use Livewire\WithPagination;

class ProgramList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $level = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    protected $queryString = ['search', 'level'];

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

    public function deleteProgram(int $id): void
    {
        $program = Program::findOrFail($id);
        $program->delete();
        
        session()->flash('success', __('Programme supprimé avec succès.'));
    }

    public function render()
    {
        $programs = Program::query()
            ->withCount('programYears')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%");
                });
            })
            ->when($this->level, fn($q) => $q->where('level', $this->level))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.programs.program-list', compact('programs'));
    }
}
