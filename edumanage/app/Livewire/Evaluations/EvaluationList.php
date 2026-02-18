<?php

namespace App\Livewire\Evaluations;

use App\Models\AcademicYear;
use App\Models\Evaluation;
use Livewire\Component;
use Livewire\WithPagination;

class EvaluationList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public string $academicYearId = '';
    public string $ecuId = '';

    protected $queryString = ['search', 'type', 'academicYearId'];

    public function mount(): void
    {
        if (!$this->academicYearId) {
            $currentYear = AcademicYear::where('is_current', true)->first();
            $this->academicYearId = $currentYear?->id ?? '';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $evaluations = Evaluation::query()
            ->with(['ecu.ue', 'academicYear'])
            ->withCount('grades')
            ->when($this->academicYearId, fn($q) => $q->where('academic_year_id', $this->academicYearId))
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->when($this->ecuId, fn($q) => $q->where('ecu_id', $this->ecuId))
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhereHas('ecu', fn($q) => $q->where('code', 'like', "%{$this->search}%")->orWhere('name', 'like', "%{$this->search}%"));
            })
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('livewire.evaluations.evaluation-list', [
            'evaluations' => $evaluations,
            'academicYears' => AcademicYear::orderBy('start_date', 'desc')->get(),
        ]);
    }
}
