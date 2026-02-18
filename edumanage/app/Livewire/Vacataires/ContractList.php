<?php

namespace App\Livewire\Vacataires;

use App\Models\AcademicYear;
use App\Models\VacataireContract;
use Livewire\Component;
use Livewire\WithPagination;

class ContractList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $academicYearId = '';

    protected $queryString = ['search', 'status', 'academicYearId'];

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
        $contracts = VacataireContract::query()
            ->with(['teacher', 'academicYear', 'ecu'])
            ->when($this->academicYearId, fn($q) => $q->where('academic_year_id', $this->academicYearId))
            ->when($this->search, function ($query) {
                $query->whereHas('teacher', function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                      ->orWhere('last_name', 'like', "%{$this->search}%")
                      ->orWhere('employee_id', 'like', "%{$this->search}%");
                })->orWhere('contract_number', 'like', "%{$this->search}%");
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.vacataires.contract-list', [
            'contracts' => $contracts,
            'academicYears' => AcademicYear::orderBy('start_date', 'desc')->get(),
        ]);
    }
}
